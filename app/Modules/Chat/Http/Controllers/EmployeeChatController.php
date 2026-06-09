<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Chat\Models\Conversation;
use App\Modules\Chat\Models\Message;
use Auth;
use Carbon\Carbon;

class EmployeeChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display conversation list and current active thread for employee.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Employee can only see conversations they are part of.
        $conversations = Conversation::with(['hrManager', 'creator'])
            ->where('employee_id', $user->id)
            ->orderBy('last_message_at', 'desc')
            ->get();

        $activeConversation = null;
        $messages = [];
        $activeId = $request->input('conversation_id');

        if ($activeId) {
            $activeConversation = Conversation::with(['hrManager', 'creator'])
                ->where('employee_id', $user->id)
                ->findOrFail($activeId);

            // Mark incoming messages as read
            Message::where('conversation_id', $activeId)
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => Carbon::now()]);

            $messages = Message::with('sender')
                ->where('conversation_id', $activeId)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $current = 'employee.chat';
        return view('chat::employee.index', compact('conversations', 'activeConversation', 'messages', 'current'));
    }

    /**
     * Reply in a conversation.
     */
    public function reply(Request $request, $id)
    {
        $this->validate($request, [
            'body' => 'required|string|min:1',
        ]);

        $user = Auth::user();
        $conversation = Conversation::where('employee_id', $user->id)->findOrFail($id);

        // Receiver is the HR Manager/Admin who participated in this conversation
        $receiverId = $conversation->hr_manager_id ?: $conversation->created_by;

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'body' => $request->input('body'),
        ]);

        $conversation->last_message_at = Carbon::now();
        $conversation->save();

        // Placeholder for real-time broadcasting and future notifications
        // event(new \App\Events\MessageSent($message));

        return redirect()->route('employee.chat.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Message sent.');
    }
}
