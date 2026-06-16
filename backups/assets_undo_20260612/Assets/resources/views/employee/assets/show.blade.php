@extends('layouts.main_employee')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Asset Assignment Details</h1>
        <a href="{{ route('employee.assets.index') }}" class="btn btn-secondary">Back to My Assets</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h2>{{ $assignment->asset->name }}</h2>
            <p><strong>Tag:</strong> {{ $assignment->asset->asset_tag }}</p>
            <p><strong>Category:</strong> {{ $assignment->asset->category->name ?? 'N/A' }}</p>
            <p><strong>Assigned Date:</strong> {{ optional($assignment->assigned_date)->format('Y-m-d') }}</p>
            <p><strong>Expected Return:</strong> {{ optional($assignment->expected_return_date)->format('Y-m-d') }}</p>
            <p><strong>Condition at Assignment:</strong> {{ ucfirst($assignment->condition_at_assignment) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($assignment->status) }}</p>
            <p><strong>Notes:</strong> {{ $assignment->notes }}</p>
        </div>
    </div>
</div>
@endsection
