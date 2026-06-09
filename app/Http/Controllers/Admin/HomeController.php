<?php

namespace App\Http\Controllers\Admin;

use App\Modules\Time\Repositories\Interfaces\TimeLogRepositoryInterface as TimeLogRepository;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the profile config page.
     *
     * @param  App\Modules\Time\Repositories\Interfaces\TimeLogRepositoryInterface $timeLogRepository
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $totalEmployees = \App\User::whereIn('role', [
            \App\User::USER_ROLE_EMPLOYEE,
            \App\User::USER_ROLE_HR_MANAGER,
            \App\User::USER_ROLE_PAYROLL_MANAGER,
            \App\User::USER_ROLE_DEPT_MANAGER
        ])->count();
        
        $activeEmployees = $totalEmployees;

        $pendingLeaves = \DB::table('user_leaves')->where('status', 'pending')->count();

        $attendanceToday = \DB::table('attendances')->whereDate('check_in', date('Y-m-d'))->count();

        $payrollSummary = \App\Modules\Pim\Models\PayrollRecord::where('payroll_month', date('Y-m'))->sum('net_salary');

        $newApplicants = \App\Modules\Recruitment\Models\CandidateApplication::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->count();

        $recentActivities = \DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.first_name', 'users.last_name')
            ->orderBy('activity_logs.id', 'desc')
            ->take(5)
            ->get();

        $current = 'home';
        return view('welcome', compact(
            'totalEmployees',
            'activeEmployees',
            'pendingLeaves',
            'attendanceToday',
            'payrollSummary',
            'newApplicants',
            'recentActivities',
            'current'
        ));
    }
}
