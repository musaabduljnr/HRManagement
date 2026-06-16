@extends('layouts.main_employee')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>My Assets</h1>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Asset Tag</th>
                <th>Name</th>
                <th>Category</th>
                <th>Assigned Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->asset->asset_tag }}</td>
                    <td>{{ $assignment->asset->name }}</td>
                    <td>{{ $assignment->asset->category->name ?? 'N/A' }}</td>
                    <td>{{ optional($assignment->assigned_date)->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($assignment->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No assets currently assigned.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
