@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a href="{{route('recruitment.job-openings.index')}}" class="nav-box">
            <h2>Job Openings</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a href="{{route('pim.candidates.index')}}" class="nav-box">
            <h2>Candidates</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a href="{{route('recruitment.interviews.index')}}" class="nav-box">
            <h2>Interviews</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a href="{{route('recruitment.reports.index')}}" class="nav-box">
            <h2>{{trans('app.recruitment.reports.main')}}</h2>
        </a>
    </div>
</div>
@endsection