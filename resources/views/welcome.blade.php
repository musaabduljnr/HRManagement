@extends('layouts.main')

@section('content')

{{-- Page Header --}}
<div class="page-header-bar">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ Auth::user()->first_name }}! Here's what's happening today.</p>
    </div>
    <div>
        <span style="font-size:13px; color:var(--text-muted);">
            <i class="fa fa-calendar"></i> {{ \Carbon\Carbon::now()->format('l, F j Y') }}
        </span>
    </div>
</div>

{{-- Stat Cards Row --}}
<div class="row" style="margin-bottom: 24px;">
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fa fa-users"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalEmployees }}</div>
                <div class="stat-label">Total Employees</div>
                <div class="stat-sub">
                    <span class="label label-success">Active: {{ $activeEmployees }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fa fa-clock-o"></i>
            </div>
            <div>
                <div class="stat-value">{{ $attendanceToday }}</div>
                <div class="stat-label">Attendance Today</div>
                <div class="stat-sub">
                    <a href="{{ route('attendance.index') }}" style="color: var(--success); font-size:11px; font-weight:600;">View Details →</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fa fa-plane"></i>
            </div>
            <div>
                <div class="stat-value">{{ $pendingLeaves }}</div>
                <div class="stat-label">Pending Leaves</div>
                <div class="stat-sub">
                    <a href="{{ route('leave.employee_leaves.index') }}" style="color: var(--warning); font-size:11px; font-weight:600;">Review Now →</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <div>
                <div class="stat-value">{{ $newApplicants }}</div>
                <div class="stat-label">New Applicants</div>
                <div class="stat-sub">
                    <a href="{{ route('pim.candidates.index') }}" style="color: var(--danger); font-size:11px; font-weight:600;">View Pipeline →</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Second Row: Payroll + Recent Activity --}}
<div class="row" style="margin-bottom: 24px;">
    <div class="col-md-4" style="margin-bottom: 16px;">
        <div class="panel" style="height: 100%;">
            <div class="panel-heading">
                <i class="fa fa-money"></i> Payroll (Current Month)
            </div>
            <div class="panel-body text-center" style="padding: 30px 20px !important;">
                <div style="font-size: 36px; font-weight: 800; color: var(--primary); line-height: 1; margin-bottom: 8px;">
                    ₦{{ number_format($payrollSummary, 2) }}
                </div>
                <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">Total Net Salary Paid / Pending</p>
                <a href="{{ route('payroll.index') }}" class="btn btn-primary btn-block">
                    <i class="fa fa-arrow-right"></i> Manage Payroll
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8" style="margin-bottom: 16px;">
        <div class="panel" style="height: 100%;">
            <div class="panel-heading">
                <i class="fa fa-list-alt"></i> Recent Activity Logs
            </div>
            <div class="panel-body" style="padding: 0 !important; max-height: 280px; overflow-y: auto;">
                <ul class="list-group" style="margin: 0; border: none;">
                    @forelse($recentActivities as $activity)
                        <li class="list-group-item" style="border-left: none; border-right: none; border-top: none;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 2px;">
                                        <span class="sidebar-user-avatar" style="width:28px; height:28px; font-size:11px; flex-shrink:0;">
                                            {{ strtoupper(substr($activity->first_name, 0, 1)) }}
                                        </span>
                                        <strong style="font-size:13px;">{{ $activity->first_name }} {{ $activity->last_name }}</strong>
                                    </div>
                                    <p style="margin:0; font-size:12px; color: var(--text-secondary); padding-left: 36px;">{{ $activity->activity }}</p>
                                </div>
                                <span style="font-size: 11px; color: var(--text-muted); white-space:nowrap; margin-left:12px; flex-shrink:0;">
                                    {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center" style="padding: 40px; color: var(--text-muted); border: none;">
                            <i class="fa fa-inbox" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                            No recent activities logged.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Third Row: Message Employee + Recent Chats --}}
<div class="row" style="margin-bottom: 24px;">
    <div class="col-md-6" style="margin-bottom: 16px;">
        <div class="panel">
            <div class="panel-heading">
                <i class="fa fa-paper-plane-o"></i> Message Employee (Quick Action)
            </div>
            <div class="panel-body">
                <?php
                    $quickEmployees = \App\User::where('role', \App\User::USER_ROLE_EMPLOYEE)->orderBy('first_name', 'asc')->get();
                ?>
                <form action="{{ route('chat.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Select Employee</label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">— Choose Employee —</option>
                            @foreach($quickEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subject (Optional)</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Leave Reminder">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="body" class="form-control" rows="3" placeholder="Write your message..." required style="resize: none;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6" style="margin-bottom: 16px;">
        <div class="panel">
            <div class="panel-heading">
                <i class="fa fa-comments-o"></i> Recent Conversations
            </div>
            <div class="panel-body" style="padding: 0 !important;">
                <?php
                    $recentChats = \App\Modules\Chat\Models\Conversation::with('employee')
                        ->orderBy('last_message_at', 'desc')
                        ->take(5)
                        ->get();
                ?>
                <ul class="list-group" style="margin: 0; border: none;">
                    @forelse($recentChats as $chat)
                        <?php $unread = $chat->unreadMessagesCount(); ?>
                        <li class="list-group-item" style="border-left: none; border-right: none; border-top: none;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="flex:1; min-width:0;">
                                    <a href="{{ route('chat.index', ['conversation_id' => $chat->id]) }}"
                                       style="font-weight: 600; text-decoration: none; color: var(--text-primary); display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        {{ $chat->employee ? $chat->employee->first_name . ' ' . $chat->employee->last_name : 'Staff Member' }}
                                    </a>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        {{ $chat->subject }} &bull; {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '' }}
                                    </div>
                                </div>
                                @if($unread > 0)
                                    <span class="nav-badge" style="background: var(--success); margin-left: 8px;">{{ $unread }}</span>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center" style="padding: 40px; color: var(--text-muted); border: none;">
                            <i class="fa fa-comment-o" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                            No active conversations.
                        </li>
                    @endforelse
                </ul>
                <div style="padding: 12px 16px; border-top: 1px solid var(--card-border);">
                    <a href="{{ route('chat.index') }}" class="btn btn-default btn-sm btn-block">
                        View All Messages
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Fourth Row: Calendars --}}
<div class="row">
    <div class="col-sm-6" style="margin-bottom: 16px;">
        <div class="panel">
            <div class="panel-heading">
                <i class="fa fa-calendar"></i> {{ trans('app.leave.calendar.main') }}
            </div>
            <div class="panel-body">
                <div id="leave-calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6" style="margin-bottom: 16px;">
        <div class="panel">
            <div class="panel-heading">
                <i class="fa fa-birthday-cake"></i> {{ trans('app.pim.birthdays') }}
            </div>
            <div class="panel-body">
                <div id="birthday-calendar"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('additionalCSS')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.print.css" media="print">
<style>
    /* Override FullCalendar for modern look */
    .fc-toolbar h2 { font-size: 15px !important; font-weight: 600 !important; }
    .fc-button {
        background: white !important;
        border: 1px solid var(--card-border) !important;
        color: var(--text-secondary) !important;
        border-radius: var(--radius) !important;
        font-size: 12px !important;
        padding: 4px 8px !important;
        font-family: var(--font-family) !important;
    }
    .fc-button:hover { background: var(--content-bg) !important; }
    .fc-button-active, .fc-button.fc-state-active {
        background: var(--primary) !important;
        color: white !important;
        border-color: var(--primary) !important;
    }
    .fc-today { background: var(--primary-light) !important; }
    .fc-event {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        border-radius: 4px !important;
        font-size: 11px !important;
    }
    .stat-card { height: 100%; }
</style>
@endsection

@section('additionalJS')
<script src="{{ url('vendor/moment/moment.min.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js"></script>
<script>
    $(document).ready(function() {
        var calOptions = {
            header: { left: 'prev,next', center: 'title', right: 'month,basicWeek,basicDay' },
            defaultDate: '{{ get_current_date() }}',
            navLinks: true,
            editable: false,
            eventLimit: true
        };

        var sources1 = [];
        $('#leave-calendar').fullCalendar($.extend({}, calOptions, {
            viewRender: function(view) {
                var date = moment($('#leave-calendar').fullCalendar('getDate')).format('YYYY-MM-DD');
                if (sources1.indexOf(date) === -1) {
                    sources1.push(date);
                    $.ajax({
                        url: "{{ route('leave.calendar.render') }}",
                        data: { date: date },
                        success: function(events) { $('#leave-calendar').fullCalendar('addEventSource', events); }
                    });
                }
            }
        }));

        var sources2 = [];
        $('#birthday-calendar').fullCalendar($.extend({}, calOptions, {
            viewRender: function(view) {
                var date = moment($('#birthday-calendar').fullCalendar('getDate')).format('YYYY-MM-DD');
                if (sources2.indexOf(date) === -1) {
                    sources2.push(date);
                    $.ajax({
                        url: "{{ route('pim.employees.birthdays') }}",
                        data: { date: date },
                        success: function(events) { $('#birthday-calendar').fullCalendar('addEventSource', events); }
                    });
                }
            }
        }));
    });
</script>
@endsection