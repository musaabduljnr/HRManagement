@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Assets</h1>
        <a href="{{ route('assets.create') }}" class="btn btn-primary">Add New Asset</a>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>Tag</th>
                <th>Name</th>
                <th>Category</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $asset)
                <tr>
                    <td>{{ $asset->asset_tag }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? 'N/A' }}</td>
                    <td>{{ $asset->status }}</td>
                    <td>{{ $asset->condition }}</td>
                    <td>
                        <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this asset?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No assets available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
