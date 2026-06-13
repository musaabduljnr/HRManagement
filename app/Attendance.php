<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'date', 'check_in', 'check_out', 'status', 'ip_address',
        'attendance_rule_id', 'company_id', 'check_in_selfie', 'check_out_selfie',
        'check_in_latitude', 'check_in_longitude', 'check_out_latitude', 'check_out_longitude',
        'check_out_ip', 'device_fingerprint', 'work_duration_minutes'
    ];

    /**
     * Get the user that owns the attendance record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendance rule used for this record.
     */
    public function rule()
    {
        return $this->belongsTo(AttendanceRule::class, 'attendance_rule_id');
    }
}
