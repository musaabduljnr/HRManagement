@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Assign Asset to: {{ $employee->first_name }} {{ $employee->last_name }}</div>
            {!! Form::open(['route' => ['pim.employees.assets.store', $employee->id], 'class' => 'form-horizontal']) !!}
                
                <div class="form-group{{ $errors->has('asset_id') ? ' has-error' : '' }}">
                    {!! Form::label('asset_id', 'Asset *', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        {!! Form::select('asset_id', $assets, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => '-- Select Available Asset --']) !!}
                        @if ($errors->has('asset_id'))
                            <span class="help-block">{{ $errors->first('asset_id') }}</span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('issue_date') ? ' has-error' : '' }}">
                    {!! Form::label('issue_date', 'Issue Date *', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        {!! Form::date('issue_date', \Carbon\Carbon::today()->format('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) !!}
                        @if ($errors->has('issue_date'))
                            <span class="help-block">{{ $errors->first('issue_date') }}</span>
                        @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('expected_return_date') ? ' has-error' : '' }}">
                    {!! Form::label('expected_return_date', 'Expected Return Date', ['class' => 'col-sm-3 control-label']) !!}
                    <div class="col-sm-9">
                        {!! Form::date('expected_return_date', null, ['class' => 'form-control']) !!}
                        @if ($errors->has('expected_return_date'))
                            <span class="help-block">{{ $errors->first('expected_return_date') }}</span>
                        @endif
                    </div>
                </div>

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
                        {!! Form::submit('Assign Asset', ['class' => 'btn btn-primary']) !!}
                        <a href="{{ route('pim.employees.assets.index', $employee->id) }}" class="btn btn-default">Cancel</a>
                    </div>
                </div>

            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
