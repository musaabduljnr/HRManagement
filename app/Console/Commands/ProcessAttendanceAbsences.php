<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Attendance;
use Carbon\Carbon;

class ProcessAttendanceAbsences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:process-absences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $now = Carbon::now();
        $timeStr = $now->format('H:i:s');

        $this->info("Processing attendance state changes. Current time: {$timeStr}");

        // 1. Process today's scheduled sessions that were missed (absences)
        $todaySessions = Attendance::where('date', $today)->get();
        $absentCount = 0;
        $missedCheckoutCount = 0;

        foreach ($todaySessions as $session) {
            $rule = $session->rule;
            if (!$rule) continue;

            // Mark absent if check_in is null and cutoff time has passed
            if ($session->status === 'scheduled' && is_null($session->check_in)) {
                if ($rule->auto_mark_absent && $timeStr > $rule->check_in_cutoff_time) {
                    $session->update(['status' => 'absent']);
                    $absentCount++;
                }
            }

            // Mark missed checkout if checked in but didn't check out AND shift is NOT overnight AND cutoff passed
            if (in_array($session->status, ['Present', 'Late', 'present', 'late']) && is_null($session->check_out)) {
                $isOvernight = $rule->check_out_start_time && ($rule->check_out_start_time < $rule->check_in_start_time);
                if (!$isOvernight && $rule->auto_mark_missed_checkout && $rule->check_out_cutoff_time && $timeStr > $rule->check_out_cutoff_time) {
                    $session->update(['status' => 'missed_checkout']);
                    $missedCheckoutCount++;
                }
            }
        }

        // 2. Process yesterday's overnight sessions that missed checkout
        $yesterdaySessions = Attendance::where('date', $yesterday)
            ->whereIn('status', ['Present', 'Late', 'present', 'late'])
            ->whereNull('check_out')
            ->get();

        foreach ($yesterdaySessions as $session) {
            $rule = $session->rule;
            if (!$rule) continue;

            $isOvernight = $rule->check_out_start_time && ($rule->check_out_start_time < $rule->check_in_start_time);
            if ($isOvernight && $rule->auto_mark_missed_checkout && $rule->check_out_cutoff_time && $timeStr > $rule->check_out_cutoff_time) {
                $session->update(['status' => 'missed_checkout']);
                $missedCheckoutCount++;
            }
        }

        $this->info("Completed. Marked {$absentCount} users as Absent, and {$missedCheckoutCount} users as Missed Checkout.");
    }
}
