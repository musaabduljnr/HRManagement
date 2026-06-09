<?php

namespace App\Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'subject',
        'created_by',
        'employee_id',
        'hr_manager_id',
        'last_message_at',
        'status',
    ];

    protected $dates = [
        'last_message_at',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function employee()
    {
        return $this->belongsTo(\App\User::class, 'employee_id');
    }

    public function hrManager()
    {
        return $this->belongsTo(\App\User::class, 'hr_manager_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    public function unreadMessagesCount($userId = null)
    {
        $userId = $userId ?: \Auth::id();
        return $this->messages()->where('receiver_id', $userId)->whereNull('read_at')->count();
    }
}
