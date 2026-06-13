@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Return Asset: {{ $assignment->asset->asset_name }} ({{ $assignment->asset->asset_code }})</div>
            <div class="panel-body">
                {!! Form::open(['route' => ['assets.assignments.return.process', $assignment->id], 'class' => 'form-horizontal']) !!}
                    @if(Request::has('redirect_to_employee'))
                        <input type="hidden" name="redirect_to_employee" value="1">
                    @endif

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Assigned To:</label>
                        <div class="col-sm-6" style="padding-top: 7px;">
                            <strong>{{ $assignment->employee->first_name }} {{ $assignment->employee->last_name }}</strong>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('actual_return_date', 'Return Date *:', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-6">
                            {!! Form::date('actual_return_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('condition', 'Return Condition *:', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-6">
                            {!! Form::select('condition', $conditions, $assignment->asset->condition, ['class' => 'form-control', 'required' => 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('assignment_notes', 'Return Remarks / Notes:', ['class' => 'col-sm-3']) !!}
                        <div class="col-sm-6">
                            {!! Form::textarea('assignment_notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>

                    @include('errors._form-errors')

                    <hr>
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-3">
                            <a href="{{ route('assets.list.show', $assignment->asset->id) }}" class="btn btn-default">{{ trans('app.cancel') }}</a>
                            {!! Form::submit('Process Return', ['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection
