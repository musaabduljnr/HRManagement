<div class="row">
    <div class="col-md-6">
        <h4 style="margin-top:0; border-bottom:1px solid #ddd; padding-bottom:8px;">General Information</h4>
        
        <div class="form-group">
            {!! Form::label('rule_name', 'Rule Name:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::text('rule_name', null, ['class' => 'form-control', 'placeholder' => 'e.g. Standard Office Rule', 'required']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('shift_name', 'Shift Name / Label:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::text('shift_name', null, ['class' => 'form-control', 'placeholder' => 'e.g. Morning (08:00 - 17:00)', 'required']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('applies_to', 'Target Audience:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::select('applies_to', [
                    'all_employees' => 'Entire Company',
                    'department' => 'Specific Department',
                    'selected_employees' => 'Specific Employees'
                ], null, ['class' => 'form-control', 'id' => 'appliesToSelect', 'required']) !!}
            </div>
        </div>

        <div class="form-group" id="departmentSelectGroup" style="display:none;">
            {!! Form::label('department_id', 'Select Department:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::select('department_id', $departments, null, ['class' => 'form-control', 'placeholder' => '-- Select Department --']) !!}
            </div>
        </div>

        <div class="form-group" id="employeeSelectGroup" style="display:none;">
            {!! Form::label('employee_ids[]', 'Select Employees:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::select('employee_ids[]', $employees, null, ['class' => 'form-control', 'multiple' => true, 'style' => 'height: 120px;']) !!}
                <small class="text-muted">Hold Ctrl to select multiple employees.</small>
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('working_days[]', 'Working Days:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                @foreach($workingDaysList as $day)
                    <label style="margin-right:15px; font-weight:normal; cursor:pointer;">
                        <input type="checkbox" name="working_days[]" value="{{ $day }}" 
                            {{ (isset($rule) && is_array($rule->working_days) && in_array($day, $rule->working_days)) || (!isset($rule) && in_array($day, ['Monday','Tuesday','Wednesday','Thursday','Friday'])) ? 'checked' : '' }}>
                        {{ $day }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('status', 'Status:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::select('status', ['active' => 'Active', 'inactive' => 'Inactive'], null, ['class' => 'form-control', 'required']) !!}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <h4 style="margin-top:0; border-bottom:1px solid #ddd; padding-bottom:8px;">Shift Timing Rules</h4>
        
        <div class="form-group">
            {!! Form::label('check_in_start_time', 'Clock-in Open Time:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::input('time', 'check_in_start_time', null, ['class' => 'form-control', 'step' => '1', 'required']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('grace_period_minutes', 'Grace Period (mins):', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::number('grace_period_minutes', null, ['class' => 'form-control', 'min' => '0', 'placeholder' => 'e.g. 15']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('check_in_cutoff_time', 'Clock-in Cutoff Time:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::input('time', 'check_in_cutoff_time', null, ['class' => 'form-control', 'step' => '1', 'required']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('check_out_enabled', 'Enable Clock-out:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8" style="padding-top:6px;">
                <input type="checkbox" name="check_out_enabled" id="checkOutEnabled" value="1" {{ !isset($rule) || $rule->check_out_enabled ? 'checked' : '' }}>
            </div>
        </div>

        <div class="checkout-fields">
            <div class="form-group">
                {!! Form::label('check_out_start_time', 'Clock-out Open Time:', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::input('time', 'check_out_start_time', null, ['class' => 'form-control', 'step' => '1']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('check_out_cutoff_time', 'Clock-out Cutoff Time:', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::input('time', 'check_out_cutoff_time', null, ['class' => 'form-control', 'step' => '1']) !!}
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('minimum_work_duration_minutes', 'Min Work Duration (mins):', ['class' => 'col-sm-4 control-label']) !!}
                <div class="col-sm-8">
                    {!! Form::number('minimum_work_duration_minutes', null, ['class' => 'form-control', 'min' => '0', 'placeholder' => 'e.g. 480']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="margin-top:20px;">
    <div class="col-md-6">
        <h4 style="margin-top:0; border-bottom:1px solid #ddd; padding-bottom:8px;">GPS & Geofencing</h4>
        
        <div class="form-group">
            {!! Form::label('office_latitude', 'Office Latitude:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::text('office_latitude', null, ['class' => 'form-control', 'placeholder' => 'e.g. 6.5244']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('office_longitude', 'Office Longitude:', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::text('office_longitude', null, ['class' => 'form-control', 'placeholder' => 'e.g. 3.3792']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('allowed_radius_meters', 'Allowed Radius (meters):', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-8">
                {!! Form::number('allowed_radius_meters', null, ['class' => 'form-control', 'min' => '0', 'placeholder' => 'e.g. 50']) !!}
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <h4 style="margin-top:0; border-bottom:1px solid #ddd; padding-bottom:8px;">Security & Automation</h4>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <label style="font-weight:normal; cursor:pointer;">
                    <input type="checkbox" name="selfie_required" value="1" {{ isset($rule) && $rule->selfie_required ? 'checked' : '' }}>
                    Require Selfie on Clock-in
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <label style="font-weight:normal; cursor:pointer;">
                    <input type="checkbox" name="checkout_selfie_required" value="1" {{ isset($rule) && $rule->checkout_selfie_required ? 'checked' : '' }}>
                    Require Selfie on Clock-out
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <label style="font-weight:normal; cursor:pointer;">
                    <input type="checkbox" name="device_lock_required" value="1" {{ isset($rule) && $rule->device_lock_required ? 'checked' : '' }}>
                    Lock to Registered Device
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <label style="font-weight:normal; cursor:pointer;">
                    <input type="checkbox" name="auto_mark_absent" value="1" {{ !isset($rule) || $rule->auto_mark_absent ? 'checked' : '' }}>
                    Auto-mark Missed Check-in as Absent
                </label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <label style="font-weight:normal; cursor:pointer;">
                    <input type="checkbox" name="auto_mark_missed_checkout" value="1" {{ !isset($rule) || $rule->auto_mark_missed_checkout ? 'checked' : '' }}>
                    Auto-mark Missed Check-out
                </label>
            </div>
        </div>
    </div>
</div>

@include('errors._form-errors')
<hr>
<div class="form-group">
    <div class="col-sm-12 text-right">
        <a href="{{ route('attendance_rules.index') }}" class="btn btn-default" style="margin-right:10px;">{{ trans('app.cancel') }}</a>
        {!! Form::submit($submitName, ['class' => 'btn btn-primary']) !!}
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var appliesTo = document.getElementById('appliesToSelect');
        var deptGroup = document.getElementById('departmentSelectGroup');
        var empGroup = document.getElementById('employeeSelectGroup');

        function toggleTargetGroups() {
            var val = appliesTo.value;
            deptGroup.style.display = (val === 'department') ? 'block' : 'none';
            empGroup.style.display = (val === 'selected_employees') ? 'block' : 'none';
        }

        appliesTo.addEventListener('change', toggleTargetGroups);
        toggleTargetGroups(); // run once on load

        var checkOutEnabled = document.getElementById('checkOutEnabled');
        var checkoutFields = document.querySelector('.checkout-fields');

        function toggleCheckoutFields() {
            checkoutFields.style.display = checkOutEnabled.checked ? 'block' : 'none';
        }
        checkOutEnabled.addEventListener('change', toggleCheckoutFields);
        toggleCheckoutFields();
    });
</script>
