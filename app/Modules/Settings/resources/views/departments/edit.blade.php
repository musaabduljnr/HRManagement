@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Department: {{ $department->name }}</div>
            {!! Form::model($department, ['method' => 'PUT', 'route' => ['settings.departments.update', $department->id], 'class' => 'form-horizontal']) !!}
                @include('settings::departments._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
