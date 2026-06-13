<?php

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Attendance;
use Carbon\Carbon;
use Auth;

use App\Services\AttendanceService;
use App\AttendanceQrToken;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }
    /**
     * Admin: List all attendance records.
     */
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $userId = $request->input('user_id');

        $query = Attendance::with('user')->where('date', $date);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $attendances = $query->orderBy('check_in', 'desc')->get();
        $users = User::whereIn('role', [
            User::USER_ROLE_EMPLOYEE,
            User::USER_ROLE_HR_MANAGER,
            User::USER_ROLE_PAYROLL_MANAGER,
            User::USER_ROLE_DEPT_MANAGER,
        ])->orderBy('first_name')->get();

        return view('attendance::index', compact('attendances', 'users'));
    }

    /**
     * Admin: Render the live webcam scanner kiosk.
     */
    public function scanner()
    {
        return view('attendance::scanner');
    }

    /**
     * Admin: Get recent scans for the scanner kiosk via AJAX.
     */
    public function recent()
    {
        $recent = Attendance::with('user')
            ->where('date', Carbon::today()->toDateString())
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($recent);
    }

    /**
     * Admin: Process QR code scans.
     */
    public function scan(Request $request)
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['status' => 'error', 'message' => 'No token scanned.'], 400);
        }

        // 1. Find dynamic QR token
        $qrToken = AttendanceQrToken::where('token', $token)->first();

        if (!$qrToken) {
            // Fallback: check if static token is matched for backward compatibility or simple scans
            $user = User::where('attendance_token', $token)->first();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Invalid QR Code token. Access Denied.'], 404);
            }
            $rule = $this->attendanceService->getApplicableRuleForUser($user);
        } else {
            // Validate expiration
            if (Carbon::parse($qrToken->expires_at)->isPast()) {
                return response()->json(['status' => 'error', 'message' => 'QR Code has expired. Please generate a fresh code.'], 410);
            }
            $user = $qrToken->user;
            $rule = $qrToken->rule;
        }

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User associated with QR code not found.'], 404);
        }

        if (!$rule) {
            return response()->json(['status' => 'error', 'message' => 'No active shift resolved for this employee today.'], 403);
        }

        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // 2. Resolve session: check if already checked in today
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // Check if yesterday overnight check-in exists for check-out
        $isCheckout = false;
        if ($attendance && !is_null($attendance->check_in)) {
            $isCheckout = true;
        } else {
            // Check overnight from yesterday
            $yesterdayAttendance = Attendance::where('user_id', $user->id)
                ->where('date', $yesterday)
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->first();

            if ($yesterdayAttendance) {
                $attendance = $yesterdayAttendance;
                $isCheckout = true;
            }
        }

        $data = $request->all();
        $data['ip_address'] = $request->ip();

        if (!$isCheckout) {
            // Check-in logic
            $validation = $this->attendanceService->validateCheckIn($user, $rule, $data);
            if (!$validation['status']) {
                return response()->json(['status' => 'error', 'message' => $validation['message']], 403);
            }

            $attendance = $this->attendanceService->processCheckIn($user, $rule, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Checked In: ' . $user->first_name . ' ' . $user->last_name . ' (' . $attendance->status . ')'
            ]);
        } else {
            // Check-out logic
            if ($attendance->check_out) {
                return response()->json([
                    'status' => 'error',
                    'message' => $user->first_name . ' ' . $user->last_name . ' has already Clocked Out for today.'
                ]);
            }

            $res = $this->attendanceService->processCheckOut($user, $data);
            if ($res['status'] === 'error') {
                return response()->json(['status' => 'error', 'message' => $res['message']], 403);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Clocked Out: ' . $user->first_name . ' ' . $user->last_name . ' (Worked ' . $attendance->fresh()->work_duration_minutes . ' mins)'
            ]);
        }
    }

    /**
     * Admin: Store manual attendance entry.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'required',
        ]);

        $inTime = Carbon::parse($request->input('check_in'));
        $outTime = $request->input('check_out') ? Carbon::parse($request->input('check_out')) : null;

        Attendance::create([
            'user_id' => $request->input('user_id'),
            'date' => $request->input('date'),
            'check_in' => $inTime,
            'check_out' => $outTime,
            'status' => 'Manual Entry',
            'ip_address' => 'Admin override'
        ]);

        return redirect()->back()->with('success', 'Manual attendance record created.');
    }

    /**
     * Admin: Update attendance entry.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'check_in' => 'required',
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->check_in = Carbon::parse($request->input('check_in'));
        $attendance->check_out = $request->input('check_out') ? Carbon::parse($request->input('check_out')) : null;
        $attendance->save();

        return redirect()->back()->with('success', 'Attendance record updated.');
    }

    /**
     * Admin: Delete record.
     */
    public function destroy($id)
    {
        $record = Attendance::findOrFail($id);
        $record->delete();

        return redirect()->back()->with('success', 'Attendance record deleted.');
    }

    /**
     * Employee: Show QR code and logs history.
     */
    public function qr()
    {
        $user = Auth::user();

        // Auto generate attendance token if missing
        if (!$user->attendance_token) {
            $user->attendance_token = str_random(40);
            $user->save();
        }

        $token = $user->attendance_token;
        $today = Carbon::today()->toDateString();

        $todayRecord = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $history = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(15)
            ->get();

        $rule = $this->attendanceService->getApplicableRuleForUser($user, $today);

        return view('attendance::qr', compact('token', 'todayRecord', 'history', 'rule'));
    }

    /**
     * Employee: Web Clock-in / Clock-out button action.
     */
    public function webClock(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $rule = $this->attendanceService->getApplicableRuleForUser($user);
        if (!$rule) {
            return redirect()->back()->with('error', 'No active attendance rule resolved for your profile today. Please contact HR.');
        }

        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // Resolve active session
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $isCheckout = false;
        if ($attendance && !is_null($attendance->check_in)) {
            $isCheckout = true;
        } else {
            // Check overnight from yesterday
            $yesterdayAttendance = Attendance::where('user_id', $user->id)
                ->where('date', $yesterday)
                ->whereNotNull('check_in')
                ->whereNull('check_out')
                ->first();

            if ($yesterdayAttendance) {
                $attendance = $yesterdayAttendance;
                $isCheckout = true;
            }
        }

        $data = $request->all();
        $data['ip_address'] = $request->ip() ?: 'Web Interface';

        if (!$isCheckout) {
            // Validate check-in
            $validation = $this->attendanceService->validateCheckIn($user, $rule, $data);
            if (!$validation['status']) {
                return redirect()->back()->with('error', $validation['message']);
            }

            $attendance = $this->attendanceService->processCheckIn($user, $rule, $data);
            return redirect()->back()->with('success', 'Checked In successfully. Status: ' . $attendance->status);
        } else {
            // Check if already checked out
            if ($attendance->check_out) {
                return redirect()->back()->with('error', 'You have already Checked Out for today.');
            }

            $res = $this->attendanceService->processCheckOut($user, $data);
            if ($res['status'] === 'error') {
                return redirect()->back()->with('error', $res['message']);
            }

            return redirect()->back()->with('success', 'Checked Out successfully. Duration: ' . $attendance->fresh()->work_duration_minutes . ' minutes.');
        }
    }
}
