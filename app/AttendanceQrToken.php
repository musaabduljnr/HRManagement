<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceQrToken extends Model
{
    protected $fillable = [
        'token',
        'user_id',
        'attendance_rule_id',
        'expires_at'
    ];

    protected $dates = [
        'expires_at'
    ];

    /**
     * Get the user associated with this token.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendance rule associated with this token.
     */
    public function rule()
    {
        return $this->belongsTo(AttendanceRule::class, 'attendance_rule_id');
    }
}
