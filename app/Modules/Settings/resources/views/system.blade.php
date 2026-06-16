@extends('layouts.main')

@section('content')
<div class="page-header-bar">
    <div>
        <h1 class="page-title">System Settings</h1>
        <p class="page-subtitle">Configure application settings and attendance permissions.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default" style="border-radius: 12px; border: 1px solid #eef0f2; box-shadow: 0 4px 15px rgba(0,0,0,0.03); overflow: hidden;">
            <div class="panel-heading" style="background-color: white; border-bottom: 1px solid #eee; padding: 16px 20px; font-weight: 700; color: #1e3c72; font-size: 15px;">
                <i class="fa fa-cogs"></i> Attendance Configurations
            </div>
            
            {!! Form::open(['route' => 'settings.system.store', 'method' => 'POST']) !!}
            <div class="panel-body" style="padding: 24px;">
                <div class="form-group" style="margin-bottom: 24px;">
                    <div class="checkbox">
                        <label style="font-weight: 600; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            {!! Form::checkbox('manual_clockin_enabled', 1, $manualClockin === 'true', ['id' => 'manual_clockin_enabled']) !!}
                            Enable Manual Clock-In Button
                        </label>
                    </div>
                    <p class="help-block" style="margin-left: 24px; color: var(--text-secondary); font-size: 12.5px; margin-top: 4px;">When enabled, employees will see the "Clock In/Out Today" manual web button on their dashboard. If disabled, the manual button is hidden.</p>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <div class="checkbox">
                        <label style="font-weight: 600; color: var(--text-primary); cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            {!! Form::checkbox('qr_clockin_enabled', 1, $qrClockin === 'true', ['id' => 'qr_clockin_enabled']) !!}
                            Enable QR Code Clock-In
                        </label>
                    </div>
                    <p class="help-block" style="margin-left: 24px; color: var(--text-secondary); font-size: 12.5px; margin-top: 4px;">When enabled, employees can generate QR codes to scan at a kiosk/scanner station for clocking in and out.</p>
                </div>
            </div>
            
            <div class="panel-footer" style="background-color: #f8fafc; border-top: 1px solid var(--card-border); padding: 16px 24px; display: flex; gap: 12px;">
                {!! Form::submit('Save System Settings', ['class' => 'btn btn-primary', 'style' => 'border-radius: var(--radius); font-weight: 600;']) !!}
                <a href="{{ route('settings.index') }}" class="btn btn-default" style="border-radius: var(--radius); font-weight: 600;">Cancel</a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
