@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('attendance_rules.create') }}" class="btn btn-primary pull-right" style="margin-bottom: 15px;">
            <i class="fa fa-plus"></i> Add New Rule
        </a>
    </div>
</div>

@if($rules->isEmpty())
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Attendance Rules</div>
            <div style="padding: 40px; text-align: center; color: #888;">
                <i class="fa fa-cog" style="font-size: 48px; margin-bottom: 15px; display: block;"></i>
                <p>No attendance rules have been created yet.</p>
                <a href="{{ route('attendance_rules.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Create First Rule
                </a>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Active Attendance Rules & Shifts</div>
            <table class="table table-bordered table-hover" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Rule Name</th>
                        <th>Shift Type</th>
                        <th>Check-in Window</th>
                        <th>Geofencing</th>
                        <th>Verification</th>
                        <th>Status</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rules as $rule)
                    <tr>
                        <td>{{ $rule->id }}</td>
                        <td>
                            <strong>{{ $rule->rule_name }}</strong>
                            <br>
                            <small class="text-muted">Applies to: {{ ucfirst(str_replace('_', ' ', $rule->applies_to)) }}</small>
                        </td>
                        <td>{{ $rule->shift_name }}</td>
                        <td>
                            <code>{{ $rule->check_in_start_time }}</code> to <code>{{ $rule->check_in_cutoff_time }}</code>
                            @if($rule->grace_period_minutes > 0)
                                <span class="label label-warning" style="margin-left:5px;">+{{ $rule->grace_period_minutes }}m grace</span>
                            @endif
                        </td>
                        <td>
                            @if($rule->office_latitude && $rule->office_longitude)
                                <span class="label label-info">Enabled ({{ $rule->allowed_radius_meters }}m)</span>
                            @else
                                <span class="label label-default">Disabled</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 3px; flex-wrap: wrap;">
                                @if($rule->selfie_required) <span class="label label-success">Selfie (In)</span> @endif
                                @if($rule->checkout_selfie_required) <span class="label label-success">Selfie (Out)</span> @endif
                                @if($rule->device_lock_required) <span class="label label-primary">Device Lock</span> @endif
                            </div>
                        </td>
                        <td>
                            @if($rule->status === 'active')
                                <span class="label label-success">Active</span>
                            @else
                                <span class="label label-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('attendance_rules.edit', $rule->id) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('attendance_rules.destroy', $rule->id) }}" style="display:inline;"
                                  onsubmit="return confirm('Delete attendance rule \'{{ addslashes($rule->rule_name) }}\'? This cannot be undone.');">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-xs btn-danger">
                                    <i class="fa fa-trash-o"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
