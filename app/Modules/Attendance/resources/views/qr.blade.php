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
                    <div style="background: white; padding: 15px; display: inline-block; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); margin-bottom: 25px; position: relative;">
                        <canvas id="qr-code-canvas"></canvas>
                        <!-- QR Expire overlay/countdown -->
                        <div id="qr-timer" style="position: absolute; bottom: -20px; left: 0; right: 0; text-align: center; color: #fff; font-size: 11px; font-weight: 600;">
                            Static ID Card QR Code
                        </div>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <button type="button" class="btn btn-sm btn-info" onclick="window.print()" style="border-radius: 20px;"><i class="glyphicon glyphicon-print"></i> Print QR Badge</button>
                    </div>

                    <div style="font-weight: 600; font-size: 18px; margin-bottom: 5px; margin-top: 15px;">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </div>
                    <div style="color: rgba(255,255,255,0.8); font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px;">
                        {{ Auth::user()->jobTitle ? Auth::user()->jobTitle->name : 'Employee' }}
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
                        <form id="webClockForm" action="{{ route('employee.attendance.web_clock') }}" method="POST">
                            {{ csrf_field() }}
                            <!-- Hidden validation fields -->
                            <input type="hidden" name="latitude" id="formLatitude">
                            <input type="hidden" name="longitude" id="formLongitude">
                            <input type="hidden" name="device_uuid" id="formDeviceUuid">
                            <input type="hidden" name="device_name" id="formDeviceName">
                            <input type="hidden" name="selfie_base64" id="formSelfieBase64">

                            @if(!$todayRecord)
                                <button type="button" id="clockInBtn" class="btn btn-success btn-lg" style="border-radius: 24px; padding: 10px 24px; font-weight: 600; width: 80%;">
                                    <i class="glyphicon glyphicon-play"></i> Clock In
                                </button>
                            @elseif(!$todayRecord->check_out)
                                <button type="button" id="clockOutBtn" class="btn btn-danger btn-lg" style="border-radius: 24px; padding: 10px 24px; font-weight: 600; width: 80%;">
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
                                    <td colspan="6" class="text-center text-muted" style="padding: 50px 0;">
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

