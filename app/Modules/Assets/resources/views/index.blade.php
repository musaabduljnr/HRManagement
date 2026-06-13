@extends('layouts.main')

@section('content')
<div class="row" style="margin-bottom: 24px;">
    <div class="col-sm-12">
        <h2 style="margin-top: 0; font-weight: 700; color: var(--text-primary);">Asset Management Dashboard</h2>
        <p class="text-muted">Track, assign, monitor, and manage company assets issued to employees.</p>
    </div>
</div>

<div class="row" style="margin-bottom: 24px;">
    <div class="col-lg-2-5 col-md-4 col-sm-6" style="margin-bottom: 15px;">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fa fa-cubes"></i>
            </div>
            <div>
                <div class="stat-value">{{ $counts['total'] }}</div>
                <div class="stat-label">Total Assets</div>
            </div>
        </div>
    </div>
    <div class="col-lg-2-5 col-md-4 col-sm-6" style="margin-bottom: 15px;">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fa fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-value">{{ $counts['available'] }}</div>
                <div class="stat-label">Available</div>
            </div>
        </div>
    </div>
    <div class="col-lg-2-5 col-md-4 col-sm-6" style="margin-bottom: 15px;">
        <div class="stat-card" style="border-left: 3px solid var(--info);">
            <div class="stat-icon" style="background: var(--info-light); color: var(--info);">
                <i class="fa fa-user"></i>
            </div>
            <div>
                <div class="stat-value">{{ $counts['assigned'] }}</div>
                <div class="stat-label">Assigned</div>
            </div>
        </div>
    </div>
    <div class="col-lg-2-5 col-md-4 col-sm-6" style="margin-bottom: 15px;">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fa fa-wrench"></i>
            </div>
            <div>
                <div class="stat-value">{{ $counts['maintenance'] }}</div>
                <div class="stat-label">In Maintenance</div>
            </div>
        </div>
    </div>
    <div class="col-lg-2-5 col-md-4 col-sm-6" style="margin-bottom: 15px;">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <div>
                <div class="stat-value">{{ $counts['overdue'] }}</div>
                <div class="stat-label">Overdue Returns</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">
                <i class="fa fa-sliders"></i> Operations Control Panel
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <a class="nav-box card-primary" href="{{ route('assets.list.index') }}" style="text-decoration: none; display: block; margin-bottom: 15px;">
                            <div class="nav-card-icon">
                                <i class="fa fa-laptop"></i>
                            </div>
                            <h2>Manage Assets</h2>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">View inventory and add assets</p>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a class="nav-box card-success" href="{{ route('assets.categories.index') }}" style="text-decoration: none; display: block; margin-bottom: 15px;">
                            <div class="nav-card-icon">
                                <i class="fa fa-tags"></i>
                            </div>
                            <h2>Asset Categories</h2>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Manage Laptop, Phone, SIM card, etc.</p>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a class="nav-box card-warning" href="{{ route('assets.maintenances.index') }}" style="text-decoration: none; display: block; margin-bottom: 15px;">
                            <div class="nav-card-icon">
                                <i class="fa fa-wrench"></i>
                            </div>
                            <h2>Maintenance Log</h2>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Track repair & servicing history</p>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a class="nav-box card-danger" href="{{ route('assets.reports.index') }}" style="text-decoration: none; display: block; margin-bottom: 15px;">
                            <div class="nav-card-icon">
                                <i class="fa fa-bar-chart"></i>
                            </div>
                            <h2>Inventory Reports</h2>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 5px;">Export filtered asset details</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* 5 Columns layout support for larger screens */
    @media (min-width: 1200px) {
        .col-lg-2-5 {
            width: 20%;
            float: left;
        }
    }
</style>
@endsection
