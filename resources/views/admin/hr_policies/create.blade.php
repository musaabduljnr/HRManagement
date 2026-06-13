@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Create HR Policy</div>
            {!! Form::open(['method' => 'POST', 'route' => ['hr_policies.store'], 'class' => 'form-horizontal']) !!}
                @include('admin.hr_policies._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