<!-- Webcam Verification Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1" role="dialog" aria-labelledby="cameraModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 1;"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="cameraModalLabel"><i class="glyphicon glyphicon-camera"></i> Secure Portal Clock-In</h4>
            </div>
            <div class="modal-body text-center" style="background: #fafbfc; padding: 25px;">
                <!-- Selfie view -->
                <div id="selfieArea" style="position: relative; width: 100%; max-width: 320px; height: 240px; margin: 0 auto 15px; border-radius: 12px; overflow: hidden; background: #000; box-shadow: inset 0 0 20px rgba(0,0,0,0.8);">
                    <video id="webcam" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                    <canvas id="photoCanvas" width="320" height="240" style="display: none;"></canvas>
                </div>
                
                <div id="gpsStatus" class="alert alert-info" style="border-radius: 8px; margin-bottom: 15px; padding: 10px; font-weight: 600; font-size: 13px;">
                    <i class="glyphicon glyphicon-map-marker"></i> Requesting GPS Location...
                </div>

                <div id="errorMessage" class="alert alert-danger" style="display: none; border-radius: 8px; margin-bottom: 15px; padding: 10px; font-size: 13px;"></div>

                <button type="button" id="submitClockBtn" class="btn btn-success btn-lg" style="border-radius: 24px; padding: 10px 35px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; width: 80%;">
                    Confirm & Submit
                </button>
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
<script src="/js/qrious.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let qr = null;
    let countdownInterval = null;
    let countdownSecs = 30;

    // Initialize QRious
    const canvas = document.getElementById('qr-code-canvas');
    qr = new QRious({
        element: canvas,
        value: "{{ $token }}",
        size: 200,
        level: 'H'
    });

    // 1. Dynamic Rotating QR Codes (Disabled to use Static Unique ID Card QR)
    // function fetchAndRotateQr() {
    //     fetch("{{ route('employee.attendance.token') }}")
    //     ...
    // }

    // Use Static Default QR Code for ID Card
    document.getElementById('qr-timer').innerHTML = 'Static ID Badge QR';

    // 2. Local Device Fingerprinting
    function getOrGenerateDeviceUuid() {
        let uuid = localStorage.getItem('hrm_device_uuid');
        if (!uuid) {
            uuid = 'device_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
            localStorage.setItem('hrm_device_uuid', uuid);
        }
        return uuid;
    }
    
    const deviceUuid = getOrGenerateDeviceUuid();
    document.getElementById('formDeviceUuid').value = deviceUuid;
    document.getElementById('formDeviceName').value = navigator.userAgent.substring(0, 100);

    // 3. Web Clock validation and Selfie/GPS verification
    const selfieRequired = {{ ($rule && $rule->selfie_required) ? 'true' : 'false' }};
    const checkoutSelfieRequired = {{ ($rule && $rule->checkout_selfie_required) ? 'true' : 'false' }};
    const radiusRequired = {{ ($rule && $rule->allowed_radius_meters) ? 'true' : 'false' }};
    const hasTodayRecord = {{ $todayRecord ? 'true' : 'false' }};
    const hasTodayCheckout = {{ ($todayRecord && $todayRecord->check_out) ? 'true' : 'false' }};

    const clockInBtn = document.getElementById('clockInBtn');
    const clockOutBtn = document.getElementById('clockOutBtn');
    const cameraModal = $('#cameraModal');
    const webcamElement = document.getElementById('webcam');
    const photoCanvas = document.getElementById('photoCanvas');
    const submitClockBtn = document.getElementById('submitClockBtn');
    const gpsStatusDiv = document.getElementById('gpsStatus');
    const errorDiv = document.getElementById('errorMessage');
    
    let stream = null;
    let gpsWatchId = null;

    if (clockInBtn) clockInBtn.addEventListener('click', () => triggerVerificationFlow('in'));
    if (clockOutBtn) clockOutBtn.addEventListener('click', () => triggerVerificationFlow('out'));

    function triggerVerificationFlow(type) {
        const needsSelfie = (type === 'in' && selfieRequired) || (type === 'out' && checkoutSelfieRequired);
        
        // Update modal title
        document.getElementById('cameraModalLabel').innerHTML = `<i class="glyphicon glyphicon-camera"></i> Secure Portal ${type === 'in' ? 'Clock-In' : 'Clock-Out'}`;
        errorDiv.style.display = 'none';

        // Check if we need Selfie or GPS
        if (needsSelfie || radiusRequired) {
            cameraModal.modal('show');
            
            // Start GPS acquisition
            acquireGPS();

            // Start webcam if selfie required
            if (needsSelfie) {
                document.getElementById('selfieArea').style.display = 'block';
                startWebcam();
            } else {
                document.getElementById('selfieArea').style.display = 'none';
            }
        } else {
            // Submit immediately
            submitWebClock();
        }
    }

    function startWebcam() {
        navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240 } })
        .then(s => {
            stream = s;
            webcamElement.srcObject = stream;
        })
        .catch(err => {
            console.error("Camera access error:", err);
            errorDiv.textContent = "Unable to access web camera. Camera permissions are required.";
            errorDiv.style.display = 'block';
        });
    }

    function stopWebcam() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    function acquireGPS() {
        gpsStatusDiv.className = "alert alert-info";
        gpsStatusDiv.innerHTML = '<i class="glyphicon glyphicon-map-marker"></i> Requesting GPS Location...';
        
        if (!navigator.geolocation) {
            gpsStatusDiv.className = "alert alert-warning";
            gpsStatusDiv.innerHTML = '<i class="glyphicon glyphicon-alert"></i> Geolocation is not supported by your browser.';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            position => {
                document.getElementById('formLatitude').value = position.coords.latitude;
                document.getElementById('formLongitude').value = position.coords.longitude;
                gpsStatusDiv.className = "alert alert-success";
                gpsStatusDiv.innerHTML = `<i class="glyphicon glyphicon-ok"></i> Location acquired (Accuracy: ${Math.round(position.coords.accuracy)}m)`;
            },
            err => {
                console.error("GPS error:", err);
                gpsStatusDiv.className = "alert alert-danger";
                gpsStatusDiv.innerHTML = '<i class="glyphicon glyphicon-alert"></i> GPS retrieval failed. Please allow location access.';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    // Capture photo and submit
    submitClockBtn.addEventListener('click', function() {
        const type = !hasTodayRecord ? 'in' : 'out';
        const needsSelfie = (type === 'in' && selfieRequired) || (type === 'out' && checkoutSelfieRequired);

        if (needsSelfie && stream) {
            const context = photoCanvas.getContext('2d');
            // Mirror image capture to match view
            context.translate(320, 0);
            context.scale(-1, 1);
            context.drawImage(webcamElement, 0, 0, 320, 240);
            
            // Get base64 string
            const dataUrl = photoCanvas.toDataURL('image/png');
            document.getElementById('formSelfieBase64').value = dataUrl;
        }

        // Check if GPS is required but missing
        if (radiusRequired && (!document.getElementById('formLatitude').value || !document.getElementById('formLongitude').value)) {
            errorDiv.textContent = "GPS location coordinates are required. Please wait for coordinates or allow GPS access.";
            errorDiv.style.display = 'block';
            return;
        }

        cameraModal.modal('hide');
        stopWebcam();
        submitWebClock();
    });

    cameraModal.on('hidden.bs.modal', function () {
        stopWebcam();
    });

    function submitWebClock() {
        document.getElementById('webClockForm').submit();
    }
});
</script>
@endsection
