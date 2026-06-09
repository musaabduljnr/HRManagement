@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Job Opening</div>
            {!! Form::model($jobOpening, ['method' => 'PUT', 'route' => ['recruitment.job-openings.update', $jobOpening->id], 'class' => 'form-horizontal']) !!}
                @include('recruitment::job_openings._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
