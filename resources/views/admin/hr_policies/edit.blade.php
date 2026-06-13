@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Policy: {{ $policy->title }}</div>
            {!! Form::model($policy, ['method' => 'PUT', 'route' => ['hr_policies.update', $policy->id], 'class' => 'form-horizontal']) !!}
                @include('admin.hr_policies._form', ['submitName' => trans('app.submit')])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
