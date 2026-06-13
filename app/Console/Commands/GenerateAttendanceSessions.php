<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;

class GenerateAttendanceSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:generate-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $attendanceService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AttendanceService $attendanceService)
    {
        parent::__construct();
        $this->attendanceService = $attendanceService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $this->info("Starting attendance session generation for: {$today}");

        // Fetch all active employees
        $employees = User::whereIn('role', [
            User::USER_ROLE_EMPLOYEE,
            User::USER_ROLE_HR_MANAGER,
            User::USER_ROLE_PAYROLL_MANAGER,
            User::USER_ROLE_DEPT_MANAGER
        ])->get();

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($employees as $employee) {
            // Check if session already exists
            $exists = Attendance::where('user_id', $employee->id)
                ->where('date', $today)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            // Resolve rule
            $rule = $this->attendanceService->getApplicableRuleForUser($employee, $today);

            if ($rule) {
                // Check if holiday
                $isHoliday = \DB::table('holidays')->where('date', $today)->whereNull('deleted_at')->exists();
                
                // Check if on approved leave
                $isOnLeave = \DB::table('user_leaves')
                    ->where('user_id', $employee->id)
                    ->where('status', 'approved')
                    ->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today)
                    ->whereNull('deleted_at')
                    ->exists();

                $status = 'scheduled';
                if ($isHoliday) {
                    $status = 'holiday';
                } elseif ($isOnLeave) {
                    $status = 'leave';
                }

                Attendance::create([
                    'user_id' => $employee->id,
                    'date' => $today,
                    'check_in' => null,
                    'check_out' => null,
                    'status' => $status,
                    'attendance_rule_id' => $rule->id,
                    'company_id' => $rule->company_id,
                ]);

                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->info("Completed. Pre-generated: {$createdCount} sessions. Skipped: {$skippedCount} employees.");
    }
}
