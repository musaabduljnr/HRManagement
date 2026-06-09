@extends ('layouts.main')

@section('content')
<div class="row" style="margin-bottom: 20px;">
    <!-- Card 1: Total Employees -->
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary" style="border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="padding: 15px;">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-users fa-4x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 32px; font-weight: bold;">{{ $totalEmployees }}</div>
                        <div>Total Employees</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="padding: 10px 15px;">
                <span class="pull-left"><span class="label label-info">Active: {{ $activeEmployees }}</span></span>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- Card 2: Attendance Today -->
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-success" style="border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="padding: 15px; background-color: #5cb85c; border-color: #4cae4c; color: white;">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-clock-o fa-4x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 32px; font-weight: bold;">{{ $attendanceToday }}</div>
                        <div>Attendance Today</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="padding: 10px 15px;">
                <a href="{{ route('attendance.index') }}" class="pull-left text-success">View Details</a>
                <span class="pull-right text-success"><i class="fa fa-arrow-circle-right"></i></span>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- Card 3: Pending Leaves -->
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-warning" style="border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="padding: 15px; background-color: #f0ad4e; border-color: #eea236; color: white;">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-plane fa-4x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 32px; font-weight: bold;">{{ $pendingLeaves }}</div>
                        <div>Pending Leaves</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="padding: 10px 15px;">
                <a href="{{ route('leave.employee_leaves.index') }}" class="pull-left text-warning">Approve Leaves</a>
                <span class="pull-right text-warning"><i class="fa fa-arrow-circle-right"></i></span>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- Card 4: New Applicants / ATS -->
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-danger" style="border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="padding: 15px; background-color: #d9534f; border-color: #d43f3a; color: white;">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-graduation-cap fa-4x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge" style="font-size: 32px; font-weight: bold;">{{ $newApplicants }}</div>
                        <div>New Applicants</div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" style="padding: 10px 15px;">
                <span class="pull-left"><span class="label label-danger">Last 30 Days</span></span>
                <a href="{{ route('pim.candidates.index') }}" class="pull-right text-danger">View Pipeline</a>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 20px;">
    <!-- Payroll and Recent Activity Summary Row -->
    <div class="col-md-4">
        <div class="panel panel-default" style="border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="font-weight: bold;"><i class="fa fa-money"></i> Payroll Payout (Current Month)</div>
            <div class="panel-body text-center" style="padding: 25px;">
                <h2 style="color: #2e6da4; font-weight: bold; margin-top: 0;">${{ number_format($payrollSummary, 2) }}</h2>
                <p class="text-muted">Total Net Salary Paid / Pending</p>
                <a href="{{ route('payroll.index') }}" class="btn btn-primary btn-block">Manage Payroll</a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-default" style="border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="font-weight: bold;"><i class="fa fa-list"></i> Recent Activity Logs</div>
            <div class="panel-body" style="padding: 0;">
                <ul class="list-group" style="margin-bottom: 0;">
                    @forelse($recentActivities as $activity)
                        <li class="list-group-item" style="border-left: none; border-right: none;">
                            <strong>{{ $activity->first_name }} {{ $activity->last_name }}</strong>: {{ $activity->activity }}
                            <span class="text-muted pull-right" style="font-size: 11px;">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">No recent activities logged.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 20px;">
    <!-- Message employee quick action & recent chats -->
    <div class="col-md-6">
        <div class="panel panel-default" style="border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="font-weight: bold; background: white;"><i class="fa fa-paper-plane-o"></i> Message Employee (Quick Action)</div>
            <div class="panel-body" style="padding: 20px;">
                <?php
                    $quickEmployees = \App\User::where('role', \App\User::USER_ROLE_EMPLOYEE)->orderBy('first_name', 'asc')->get();
                ?>
                <form action="{{ route('chat.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <select name="employee_id" class="form-control" required style="border-radius: 8px;">
                            <option value="">-- Choose Employee to Message --</option>
                            @foreach($quickEmployees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject" class="form-control" placeholder="Subject (Optional)" style="border-radius: 8px;">
                    </div>
                    <div class="form-group">
                        <textarea name="body" class="form-control" rows="2" placeholder="Write message..." required style="border-radius: 8px; resize: none;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" style="border-radius: 20px; font-weight: bold;">
                        <i class="fa fa-paper-plane"></i> Send Direct Message
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default" style="border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div class="panel-heading" style="font-weight: bold; background: white;"><i class="fa fa-comments-o"></i> Recent Chats</div>
            <div class="panel-body" style="padding: 0;">
                <?php
                    $recentChats = \App\Modules\Chat\Models\Conversation::with('employee')
                        ->orderBy('last_message_at', 'desc')
                        ->take(3)
                        ->get();
                ?>
                <ul class="list-group" style="margin-bottom: 0;">
                    @forelse($recentChats as $chat)
                        <?php $unread = $chat->unreadMessagesCount(); ?>
                        <li class="list-group-item" style="border-left: none; border-right: none; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <a href="{{ route('chat.index', ['conversation_id' => $chat->id]) }}" style="font-weight: bold; text-decoration: none; color: #1e3c72;">
                                    {{ $chat->employee ? $chat->employee->first_name . ' ' . $chat->employee->last_name : 'Staff Member' }}
                                </a>
                                <div style="font-size: 11px; color: #777;">
                                    Subject: {{ $chat->subject }} &bull; {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '' }}
                                </div>
                            </div>
                            @if($unread > 0)
                                <span class="badge" style="background-color: #27ae60; color: white;">{{ $unread }}</span>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted" style="padding: 30px 10px;">No active conversations.</li>
                    @endforelse
                </ul>
                <div class="panel-footer text-center" style="background: white; border-top: 1px solid #eee;">
                    <a href="{{ route('chat.index') }}" class="btn btn-default btn-xs btn-block">View All Messages</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="custom-panel">
            <div class="custom-panel-heading">{{trans('app.leave.calendar.main')}}</div>
            <div id="leave-calendar"></div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="custom-panel">
            <div class="custom-panel-heading">{{trans('app.pim.birthdays')}}</div>
            <div id="birthday-calendar"></div>
        </div>
    </div>
</div>
@endsection
@section('additionalCSS')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.print.css" media='print'>
@endsection
@section('additionalJS')
<script src="{{url('vendor/moment/moment.min.js')}}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js"></script>
<script>
    $(document).ready(function() {
        var sources = [];
        $('#leave-calendar').fullCalendar({
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            defaultDate: '{{get_current_date()}}',
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            viewRender: function(view, element) {
                var date = $('#leave-calendar').fullCalendar('getDate');
                date = moment(date).format('YYYY-MM-DD');
                if(sources.indexOf(date) == -1) {
                    sources.push(date);
                    $.ajax({
                        url: "{{route('leave.calendar.render')}}",
                        data: {date: date},
                        success: function(events) {
                            $('#leave-calendar').fullCalendar('addEventSource', events);
                        }
                    });
                }
            }
        });
        var sources = [];
        $('#birthday-calendar').fullCalendar({
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'month,basicWeek,basicDay'
            },
            defaultDate: '{{get_current_date()}}',
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            viewRender: function(view, element) {
                var date = $('#birthday-calendar').fullCalendar('getDate');
                date = moment(date).format('YYYY-MM-DD');
                if(sources.indexOf(date) == -1) {
                    sources.push(date);
                    $.ajax({
                        url: "{{route('pim.employees.birthdays')}}",
                        data: {date: date},
                        success: function(events) {
                            $('#birthday-calendar').fullCalendar('addEventSource', events);
                        }
                    });
                }
            }
        });
    });
</script>
@endsection