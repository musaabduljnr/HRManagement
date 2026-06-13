<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    protected $fillable = [
        'company_id',
        'rule_name',
        'shift_name',
        'branch_id',
        'department_id',
        'applies_to',
        'employee_ids',
        'working_days',
        'check_in_start_time',
        'grace_period_minutes',
        'check_in_cutoff_time',
        'check_out_enabled',
        'check_out_start_time',
        'check_out_cutoff_time',
        'minimum_work_duration_minutes',
        'office_latitude',
        'office_longitude',
        'allowed_radius_meters',
        'selfie_required',
        'checkout_selfie_required',
        'device_lock_required',
        'auto_mark_absent',
        'auto_mark_missed_checkout',
        'status',
        'created_by'
    ];

    /**
     * Decode JSON fields automatically.
     */
    protected $casts = [
        'employee_ids' => 'array',
        'working_days' => 'array',
        'check_out_enabled' => 'boolean',
        'selfie_required' => 'boolean',
        'checkout_selfie_required' => 'boolean',
        'device_lock_required' => 'boolean',
        'auto_mark_absent' => 'boolean',
        'auto_mark_missed_checkout' => 'boolean',
    ];

    /**
     * Get the attendances associated with this rule.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
