@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Edit Asset: {{ $asset->asset_code }}</div>
            {!! Form::model($asset, ['method' => 'PUT', 'route' => ['assets.list.update', $asset->id], 'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) !!}
                @include('assets::list._form', ['submitName' => 'Update Asset'])
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
