<?php

namespace App\Modules\Chat\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Chat\Models\Conversation;
use App\Modules\Chat\Models\Message;
use App\User;
use Auth;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // Only admins, HR managers, and other approved management roles can access
            if (!in_array(Auth::user()->role, [User::USER_ROLE_ADMIN, User::USER_ROLE_HR_MANAGER, User::USER_ROLE_PAYROLL_MANAGER, User::USER_ROLE_DEPT_MANAGER])) {
                abort(403, 'Unauthorized.');
            }
            return $next($request);
        });
    }

    /**
     * Display conversation list and current active thread.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Admin/HR can see all conversations.
        $conversations = Conversation::with(['employee', 'creator'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $activeConversation = null;
        $messages = [];
        $activeId = $request->input('conversation_id');

        if ($activeId) {
            $activeConversation = Conversation::with(['employee', 'creator'])->findOrFail($activeId);

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

        // Get employees list for starting new chats
        $employees = User::where('role', User::USER_ROLE_EMPLOYEE)
            ->orderBy('first_name', 'asc')
            ->get();

        $current = 'chat';
        return view('chat::admin.index', compact('conversations', 'activeConversation', 'messages', 'employees', 'current'));
    }

    /**
     * Start a new conversation.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required|exists:users,id',
            'body' => 'required|string|min:1',
            'subject' => 'nullable|string|max:255',
        ]);

        $employeeId = $request->input('employee_id');
        $subject = $request->input('subject') ?: 'Direct Message';
        $body = $request->input('body');
        $userId = Auth::id();

        // Check if there is an existing conversation between this HR manager/Admin and employee
        $conversation = Conversation::where('employee_id', $employeeId)
            ->where('hr_manager_id', $userId)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'subject' => $subject,
                'created_by' => $userId,
                'employee_id' => $employeeId,
                'hr_manager_id' => $userId,
                'last_message_at' => Carbon::now(),
            ]);
        } else {
            if ($request->input('subject')) {
                $conversation->subject = $subject;
            }
            $conversation->last_message_at = Carbon::now();
            $conversation->save();
        }

        // Create the message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'receiver_id' => $employeeId,
            'body' => $body,
        ]);

        // Placeholder for future in-app/push/email notifications
        // event(new \App\Events\MessageSent($message));
        // Mail::to($conversation->employee->email)->send(new NewMessageNotification($message));

        return redirect()->route('chat.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Conversation started successfully.');
    }

    /**
     * Reply in a conversation.
     */
    public function reply(Request $request, $id)
    {
        $this->validate($request, [
            'body' => 'required|string|min:1',
        ]);

        $conversation = Conversation::findOrFail($id);
        $user = Auth::user();

        // Create reply message
        $receiverId = $conversation->employee_id;

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'body' => $request->input('body'),
        ]);

        $conversation->last_message_at = Carbon::now();
        $conversation->save();

        return redirect()->route('chat.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Message sent.');
    }
}
