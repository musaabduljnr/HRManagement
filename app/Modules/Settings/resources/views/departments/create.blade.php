@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Create Department</div>
            {!! Form::open(['route' => 'settings.departments.store', 'class' => 'form-horizontal']) !!}
                @include('settings::departments._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
