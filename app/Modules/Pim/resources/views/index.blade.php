@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-primary" href="{{route('pim.employees.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-users"></i>
            </div>
            <h2>{{trans('app.pim.employees.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-success" href="{{route('pim.candidates.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <h2>{{trans('app.pim.candidates.main')}}</h2>
        </a>
    </div>
</div>
@endsection
@section('additionalCSS')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection