<div class="form-group{{ $errors->has('asset_id') ? ' has-error' : '' }}">
    {!! Form::label('asset_id', 'Asset *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        @if(isset($assignment))
            {!! Form::select('asset_id', $assets, null, ['class' => 'form-control', 'required' => 'required', 'disabled' => 'disabled']) !!}
            {!! Form::hidden('asset_id', $assignment->asset_id) !!}
        @else
            {!! Form::select('asset_id', $assets, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => '-- Select Available Asset --']) !!}
        @endif
        @if ($errors->has('asset_id'))
            <span class="help-block">{{ $errors->first('asset_id') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
    {!! Form::label('employee_id', 'Employee *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::select('employee_id', $employees, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => '-- Select Employee --']) !!}
        @if ($errors->has('employee_id'))
            <span class="help-block">{{ $errors->first('employee_id') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('issue_date') ? ' has-error' : '' }}">
    {!! Form::label('issue_date', 'Issue Date *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::date('issue_date', isset($assignment) && $assignment->issue_date ? (is_string($assignment->issue_date) ? $assignment->issue_date : $assignment->issue_date->format('Y-m-d')) : \Carbon\Carbon::today()->format('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) !!}
        @if ($errors->has('issue_date'))
            <span class="help-block">{{ $errors->first('issue_date') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('expected_return_date') ? ' has-error' : '' }}">
    {!! Form::label('expected_return_date', 'Expected Return Date', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::date('expected_return_date', isset($assignment) && $assignment->expected_return_date ? (is_string($assignment->expected_return_date) ? $assignment->expected_return_date : $assignment->expected_return_date->format('Y-m-d')) : null, ['class' => 'form-control']) !!}
        @if ($errors->has('expected_return_date'))
            <span class="help-block">{{ $errors->first('expected_return_date') }}</span>
        @endif
    </div>
</div>

@if(isset($assignment))
<div class="form-group{{ $errors->has('actual_return_date') ? ' has-error' : '' }}">
    {!! Form::label('actual_return_date', 'Actual Return Date', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::date('actual_return_date', $assignment->actual_return_date ? (is_string($assignment->actual_return_date) ? $assignment->actual_return_date : $assignment->actual_return_date->format('Y-m-d')) : null, ['class' => 'form-control']) !!}
        @if ($errors->has('actual_return_date'))
            <span class="help-block">{{ $errors->first('actual_return_date') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('received_by') ? ' has-error' : '' }}">
    {!! Form::label('received_by', 'Received By', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::text('received_by', null, ['class' => 'form-control']) !!}
        @if ($errors->has('received_by'))
            <span class="help-block">{{ $errors->first('received_by') }}</span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    {!! Form::label('status', 'Assignment Status *', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::select('status', [
            'Active' => 'Active',
            'Returned' => 'Returned',
            'Replaced' => 'Replaced',
            'Lost' => 'Lost'
        ], null, ['class' => 'form-control', 'required' => 'required']) !!}
        @if ($errors->has('status'))
            <span class="help-block">{{ $errors->first('status') }}</span>
        @endif
    </div>
</div>
@else
    {!! Form::hidden('status', 'Active') !!}
@endif

<div class="form-group{{ $errors->has('assignment_notes') ? ' has-error' : '' }}">
    {!! Form::label('assignment_notes', 'Assignment Notes', ['class' => 'col-sm-3 control-label']) !!}
    <div class="col-sm-9">
        {!! Form::textarea('assignment_notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
        @if ($errors->has('assignment_notes'))
            <span class="help-block">{{ $errors->first('assignment_notes') }}</span>
        @endif
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-3 col-sm-9">
        {!! Form::submit($submitName, ['class' => 'btn btn-primary']) !!}
        <a href="{{ route('assets.assignments.index') }}" class="btn btn-default">Cancel</a>
    </div>
</div>
