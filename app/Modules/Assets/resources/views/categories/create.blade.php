@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Add New Category</div>
            {!! Form::open(['route' => 'assets.categories.store', 'class' => 'form-horizontal']) !!}
                @include('assets::categories._form', ['submitName' => 'Save Category'])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
