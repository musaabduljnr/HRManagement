@extends('layouts.main_employee')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <h2 style="margin-top: 0; font-weight: 600; color: #1e293b;">My Assigned Assets</h2>
        <p style="color: #64748b; margin-bottom: 24px;">View the company devices and hardware currently issued under your name.</p>
    </div>
</div>

<div class="row">
    @if($assignments->count() > 0)
        @foreach($assignments as $assignment)
            <?php $asset = $assignment->asset; ?>
            @if($asset)
                <div class="col-md-4 col-sm-6" style="margin-bottom: 20px;">
                    <div class="custom-panel" style="height: 100%; display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease; border-radius: 8px; border-top: 4px solid #6366f1;">
                        <div style="padding: 20px; flex-grow: 1; text-align: center;">
                            @if($asset->image)
                                <img src="{{ route('storage', ['path' => $asset->image]) }}" alt="Asset Photo" style="max-height: 100px; max-width: 100%; border-radius: 4px; margin-bottom: 12px;">
                            @else
                                <div style="height: 80px; background: #f8fafc; display: flex; align-items: center; justify-content: center; border-radius: 6px; color: #94a3b8; font-size: 32px; margin-bottom: 12px;">
                                    <i class="fa fa-laptop"></i>
                                </div>
                            @endif
                            
                            <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b;">{{ $asset->asset_name }}</h3>
                            <span class="label label-info" style="display: inline-block; margin-top: 5px; font-size: 11px;">
                                {{ $asset->category ? $asset->category->name : 'N/A' }}
                            </span>
                            
                            <div style="margin-top: 15px; text-align: left; font-size: 12.5px; color: #475569;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span style="color: #64748b;">Asset Code:</span>
                                    <strong>{{ $asset->asset_code }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span style="color: #64748b;">Serial Number:</span>
                                    <strong>{{ $asset->serial_number ?: 'N/A' }}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span style="color: #64748b;">Issue Date:</span>
                                    <strong>{{ is_string($assignment->issue_date) ? $assignment->issue_date : $assignment->issue_date->format('Y-m-d') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; text-align: center;">
                            <a href="{{ route('employee.assets.show', $assignment->id) }}" class="btn btn-default btn-sm btn-block" style="font-weight: 500;">
                                <i class="fa fa-qrcode"></i> View Asset Tag & Details
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @else
        <div class="col-sm-12">
            <div class="custom-panel" style="padding: 40px; text-align: center; color: #94a3b8; border-radius: 8px;">
                <i class="fa fa-laptop" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px; display: block;"></i>
                <h4 style="margin: 0 0 8px 0; font-weight: 600; color: #64748b;">No Assets Assigned</h4>
                <p style="margin: 0; font-size: 13.5px;">You currently have no hardware assets checked out to you.</p>
            </div>
        </div>
    @endif
</div>
@endsection

@section('additionalCSS')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .custom-panel:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02) !important;
    }
</style>
@endsection
