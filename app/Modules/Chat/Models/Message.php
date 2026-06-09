<?php

namespace App\Modules\Chat\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'body',
        'read_at',
    ];

    protected $dates = [
        'read_at',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(\App\User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(\App\User::class, 'receiver_id');
    }
}
