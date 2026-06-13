@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Attendance Rule: {{ $rule->rule_name }}</div>
            {!! Form::model($rule, ['method' => 'PUT', 'route' => ['attendance_rules.update', $rule->id], 'class' => 'form-horizontal']) !!}
                @include('admin.attendance_rules._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
