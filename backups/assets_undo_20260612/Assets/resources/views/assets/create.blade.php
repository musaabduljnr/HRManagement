@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Add New Asset</h1>
        <a href="{{ route('assets.index') }}" class="btn btn-secondary">Back to Assets</a>
    </div>

    <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name">Asset Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="form-group col-md-6">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="brand">Brand</label>
                <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand') }}">
            </div>
            <div class="form-group col-md-4">
                <label for="model">Model</label>
                <input type="text" name="model" id="model" class="form-control" value="{{ old('model') }}">
            </div>
            <div class="form-group col-md-4">
                <label for="serial_number">Serial Number</label>
                <input type="text" name="serial_number" id="serial_number" class="form-control" value="{{ old('serial_number') }}">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                    <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="condition">Condition</label>
                <select name="condition" id="condition" class="form-control" required>
                    <option value="excellent" {{ old('condition') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                    <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                    <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                    <option value="poor" {{ old('condition') == 'poor' ? 'selected' : '' }}>Poor</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="purchase_date">Purchase Date</label>
                <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
            </div>
            <div class="form-group col-md-3">
                <label for="purchase_cost">Purchase Cost</label>
                <input type="number" name="purchase_cost" id="purchase_cost" step="0.01" class="form-control" value="{{ old('purchase_cost') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="vendor">Vendor</label>
                <input type="text" name="vendor" id="vendor" class="form-control" value="{{ old('vendor') }}">
            </div>
            <div class="form-group col-md-4">
                <label for="warranty_expiry">Warranty Expiry</label>
                <input type="date" name="warranty_expiry" id="warranty_expiry" class="form-control" value="{{ old('warranty_expiry') }}">
            </div>
            <div class="form-group col-md-4">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}">
            </div>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" name="image" id="image" class="form-control-file">
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Asset</button>
    </form>
</div>
@endsection
