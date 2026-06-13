@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Assignment: #{{ $assignment->id }}</div>
            {!! Form::model($assignment, ['method' => 'PUT', 'route' => ['assets.assignments.update', $assignment->id], 'class' => 'form-horizontal']) !!}
                @include('assets::assignments._form', ['submitName' => 'Update Assignment'])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
