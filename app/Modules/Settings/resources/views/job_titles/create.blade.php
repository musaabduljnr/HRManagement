@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Create Job Title</div>
            {!! Form::open(['route' => 'settings.job_titles.store', 'class' => 'form-horizontal']) !!}
                @include('settings::job_titles._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
