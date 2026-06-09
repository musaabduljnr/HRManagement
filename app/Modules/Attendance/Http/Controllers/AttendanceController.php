<?php

namespace App\Modules\Attendance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Attendance;
use Carbon\Carbon;
use Auth;

class AttendanceController extends Controller
{
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
        $users = User::where('role', User::USER_ROLE_EMPLOYEE)->get();

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

        // Find user by secure token
        $user = User::where('attendance_token', $token)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Invalid QR Code token. Access Denied.'], 404);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Check if there is already a scan for today
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // Check-in logic
            // E.g., Late if after 09:30 AM
            $limitTime = Carbon::createFromFormat('H:i', '09:30');
            $status = $now->format('H:i') > $limitTime->format('H:i') ? 'Late' : 'Present';

            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $now,
                'status' => $status,
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Checked In: ' . $user->first_name . ' ' . $user->last_name . ' (' . $status . ')'
            ]);
        } else {
            // Check-out logic
            if ($attendance->check_out) {
                return response()->json([
                    'status' => 'error',
                    'message' => $user->first_name . ' ' . $user->last_name . ' has already Clocked Out for today.'
                ]);
            }

            $attendance->check_out = $now;
            $attendance->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Clocked Out: ' . $user->first_name . ' ' . $user->last_name
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

        return view('attendance::qr', compact('token', 'todayRecord', 'history'));
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

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Check if there is already a scan for today
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            // Check-in logic
            // E.g., Late if after 09:30 AM
            $limitTime = Carbon::createFromFormat('H:i', '09:30');
            $status = $now->format('H:i') > $limitTime->format('H:i') ? 'Late' : 'Present';

            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'check_in' => $now,
                'status' => $status,
                'ip_address' => $request->ip() ?: 'Web Interface'
            ]);

            return redirect()->back()->with('success', 'Checked In successfully. Status: ' . $status);
        } else {
            // Check-out logic
            if ($attendance->check_out) {
                return redirect()->back()->with('error', 'You have already Checked Out for today.');
            }

            $attendance->check_out = $now;
            $attendance->save();

            return redirect()->back()->with('success', 'Checked Out successfully.');
        }
    }
}
