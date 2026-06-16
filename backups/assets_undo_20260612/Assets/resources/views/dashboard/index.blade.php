@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Assets Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card card-summary">
                <div class="card-body">
                    <h5>Total Assets</h5>
                    <strong>{{ $statistics['total'] ?? 0 }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary">
                <div class="card-body">
                    <h5>Available</h5>
                    <strong>{{ $statistics['available'] ?? 0 }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary">
                <div class="card-body">
                    <h5>Assigned</h5>
                    <strong>{{ $statistics['assigned'] ?? 0 }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-summary">
                <div class="card-body">
                    <h5>Under Maintenance</h5>
                    <strong>{{ $statistics['maintenance'] ?? 0 }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('assets.categories.index') }}" class="btn btn-secondary">Manage Categories</a>
        <a href="{{ route('assets.create') }}" class="btn btn-primary">Add Asset</a>
        <a href="{{ route('assets.index') }}" class="btn btn-outline-primary">View Assets</a>
    </div>
</div>
@endsection
