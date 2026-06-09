@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Add New Job Opening</div>
            {!! Form::open(['route' => ['recruitment.job-openings.store'], 'class' => 'form-horizontal']) !!}
                @include('recruitment::job_openings._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
