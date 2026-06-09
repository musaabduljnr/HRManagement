@extends('layouts.main')

@section('content')
<div class="row">
    <!-- Sidebar: Conversation List & New Chat Button -->
    <div class="col-md-4">
        <div class="panel panel-default" style="border-radius: 12px; border: 1px solid #eef0f2; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
            <div class="panel-heading" style="background-color: white; border-bottom: 1px solid #eee; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                <h4 style="margin: 0; font-weight: 700; color: #1e3c72; font-size: 16px;">
                    <i class="fa fa-comments" style="margin-right: 5px;"></i> Conversations
                </h4>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#newChatModal" style="border-radius: 20px; font-weight: 600;">
                    <i class="fa fa-plus"></i> New Chat
                </button>
            </div>
            
            <div class="panel-body" style="padding: 10px;">
                <!-- Employee List Quick Filter Search -->
                <div class="form-group" style="margin-bottom: 10px;">
                    <div class="input-group">
                        <span class="input-group-addon" style="background: #fafbfc; border-right: none; border-radius: 8px 0 0 8px;"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" id="conversation-search" class="form-control" placeholder="Search conversations..." style="border-left: none; border-radius: 0 8px 8px 0; height: 38px; box-shadow: none;">
                    </div>
                </div>

                <div class="list-group" id="conversation-list" style="max-height: 480px; overflow-y: auto; margin-bottom: 0;">
                    @forelse($conversations as $conv)
                        <?php
                            $employee = $conv->employee;
                            $unreadCount = $conv->unreadMessagesCount();
                            $lastMessage = $conv->messages()->orderBy('created_at', 'desc')->first();
                            $isActive = $activeConversation && $activeConversation->id == $conv->id;
                        ?>
                        <a href="{{ route('chat.index', ['conversation_id' => $conv->id]) }}" 
                           class="list-group-item conversation-item {{ $isActive ? 'active-chat' : '' }}" 
                           data-employee="{{ strtolower($employee ? $employee->first_name . ' ' . $employee->last_name : '') }}"
                           style="border-radius: 8px; margin-bottom: 5px; border: 1px solid {{ $isActive ? '#1e3c72' : '#f1f3f5' }}; padding: 12px; transition: all 0.2s; background: {{ $isActive ? '#f4f7fa' : 'white' }}; display: block; text-decoration: none; color: inherit;">
                            
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <!-- User Initials Avatar -->
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background-color: {{ $isActive ? '#1e3c72' : '#e9ecef' }}; color: {{ $isActive ? 'white' : '#495057' }}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px;">
                                        @if($employee)
                                            {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                        @else
                                            DM
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <h5 style="margin: 0; font-weight: 700; color: #333; font-size: 14px;">
                                            {{ $employee ? $employee->first_name . ' ' . $employee->last_name : 'Staff Member' }}
                                        </h5>
                                        <p style="margin: 2px 0 0 0; font-size: 11px; color: #777;">
                                            Subject: <span style="font-weight: 600;">{{ $conv->subject }}</span>
                                        </p>
                                        @if($lastMessage)
                                            <p style="margin: 3px 0 0 0; font-size: 12px; color: #555; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; {{ $unreadCount > 0 ? 'font-weight: bold; color: #000;' : '' }}">
                                                {{ $lastMessage->body }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right" style="flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end;">
                                    <span style="font-size: 9px; color: #999;">
                                        {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : '' }}
                                    </span>
                                    @if($unreadCount > 0)
                                        <span class="badge" style="background-color: #27ae60; color: white; border-radius: 10px; font-size: 10px; padding: 3px 7px; margin-top: 5px;">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-muted" style="padding: 30px 10px;">
                            <i class="fa fa-comment-o fa-3x" style="margin-bottom: 10px; color: #ccc;"></i>
                            <p style="font-size: 13px;">No conversations yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Message Thread -->
    <div class="col-md-8">
        @if($activeConversation)
            <?php $employee = $activeConversation->employee; ?>
            <div class="panel panel-default" style="border-radius: 12px; border: 1px solid #eef0f2; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
                <!-- Conversation Header -->
                <div class="panel-heading" style="background-color: white; border-bottom: 1px solid #eee; padding: 15px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 15px;">
                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                            </div>
                            <div>
                                <h4 style="margin: 0; font-weight: 700; color: #1e3c72; font-size: 16px;">
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </h4>
                                <p style="margin: 2px 0 0 0; font-size: 11px; color: #888;">
                                    Dept: {{ $employee->department ? $employee->department->name : 'N/A' }} | Title: {{ $employee->jobTitle ? $employee->jobTitle->name : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <span class="label label-success">Active Thread</span>
                    </div>
                </div>

                <!-- Message History Box -->
                <div class="panel-body" id="messages-container" style="height: 380px; overflow-y: auto; background-color: #f8f9fa; padding: 20px;">
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($messages as $msg)
                            <?php $isOutgoing = $msg->sender_id == Auth::id(); ?>
                            <div style="display: flex; justify-content: {{ $isOutgoing ? 'flex-end' : 'flex-start' }}; width: 100%;">
                                <div style="max-width: 70%; display: flex; flex-direction: column; align-items: {{ $isOutgoing ? 'flex-end' : 'flex-start' }};">
                                    <!-- Message Bubble -->
                                    <div style="padding: 12px 16px; border-radius: 16px; font-size: 13.5px; line-height: 1.5; 
                                                background: {{ $isOutgoing ? 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)' : '#ffffff' }}; 
                                                color: {{ $isOutgoing ? '#ffffff' : '#333333' }}; 
                                                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                                                border: {{ $isOutgoing ? 'none' : '1px solid #eef0f2' }};
                                                border-bottom-{{ $isOutgoing ? 'right' : 'left' }}-radius: 2px;
                                                word-break: break-word;">
                                        {{ $msg->body }}
                                    </div>
                                    <!-- Sender and Timestamp -->
                                    <div style="margin-top: 4px; font-size: 10px; color: #888; display: flex; align-items: center; gap: 6px;">
                                        <span style="font-weight: bold;">{{ $msg->sender->first_name }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $msg->created_at->format('M d, h:i A') }}</span>
                                        @if($isOutgoing)
                                            <span>&bull;</span>
                                            @if($msg->read_at)
                                                <span class="text-success"><i class="fa fa-check-circle"></i> Read</span>
                                            @else
                                                <span class="text-muted"><i class="fa fa-check"></i> Sent</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Message Reply Composer -->
                <div class="panel-footer" style="background-color: white; border-top: 1px solid #eee; padding: 15px;">
                    <form action="{{ route('chat.reply', $activeConversation->id) }}" method="POST">
                        {{ csrf_field() }}
                        <div class="input-group">
                            <input type="text" name="body" class="form-control" placeholder="Write a reply..." required style="height: 44px; border-radius: 22px 0 0 22px; border: 1px solid #ccc; padding-left: 20px; box-shadow: none;" autocomplete="off">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit" style="height: 44px; border-radius: 0 22px 22px 0; padding: 0 25px; font-weight: 600;">
                                    <i class="fa fa-paper-plane"></i> Send
                                </button>
                            </span>
                        </div>
                        @if($errors->has('body'))
                            <div class="help-block text-danger" style="margin-top: 5px; margin-left: 15px;">{{ $errors->first('body') }}</div>
                        @endif
                    </form>
                </div>
            </div>
        @else
            <!-- Empty state -->
            <div class="panel panel-default" style="border-radius: 12px; border: 1px solid #eef0f2; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
                <div class="panel-body text-center" style="padding: 120px 20px; background-color: #fcfdfe;">
                    <i class="fa fa-comments-o fa-5x" style="color: #cbd5e0; margin-bottom: 20px;"></i>
                    <h3 style="margin-top: 0; font-weight: 700; color: #4a5568;">Select a Conversation</h3>
                    <p class="text-muted" style="max-width: 400px; margin: 0 auto 20px auto;">
                        Click on an existing thread on the left pane to view messages, or click the <strong>New Chat</strong> button to message a specific employee.
                    </p>
                    <button class="btn btn-primary btn-md" data-toggle="modal" data-target="#newChatModal" style="border-radius: 20px; padding: 8px 24px; font-weight: 600;">
                        <i class="fa fa-plus-circle"></i> Initiate Message Thread
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Start New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1" role="dialog" aria-labelledby="newChatModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="newChatModalLabel" style="font-weight: 700;"><i class="fa fa-plus-circle"></i> Start Conversation with Employee</h4>
            </div>
            <form action="{{ route('chat.store') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-body" style="padding: 25px;">
                    <!-- Filter search for selecting employee -->
                    <div class="form-group">
                        <label for="employee_id" style="font-weight: 600; color: #444;">1. Select Employee</label>
                        <input type="text" id="employee-dropdown-filter" class="form-control" placeholder="Type name to filter list below..." style="margin-bottom: 8px; border-radius: 8px; height: 38px;">
                        
                        <select name="employee_id" id="employee_id" class="form-control" required style="border-radius: 8px; height: 42px;">
                            <option value="">-- Choose Employee --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->first_name }} {{ $emp->last_name }} (Dept: {{ $emp->department ? $emp->department->name : 'General' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subject" style="font-weight: 600; color: #444;">2. Conversation Subject (Optional)</label>
                        <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g. Leave Query, Performance Review..." style="border-radius: 8px; height: 40px;">
                    </div>

                    <div class="form-group">
                        <label for="body" style="font-weight: 600; color: #444;">3. Write First Message</label>
                        <textarea name="body" id="body" rows="4" class="form-control" placeholder="Write your message here..." required style="border-radius: 8px; resize: none;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #fafbfc; border-top: 1px solid #eee; padding: 15px 25px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 20px; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 20px; font-weight: 600; padding: 6px 20px;">
                        <i class="fa fa-paper-plane"></i> Send First Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.active-chat {
    background-color: #f4f7fa !important;
    border-left: 4px solid #1e3c72 !important;
}
.conversation-item:hover {
    background-color: #f8fafc !important;
    border-color: #cbd5e0 !important;
}
#messages-container::-webkit-scrollbar {
    width: 6px;
}
#messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}
#messages-container::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}
#messages-container::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>
@endsection

@section('additionalJS')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Scroll Message Container to Bottom
    var container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }

    // 2. Filter Search for Conversation List
    var convSearch = document.getElementById('conversation-search');
    if (convSearch) {
        convSearch.addEventListener('keyup', function() {
            var filter = convSearch.value.toLowerCase();
            var items = document.querySelectorAll('.conversation-item');
            
            items.forEach(function(item) {
                var empName = item.getAttribute('data-employee');
                if (empName.indexOf(filter) > -1) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        });
    }

    // 3. Search filter for Select Employee Modal dropdown
    var empFilter = document.getElementById('employee-dropdown-filter');
    var empSelect = document.getElementById('employee_id');
    if (empFilter && empSelect) {
        var originalOptions = Array.from(empSelect.options);
        
        empFilter.addEventListener('keyup', function() {
            var val = empFilter.value.toLowerCase();
            
            // Clear current options
            empSelect.innerHTML = '';
            
            originalOptions.forEach(function(opt) {
                if (opt.value === '' || opt.text.toLowerCase().indexOf(val) > -1) {
                    empSelect.appendChild(opt);
                }
            });
        });
    }
});
</script>
@endsection
