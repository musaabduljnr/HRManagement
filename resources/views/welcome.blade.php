@extends('layouts.main')

@section('content')

{{-- ══════════════════════════════════════════════
     GREETING BANNER
     ══════════════════════════════════════════════ --}}
<div class="dashboard-greeting">
    <div class="greeting-text">
        <div class="greeting-hello">
            Welcome back, {{ Auth::user()->first_name }}! 👋
        </div>
        <div class="greeting-date">
            <i class="fa fa-calendar-o" style="opacity:0.8; margin-right:5px;"></i>
            {{ \Carbon\Carbon::now()->format('l, F j Y') }}
        </div>
        <div class="greeting-meta" style="margin-top: 8px;">
            Here's what's happening at your organisation today.
        </div>
    </div>
    <div class="greeting-actions">
        <a href="{{ route('pim.employees.create') }}" class="greeting-btn solid">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Add Employee</span>
        </a>
        <a href="{{ route('attendance.index') }}" class="greeting-btn outline">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Attendance</span>
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     STAT CARDS ROW
     ══════════════════════════════════════════════ --}}
<div class="row" style="margin-bottom: 24px;">

    {{-- Total Employees --}}
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card" style="color: var(--primary);">
            <div class="stat-icon primary">
                <i class="fa fa-users"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $totalEmployees }}</div>
                <div class="stat-label">Total Employees</div>
                <div class="stat-sub">
                    <span class="stat-trend flat">
                        <i class="fa fa-check-circle" style="font-size:10px;"></i>
                        Active: {{ $activeEmployees }}
                    </span>
                </div>
                <div class="stat-progress" style="margin-top:12px;">
                    <div class="stat-progress-bar primary" style="width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance Today --}}
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card" style="color: var(--success);">
            <div class="stat-icon success">
                <i class="fa fa-clock-o"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $attendanceToday }}</div>
                <div class="stat-label">Present Today</div>
                <div class="stat-sub">
                    <a href="{{ route('attendance.index') }}" style="color: var(--success); font-size:11px; font-weight:600; text-decoration:none !important;">
                        View Details →
                    </a>
                </div>
                <div class="stat-progress" style="margin-top:12px;">
                    @php
                        $attendancePct = $totalEmployees > 0 ? min(100, round(($attendanceToday / $totalEmployees) * 100)) : 0;
                    @endphp
                    <div class="stat-progress-bar success" style="width: {{ $attendancePct }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Leaves --}}
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card" style="color: var(--warning);">
            <div class="stat-icon warning">
                <i class="fa fa-plane"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $pendingLeaves }}</div>
                <div class="stat-label">Pending Leaves</div>
                <div class="stat-sub">
                    <a href="{{ route('leave.employee_leaves.index') }}" style="color: var(--warning); font-size:11px; font-weight:600; text-decoration:none !important;">
                        Review Now →
                    </a>
                </div>
                <div class="stat-progress" style="margin-top:12px;">
                    <div class="stat-progress-bar warning" style="width: {{ $pendingLeaves > 0 ? min(100, $pendingLeaves * 10) : 0 }}%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- New Applicants --}}
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card" style="color: var(--info);">
            <div class="stat-icon info">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $newApplicants }}</div>
                <div class="stat-label">New Applicants</div>
                <div class="stat-sub">
                    <a href="{{ route('pim.candidates.index') }}" style="color: var(--info); font-size:11px; font-weight:600; text-decoration:none !important;">
                        View Pipeline →
                    </a>
                </div>
                <div class="stat-progress" style="margin-top:12px;">
                    <div class="stat-progress-bar" style="width: {{ $newApplicants > 0 ? min(100, $newApplicants * 8) : 0 }}%; background: linear-gradient(90deg, var(--info), #60a5fa);"></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     ROW 2: PAYROLL + QUICK ACTIONS + ACTIVITY
     ══════════════════════════════════════════════ --}}
