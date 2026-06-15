@extends ('layouts.main_employee')

@section('content')
<div class="row" id="dashboard-content">
    <!-- Profile & ID Card Generator -->
    <div class="col-md-6" style="margin-bottom: 20px;">
        <div class="custom-panel" style="height: 100%; margin-bottom: 0;">
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
                        <p><strong>Bank Name:</strong> <span id="display-bank-name" style="font-weight: 500;">{{ $user->bank_name ?: 'Not Provided' }}</span></p>
                        <p><strong>Account Number:</strong> <span id="display-account-number" style="font-weight: 500;">{{ $user->account_number ?: 'Not Provided' }}</span></p>
                        <br>

                        {{-- Photo upload button --}}
                        <input type="file" id="emp-photo-input" accept="image/*" style="display:none;" onchange="handleEmpPhotoChange(this)">
                        <button type="button" class="btn btn-default btn-block" style="margin-bottom:8px;" onclick="document.getElementById('emp-photo-input').click();">
                            <i class="fa fa-camera"></i> Change Photo
                        </button>
                        <button type="button" class="btn btn-default btn-block" style="margin-bottom:8px;" data-toggle="modal" data-target="#bankDetailsModal">
                            <i class="fa fa-university"></i> Edit Bank Details
                        </button>
                        <div id="emp-photo-status" style="font-size:11px; margin-bottom:8px; min-height:16px;"></div>

                        <button type="button" class="btn btn-primary btn-block" onclick="printIDCard()">
                            <i class="fa fa-print"></i> Print Official ID Card
                        </button>
                    </div>

                    <div class="col-sm-6 text-center" style="margin-top: 16px;">
                        <!-- ID Card Widget Preview -->
                        <div id="employee-id-card" style="max-width: 220px; width: 100%; border: 1px solid #ccc; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background-color: white; overflow: hidden; display: inline-block; text-align: center; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                            <!-- Top Header banner -->
                            <div style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 12px 10px; font-weight: bold; font-size: 15px; letter-spacing: 0.5px;">
                                HRM ENTERPRISE
                            </div>

                            <!-- Profile picture — shows photo if uploaded, else icon -->
                            <div style="margin-top: 15px; display: inline-block; position: relative;">
                                <div id="id-card-avatar-wrap" style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid #1e3c72; background-color: #eee; display: flex; align-items: center; justify-content: center; overflow: hidden; margin: 0 auto; cursor: pointer;" onclick="document.getElementById('emp-photo-input').click();" title="Click to change photo">
                                    @if($user->profile_photo)
                                        <img id="id-card-photo" src="{{ $user->profile_photo_url }}" alt="Profile" style="width:100%; height:100%; object-fit:cover;">
                                    @else
                                        <i id="id-card-placeholder-icon" class="fa fa-user" style="color: #ccc; font-size: 40px;"></i>
                                    @endif
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
    <div class="col-md-6" style="margin-bottom: 20px;">
        <!-- Time clock widget -->
        <div class="custom-panel" style="margin-bottom: 20px;">
            <div class="custom-panel-heading"><i class="fa fa-clock-o"></i> Attendance Clock</div>
            <div class="panel-body text-center" style="padding: 20px; overflow: hidden;">
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
                        <button type="submit" class="btn btn-success btn-block" style="border-radius: 20px; max-width: 250px; margin: 0 auto; display: flex; justify-content: center;">
                            <i class="fa fa-sign-in"></i> Clock In Today
                        </button>
                    @elseif(!$todayRecord->check_out)
                        <button type="submit" class="btn btn-danger btn-block" style="border-radius: 20px; max-width: 250px; margin: 0 auto; display: flex; justify-content: center;">
                            <i class="fa fa-sign-out"></i> Clock Out Today
                        </button>
                    @else
                        <button type="button" class="btn btn-default btn-block disabled" style="border-radius: 20px; max-width: 250px; margin: 0 auto;">
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
                            <h3 style="color: #2e6da4; font-weight: bold; margin-top: 0;">₦{{ number_format($latestPayslip->net_salary, 2) }}</h3>
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

        <!-- Latest Messages from HR -->
        <div class="custom-panel" style="margin-top: 20px;">
            <div class="custom-panel-heading"><i class="fa fa-envelope-o"></i> Recent Messages from HR</div>
            <div class="panel-body" style="padding: 0;">
                <?php
                    $latestHrMessages = \App\Modules\Chat\Models\Conversation::with(['hrManager', 'creator'])
                        ->where('employee_id', Auth::id())
                        ->orderBy('last_message_at', 'desc')
                        ->take(3)
                        ->get();
                ?>
                <ul class="list-group" style="margin-bottom: 0;">
                    @forelse($latestHrMessages as $chat)
                        <?php 
                            $hrSender = $chat->hrManager ?: $chat->creator;
                            $unread = $chat->unreadMessagesCount();
                        ?>
                        <li class="list-group-item" style="border-left: none; border-right: none; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <a href="{{ route('employee.chat.index', ['conversation_id' => $chat->id]) }}" style="font-weight: bold; text-decoration: none; color: #1e3c72;">
                                    HR: {{ $hrSender ? $hrSender->first_name . ' ' . $hrSender->last_name : 'Admin' }}
                                </a>
                                <div style="font-size: 12px; color: #555; margin-top: 2px;">
                                    Subject: <strong>{{ $chat->subject }}</strong>
                                </div>
                                <div style="font-size: 11px; color: #777; margin-top: 2px;">
                                    Last message &bull; {{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : '' }}
                                </div>
                            </div>
                            @if($unread > 0)
                                <span class="badge" style="background-color: #27ae60; color: white;">{{ $unread }}</span>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted" style="padding: 30px 10px;">No message threads from HR.</li>
                    @endforelse
                </ul>
                <div class="panel-footer text-center" style="background: white; border-top: 1px solid #eee;">
                    <a href="{{ route('employee.chat.index') }}" class="btn btn-default btn-xs btn-block">Open HR Inbox</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bank Details Modal -->
