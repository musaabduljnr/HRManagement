@extends('layouts.main')
@section('content')

{{-- Page Header --}}
<div class="page-header-bar">
    <div>
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Update your personal information and profile photo.</p>
    </div>
</div>

<div class="row">
    {{-- Profile Photo Card --}}
    <div class="col-md-4" style="margin-bottom: 20px;">
        <div class="panel">
            <div class="panel-heading"><i class="fa fa-camera"></i> Profile Photo</div>
            <div class="panel-body text-center" style="padding: 30px 20px;">

                {{-- Avatar preview --}}
                <div id="avatar-preview-wrap" style="
                    width: 120px; height: 120px; border-radius: 50%;
                    margin: 0 auto 20px; overflow: hidden;
                    border: 3px solid var(--primary);
                    background: var(--primary-light);
                    display: flex; align-items: center; justify-content: center;
                    position: relative; cursor: pointer;
                " onclick="document.getElementById('photo-file-input').click();" title="Click to change photo">

                    @if($user->profile_photo)
                        <img id="avatar-img" src="{{ $user->profile_photo_url }}"
                             alt="Profile Photo"
                             style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <div id="avatar-initials" style="font-size:40px; font-weight:700; color: var(--primary);">
                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                        </div>
                    @endif

                    {{-- Hover overlay --}}
                    <div style="
                        position:absolute; inset:0; background:rgba(79,70,229,0.6);
                        display:flex; align-items:center; justify-content:center;
                        opacity:0; transition:opacity 0.2s; border-radius:50%;
                        color:white; font-size:22px;
                    " id="avatar-overlay">
                        <i class="fa fa-camera"></i>
                    </div>
                </div>

                <p style="font-size:12px; color:var(--text-muted); margin-bottom:16px;">
                    JPG, PNG, GIF or WebP · Max 3 MB
                </p>

                {{-- Hidden file input --}}
                <input type="file" id="photo-file-input" accept="image/*" style="display:none;" onchange="handlePhotoChange(this)">

                <button type="button" class="btn btn-primary btn-block" onclick="document.getElementById('photo-file-input').click();">
                    <i class="fa fa-upload"></i> Upload Photo
                </button>

                @if($user->profile_photo)
                <button type="button" class="btn btn-default btn-block" style="margin-top:8px;" onclick="removePhoto()">
                    <i class="fa fa-trash"></i> Remove Photo
                </button>
                @else
                <button type="button" id="btn-remove-photo" class="btn btn-default btn-block" style="margin-top:8px; display:none;" onclick="removePhoto()">
                    <i class="fa fa-trash"></i> Remove Photo
                </button>
                @endif

                <div id="photo-status" style="margin-top:12px; font-size:12px;"></div>
            </div>
        </div>
    </div>

    {{-- Profile Details Card --}}
    <div class="col-md-8" style="margin-bottom: 20px;">
        <div class="panel">
            <div class="panel-heading"><i class="fa fa-user"></i> {{ trans('app.profile.update') }}</div>
            <div class="panel-body">
                {!! Form::model($user, ['route' => ['profile.store'], 'class' => 'form-horizontal']) !!}
                    <div class="form-group">
                        {!! Form::label('first_name', trans('app.pim.employees.first_name').':', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('last_name', trans('app.pim.employees.last_name').':', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-6">
                            {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('email', trans('app.pim.employees.email').':', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-6">
                            {!! Form::input('email', 'email', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('gender', trans('app.pim.employees.gender').':', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-6">
                            {!! Form::label('male', trans('app.pim.employees.gender_male')) !!}
                            {!! Form::radio('gender', 'm', @$employee->gender == 'm', ['id' => 'male']) !!}
                            {!! Form::label('female', trans('app.pim.employees.gender_female')) !!}
                            {!! Form::radio('gender', 'f', @$employee->gender == 'f', ['id' => 'female']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('birth_date', trans('app.pim.employees.birth_date').':', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-6">
                            {!! Form::input('date', 'birth_date', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    @include('errors._form-errors')
                    <hr>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-3">
                            {!! Form::submit(trans('app.submit'), ['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@endsection

@section('additionalJS')
<script>
var CSRF_TOKEN = '{{ csrf_token() }}';
var UPLOAD_URL = '{{ route("profile.photo.upload") }}';
var REMOVE_URL = '{{ route("profile.photo.remove") }}';

// Show camera icon on hover
var wrap = document.getElementById('avatar-preview-wrap');
var overlay = document.getElementById('avatar-overlay');
if (wrap && overlay) {
    wrap.addEventListener('mouseenter', function() { overlay.style.opacity = '1'; });
    wrap.addEventListener('mouseleave', function() { overlay.style.opacity = '0'; });
}

function handlePhotoChange(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];

    // Client-side size check (3 MB)
    if (file.size > 3 * 1024 * 1024) {
        showStatus('File is too large. Max 3 MB.', 'error');
        return;
    }

    // Preview immediately
    var reader = new FileReader();
    reader.onload = function(e) {
        updateAvatarPreview(e.target.result);
    };
    reader.readAsDataURL(file);

    // Upload
    var formData = new FormData();
    formData.append('profile_photo', file);
    formData.append('_token', CSRF_TOKEN);

    showStatus('<i class="fa fa-spinner fa-spin"></i> Uploading…', 'info');

    fetch(UPLOAD_URL, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                showStatus('<i class="fa fa-check"></i> ' + data.message, 'success');
                document.getElementById('btn-remove-photo') && (document.getElementById('btn-remove-photo').style.display = 'block');
            } else {
                showStatus('Upload failed. Please try again.', 'error');
            }
        })
        .catch(function() {
            showStatus('Network error. Please try again.', 'error');
        });
}

function updateAvatarPreview(src) {
    var wrap = document.getElementById('avatar-preview-wrap');
    // Remove initials div if present
    var initials = document.getElementById('avatar-initials');
    if (initials) initials.style.display = 'none';
    // Update or create img
    var img = document.getElementById('avatar-img');
    if (!img) {
        img = document.createElement('img');
        img.id = 'avatar-img';
        img.style.cssText = 'width:100%; height:100%; object-fit:cover; position:absolute; inset:0;';
        var overlay = document.getElementById('avatar-overlay');
        wrap.insertBefore(img, overlay);
    }
    img.src = src;
}

function removePhoto() {
    if (!confirm('Remove your profile photo?')) return;
    showStatus('<i class="fa fa-spinner fa-spin"></i> Removing…', 'info');

    fetch(REMOVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({})
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var img = document.getElementById('avatar-img');
            if (img) img.remove();
            var initials = document.getElementById('avatar-initials');
            if (initials) initials.style.display = 'flex';
            var btnRemove = document.getElementById('btn-remove-photo');
            if (btnRemove) btnRemove.style.display = 'none';
            showStatus('Photo removed.', 'success');
        }
    });
}

function showStatus(msg, type) {
    var el = document.getElementById('photo-status');
    var colors = { success: 'var(--success)', error: 'var(--danger)', info: 'var(--text-muted)' };
    el.innerHTML = msg;
    el.style.color = colors[type] || 'inherit';
}
</script>
@endsection