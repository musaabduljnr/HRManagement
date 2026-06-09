@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Job Title: {{ $jobTitle->name }}</div>
            {!! Form::model($jobTitle, ['method' => 'PUT', 'route' => ['settings.job_titles.update', $jobTitle->id], 'class' => 'form-horizontal']) !!}
                @include('settings::job_titles._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