<div id="bankDetailsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bank-details-form" action="{{ route('employee.profile.bank_details') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; border-top-left-radius: 4px; border-top-right-radius: 4px;">
                    <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">&times;</button>
                    <h4 class="modal-title" style="font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fa fa-university"></i> Update Bank Details</h4>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    <div id="bank-form-status"></div>
                    <div class="form-group" style="margin-bottom: 18px;">
                        <label for="bank_name" style="font-weight: 600; color: #475569; margin-bottom: 6px;">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ $user->bank_name }}" required placeholder="e.g. Zenith Bank" style="border-radius: 6px; padding: 10px; border-color: #cbd5e1;">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="account_number" style="font-weight: 600; color: #475569; margin-bottom: 6px;">Account Number</label>
                        <input type="text" name="account_number" id="account_number" class="form-control" value="{{ $user->account_number }}" required placeholder="e.g. 1029384756" style="border-radius: 6px; padding: 10px; border-color: #cbd5e1;">
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="btn btn-primary" id="btn-save-bank" style="background: #1e3c72; border: none; padding: 8px 16px; font-weight: 600; border-radius: 6px; transition: all 0.2s;">Save Details</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; padding: 8px 16px;">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden iframe used as isolated print target -->
<iframe id="print-frame" style="display:none; position:fixed; left:-9999px; top:-9999px; width:0; height:0; border:none;" title="ID Card Print"></iframe>

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

// 3. Employee photo upload handler
var EMP_UPLOAD_URL = '{{ route("employee.profile.photo.upload") }}';
var EMP_CSRF = '{{ csrf_token() }}';

// Track current photo as base64 for printing
var currentPhotoBase64 = @if($user->profile_photo) '{{ $user->profile_photo_url }}' @else null @endif;

