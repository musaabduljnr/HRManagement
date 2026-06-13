@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Category: {{ $category->name }}</div>
            {!! Form::model($category, ['method' => 'PUT', 'route' => ['assets.categories.update', $category->id], 'class' => 'form-horizontal']) !!}
                @include('assets::categories._form', ['submitName' => 'Update Category'])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
