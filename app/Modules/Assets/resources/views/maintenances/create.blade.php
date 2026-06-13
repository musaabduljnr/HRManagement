@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Add Maintenance Log Entry</div>
            {!! Form::open(['route' => 'assets.maintenances.store', 'class' => 'form-horizontal']) !!}
                @include('assets::maintenances._form', ['submitName' => 'Save Entry'])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