function handleEmpPhotoChange(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];

    if (file.size > 3 * 1024 * 1024) {
        showEmpStatus('<i class="fa fa-exclamation-circle"></i> File too large. Max 3 MB.', '#ef4444');
        return;
    }

    // Read for immediate local preview
    var reader = new FileReader();
    reader.onload = function(e) {
        updateIdCardPhoto(e.target.result);
        currentPhotoBase64 = e.target.result;
    };
    reader.readAsDataURL(file);

    // Upload to server
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
            } else {
                showEmpStatus('Upload failed. Try again.', '#ef4444');
            }
        })
        .catch(function() { showEmpStatus('Network error.', '#ef4444'); });
}

function updateIdCardPhoto(src) {
    var wrap = document.getElementById('id-card-avatar-wrap');
    if (!wrap) return;
    // Remove placeholder icon if present
    var icon = document.getElementById('id-card-placeholder-icon');
    if (icon) icon.style.display = 'none';
    // Update or create img
    var img = document.getElementById('id-card-photo');
    if (!img) {
        img = document.createElement('img');
        img.id = 'id-card-photo';
        img.alt = 'Profile';
        img.style.cssText = 'width:100%; height:100%; object-fit:cover;';
        wrap.appendChild(img);
    }
    img.src = src;
}

function showEmpStatus(msg, color) {
    var el = document.getElementById('emp-photo-status');
    if (el) { el.innerHTML = msg; el.style.color = color; }
}

