@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a href="{{route('recruitment.job-openings.index')}}" class="nav-box card-primary">
            <div class="nav-card-icon">
                <i class="fa fa-briefcase"></i>
            </div>
            <h2>Job Openings</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a href="{{route('pim.candidates.index')}}" class="nav-box card-success">
            <div class="nav-card-icon">
                <i class="fa fa-graduation-cap"></i>
            </div>
            <h2>Candidates</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a href="{{route('recruitment.interviews.index')}}" class="nav-box card-warning">
            <div class="nav-card-icon">
                <i class="fa fa-comments"></i>
            </div>
            <h2>Interviews</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a href="{{route('recruitment.reports.index')}}" class="nav-box card-info">
            <div class="nav-card-icon">
                <i class="fa fa-line-chart"></i>
            </div>
            <h2>{{trans('app.recruitment.reports.main')}}</h2>
        </a>
    </div>
</div>
@endsection