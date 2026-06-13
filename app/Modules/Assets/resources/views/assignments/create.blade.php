@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Assign Asset</div>
            {!! Form::open(['route' => 'assets.assignments.store', 'class' => 'form-horizontal']) !!}
                @include('assets::assignments._form', ['submitName' => 'Assign Asset'])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