// 4. Print ID Card action — uses an isolated iframe so only the card prints
function printIDCard() {
    // Convert QR canvas → base64 PNG before printing
    var canvas  = document.getElementById('id-card-qr-canvas');
    var qrImage = canvas ? canvas.toDataURL('image/png') : '';

    // Capture text from the live card's DOM
    var card    = document.getElementById('employee-id-card');
    var nameEl  = card.querySelector('h4');
    var roleEl  = card.querySelectorAll('p')[0];
    var deptEl  = card.querySelectorAll('p')[1];
    var idEl    = card.querySelector('strong');

    var empName = nameEl ? nameEl.textContent.trim() : '';
    var empRole = roleEl ? roleEl.textContent.trim() : '';
    var empDept = deptEl ? deptEl.textContent.trim() : '';
    var empId   = idEl   ? idEl.textContent.trim()   : '';

    // Decide avatar HTML
    var avatarHTML;
    if (currentPhotoBase64) {
        avatarHTML = '<img src="' + currentPhotoBase64 + '" alt="Photo" style="width:100%; height:100%; object-fit:cover;">';
    } else {
        avatarHTML = '<svg viewBox="0 0 24 24" fill="#ccc" xmlns="http://www.w3.org/2000/svg" style="width:50px;height:50px;">' +
            '<path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>' +
            '</svg>';
    }

    var printHTML = '<!DOCTYPE html><html><head><meta charset="utf-8">' +
        '<title>Employee ID Card</title>' +
        '<style>' +
        '  * { box-sizing: border-box; margin: 0; padding: 0; }' +
        '  @page { size: A6 portrait; margin: 0; }' +
        '  html, body { width:105mm; height:148mm; display:flex; align-items:center; justify-content:center; background:#fff; -webkit-print-color-adjust:exact; print-color-adjust:exact; color-adjust:exact; }' +
        '  .id-card { width:80mm; background:#fff; border:1px solid #ccc; border-radius:10px; overflow:hidden; font-family:"Helvetica Neue",Helvetica,Arial,sans-serif; box-shadow:0 2px 8px rgba(0,0,0,0.15); -webkit-print-color-adjust:exact; print-color-adjust:exact; color-adjust:exact; }' +
        '  .card-header { background:linear-gradient(135deg,#1e3c72 0%,#2a5298 100%) !important; -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; color:#fff !important; padding:14px 10px; text-align:center; font-weight:bold; font-size:15px; letter-spacing:0.5px; }' +
        '  .avatar-wrap { margin:14px auto 0; width:82px; height:82px; border-radius:50%; border:3px solid #1e3c72; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#eee; }' +
        '  .name-block { text-align:center; padding:10px 10px 0; }' +
        '  .name-block h4 { font-size:16px; font-weight:700; color:#333; margin-bottom:3px; }' +
        '  .name-block .role { font-size:11px; color:#666; text-transform:uppercase; margin-bottom:2px; }' +
        '  .name-block .dept { font-size:10px; color:#888; }' +
        '  .qr-block { text-align:center; padding:12px 10px 10px; }' +
        '  .qr-block img { width:100px; height:100px; border:1px solid #eee; border-radius:4px; padding:3px; }' +
        '  .card-footer { background:#f5f5f5 !important; -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; border-top:1px solid #e5e5e5; padding:8px 10px; text-align:center; }' +
        '  .card-footer .emp-id { font-size:11px; color:#333; font-weight:bold; }' +
        '  .card-footer .restricted { font-size:9px; color:#c0392b; font-weight:bold; letter-spacing:0.5px; margin-top:3px; }' +
        '</style></head><body><div class="id-card">';

    printHTML += '<div class="card-header">HRM ENTERPRISE</div>';
    printHTML += '<div style="text-align:center"><div class="avatar-wrap">' + avatarHTML + '</div></div>';
    printHTML += '<div class="name-block"><h4>' + empName + '</h4>' +
        '<div class="role">' + empRole + '</div>' +
        '<div class="dept">' + empDept + '</div></div>';
    printHTML += qrImage
        ? '<div class="qr-block"><img src="' + qrImage + '" alt="QR Code"></div>'
        : '<div class="qr-block" style="padding:20px;color:#999;font-size:11px;">QR Code not available</div>';
    printHTML += '<div class="card-footer"><div class="emp-id">' + empId + '</div>' +
        '<div class="restricted">AUTHORIZED ACCESS ONLY</div></div>';
    printHTML += '</div></body></html>';

    var iframe = document.getElementById('print-frame');
    iframe.style.display = 'block';
    iframe.style.width  = '210mm';
    iframe.style.height = '297mm';
    var iDoc = iframe.contentWindow.document;
    iDoc.open();
    iDoc.write(printHTML);
    iDoc.close();

    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        setTimeout(function() {
            iframe.style.display = 'none';
            iframe.style.width  = '0';
            iframe.style.height = '0';
        }, 2000);
    }, 500);
}

// 5. Bank Details AJAX Form Submission
$(document).ready(function() {
    $('#bank-details-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();
        var btn = $('#btn-save-bank');
        var statusDiv = $('#bank-form-status');
        
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        statusDiv.html('');
        
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false).html('Save Details');
                if (response.success) {
                    var bankName = $('#bank_name').val();
                    var accNum = $('#account_number').val();
                    $('#display-bank-name').text(bankName);
                    $('#display-account-number').text(accNum);
                    statusDiv.html('<div class="alert alert-success" style="padding: 10px; border-radius: 6px;">Bank details updated successfully!</div>');
                    setTimeout(function() {
                        $('#bankDetailsModal').modal('hide');
                        statusDiv.html('');
                    }, 1500);
                } else {
                    statusDiv.html('<div class="alert alert-danger" style="padding: 10px; border-radius: 6px;">An error occurred. Please try again.</div>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('Save Details');
                var errors = xhr.responseJSON;
                var errorMsg = 'Failed to save details.';
                if (errors && errors.bank_name) {
                    errorMsg = errors.bank_name[0];
                } else if (errors && errors.account_number) {
                    errorMsg = errors.account_number[0];
                }
                statusDiv.html('<div class="alert alert-danger" style="padding: 10px; border-radius: 6px;">' + errorMsg + '</div>');
            }
        });
    });
});
</script>
@endsection

