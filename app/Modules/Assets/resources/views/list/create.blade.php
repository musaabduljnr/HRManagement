@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Add New Asset</div>
            {!! Form::open(['route' => 'assets.list.store', 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
                @include('assets::list._form', ['submitName' => 'Save Asset'])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
