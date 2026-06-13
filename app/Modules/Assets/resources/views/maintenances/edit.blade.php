@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Maintenance Log Entry: #{{ $maintenance->id }}</div>
            {!! Form::model($maintenance, ['method' => 'PUT', 'route' => ['assets.maintenances.update', $maintenance->id], 'class' => 'form-horizontal']) !!}
                @include('assets::maintenances._form', ['submitName' => 'Update Entry'])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
