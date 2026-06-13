@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-primary" href="{{route('time.clients.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-handshake-o"></i>
            </div>
            <h2>{{trans('app.time.clients.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-success" href="{{route('time.projects.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-tasks"></i>
            </div>
            <h2>{{trans('app.time.projects.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-warning" href="{{route('time.time_logs.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-clock-o"></i>
            </div>
            <h2>{{trans('app.time.time_logs.main')}}</h2>
        </a>
    </div>
</div>
@endsection