@extends ('layouts.main_employee')

@section('content')
<div class="row" id="dashboard-content">
    <!-- Profile & ID Card Generator -->
    <div class="col-md-6">
        <div class="custom-panel">
            <div class="custom-panel-heading"><i class="fa fa-user"></i> My Profile & ID Badge</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <h4><strong>Employee Profile</strong></h4>
                        <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Department:</strong> {{ $user->department ? $user->department->name : 'N/A' }}</p>
                        <p><strong>Job Title:</strong> {{ $user->jobTitle ? $user->jobTitle->name : 'N/A' }}</p>
                        <p><strong>Employment Status:</strong> <span class="label label-info">{{ $user->employment_status ?: 'Full-Time' }}</span></p>
                        <br>
                        <button type="button" class="btn btn-primary btn-block" onclick="printIDCard()">
                            <i class="fa fa-print"></i> Print Official ID Card
                        </button>
                    </div>
                    
                    <div class="col-sm-6 text-center">
                        <!-- ID Card Widget Preview -->
                        <div id="employee-id-card" style="width: 250px; height: 380px; border: 1px solid #ccc; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background-color: white; overflow: hidden; display: inline-block; text-align: center; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                            <!-- Top Header banner -->
                            <div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 12px 10px; font-weight: bold; font-size: 15px; letter-spacing: 0.5px;">
                                HRM ENTERPRISE
                            </div>
                            
                            <!-- Profile picture placeholder -->
                            <div style="margin-top: 15px; display: inline-block; position: relative;">
                                <div style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid #1e3c72; background-color: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden; margin: 0 auto;">
                                    <i class="fa fa-user fa-4x" style="color: #ccc; margin-top: 15px; font-size: 50px;"></i>
                                </div>
                            </div>
                            
                            <!-- Name & Title -->
                            <div style="margin-top: 10px; padding: 0 10px;">
                                <h4 style="margin: 0; font-weight: 700; color: #333; font-size: 16px;">{{ $user->first_name }} {{ $user->last_name }}</h4>
                                <p style="margin: 3px 0 0 0; font-size: 12px; color: #666; text-transform: uppercase;">{{ $user->jobTitle ? $user->jobTitle->name : 'Staff' }}</p>
                                <p style="margin: 2px 0 0 0; font-size: 11px; color: #888;">Dept: {{ $user->department ? $user->department->name : 'General' }}</p>
                            </div>
                            
                            <!-- QR Code attendance scanner -->
                            <div style="margin-top: 15px;">
                                <canvas id="id-card-qr-canvas" style="background: white; padding: 4px; border-radius: 4px; border: 1px solid #eee;"></canvas>
                            </div>
                            
                            <!-- Unique Identifier & Footer -->
                            <div style="margin-top: 10px; background-color: #f5f5f5; padding: 8px 10px; border-top: 1px solid #e5e5e5; font-size: 10px; color: #777; width: 100%;">
                                <strong style="color: #333;">ID: EMP-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                <div style="font-size: 9px; margin-top: 2px; color: #c0392b; font-weight: bold; letter-spacing: 0.5px;">AUTHORIZED ACCESS ONLY</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Time Tracking & Leave / Payroll Widgets -->
    <div class="col-md-6">
        <!-- Time clock widget -->
        <div class="custom-panel" style="margin-bottom: 20px;">
            <div class="custom-panel-heading"><i class="fa fa-clock-o"></i> Attendance Clock</div>
            <div class="panel-body text-center" style="padding: 20px;">
                <div style="font-size: 26px; font-weight: bold; color: #333;" id="clock-display">00:00:00 AM</div>
                <div class="text-muted" style="margin-bottom: 15px;">{{ date('l, F d, Y') }}</div>
                
                <div style="margin-bottom: 15px;">
                    @if($todayRecord)
                        @if($todayRecord->check_out)
                            <span class="label label-default" style="font-size: 14px; padding: 6px 12px;">Clocked Out Today</span>
                        @else
                            <span class="label label-success" style="font-size: 14px; padding: 6px 12px; animation: pulse 1.5s infinite;">Checked In (Active)</span>
                        @endif
                    @else
                        <span class="label label-danger" style="font-size: 14px; padding: 6px 12px;">Absent / Not Clocked In</span>
                    @endif
                </div>
                
                <form action="{{ route('employee.attendance.web_clock') }}" method="POST">
                    {{ csrf_field() }}
                    @if(!$todayRecord)
                        <button type="submit" class="btn btn-success btn-lg" style="border-radius: 20px; width: 60%;">
                            <i class="fa fa-sign-in"></i> Clock In Today
                        </button>
                    @elseif(!$todayRecord->check_out)
                        <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 20px; width: 60%;">
                            <i class="fa fa-sign-out"></i> Clock Out Today
                        </button>
                    @else
                        <button type="button" class="btn btn-default btn-lg disabled" style="border-radius: 20px; width: 60%;">
                            Shift Completed
                        </button>
                    @endif
                </form>
            </div>
        </div>
        
        <!-- Leave balances and payslips -->
        <div class="row">
            <div class="col-sm-6">
                <div class="custom-panel">
                    <div class="custom-panel-heading"><i class="fa fa-plane"></i> Leaves Balance</div>
                    <div class="panel-body" style="padding: 0;">
                        <table class="table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Avail</th>
                                    <th>Used</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaveStatuses as $status)
                                    <tr>
                                        <td>{{ $status->leave_type_name }}</td>
                                        <td><strong>{{ $status->total_available }}</strong></td>
                                        <td><span class="text-danger">{{ $status->total_used }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted" style="padding: 15px;">No balances available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div style="padding: 10px; border-top: 1px solid #ddd; background-color: #fafafa;" class="text-center">
                            <span class="label label-warning">Pending Requests: {{ $pendingLeavesCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6">
                <div class="custom-panel">
                    <div class="custom-panel-heading"><i class="fa fa-file-text-o"></i> Latest Payslip</div>
                    <div class="panel-body text-center" style="padding: 20px;">
                        @if($latestPayslip)
                            <h3 style="color: #2e6da4; font-weight: bold; margin-top: 0;">${{ number_format($latestPayslip->net_salary, 2) }}</h3>
                            <p class="text-muted">Month: {{ $latestPayslip->payroll_month }}</p>
                            <a href="{{ route('employee.payroll.show', $latestPayslip->id) }}" class="btn btn-sm btn-default btn-block">
                                <i class="fa fa-eye"></i> View Payslip Details
                            </a>
                        @else
                            <i class="fa fa-money fa-3x text-muted" style="margin-bottom: 10px;"></i>
                            <p class="text-muted">No paid payslips logged yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Only Frame (hidden on screen) -->
<div id="print-badge-container" class="print-only" style="display: none;">
    <div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
        <div id="print-badge-content"></div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 0.8; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.03); }
    100% { opacity: 0.8; transform: scale(1); }
}