<div class="row" style="margin-bottom: 24px;">

    {{-- Payroll Summary --}}
    <div class="col-md-4" style="margin-bottom: 16px;">
        <div class="panel" style="height: 100%; margin-bottom: 0;">
            <div class="panel-heading">
                <i class="fa fa-money"></i> Payroll Summary
                <span class="label label-primary" style="margin-left: auto; font-size: 9px; letter-spacing:0.5px;">
                    {{ \Carbon\Carbon::now()->format('M Y') }}
                </span>
            </div>
            <div class="panel-body text-center" style="padding: 36px 24px !important;">
                <div style="width: 70px; height: 70px; background: var(--primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fa fa-dollar" style="font-size: 28px; color: var(--primary);"></i>
                </div>
                <div class="payroll-amount">
                    ₦{{ number_format($payrollSummary, 0) }}
                </div>
                <div class="payroll-label" style="margin-bottom: 24px;">Total Net Salary — Current Month</div>
                <a href="{{ route('payroll.index') }}" class="btn btn-primary btn-block">
                    <i class="fa fa-arrow-right"></i> Manage Payroll
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="col-md-4" style="margin-bottom: 16px;">
        <div class="panel" style="height: 100%; margin-bottom: 0;">
            <div class="panel-heading">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                Quick Actions
            </div>
            <div class="panel-body">
                <div class="quick-action-grid">
                    <a href="{{ route('pim.employees.create') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: var(--primary-light); color: var(--primary);">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        Add Employee
                    </a>
                    <a href="{{ route('leave.employee_leaves.index') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: var(--warning-light); color: var(--warning);">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                        Leave Requests
                    </a>
                    <a href="{{ route('attendance.index') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: var(--success-light); color: var(--success);">
                            <i class="fa fa-check-square-o"></i>
                        </div>
                        Attendance
                    </a>
                    <a href="{{ route('payroll.index') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: var(--info-light); color: var(--info);">
                            <i class="fa fa-file-text-o"></i>
                        </div>
                        Run Payroll
                    </a>
                    <a href="{{ route('recruitment.index') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: #f3e8ff; color: #9333ea;">
                            <i class="fa fa-briefcase"></i>
                        </div>
                        Recruitment
                    </a>
                    <a href="{{ route('chat.index') }}" class="quick-action-card">
                        <div class="quick-action-icon" style="background: #fff1f2; color: #e11d48;">
                            <i class="fa fa-comments-o"></i>
                        </div>
                        Messages
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-md-4" style="margin-bottom: 16px;">
        <div class="panel" style="height: 100%; margin-bottom: 0;">
            <div class="panel-heading">
                <i class="fa fa-list-alt"></i> Recent Activity
                <a href="#" class="btn btn-default btn-xs" style="margin-left: auto; font-size: 11px; padding: 3px 10px !important;">
                    View All
                </a>
            </div>
            <div class="panel-body" style="padding: 16px 20px !important; max-height: 320px; overflow-y: auto;">
                @forelse($recentActivities as $activity)
                    <div class="activity-item">
                        <div class="activity-avatar">
                            {{ strtoupper(substr($activity->first_name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">
                                {{ $activity->first_name }} {{ $activity->last_name }}
                            </div>
                            <div class="activity-desc">{{ $activity->activity }}</div>
                        </div>
                        <div class="activity-time">
                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <i class="fa fa-inbox" style="font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        <div style="font-size: 13px; font-weight: 500;">No recent activity</div>
                        <div style="font-size: 12px; margin-top: 4px;">Activity logs will appear here</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     ROW 3: MESSAGING
     ══════════════════════════════════════════════ --}}
<div class="row" style="margin-bottom: 24px;">

    {{-- Message Employee --}}
    <div class="col-md-5" style="margin-bottom: 16px;">
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-heading">
                <i class="fa fa-paper-plane-o"></i> Message an Employee
            </div>
            <div class="panel-body">
                <?php
                    $quickEmployees = \App\User::where('role', \App\User::USER_ROLE_EMPLOYEE)
                        ->orderBy('first_name', 'asc')
                        ->get();
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
                        <label>Subject <span style="color:var(--text-muted); font-weight:400;">(Optional)</span></label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Leave Reminder, Schedule Update…">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="body" class="form-control" rows="3" placeholder="Write your message…" required style="resize: none; min-height: 80px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Recent Conversations --}}
    <div class="col-md-7" style="margin-bottom: 16px;">
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-heading">
                <i class="fa fa-comments-o"></i> Recent Conversations
                <a href="{{ route('chat.index') }}" class="btn btn-default btn-xs" style="margin-left: auto; font-size: 11px; padding: 3px 10px !important;">
                    View All
                </a>
            </div>
            <div class="panel-body" style="padding: 16px 20px !important;">
                <?php
                    $recentChats = \App\Modules\Chat\Models\Conversation::with('employee')
                        ->orderBy('last_message_at', 'desc')
                        ->take(6)
                        ->get();
                ?>
                @forelse($recentChats as $chat)
                    <?php $unread = $chat->unreadMessagesCount(); ?>
                    <a href="{{ route('chat.index', ['conversation_id' => $chat->id]) }}"
                       class="conversation-item" style="text-decoration:none!important;">
                        <div class="conversation-avatar">
                            {{ strtoupper(substr($chat->employee ? $chat->employee->first_name : 'S', 0, 1)) }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="conversation-name">
                                {{ $chat->employee ? $chat->employee->first_name . ' ' . $chat->employee->last_name : 'Staff Member' }}
                            </div>
                            <div class="conversation-meta">
                                {{ $chat->subject }}
                                @if($chat->last_message_at)
                                    &bull; {{ $chat->last_message_at->diffForHumans() }}
                                @endif
                            </div>
                        </div>
                        @if($unread > 0)
                            <span class="nav-badge" style="background: var(--success);">{{ $unread }}</span>
                        @endif
                    </a>
                @empty
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <i class="fa fa-comment-o" style="font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        <div style="font-size: 13px; font-weight: 500;">No conversations yet</div>
                        <div style="font-size: 12px; margin-top: 4px;">Start a conversation using the form</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     ROW 4: CALENDARS
     ══════════════════════════════════════════════ --}}
<div class="row">
    <div class="col-sm-6" style="margin-bottom: 16px;">
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-heading">
                <i class="fa fa-calendar-o"></i> {{ trans('app.leave.calendar.main') }}
            </div>
            <div class="panel-body">
                <div id="leave-calendar"></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6" style="margin-bottom: 16px;">
        <div class="panel" style="margin-bottom: 0;">
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
    /* FullCalendar modern overrides */
    .fc-toolbar h2 {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: var(--text-primary) !important;
    }
    .fc-button {
        background: white !important;
        border: 1px solid var(--card-border) !important;
        color: var(--text-secondary) !important;
        border-radius: var(--radius) !important;
        font-size: 11.5px !important;
        padding: 5px 9px !important;
        font-family: var(--font-family) !important;
        font-weight: 500 !important;
        box-shadow: none !important;
        text-shadow: none !important;
    }
    .fc-button:hover {
        background: var(--content-bg) !important;
        color: var(--text-primary) !important;
    }
    .fc-button-active, .fc-button.fc-state-active {
        background: var(--primary) !important;
        color: white !important;
        border-color: var(--primary) !important;
    }
    .fc-today {
        background: var(--primary-light) !important;
    }
    .fc-event {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        border-radius: 4px !important;
        font-size: 11px !important;
        font-weight: 500 !important;
    }
    .fc-day-header {
        font-size: 11px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        color: var(--text-secondary) !important;
    }
    /* Stat card equal heights */
    .row .col-lg-3.col-md-6 { display: flex; flex-direction: column; }
    .row .col-lg-3.col-md-6 > .stat-card { height: 100%; }
    /* Panel equal heights in row 2 */
    .row .col-md-4 { display: flex; flex-direction: column; }
    .row .col-md-4 > .panel { flex: 1; }
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