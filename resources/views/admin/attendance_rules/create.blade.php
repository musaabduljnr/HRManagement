@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Create Attendance Rule & Shift</div>
            {!! Form::open(['method' => 'POST', 'route' => ['attendance_rules.store'], 'class' => 'form-horizontal']) !!}
                @include('admin.attendance_rules._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