@media print {
    body * {
        visibility: hidden;
    }
    #print-badge-container, #print-badge-container * {
        visibility: visible;
        display: block !important;
    }
    #print-badge-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
    }
}
</style>
@endsection

@section('additionalJS')
<script src="{{ asset('js/qrious.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Generate QR Code for ID Card
    const token = "{{ $token }}";
    if (token) {
        new QRious({
            element: document.getElementById('id-card-qr-canvas'),
            value: token,
            size: 110,
            level: 'H'
        });
    }
    
    // 2. Real-time Digital Clock
    function updateClock() {
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        
        var timeStr = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
        var clockDisplay = document.getElementById('clock-display');
        if (clockDisplay) {
            clockDisplay.textContent = timeStr;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
});

// 3. Print ID Card action
function printIDCard() {
    var badgeContent = document.getElementById('employee-id-card').cloneNode(true);
    
    var canvas = document.getElementById('id-card-qr-canvas');
    var printedCanvas = badgeContent.querySelector('#id-card-qr-canvas');
    if (canvas && printedCanvas) {
        var imgUrl = canvas.toDataURL("image/png");
        var img = document.createElement('img');
        img.src = imgUrl;
        img.style.width = "110px";
        img.style.height = "110px";
        printedCanvas.parentNode.replaceChild(img, printedCanvas);
    }
    
    document.getElementById('print-badge-content').innerHTML = '';
    document.getElementById('print-badge-content').appendChild(badgeContent);
    
    window.print();
}
</script>
@endsection