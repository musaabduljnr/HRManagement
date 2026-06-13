<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'ip_address',
    ];

    /**
     * Relationship: the user who performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenience static method to log an activity.
     *
     * @param  string       $activity     Short label, e.g. "Employee Created"
     * @param  string|null  $description  Optional longer description
     * @return static
     */
    public static function log($activity, $description = null)
    {
        return static::create([
            'user_id'     => Auth::check() ? Auth::id() : null,
            'activity'    => $activity,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}
