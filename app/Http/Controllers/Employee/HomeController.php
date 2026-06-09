<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the profile config page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = \Auth::user();

        $leaveStatuses = \DB::table('user_leave_status')
            ->join('leave_types', 'user_leave_status.leave_type_id', '=', 'leave_types.id')
            ->where('user_leave_status.user_id', $user->id)
            ->select('user_leave_status.*', 'leave_types.name as leave_type_name')
            ->get();

        $pendingLeavesCount = \DB::table('user_leaves')
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $today = \Carbon\Carbon::today()->toDateString();
        $todayRecord = \App\Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $latestPayslip = \App\Modules\Pim\Models\PayrollRecord::where('user_id', $user->id)
            ->where('status', 'paid')
            ->orderBy('payroll_month', 'desc')
            ->first();

        if (!$user->attendance_token) {
            $user->attendance_token = str_random(40);
            $user->save();
        }
        $token = $user->attendance_token;

        $current = 'employee.home';

        return view('welcome-employee', compact(
            'user',
            'leaveStatuses',
            'pendingLeavesCount',
            'todayRecord',
            'latestPayslip',
            'token',
            'current'
        ));
    }
}
