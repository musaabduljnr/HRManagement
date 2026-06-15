@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default" style="border-radius: 12px; border: 1px solid #e1e1e1; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden;">
            <div class="panel-heading" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 15px 20px; font-weight: bold; border: none;">
                <h4 style="margin: 0; display: flex; align-items: center;">
                    <i class="glyphicon glyphicon-camera" style="margin-right: 10px;"></i> Attendance Scanner Terminal
                </h4>
            </div>
            <div class="panel-body" style="padding: 25px; background-color: #fafbfc;">
                <div style="margin-bottom: 20px;">
                    <label for="camera-select" style="font-weight: 600; color: #555;">Select Camera Device</label>
                    <select id="camera-select" class="form-control" style="border-radius: 8px; border: 1px solid #ccc; height: 40px; box-shadow: none;">
                        <option value="">Detecting cameras...</option>
                    </select>
                </div>

                <!-- Scanner Viewport -->
                <div style="position: relative; border-radius: 12px; overflow: hidden; background: #000; box-shadow: inset 0 0 20px rgba(0,0,0,0.6); margin-bottom: 15px;">
                    <div id="reader" style="width: 100%; border: none;"></div>
                    <!-- Scanning Overlay Line -->
                    <div class="scan-line" style="position: absolute; left: 0; right: 0; height: 3px; background: rgba(0, 255, 0, 0.8); box-shadow: 0 0 10px #0f0; z-index: 10; animation: scan 3s linear infinite;"></div>
                </div>

                <div class="text-center" style="margin-top: 15px;">
                    <button id="toggle-scan-btn" class="btn btn-primary btn-lg" style="border-radius: 8px; font-weight: 600; padding: 10px 25px; text-transform: uppercase; letter-spacing: 0.5px;">
                        Start Camera
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Live Scan Feed -->
        <div class="panel panel-default" style="border-radius: 12px; border: 1px solid #e1e1e1; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; height: 500px; display: flex; flex-direction: column;">
            <div class="panel-heading" style="background: #ffffff; color: #333; padding: 15px 20px; font-weight: bold; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h4 style="margin: 0; font-weight: 700; color: #1e3c72;">
                    <i class="glyphicon glyphicon-transfer" style="margin-right: 10px;"></i> Today's Scans
                </h4>
                <span class="label label-info" id="scan-count" style="font-size: 12px; padding: 4px 8px; border-radius: 10px;">0 scans</span>
            </div>
            <div class="panel-body" style="padding: 0; flex-grow: 1; overflow-y: auto;">
                <div id="recent-scans-container" style="padding: 15px;">
                    <table class="table table-hover" style="margin: 0; vertical-align: middle;">
                        <thead>
                            <tr style="color: #777; font-weight: 600;">
                                <th>Employee</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="scans-list">
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="padding: 50px 0;">
                                    <i class="glyphicon glyphicon-qrcode" style="font-size: 48px; margin-bottom: 15px; color: #ddd; display: block;"></i>
                                    Waiting for first scan...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scan {
    0% { top: 0%; }
    50% { top: 100%; }
    100% { top: 0%; }
}
.scan-success-flash {
    animation: flashGreen 0.5s ease-out;
}
@keyframes flashGreen {
    0% { box-shadow: 0 0 0px #0f0; }
    50% { box-shadow: 0 0 30px #0f0; }
    100% { box-shadow: 0 0 0px #0f0; }
}
.scan-error-flash {
    animation: flashRed 0.5s ease-out;
}
@keyframes flashRed {
    0% { box-shadow: 0 0 0px #f00; }
    50% { box-shadow: 0 0 30px #f00; }
    100% { box-shadow: 0 0 0px #f00; }
}
</style>
@endsection

@section('additionalJS')
<script src="/js/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let html5QrCode = null;
    let cameraList = [];
    let isScanning = false;
    const select = document.getElementById('camera-select');
    const toggleBtn = document.getElementById('toggle-scan-btn');
    const readerDiv = document.getElementById('reader');

    // Load recent scans
    fetchRecentScans();

    // Sound effects using Web Audio API
    function playSound(type) {
        try {
            const context = new (window.AudioContext || window.webkitAudioContext)();
            const osc = context.createOscillator();
            const gain = context.createGain();
            osc.connect(gain);
            gain.connect(context.destination);

            if (type === 'success') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, context.currentTime); // D5
                osc.frequency.setValueAtTime(880.00, context.currentTime + 0.1); // A5
                gain.gain.setValueAtTime(0.08, context.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, context.currentTime + 0.25);
                osc.start();
                osc.stop(context.currentTime + 0.25);
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(150.00, context.currentTime);
                gain.gain.setValueAtTime(0.12, context.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, context.currentTime + 0.45);
                osc.start();
                osc.stop(context.currentTime + 0.45);
            }
        } catch(e) {
            console.error("Audio error:", e);
        }
    }

    // Get Cameras
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            cameraList = devices;
            select.innerHTML = '';
            devices.forEach((device, index) => {
                const option = document.createElement('option');
                option.value = device.id;
                option.text = device.label || `Camera ${index + 1}`;
                select.appendChild(option);
            });
        } else {
            select.innerHTML = '<option value="">No cameras found</option>';
        }
    }).catch(err => {
        console.error("Camera detect error:", err);
        select.innerHTML = '<option value="">Permission or load error</option>';
    });

    toggleBtn.addEventListener('click', function() {
        if (isScanning) {
            stopScanning();
        } else {
            startScanning();
        }
    });

    function startScanning() {
        const cameraId = select.value;
        if (!cameraId) {
            alert('Please select a camera device first.');
            return;
        }

        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            cameraId, 
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            qrCodeMessage => {
                // Throttle scans
                stopScanning();
                processScan(qrCodeMessage);
            },
            errorMessage => {
                // Verbose log suppressed
            }
        ).then(() => {
            isScanning = true;
            toggleBtn.textContent = 'Stop Camera';
            toggleBtn.className = 'btn btn-danger btn-lg';
            readerDiv.parentElement.classList.remove('scan-success-flash', 'scan-error-flash');
        }).catch(err => {
            alert('Error starting scanner: ' + err);
        });
    }

    function stopScanning() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                isScanning = false;
                toggleBtn.textContent = 'Start Camera';
                toggleBtn.className = 'btn btn-primary btn-lg';
            }).catch(err => console.error("Stop error:", err));
        }
    }

    function processScan(token) {
        fetch("{{ route('attendance.scan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                playSound('success');
                readerDiv.parentElement.classList.add('scan-success-flash');
                showNotification(data.message, 'success');
            } else {
                playSound('error');
                readerDiv.parentElement.classList.add('scan-error-flash');
                showNotification(data.message, 'danger');
            }
            fetchRecentScans();
            // Resume scanning after 2 seconds
            setTimeout(() => {
                readerDiv.parentElement.classList.remove('scan-success-flash', 'scan-error-flash');
                startScanning();
            }, 2500);
        })
        .catch(err => {
            console.error("Scan error:", err);
            playSound('error');
            readerDiv.parentElement.classList.add('scan-error-flash');
            showNotification('Server connection failed.', 'danger');
            setTimeout(() => {
                readerDiv.parentElement.classList.remove('scan-success-flash', 'scan-error-flash');
                startScanning();
            }, 2500);
        });
    }

    function fetchRecentScans() {
        fetch("{{ route('attendance.recent') }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('scan-count').textContent = `${data.length} scans`;
            const list = document.getElementById('scans-list');
            if (data.length === 0) {
                list.innerHTML = `<tr><td colspan="4" class="text-center text-muted" style="padding: 50px 0;"><i class="glyphicon glyphicon-qrcode" style="font-size: 48px; margin-bottom: 15px; color: #ddd; display: block;"></i>Waiting for first scan...</td></tr>`;
                return;
            }

            list.innerHTML = '';
            data.forEach(item => {
                const checkInTime = new Date(item.check_in).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const checkOutTime = item.check_out ? new Date(item.check_out).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '-';
                
                const statusBadge = item.check_out 
                    ? '<span class="label label-default" style="border-radius: 4px; padding: 3px 6px;">Clocked Out</span>' 
                    : '<span class="label label-success" style="border-radius: 4px; padding: 3px 6px; animation: pulse 1.5s infinite;">Active</span>';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="font-weight: 600; color: #333;">${item.user.first_name} ${item.user.last_name}</td>
                    <td style="color: #555;">${checkInTime}</td>
                    <td style="color: #555;">${checkOutTime}</td>
                    <td>${statusBadge}</td>
                `;
                list.appendChild(row);
            });
        });
    }

    function showNotification(msg, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.style.position = 'fixed';
        alertDiv.style.top = '80px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.style.borderRadius = '8px';
        alertDiv.style.boxShadow = '0 5px 15px rgba(0,0,0,0.15)';
        alertDiv.innerHTML = `
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>${type === 'success' ? 'SUCCESS' : 'ALERT'}!</strong> ${msg}
        `;
        document.body.appendChild(alertDiv);
        setTimeout(() => {
            $(alertDiv).fadeOut('slow', () => alertDiv.remove());
        }, 4000);
    }
});
</script>
<style>
@keyframes pulse {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}
</style>
@endsection
