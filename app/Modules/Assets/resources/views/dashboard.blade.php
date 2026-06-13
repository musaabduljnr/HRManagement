@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <h2 style="margin-top: 0; font-weight: 600; color: #1e293b;">Asset Management</h2>
        <p style="color: #64748b; margin-bottom: 24px;">Track and coordinate company hardware, assignments, maintenance, and inventory reporting.</p>
    </div>
</div>

{{-- Navigation Cards --}}
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel" style="background: transparent; border: none; box-shadow: none; padding: 0; margin-bottom: 24px;">
            <div class="clearfix" style="margin: 0 -15px;">
                <div class="col-lg-2-4 col-md-4 col-sm-6" style="margin-bottom: 15px;">
                    <a class="nav-box card-primary" style="border-top-color: #6366f1; transition: transform 0.2s ease, box-shadow 0.2s ease; display: block; height: 100%;" href="{{ route('assets.list.index') }}">
                        <div class="nav-card-icon" style="color: #6366f1;"><i class="fa fa-laptop"></i></div>
                        <h2 style="font-size: 16px; margin: 10px 0 0 0;">Asset Inventory</h2>
                    </a>
                </div>
                <div class="col-lg-2-4 col-md-4 col-sm-6" style="margin-bottom: 15px;">
                    <a class="nav-box card-success" style="border-top-color: #10b981; transition: transform 0.2s ease, box-shadow 0.2s ease; display: block; height: 100%;" href="{{ route('assets.assignments.index') }}">
                        <div class="nav-card-icon" style="color: #10b981;"><i class="fa fa-exchange"></i></div>
                        <h2 style="font-size: 16px; margin: 10px 0 0 0;">Assignments</h2>
                    </a>
                </div>
                <div class="col-lg-2-4 col-md-4 col-sm-6" style="margin-bottom: 15px;">
                    <a class="nav-box card-warning" style="border-top-color: #f59e0b; transition: transform 0.2s ease, box-shadow 0.2s ease; display: block; height: 100%;" href="{{ route('assets.maintenances.index') }}">
                        <div class="nav-card-icon" style="color: #f59e0b;"><i class="fa fa-wrench"></i></div>
                        <h2 style="font-size: 16px; margin: 10px 0 0 0;">Maintenance Logs</h2>
                    </a>
                </div>
                <div class="col-lg-2-4 col-md-4 col-sm-6" style="margin-bottom: 15px;">
                    <a class="nav-box card-info" style="border-top-color: #06b6d4; transition: transform 0.2s ease, box-shadow 0.2s ease; display: block; height: 100%;" href="{{ route('assets.categories.index') }}">
                        <div class="nav-card-icon" style="color: #06b6d4;"><i class="fa fa-tags"></i></div>
                        <h2 style="font-size: 16px; margin: 10px 0 0 0;">Categories</h2>
                    </a>
                </div>
                <div class="col-lg-2-4 col-md-4 col-sm-6" style="margin-bottom: 15px;">
                    <a class="nav-box card-danger" style="border-top-color: #ef4444; transition: transform 0.2s ease, box-shadow 0.2s ease; display: block; height: 100%;" href="{{ route('assets.reports.index') }}">
                        <div class="nav-card-icon" style="color: #ef4444;"><i class="fa fa-bar-chart"></i></div>
                        <h2 style="font-size: 16px; margin: 10px 0 0 0;">Inventory Reports</h2>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Metric Widgets --}}
<div class="row">
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="custom-panel" style="padding: 24px; border-left: 4px solid #6366f1; border-radius: 8px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="color: #64748b; font-weight: 500; font-size: 14px;">Total Assets</span>
                    <h3 style="margin: 8px 0 0 0; font-size: 28px; font-weight: 700; color: #1e293b;">{{ $totalAssets }}</h3>
                </div>
                <div style="background: #e0e7ff; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #6366f1; font-size: 20px;">
                    <i class="fa fa-laptop"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="custom-panel" style="padding: 24px; border-left: 4px solid #10b981; border-radius: 8px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="color: #64748b; font-weight: 500; font-size: 14px;">Assigned</span>
                    <h3 style="margin: 8px 0 0 0; font-size: 28px; font-weight: 700; color: #1e293b;">{{ $assignedAssets }}</h3>
                </div>
                <div style="background: #d1fae5; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #10b981; font-size: 20px;">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="custom-panel" style="padding: 24px; border-left: 4px solid #06b6d4; border-radius: 8px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="color: #64748b; font-weight: 500; font-size: 14px;">Available</span>
                    <h3 style="margin: 8px 0 0 0; font-size: 28px; font-weight: 700; color: #1e293b;">{{ $availableAssets }}</h3>
                </div>
                <div style="background: #cffafe; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #06b6d4; font-size: 20px;">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="custom-panel" style="padding: 24px; border-left: 4px solid #ef4444; border-radius: 8px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="color: #64748b; font-weight: 500; font-size: 14px;">Overdue Returns</span>
                    <h3 style="margin: 8px 0 0 0; font-size: 28px; font-weight: 700; color: #1e293b;">{{ $overdueAssets }}</h3>
                </div>
                <div style="background: #fee2e2; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #ef4444; font-size: 20px;">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additionalCSS')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    /* 5-column layout for modern dashboard feel */
    @media (min-width: 1200px) {
        .col-lg-2-4 {
            width: 20%;
            float: left;
        }
    }
    .nav-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endsection
