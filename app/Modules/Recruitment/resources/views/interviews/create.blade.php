@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Schedule Interview</div>
            {!! Form::open(['route' => ['recruitment.interviews.store'], 'class' => 'form-horizontal']) !!}
                @include('recruitment::interviews._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
