@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Interview</div>
            {!! Form::model($interview, ['method' => 'PUT', 'route' => ['recruitment.interviews.update', $interview->id], 'class' => 'form-horizontal']) !!}
                @include('recruitment::interviews._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
