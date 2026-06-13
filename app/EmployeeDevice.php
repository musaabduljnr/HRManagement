<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_uuid',
        'device_name',
        'is_trusted'
    ];

    protected $casts = [
        'is_trusted' => 'boolean'
    ];

    /**
     * Get the user that owns the device.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
