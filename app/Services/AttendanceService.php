<?php

namespace App\Services;

use App\User;
use App\Attendance;
use App\AttendanceRule;
use App\EmployeeDevice;
use App\AttendanceQrToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    /**
     * Resolve the active rule for an employee on a given date.
     */
    public function getApplicableRuleForUser(User $user, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $dayOfWeek = $date->format('l');

        // Get all active rules
        $rules = AttendanceRule::where('status', 'active')
            ->orderBy('applies_to', 'desc') // priority: selected_employees, department, branch, all_employees
            ->get();

        // 1. Employee-specific rule
        foreach ($rules as $rule) {
            if ($rule->applies_to === 'selected_employees') {
                $employeeIds = is_string($rule->employee_ids) ? json_decode($rule->employee_ids, true) : $rule->employee_ids;
                $workingDays = is_string($rule->working_days) ? json_decode($rule->working_days, true) : $rule->working_days;

                if (is_array($employeeIds) && in_array($user->id, $employeeIds)) {
                    if (is_array($workingDays) && in_array($dayOfWeek, $workingDays)) {
                        return $rule;
                    }
                }
            }
        }

        // 2. Department rule
        if ($user->department_id) {
            foreach ($rules as $rule) {
                if ($rule->applies_to === 'department' && $rule->department_id == $user->department_id) {
                    $workingDays = is_string($rule->working_days) ? json_decode($rule->working_days, true) : $rule->working_days;
                    if (is_array($workingDays) && in_array($dayOfWeek, $workingDays)) {
                        return $rule;
                    }
                }
            }
        }

        // 3. Branch rule (Fallback check in case we match department to company if needed, otherwise skip)

        // 4. Company-wide rule
        foreach ($rules as $rule) {
            if ($rule->applies_to === 'all_employees') {
                $workingDays = is_string($rule->working_days) ? json_decode($rule->working_days, true) : $rule->working_days;
                if (is_array($workingDays) && in_array($dayOfWeek, $workingDays)) {
                    return $rule;
                }
            }
        }

        return null;
    }

    /**
     * Validate check-in request parameters.
     */
    public function validateCheckIn(User $user, AttendanceRule $rule, array $data)
    {
        $now = Carbon::now();

        // 1. Validate check-in time cutoff
        $timeStr = $now->format('H:i:s');
        if ($timeStr > $rule->check_in_cutoff_time) {
            return ['status' => false, 'message' => 'Check-in window is closed for today. Cutoff was ' . $rule->check_in_cutoff_time];
        }
        if ($timeStr < $rule->check_in_start_time) {
            return ['status' => false, 'message' => 'Check-in window has not opened yet. Starts at ' . $rule->check_in_start_time];
        }

        // 2. Validate QR token (if kiosk validation)
        if (isset($data['qr_token']) && $data['qr_token']) {
            $qrToken = AttendanceQrToken::where('token', $data['qr_token'])
                ->where('attendance_rule_id', $rule->id)
                ->first();

            if (!$qrToken || Carbon::parse($qrToken->expires_at)->isPast()) {
                return ['status' => false, 'message' => 'Invalid or expired QR code. Please scan a fresh QR code.'];
            }
        }

        // 3. Validate GPS Radius (Haversine distance)
        if ($rule->allowed_radius_meters && $rule->office_latitude && $rule->office_longitude && !isset($data['is_kiosk_scan'])) {
            if (!isset($data['latitude']) || !isset($data['longitude'])) {
                return ['status' => false, 'message' => 'GPS location parameters are required for this check-in.'];
            }

            $distance = $this->calculateDistance(
                $data['latitude'],
                $data['longitude'],
                $rule->office_latitude,
                $rule->office_longitude
            );

            if ($distance > $rule->allowed_radius_meters) {
                return ['status' => false, 'message' => 'Location validation failed. You are outside the allowed office area.'];
            }
        }

        // 4. Validate Device Lock
        if ($rule->device_lock_required) {
            if (!isset($data['device_uuid']) || !$data['device_uuid']) {
                return ['status' => false, 'message' => 'Device fingerprinting is required. Please use the mobile app.'];
            }

            $devicesCount = EmployeeDevice::where('user_id', $user->id)->count();

            if ($devicesCount == 0) {
                // First-time bind
                EmployeeDevice::create([
                    'user_id' => $user->id,
                    'device_uuid' => $data['device_uuid'],
                    'device_name' => $data['device_name'] ?? 'Mobile Device',
                    'is_trusted' => true
                ]);
            } else {
                // Match trusted device
                $matched = EmployeeDevice::where('user_id', $user->id)
                    ->where('device_uuid', $data['device_uuid'])
                    ->where('is_trusted', true)
                    ->exists();

                if (!$matched) {
                    return ['status' => false, 'message' => 'This device is not registered to your account. Multiple device clock-ins are blocked.'];
                }
            }
        }

        // 5. Selfie Verification
        if ($rule->selfie_required) {
            if (!isset($data['selfie_file']) && !isset($data['selfie_base64'])) {
                return ['status' => false, 'message' => 'Selfie verification is required for this clock-in.'];
            }
        }

        return ['status' => true];
    }

    /**
     * Process check-in transaction.
     */
    public function processCheckIn(User $user, AttendanceRule $rule, array $data)
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Save Selfie
        $selfiePath = null;
        if (isset($data['selfie_file'])) {
            $selfiePath = $data['selfie_file']->store('selfies', 'public');
        } elseif (isset($data['selfie_base64']) && $data['selfie_base64']) {
            $selfiePath = $this->saveBase64Selfie($data['selfie_base64']);
        }

        // Determine attendance status (Late vs Present)
        $limitTime = Carbon::createFromFormat('H:i:s', $rule->check_in_start_time);
        if ($rule->grace_period_minutes) {
            $limitTime->addMinutes($rule->grace_period_minutes);
        }
        
        $status = $now->format('H:i:s') > $limitTime->format('H:i:s') ? 'Late' : 'Present';

        // Find or create daily session
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            // Update scheduled session
            $attendance->update([
                'check_in' => $now,
                'status' => $status,
                'attendance_rule_id' => $rule->id,
                'company_id' => $rule->company_id,
                'check_in_selfie' => $selfiePath,
                'check_in_latitude' => $data['latitude'] ?? null,
                'check_in_longitude' => $data['longitude'] ?? null,
                'device_fingerprint' => $data['device_uuid'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
            ]);
        } else {
            // Fallback: create new row if no pre-generated session exists
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $now,
                'status' => $status,
                'attendance_rule_id' => $rule->id,
                'company_id' => $rule->company_id,
                'check_in_selfie' => $selfiePath,
                'check_in_latitude' => $data['latitude'] ?? null,
                'check_in_longitude' => $data['longitude'] ?? null,
                'device_fingerprint' => $data['device_uuid'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
            ]);
        }

        return $attendance;
    }

    /**
     * Validate and process check-out request.
     */
    public function processCheckOut(User $user, array $data)
    {
        $now = Carbon::now();
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // 1. Resolve Session: first check if there's a today check-in
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->whereNotNull('check_in')
            ->first();

        // If not found, look for overnight shift check-in from yesterday
        if (!$attendance) {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $yesterday)
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->first();
        }

        if (!$attendance) {
            return ['status' => 'error', 'message' => 'No active check-in record found for checkout.'];
        }

        $rule = $attendance->rule;
        if (!$rule) {
            $rule = $this->getApplicableRuleForUser($user, $attendance->date);
        }

        if (!$rule || !$rule->check_out_enabled) {
            return ['status' => 'error', 'message' => 'Check-out is not enabled for this shift.'];
        }

        // Validate checkout time boundaries if set
        $timeStr = $now->format('H:i:s');
        if ($rule->check_out_cutoff_time && $timeStr > $rule->check_out_cutoff_time) {
            return ['status' => 'error', 'message' => 'Check-out cutoff time has passed (' . $rule->check_out_cutoff_time . ')'];
        }
        if ($rule->check_out_start_time && $timeStr < $rule->check_out_start_time && $attendance->date == $today) {
            return ['status' => 'error', 'message' => 'Check-out window has not opened yet (' . $rule->check_out_start_time . ')'];
        }

        // Validate Checkout Selfie
        $selfiePath = null;
        if ($rule->checkout_selfie_required) {
            if (!isset($data['selfie_file']) && !isset($data['selfie_base64'])) {
                return ['status' => 'error', 'message' => 'Selfie verification is required for this clock-out.'];
            }
            if (isset($data['selfie_file'])) {
                $selfiePath = $data['selfie_file']->store('selfies', 'public');
            } elseif (isset($data['selfie_base64']) && $data['selfie_base64']) {
                $selfiePath = $this->saveBase64Selfie($data['selfie_base64']);
            }
        }

        // Validate Checkout GPS
        if ($rule->allowed_radius_meters && $rule->office_latitude && $rule->office_longitude && !isset($data['is_kiosk_scan'])) {
            if (!isset($data['latitude']) || !isset($data['longitude'])) {
                return ['status' => 'error', 'message' => 'GPS location parameters are required for this check-out.'];
            }

            $distance = $this->calculateDistance(
                $data['latitude'],
                $data['longitude'],
                $rule->office_latitude,
                $rule->office_longitude
            );

            if ($distance > $rule->allowed_radius_meters) {
                return ['status' => 'error', 'message' => 'Location validation failed. You are outside the allowed office area.'];
            }
        }

        // Calculate Work Duration
        $checkInTime = Carbon::parse($attendance->check_in);
        $durationMinutes = $checkInTime->diffInMinutes($now);

        if ($rule->minimum_work_duration_minutes && $durationMinutes < $rule->minimum_work_duration_minutes) {
            return ['status' => 'error', 'message' => 'You cannot check out yet. Minimum required work duration is ' . $rule->minimum_work_duration_minutes . ' minutes (worked ' . $durationMinutes . ' mins).'];
        }

        // Save check-out
        $attendance->update([
            'check_out' => $now,
            'check_out_selfie' => $selfiePath,
            'check_out_latitude' => $data['latitude'] ?? null,
            'check_out_longitude' => $data['longitude'] ?? null,
            'check_out_ip' => $data['ip_address'] ?? null,
            'work_duration_minutes' => $durationMinutes
        ]);

        return ['status' => 'success', 'attendance' => $attendance];
    }

    /**
     * Compute Haversine distance.
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Helper to save base64-encoded webcam photo.
     */
    private function saveBase64Selfie($base64String)
    {
        // Strip out metadata if present (e.g. data:image/png;base64,)
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]); // png, jpeg, etc.
        } else {
            $type = 'png';
        }

        $imageData = base64_decode($base64String);
        if ($imageData === false) {
            return null;
        }

        $filename = 'selfies/' . str_random(40) . '.' . $type;
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }
}
