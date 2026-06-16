@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Asset Categories</h1>
        <a href="{{ route('assets.categories.create') }}" class="btn btn-primary">Create Category</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Active</th>
                <th>Assets</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->is_active ? 'Yes' : 'No' }}</td>
                    <td>{{ $category->active_assets_count }}</td>
                    <td>
                        <a href="{{ route('assets.categories.edit', $category->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('assets.categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?');">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
