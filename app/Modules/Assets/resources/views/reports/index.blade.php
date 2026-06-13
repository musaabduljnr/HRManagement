@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('assets.dashboard') }}" class="btn btn-default pull-right">Back to Dashboard</a>
    </div>
</div>

<div class="row" style="margin-top: 15px;">
    {{-- Filters Panel --}}
    <div class="col-sm-12">
        <div class="custom-panel" style="margin-bottom: 20px;">
            <div class="custom-panel-heading">Filter Inventory Reports</div>
            {!! Form::open(['method' => 'GET', 'route' => 'assets.reports.index', 'class' => 'form-inline', 'style' => 'padding: 10px;']) !!}
                <div class="form-group" style="margin-right: 15px;">
                    {!! Form::label('category_id', 'Category:', ['style' => 'margin-right: 8px; font-weight: 500;']) !!}
                    {!! Form::select('category_id', ['' => '-- All Categories --'] + $categories, Request::get('category_id'), ['class' => 'form-control input-sm']) !!}
                </div>
                <div class="form-group" style="margin-right: 15px;">
                    {!! Form::label('current_status', 'Status:', ['style' => 'margin-right: 8px; font-weight: 500;']) !!}
                    {!! Form::select('current_status', [
                        '' => '-- All Statuses --',
                        'Available' => 'Available',
                        'Assigned' => 'Assigned',
                        'Under Maintenance' => 'Under Maintenance',
                        'Damaged' => 'Damaged',
                        'Lost' => 'Lost',
                        'Retired' => 'Retired'
                    ], Request::get('current_status'), ['class' => 'form-control input-sm']) !!}
                </div>
                <div class="form-group" style="margin-right: 15px;">
                    {!! Form::label('condition', 'Condition:', ['style' => 'margin-right: 8px; font-weight: 500;']) !!}
                    {!! Form::select('condition', [
                        '' => '-- All Conditions --',
                        'Excellent' => 'Excellent',
                        'Good' => 'Good',
                        'Fair' => 'Fair',
                        'Poor' => 'Poor',
                        'Damaged' => 'Damaged'
                    ], Request::get('condition'), ['class' => 'form-control input-sm']) !!}
                </div>
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-filter"></i> Apply Filters</button>
                <a href="{{ route('assets.reports.export', Request::all()) }}" class="btn btn-sm btn-success" style="margin-left: 5px;"><i class="fa fa-file-excel-o"></i> Export to CSV</a>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<div class="row">
    {{-- Results Panel --}}
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Inventory Summary Data ({{ $assets->count() }} records)</div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th>Asset Code</th>
                            <th>Asset Name</th>
                            <th>Category</th>
                            <th>Brand / Model</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                            <th>Condition</th>
                            <th>Purchase Cost</th>
                            <th>Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($assets->count() > 0)
                            @foreach($assets as $asset)
                                <tr>
                                    <td><code>{{ $asset->asset_code }}</code></td>
                                    <td><strong>{{ $asset->asset_name }}</strong></td>
                                    <td>{{ $asset->category ? $asset->category->name : 'N/A' }}</td>
                                    <td>{{ $asset->brand ?: 'N/A' }} {{ $asset->model ?: '' }}</td>
                                    <td><code>{{ $asset->serial_number ?: 'N/A' }}</code></td>
                                    <td>
                                        @if($asset->current_status == 'Available')
                                            <span class="label label-success">Available</span>
                                        @elseif($asset->current_status == 'Assigned')
                                            <span class="label label-primary">Assigned</span>
                                        @elseif($asset->current_status == 'Under Maintenance')
                                            <span class="label label-warning">Under Maintenance</span>
                                        @else
                                            <span class="label label-default">{{ $asset->current_status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $asset->condition }}</td>
                                    <td>{{ $asset->purchase_cost ? '₦' . number_format($asset->purchase_cost, 2) : 'N/A' }}</td>
                                    <td>{{ $asset->purchase_date ? (is_string($asset->purchase_date) ? $asset->purchase_date : $asset->purchase_date->format('Y-m-d')) : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" style="text-align: center; color: #94a3b8; padding: 30px;">
                                    <i class="fa fa-info-circle" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                                    No assets match the specified filters.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additionalCSS')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection
