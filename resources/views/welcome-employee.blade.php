@extends('layouts.main_employee')

@section('content')
<?php
    $manualClockinEnabled = \DB::table('system_settings')->where('key', 'manual_clockin_enabled')->value('value') === 'true';
    $qrClockinEnabled     = \DB::table('system_settings')->where('key', 'qr_clockin_enabled')->value('value') !== 'false';

    // Determine clock status
    $clockStatus = 'absent';
    $clockLabel  = 'Not Clocked In';
    if ($todayRecord) {
        $clockStatus = $todayRecord->check_out ? 'done' : 'active';
        $clockLabel  = $todayRecord->check_out ? 'Shift Complete' : 'Clocked In — Active';
    }
?>

{{-- ══════════════════════════════════════════════
     GREETING BANNER
     ══════════════════════════════════════════════ --}}
<div class="dashboard-greeting" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e40af 100%);">
    <div class="greeting-text">
        <div class="greeting-hello">
            Good {{ \Carbon\Carbon::now()->hour < 12 ? 'morning' : (\Carbon\Carbon::now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ $user->first_name }}! 👋
        </div>
        <div class="greeting-date">
            <i class="fa fa-calendar-o" style="opacity:0.8; margin-right:5px;"></i>
            {{ \Carbon\Carbon::now()->format('l, F j Y') }}
        </div>
        <div class="greeting-meta">
            <span style="display: inline-flex; align-items: center; gap: 6px;">
                @if($clockStatus === 'active')
                    <span style="width:8px; height:8px; border-radius:50%; background:#4ade80; display:inline-block; animation: pulse-dot 1.5s infinite;"></span>
                    You are currently clocked in.
                @elseif($clockStatus === 'done')
                    <span style="width:8px; height:8px; border-radius:50%; background:#94a3b8; display:inline-block;"></span>
                    Shift completed for today.
                @else
                    <span style="width:8px; height:8px; border-radius:50%; background:#f87171; display:inline-block;"></span>
                    You haven't clocked in yet today.
                @endif
            </span>
        </div>
    </div>
    <div class="greeting-actions">
        <a href="{{ route('employee.leaves.index') }}" class="greeting-btn outline">
            <i class="fa fa-plane"></i> <span>My Leaves</span>
        </a>
        <a href="{{ route('employee.payroll.index') }}" class="greeting-btn solid" style="color: #1e40af !important;">
            <i class="fa fa-file-text-o"></i> <span>My Payslips</span>
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     TOP STAT CARDS ROW
     ══════════════════════════════════════════════ --}}
<div class="row" style="margin-bottom: 24px;">

    {{-- Leave Balance Cards --}}
    @forelse($leaveStatuses->take(3) as $status)
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card" style="color: var(--primary);">
            <div class="stat-icon primary">
                <i class="fa fa-plane"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $status->total_available }}</div>
                <div class="stat-label">{{ $status->leave_type_name }}</div>
                <div class="stat-sub">
                    <span class="stat-trend {{ $status->total_used > 0 ? 'down' : 'flat' }}">
                        <i class="fa fa-minus-circle" style="font-size:9px;"></i>
                        {{ $status->total_used }} used
                    </span>
                </div>
                <div class="stat-progress" style="margin-top:12px;">
                    @php
                        $total = $status->total_available + $status->total_used;
                        $pct   = $total > 0 ? round(($status->total_available / $total) * 100) : 100;
                    @endphp
                    <div class="stat-progress-bar primary" style="width: {{ $pct }}%;"></div>
                </div>
            </div>
        </div>
    </div>
    @empty
    {{-- If no leave data, show pending count --}}
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card" style="color: var(--warning);">
            <div class="stat-icon warning"><i class="fa fa-plane"></i></div>
            <div class="stat-body">
                <div class="stat-value">0</div>
                <div class="stat-label">Leave Balance</div>
                <div class="stat-sub" style="font-size:11px; color: var(--text-muted);">No leave types assigned</div>
            </div>
        </div>
    </div>
    @endforelse

    {{-- Pending Leaves Card --}}
    <div class="col-lg-3 col-md-6" style="margin-bottom: 16px;">
        <div class="stat-card" style="color: var(--warning);">
            <div class="stat-icon warning">
                <i class="fa fa-hourglass-half"></i>
            </div>
            <div class="stat-body">
                <div class="stat-value">{{ $pendingLeavesCount }}</div>
                <div class="stat-label">Pending Requests</div>
                <div class="stat-sub">
                    <a href="{{ route('employee.leaves.index') }}" style="color: var(--warning); font-size:11px; font-weight:600; text-decoration:none !important;">
                        View All →
                    </a>
                </div>
                <div class="stat-progress" style="margin-top:12px;">
                    <div class="stat-progress-bar warning" style="width: {{ $pendingLeavesCount > 0 ? min(100, $pendingLeavesCount * 20) : 0 }}%;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     ROW 2: PROFILE + ATTENDANCE CLOCK
     ══════════════════════════════════════════════ --}}
<div class="row" style="margin-bottom: 24px;">

    {{-- Profile & ID Card --}}
    <div class="col-md-7" style="margin-bottom: 16px;">
        <div class="panel" style="margin-bottom: 0; height: 100%;">
            <div class="panel-heading">
                <i class="fa fa-user"></i> My Profile &amp; ID Badge
                <button type="button" class="btn btn-default btn-xs" style="margin-left: auto;" data-toggle="modal" data-target="#bankDetailsModal">
                    <i class="fa fa-university"></i> Bank Details
                </button>
            </div>
            <div class="panel-body">
                <div class="row">
                    {{-- Profile Info --}}
                    <div class="col-sm-7">
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; align-items: center; gap: 12px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;">
                                {{-- Profile photo avatar --}}
                                <div id="profile-avatar-circle" onclick="document.getElementById('emp-photo-input').click()" title="Click to change photo"
                                     style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #4f46e5, #6366f1); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 20px; cursor: pointer; overflow: hidden; flex-shrink: 0; position: relative; border: 3px solid var(--primary-light);">
                                    @if($user->profile_photo)
                                        <img id="profile-avatar-img" src="{{ $user->profile_photo_url }}" alt="Profile" style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;">
                                    @else
                                        <span id="profile-avatar-initials">{{ strtoupper(substr($user->first_name,0,1) . substr($user->last_name??'',0,1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-size: 16px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.3px;">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 3px;">
                                        {{ $user->jobTitle ? $user->jobTitle->name : 'Staff' }}
                                        @if($user->department)
                                            &bull; {{ $user->department->name }}
                                        @endif
                                    </div>
                                    <span class="label label-info" style="margin-top: 5px; display: inline-block;">{{ $user->employment_status ?: 'Full-Time' }}</span>
                                </div>
                            </div>

                            {{-- Details list --}}
                            <div style="display: grid; gap: 7px;">
                                <div style="display: flex; gap: 8px; font-size: 13px;">
                                    <span style="color: var(--text-muted); width: 110px; flex-shrink: 0; font-weight: 500;">Email</span>
                                    <span style="color: var(--text-primary); font-weight: 500; overflow: hidden; text-overflow: ellipsis;">{{ $user->email }}</span>
                                </div>
                                <div style="display: flex; gap: 8px; font-size: 13px;">
                                    <span style="color: var(--text-muted); width: 110px; flex-shrink: 0; font-weight: 500;">Employee ID</span>
                                    <span style="color: var(--text-primary); font-weight: 700;">EMP-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div style="display: flex; gap: 8px; font-size: 13px;">
                                    <span style="color: var(--text-muted); width: 110px; flex-shrink: 0; font-weight: 500;">Bank Name</span>
                                    <span id="display-bank-name" style="color: var(--text-primary); font-weight: 500;">{{ $user->bank_name ?: '—' }}</span>
                                </div>
                                <div style="display: flex; gap: 8px; font-size: 13px;">
                                    <span style="color: var(--text-muted); width: 110px; flex-shrink: 0; font-weight: 500;">Account No.</span>
                                    <span id="display-account-number" style="color: var(--text-primary); font-weight: 500;">{{ $user->account_number ?: '—' }}</span>
                                </div>
                            </div>

                            {{-- Hidden file input --}}
                            <input type="file" id="emp-photo-input" accept="image/*" style="display:none;" onchange="handleEmpPhotoChange(this)">
                            <div id="emp-photo-status" style="font-size:11.5px; min-height:16px;"></div>

                            <div style="display: flex; gap: 8px; margin-top: 4px;">
                                <button type="button" class="btn btn-default btn-sm" onclick="document.getElementById('emp-photo-input').click();" style="width: auto !important;">
                                    <i class="fa fa-camera"></i> Change Photo
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" onclick="printIDCard()" style="width: auto !important;">
                                    <i class="fa fa-print"></i> Print ID Card
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ID Card Preview --}}
                    <div class="col-sm-5 text-center" style="display: flex; align-items: center; justify-content: center;">
                        <div id="employee-id-card" style="max-width: 200px; width: 100%; border: 1px solid #e2e8f0; border-radius: 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.10); background-color: white; overflow: hidden; display: inline-block; text-align: center; font-family: 'Inter', 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                            <div style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; padding: 12px 10px; font-weight: 800; font-size: 13px; letter-spacing: 0.5px;">
                                {{ strtoupper(config('app.name', 'HRM')) }}
                            </div>
                            <div style="margin-top: 14px; display: inline-block; position: relative;">
                                <div id="id-card-avatar-wrap" style="width: 72px; height: 72px; border-radius: 50%; border: 3px solid #4f46e5; background-color: #ede9fe; display: flex; align-items: center; justify-content: center; overflow: hidden; margin: 0 auto; cursor: pointer;" onclick="document.getElementById('emp-photo-input').click();" title="Click to change photo">
                                    @if($user->profile_photo)
                                        <img id="id-card-photo" src="{{ $user->profile_photo_url }}" alt="Profile" style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        <i id="id-card-placeholder-icon" class="fa fa-user" style="color: #a5b4fc; font-size: 34px;"></i>
                                    @endif
                                </div>
                            </div>
                            <div style="margin-top: 10px; padding: 0 10px;">
                                <h4 style="margin: 0; font-weight: 800; color: #0f172a; font-size: 14px;">{{ $user->first_name }} {{ $user->last_name }}</h4>
                                <p style="margin: 3px 0 0 0; font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">{{ $user->jobTitle ? $user->jobTitle->name : 'Staff' }}</p>
                                <p style="margin: 2px 0 0 0; font-size: 10px; color: #9ca3af;">{{ $user->department ? $user->department->name : 'General' }}</p>
                            </div>
                            @if($qrClockinEnabled)
                            <div style="margin-top: 12px;">
                                <canvas id="id-card-qr-canvas" style="background: white; padding: 4px; border-radius: 4px; border: 1px solid #f1f5f9;"></canvas>
                            </div>
                            @endif
                            <div style="margin-top: 10px; background-color: #f8fafc; padding: 8px 10px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #64748b; width: 100%;">
                                <strong style="color: #0f172a;">ID: EMP-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                <div style="font-size: 8px; margin-top: 2px; color: #ef4444; font-weight: 700; letter-spacing: 0.5px;">AUTHORIZED ACCESS ONLY</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance Clock --}}
    <div class="col-md-5" style="margin-bottom: 16px;">
        <div class="panel" style="margin-bottom: 16px; overflow: hidden;">
            <div class="panel-heading">
                <i class="fa fa-clock-o"></i> Attendance Clock
                {{-- Status pill --}}
                @if($clockStatus === 'active')
                    <span class="status-pill active" style="margin-left: auto;">Clocked In</span>
                @elseif($clockStatus === 'done')
                    <span class="status-pill inactive" style="margin-left: auto;">Shift Done</span>
                @else
                    <span class="status-pill" style="margin-left: auto; background: var(--danger-light); color: #7f1d1d;">
                        <span style="width:6px; height:6px; border-radius:50%; background: var(--danger); display:inline-block; flex-shrink:0;"></span>
                        Absent
                    </span>
                @endif
            </div>
            <div class="panel-body text-center" style="padding: 28px 22px !important;">
                {{-- Digital clock --}}
                <div id="clock-display" style="font-size: 38px; font-weight: 900; color: var(--text-primary); letter-spacing: -2px; font-variant-numeric: tabular-nums; line-height: 1;">
                    00:00:00
                </div>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 6px; margin-bottom: 22px; font-weight: 500;">
                    {{ \Carbon\Carbon::now()->format('l, F d, Y') }}
                </div>

                @if($todayRecord)
                    <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 20px; font-size: 12px; color: var(--text-secondary);">
                        <div style="text-align: center;">
                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.6px;">Clock In</div>
                            <div style="font-size: 15px; font-weight: 700; color: var(--success); margin-top: 3px;">
                                {{ \Carbon\Carbon::parse($todayRecord->check_in)->format('h:i A') }}
                            </div>
                        </div>
                        @if($todayRecord->check_out)
                        <div style="width: 1px; background: #e2e8f0;"></div>
                        <div style="text-align: center;">
                            <div style="font-size: 11px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: 0.6px;">Clock Out</div>
                            <div style="font-size: 15px; font-weight: 700; color: var(--danger); margin-top: 3px;">
                                {{ \Carbon\Carbon::parse($todayRecord->check_out)->format('h:i A') }}
                            </div>
                        </div>
                        @endif
                    </div>
                @endif

                @if($manualClockinEnabled)
                <form action="{{ route('employee.attendance.web_clock') }}" method="POST">
                    {{ csrf_field() }}
                    @if(!$todayRecord)
                        <button type="submit" class="btn btn-success btn-block" style="border-radius: var(--radius-full) !important; max-width: 220px; margin: 0 auto; width: 100%;">
                            <i class="fa fa-sign-in"></i> Clock In Now
                        </button>
                    @elseif(!$todayRecord->check_out)
                        <button type="submit" class="btn btn-danger btn-block" style="border-radius: var(--radius-full) !important; max-width: 220px; margin: 0 auto; width: 100%;">
                            <i class="fa fa-sign-out"></i> Clock Out Now
                        </button>
                    @else
                        <button type="button" class="btn btn-default btn-block disabled" style="border-radius: var(--radius-full) !important; max-width: 220px; margin: 0 auto; width: 100%; opacity: 0.6; cursor: not-allowed;">
                            <i class="fa fa-check-circle"></i> Shift Completed
                        </button>
                    @endif
                </form>
                @else
                <div class="alert alert-info" style="max-width: 280px; margin: 0 auto; text-align: left; border-radius: var(--radius-md) !important;">
                    <i class="fa fa-info-circle"></i> Manual clock-in is disabled. Please use your ID badge at a kiosk.
                </div>
                @endif
            </div>
        </div>

        {{-- Latest Payslip Mini --}}
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-heading">
                <i class="fa fa-file-text-o"></i> Latest Payslip
                @if($latestPayslip)
                    <a href="{{ route('employee.payroll.show', $latestPayslip->id) }}" class="btn btn-default btn-xs" style="margin-left: auto; font-size: 11px; padding: 3px 10px !important; width:auto !important;">
                        View Details
                    </a>
                @endif
            </div>
            <div class="panel-body text-center" style="padding: 20px 22px !important;">
                @if($latestPayslip)
                    <div class="payroll-amount" style="font-size: 30px; margin-bottom: 4px;">
                        ₦{{ number_format($latestPayslip->net_salary, 2) }}
                    </div>
                    <div class="payroll-label">{{ $latestPayslip->payroll_month }}</div>
                    <a href="{{ route('employee.payroll.index') }}" class="btn btn-default btn-sm btn-block" style="margin-top: 14px;">
                        <i class="fa fa-list"></i> All Payslips
                    </a>
                @else
                    <div style="padding: 12px 0; color: var(--text-muted);">
                        <i class="fa fa-money fa-2x" style="opacity: 0.25; display: block; margin-bottom: 8px;"></i>
                        <div style="font-size: 13px; font-weight: 500;">No payslips yet</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     ROW 3: LEAVE BALANCE TABLE + MESSAGES
     ══════════════════════════════════════════════ --}}
<div class="row">

    {{-- Leave Balance Table --}}
    <div class="col-md-5" style="margin-bottom: 16px;">
        <div class="panel" style="margin-bottom: 0; height: 100%;">
            <div class="panel-heading">
                <i class="fa fa-plane"></i> Leave Balances
                <a href="{{ route('employee.leaves.index') }}" class="btn btn-primary btn-xs" style="margin-left: auto; font-size: 11px; padding: 3px 10px !important; width: auto !important;">
                    <i class="fa fa-plus"></i> Apply
                </a>
            </div>
            <div class="panel-body" style="padding: 0 !important;">
                <table class="table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th style="text-align: right;">Available</th>
                            <th style="text-align: right;">Used</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveStatuses as $status)
                            <tr>
                                <td style="font-weight: 600;">{{ $status->leave_type_name }}</td>
                                <td style="text-align: right;">
                                    <span class="label label-success">{{ $status->total_available }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <span class="label label-danger">{{ $status->total_used }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center" style="padding: 32px; color: var(--text-muted);">
                                    <i class="fa fa-inbox" style="font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.3;"></i>
                                    No leave balances assigned yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($pendingLeavesCount > 0)
                <div class="panel-footer" style="text-align: center; padding: 10px 16px !important;">
                    <span class="label label-warning" style="font-size: 11px; padding: 4px 10px !important;">
                        <i class="fa fa-hourglass-half"></i> {{ $pendingLeavesCount }} pending approval
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Messages from HR --}}
    <div class="col-md-7" style="margin-bottom: 16px;">
        <div class="panel" style="margin-bottom: 0; height: 100%;">
            <div class="panel-heading">
                <i class="fa fa-envelope-o"></i> Recent Messages from HR
                <a href="{{ route('employee.chat.index') }}" class="btn btn-default btn-xs" style="margin-left: auto; font-size: 11px; padding: 3px 10px !important; width: auto !important;">
                    Open Inbox
                </a>
            </div>
            <div class="panel-body" style="padding: 16px 20px !important;">
                <?php
                    $latestHrMessages = \App\Modules\Chat\Models\Conversation::with(['hrManager', 'creator'])
                        ->where('employee_id', Auth::id())
                        ->orderBy('last_message_at', 'desc')
                        ->take(5)
                        ->get();
                ?>
                @forelse($latestHrMessages as $chat)
                    <?php
                        $hrSender = $chat->hrManager ?: $chat->creator;
                        $unread   = $chat->unreadMessagesCount();
                        $senderInitials = $hrSender ? strtoupper(substr($hrSender->first_name, 0, 1)) : 'H';
                    ?>
                    <a href="{{ route('employee.chat.index', ['conversation_id' => $chat->id]) }}"
                       class="conversation-item" style="text-decoration:none!important;">
                        <div class="conversation-avatar" style="background: linear-gradient(135deg, #0f172a, #1e40af);">
                            {{ $senderInitials }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div class="conversation-name">
                                HR: {{ $hrSender ? $hrSender->first_name . ' ' . $hrSender->last_name : 'Admin' }}
                            </div>
                            <div class="conversation-meta">
                                {{ $chat->subject }}
                                @if($chat->last_message_at)
                                    &bull; {{ $chat->last_message_at->diffForHumans() }}
                                @endif
                            </div>
                        </div>
                        @if($unread > 0)
                            <span class="nav-badge">{{ $unread }}</span>
                        @endif
                    </a>
                @empty
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <i class="fa fa-comment-o" style="font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                        <div style="font-size: 13px; font-weight: 500;">No messages yet</div>
                        <div style="font-size: 12px; margin-top: 4px;">HR messages will appear here</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════
     BANK DETAILS MODAL
     ══════════════════════════════════════════════ --}}
<div id="bankDetailsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bank-details-form" action="{{ route('employee.profile.bank_details') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" style="color: var(--text-muted); font-size: 20px; opacity:1;">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-university"></i> Update Bank Details</h4>
                </div>
                <div class="modal-body">
                    <div id="bank-form-status"></div>
                    <div class="form-group">
                        <label for="bank_name">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ $user->bank_name }}" required placeholder="e.g. Zenith Bank">
                    </div>
                    <div class="form-group">
                        <label for="account_number">Account Number</label>
                        <input type="text" name="account_number" id="account_number" class="form-control" value="{{ $user->account_number }}" required placeholder="e.g. 1029384756">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btn-save-bank">
                        <i class="fa fa-save"></i> Save Details
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="width: auto !important;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Hidden print frame --}}
<iframe id="print-frame" style="display:none; position:fixed; left:-9999px; top:-9999px; width:0; height:0; border:none;" title="ID Card Print"></iframe>

<style>
@keyframes pulse {
    0%   { opacity: 0.85; transform: scale(1);    }
    50%  { opacity: 1;    transform: scale(1.03); }
    100% { opacity: 0.85; transform: scale(1);    }
}
@keyframes pulse-dot {
    0%   { opacity: 1; transform: scale(1);   box-shadow: 0 0 0 0 rgba(74,222,128,0.5); }
    70%  { opacity: 1; transform: scale(1);   box-shadow: 0 0 0 6px rgba(74,222,128,0); }
    100% { opacity: 1; transform: scale(1);   box-shadow: 0 0 0 0 rgba(74,222,128,0); }
}
/* Stat card flex columns equal height */
.row .col-lg-3.col-md-6 { display: flex; flex-direction: column; }
.row .col-lg-3.col-md-6 > .stat-card { flex: 1; }
.row .col-md-7, .row .col-md-5 { display: flex; flex-direction: column; }
.row .col-md-7 > .panel, .row .col-md-5 > .panel { flex: 1; }
</style>
@endsection

@section('additionalJS')
<script src="/js/qrious.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── 1. QR Code on ID Card
    var token   = "{{ $token }}";
    var qrCanvas = document.getElementById('id-card-qr-canvas');
    if (token && qrCanvas) {
        new QRious({ element: qrCanvas, value: token, size: 100, level: 'H' });
    }

    // ── 2. Real-time Digital Clock
    function updateClock() {
        var now = new Date(), h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12; h = h ? h : 12;
        m = m < 10 ? '0' + m : m;
        s = s < 10 ? '0' + s : s;
        var el = document.getElementById('clock-display');
        if (el) el.textContent = h + ':' + m + ':' + s + ' ' + ampm;
    }
    setInterval(updateClock, 1000);
    updateClock();
});

// ── 3. Photo Upload
var EMP_UPLOAD_URL = '{{ route("employee.profile.photo.upload") }}';
var EMP_CSRF       = '{{ csrf_token() }}';
var currentPhotoBase64 = @if($user->profile_photo) '{{ $user->profile_photo_url }}' @else null @endif;

function handleEmpPhotoChange(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    if (file.size > 3 * 1024 * 1024) {
        showEmpStatus('<i class="fa fa-exclamation-circle"></i> File too large. Max 3 MB.', '#ef4444');
        return;
    }
    var reader = new FileReader();
    reader.onload = function(e) {
        updateIdCardPhoto(e.target.result);
        updateProfileAvatar(e.target.result);
        currentPhotoBase64 = e.target.result;
    };
    reader.readAsDataURL(file);

    var fd = new FormData();
    fd.append('profile_photo', file);
    fd.append('_token', EMP_CSRF);
    showEmpStatus('<i class="fa fa-spinner fa-spin"></i> Uploading…', '#94a3b8');
    fetch(EMP_UPLOAD_URL, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                currentPhotoBase64 = data.url;
                showEmpStatus('<i class="fa fa-check-circle"></i> Photo updated!', '#10b981');
                setTimeout(function() { showEmpStatus('', ''); }, 3000);
            } else { showEmpStatus('Upload failed. Try again.', '#ef4444'); }
        })
        .catch(function() { showEmpStatus('Network error.', '#ef4444'); });
}

function updateIdCardPhoto(src) {
    var wrap = document.getElementById('id-card-avatar-wrap');
    if (!wrap) return;
    var icon = document.getElementById('id-card-placeholder-icon');
    if (icon) icon.style.display = 'none';
    var img = document.getElementById('id-card-photo');
    if (!img) {
        img = document.createElement('img');
        img.id = 'id-card-photo'; img.alt = 'Profile';
        img.style.cssText = 'width:100%; height:100%; object-fit:cover;';
        wrap.appendChild(img);
    }
    img.src = src;
}

function updateProfileAvatar(src) {
    var circle = document.getElementById('profile-avatar-circle');
    if (!circle) return;
    var initials = document.getElementById('profile-avatar-initials');
    if (initials) initials.style.display = 'none';
    var img = document.getElementById('profile-avatar-img');
    if (!img) {
        img = document.createElement('img');
        img.id = 'profile-avatar-img'; img.alt = 'Profile';
        img.style.cssText = 'width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;';
        circle.appendChild(img);
    }
    img.src = src;
}

function showEmpStatus(msg, color) {
    var el = document.getElementById('emp-photo-status');
    if (el) { el.innerHTML = msg; el.style.color = color; }
}

// ── 4. Print ID Card
function printIDCard() {
    var canvas  = document.getElementById('id-card-qr-canvas');
    var qrImage = canvas ? canvas.toDataURL('image/png') : '';
    var card    = document.getElementById('employee-id-card');
    var nameEl  = card.querySelector('h4');
    var roleEl  = card.querySelectorAll('p')[0];
    var deptEl  = card.querySelectorAll('p')[1];
    var idEl    = card.querySelector('strong');

    var empName = nameEl ? nameEl.textContent.trim() : '';
    var empRole = roleEl ? roleEl.textContent.trim() : '';
    var empDept = deptEl ? deptEl.textContent.trim() : '';
    var empId   = idEl   ? idEl.textContent.trim()   : '';

    var avatarHTML = currentPhotoBase64
        ? '<img src="' + currentPhotoBase64 + '" alt="Photo" style="width:100%; height:100%; object-fit:cover;">'
        : '<svg viewBox="0 0 24 24" fill="#a5b4fc" xmlns="http://www.w3.org/2000/svg" style="width:46px;height:46px;"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>';

    var printHTML = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Employee ID Card</title>' +
        '<style>' +
        '* { box-sizing: border-box; margin: 0; padding: 0; }' +
        '@page { size: A6 portrait; margin: 0; }' +
        'html, body { width:105mm; height:148mm; display:flex; align-items:center; justify-content:center; background:#fff; -webkit-print-color-adjust:exact; print-color-adjust:exact; }' +
        '.id-card { width:80mm; background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; font-family:"Inter","Helvetica Neue",Helvetica,Arial,sans-serif; box-shadow:0 4px 16px rgba(0,0,0,0.12); -webkit-print-color-adjust:exact; print-color-adjust:exact; }' +
        '.card-header { background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%) !important; -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; color:#fff !important; padding:13px 10px; text-align:center; font-weight:800; font-size:13px; letter-spacing:0.5px; }' +
        '.avatar-wrap { margin:14px auto 0; width:78px; height:78px; border-radius:50%; border:3px solid #4f46e5; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#ede9fe; }' +
        '.name-block { text-align:center; padding:10px 10px 0; }' +
        '.name-block h4 { font-size:15px; font-weight:800; color:#0f172a; margin-bottom:4px; }' +
        '.name-block .role { font-size:10px; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; }' +
        '.name-block .dept { font-size:10px; color:#9ca3af; margin-top:2px; }' +
        '.qr-block { text-align:center; padding:12px 10px 10px; }' +
        '.qr-block img { width:96px; height:96px; border:1px solid #f1f5f9; border-radius:4px; padding:3px; }' +
        '.card-footer { background:#f8fafc !important; -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; border-top:1px solid #e2e8f0; padding:8px 10px; text-align:center; }' +
        '.card-footer .emp-id { font-size:11px; color:#0f172a; font-weight:700; }' +
        '.card-footer .restricted { font-size:8px; color:#ef4444; font-weight:700; letter-spacing:0.6px; margin-top:3px; }' +
        '</style></head><body><div class="id-card">' +
        '<div class="card-header">{{ strtoupper(config("app.name","HRM")) }}</div>' +
        '<div style="text-align:center"><div class="avatar-wrap">' + avatarHTML + '</div></div>' +
        '<div class="name-block"><h4>' + empName + '</h4>' +
        '<div class="role">' + empRole + '</div>' +
        '<div class="dept">' + empDept + '</div></div>' +
        (qrImage ? '<div class="qr-block"><img src="' + qrImage + '" alt="QR"></div>' : '<div class="qr-block" style="padding:16px;color:#999;font-size:10px;">QR not available</div>') +
        '<div class="card-footer"><div class="emp-id">' + empId + '</div><div class="restricted">AUTHORIZED ACCESS ONLY</div></div>' +
        '</div></body></html>';

    var iframe = document.getElementById('print-frame');
    iframe.style.display = 'block';
    iframe.style.width  = '210mm';
    iframe.style.height = '297mm';
    var iDoc = iframe.contentWindow.document;
    iDoc.open(); iDoc.write(printHTML); iDoc.close();
    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() { iframe.style.display='none'; iframe.style.width='0'; iframe.style.height='0'; }, 2000);
    }, 500);
}

// ── 5. Bank Details AJAX
$(document).ready(function() {
    $('#bank-details-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this), btn = $('#btn-save-bank'), statusDiv = $('#bank-form-status');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        statusDiv.html('');
        $.ajax({
            type: 'POST', url: form.attr('action'), data: form.serialize(), dataType: 'json',
            success: function(resp) {
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Details');
                if (resp.success) {
                    $('#display-bank-name').text($('#bank_name').val());
                    $('#display-account-number').text($('#account_number').val());
                    statusDiv.html('<div class="alert alert-success">Bank details updated!</div>');
                    setTimeout(function() { $('#bankDetailsModal').modal('hide'); statusDiv.html(''); }, 1500);
                } else {
                    statusDiv.html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Details');
                var errors = xhr.responseJSON;
                var msg = (errors && errors.bank_name) ? errors.bank_name[0] : (errors && errors.account_number ? errors.account_number[0] : 'Failed to save details.');
                statusDiv.html('<div class="alert alert-danger">' + msg + '</div>');
            }
        });
    });
});
</script>
@endsection
