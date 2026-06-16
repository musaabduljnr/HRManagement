@extends('layouts.main')
@section('content')
<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-primary" href="{{route('settings.companies.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-building"></i>
            </div>
            <h2>{{trans('app.settings.companies.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-success" href="{{route('settings.contract_types.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-file-text-o"></i>
            </div>
            <h2>{{trans('app.settings.contract_types.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-warning" href="{{route('settings.document_templates.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-clipboard"></i>
            </div>
            <h2>{{trans('app.settings.document_templates.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-danger" href="{{route('settings.education_institutions.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-university"></i>
            </div>
            <h2>{{trans('app.settings.education_institutions.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-info" href="{{route('settings.job_positions.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-id-card-o"></i>
            </div>
            <h2>{{trans('app.settings.job_positions.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-primary" href="{{route('settings.languages.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-language"></i>
            </div>
            <h2>{{trans('app.settings.languages.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-success" href="{{route('settings.salary_components.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-money"></i>
            </div>
            <h2>{{trans('app.settings.salary_components.main')}}</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-warning" href="{{route('settings.departments.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-sitemap"></i>
            </div>
            <h2>Departments</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-info" href="{{route('settings.job_titles.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-briefcase"></i>
            </div>
            <h2>Job Titles</h2>
        </a>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="nav-box card-primary" href="{{route('settings.system.index')}}">
            <div class="nav-card-icon">
                <i class="fa fa-cogs"></i>
            </div>
            <h2>System Settings</h2>
        </a>
    </div>
</div>
@endsection