@extends('layouts.main_employee')

@section('content')
<div class="row">
    <div class="col-md-5">
        <!-- QR Card -->
        <div class="panel panel-default" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.06); overflow: hidden; margin-bottom: 30px;">
            <div class="panel-body text-center" style="padding: 40px 30px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; position: relative;">
                <!-- Glassmorphism Card Inner -->
                <div style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; padding: 25px; backdrop-filter: blur(10px); display: inline-block; width: 100%;">
                    
                    <h3 style="margin-top: 0; font-weight: 700; color: white; font-size: 24px;">Your Digital Badge</h3>
                    <p style="color: rgba(255,255,255,0.7); margin-bottom: 25px;">Scan this at the office terminal to clock in/out</p>

                    <!-- QR Code Canvas -->
                    <div style="background: white; padding: 15px; display: inline-block; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); margin-bottom: 25px;">
                        <canvas id="qr-code-canvas"></canvas>
                    </div>

                    <div style="font-weight: 600; font-size: 18px; margin-bottom: 5px;">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">
                        Employee
                    </div>

                    <!-- Status Indicator -->
                    <div style="margin-top: 15px;">
                        @if($todayRecord)
                            @if($todayRecord->check_out)
                                <span class="label label-default" style="font-size: 14px; padding: 8px 16px; border-radius: 20px; font-weight: bold; background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                                    <i class="glyphicon glyphicon-log-out" style="margin-right: 5px;"></i> Clocked Out Today
                                </span>
                            @else
                                <span class="label label-success" style="font-size: 14px; padding: 8px 16px; border-radius: 20px; font-weight: bold; background-color: #27ae60; border: 1px solid rgba(255,255,255,0.3); animation: pulse 1.5s infinite;">
                                    <i class="glyphicon glyphicon-log-in" style="margin-right: 5px;"></i> Checked In Active
                                </span>
                            @endif
                        @else
                            <span class="label label-danger" style="font-size: 14px; padding: 8px 16px; border-radius: 20px; font-weight: bold; background-color: #e74c3c; border: 1px solid rgba(255,255,255,0.3);">
                                <i class="glyphicon glyphicon-remove" style="margin-right: 5px;"></i> Absent / Not Clocked In
                            </span>
                        @endif
                    </div>

                    <!-- Manual Clock Buttons -->
                    <div style="margin-top: 25px;">
                        <form action="{{ route('employee.attendance.web_clock') }}" method="POST">
                            {{ csrf_field() }}
                            @if(!$todayRecord)
                                <button type="submit" class="btn btn-success btn-lg" style="border-radius: 24px; padding: 10px 24px; font-weight: 600; width: 80%;">
                                    <i class="glyphicon glyphicon-play"></i> Clock In
                                </button>
                            @elseif(!$todayRecord->check_out)
                                <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 24px; padding: 10px 24px; font-weight: 600; width: 80%;">
                                    <i class="glyphicon glyphicon-stop"></i> Clock Out
                                </button>
                            @else
                                <button type="button" class="btn btn-default btn-lg disabled" style="border-radius: 24px; padding: 10px 24px; font-weight: 600; width: 80%;">
                                    Completed Today
                                </button>
                            @endif
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <!-- Recent Logs -->
        <div class="panel panel-default" style="border-radius: 16px; border: 1px solid #eef0f2; box-shadow: 0 4px 25px rgba(0,0,0,0.03); overflow: hidden;">
            <div class="panel-heading" style="background-color: white; border-bottom: 1px solid #eee; padding: 20px;">
                <h4 style="margin: 0; font-weight: 700; color: #1e3c72; font-size: 18px;">
                    <i class="glyphicon glyphicon-time" style="margin-right: 10px;"></i> Attendance History
                </h4>
            </div>
            <div class="panel-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table table-hover" style="margin: 0; vertical-align: middle;">
                        <thead>
                            <tr style="color: #777; background-color: #fafbfc; border-bottom: 1px solid #eee;">
                                <th style="padding: 15px;">Date</th>
                                <th style="padding: 15px;">Check In</th>
                                <th style="padding: 15px;">Check Out</th>
                                <th style="padding: 15px;">Working Time</th>
                                <th style="padding: 15px;">Overtime</th>
                                <th style="padding: 15px;">Location IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($history as $record)
                                <tr>
                                    <td style="padding: 15px; font-weight: 600; color: #333;">
                                        {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}
                                    </td>
                                    <td style="padding: 15px; color: #27ae60; font-weight: 500;">
                                        {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                    </td>
                                    <td style="padding: 15px; color: #c0392b; font-weight: 500;">
                                        {{ $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('h:i A') : 'Active' }}
                                    </td>
                                    <td style="padding: 15px; font-weight: 600; color: #555;">
                                        @if($record->check_out)
                                            <?php
                                                $in = \Carbon\Carbon::parse($record->check_in);
                                                $out = \Carbon\Carbon::parse($record->check_out);
                                                $diff = $in->diffInMinutes($out);
                                                $hours = floor($diff / 60);
                                                $mins = $diff % 60;
                                                echo sprintf('%dh %02dm', $hours, $mins);
                                            ?>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="padding: 15px; font-weight: 600; color: #e67e22;">
                                        @if($record->check_out)
                                            <?php
                                                $in = \Carbon\Carbon::parse($record->check_in);
                                                $out = \Carbon\Carbon::parse($record->check_out);
                                                $diff = $in->diffInMinutes($out);
                                                if ($diff > 480) {
                                                    $ot = $diff - 480;
                                                    $otHours = floor($ot / 60);
                                                    $otMins = $ot % 60;
                                                    echo sprintf('%dh %02dm', $otHours, $otMins);
                                                } else {
                                                    echo '0h 00m';
                                                }
                                            ?>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="padding: 15px;"><code style="font-size: 11px;">{{ $record->ip_address ?: 'Local Kiosk' }}</code></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted" style="padding: 50px 0;">
                                        <i class="glyphicon glyphicon-folder-open" style="font-size: 40px; margin-bottom: 10px; color: #ddd; display: block;"></i>
                                        No attendance logs found in your history.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% { opacity: 0.8; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.03); }
    100% { opacity: 0.8; transform: scale(1); }
}
</style>
@endsection

@section('additionalJS')
<script src="{{ asset('js/qrious.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = "{{ $token }}";
    if (token) {
        new QRious({
            element: document.getElementById('qr-code-canvas'),
            value: token,
            size: 200,
            level: 'H'
        });
    } else {
        console.error("Token is not generated/available.");
    }
});
</script>
@endsection
