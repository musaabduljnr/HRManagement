@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('assets.list.index') }}" class="btn btn-default pull-right">Back to Inventory</a>
        <a href="{{ route('assets.list.edit', $asset->id) }}" class="btn btn-primary pull-right" style="margin-right: 10px;">Edit Asset</a>
    </div>
</div>

<div class="row" style="margin-top: 15px;">
    {{-- Left: Details Card & QR --}}
    <div class="col-md-5">
        <div class="custom-panel" style="margin-bottom: 20px;">
            <div class="custom-panel-heading">Asset Profile: {{ $asset->asset_code }}</div>
            <div style="padding: 15px; text-align: center;">
                @if($asset->image)
                    <img src="{{ route('storage', ['path' => $asset->image]) }}" alt="Asset Photograph" style="max-width: 100%; max-height: 200px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px; margin-bottom: 15px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                @else
                    <div style="height: 120px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #94a3b8; font-size: 48px; margin-bottom: 15px;">
                        <i class="fa fa-laptop"></i>
                    </div>
                @endif
                <h3 style="margin: 0; font-weight: 700; color: #1e293b;">{{ $asset->asset_name }}</h3>
                <span class="label {{ strtolower($asset->current_status) == 'available' ? 'label-success' : 'label-primary' }}" style="font-size: 13px; display: inline-block; margin-top: 8px; padding: .3em .6em .3em;">
                    {{ $asset->current_status }}
                </span>
            </div>
            
            <table class="table" style="margin-bottom: 0; font-size: 14px;">
                <tr>
                    <th style="width: 40%; border-top: none; color: #64748b;">Category</th>
                    <td style="border-top: none;">{{ $asset->category ? $asset->category->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Brand / Model</th>
                    <td>{{ $asset->brand ?: 'N/A' }} {{ $asset->model ?: '' }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Serial Number</th>
                    <td><code>{{ $asset->serial_number ?: 'N/A' }}</code></td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Condition</th>
                    <td>{{ $asset->condition }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Purchase Cost</th>
                    <td>{{ $asset->purchase_cost ? '₦' . number_format($asset->purchase_cost, 2) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Purchase Date</th>
                    <td>{{ $asset->purchase_date ? (is_string($asset->purchase_date) ? $asset->purchase_date : $asset->purchase_date->format('Y-m-d')) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Warranty Expiry</th>
                    <td>{{ $asset->warranty_expiry ? (is_string($asset->warranty_expiry) ? $asset->warranty_expiry : $asset->warranty_expiry->format('Y-m-d')) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Supplier</th>
                    <td>{{ $asset->supplier ?: 'N/A' }}</td>
                </tr>
            </table>
        </div>

        {{-- QR Code printable widget --}}
        <div class="custom-panel" id="qrCard" style="margin-bottom: 20px; text-align: center; padding: 20px;">
            <div class="custom-panel-heading" style="text-align: left; margin: -20px -20px 20px -20px;">Asset Tag / QR Code</div>
            <div style="background: #fff; padding: 15px; display: inline-block; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <img src="{{ $asset->qr_code_url }}" alt="QR Code Link" style="width: 150px; height: 150px;">
                <div style="margin-top: 10px; font-weight: 600; color: #1e293b; font-size: 13px;">{{ $asset->asset_code }}</div>
                <div style="font-size: 11px; color: #64748b;">Scan to view details</div>
            </div>
            <div style="margin-top: 15px;">
                <button onclick="printQR()" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print QR Label</button>
            </div>
        </div>
    </div>

    {{-- Right: Vertical CSS Lifecycle Timeline --}}
    <div class="col-md-7">
        <div class="custom-panel">
            <div class="custom-panel-heading">Asset Lifecycle History Timeline</div>
            <div style="padding: 15px;">
                @if($histories->count() > 0)
                    <div class="timeline">
                        @foreach($histories as $history)
                            <div class="timeline-item">
                                <div class="timeline-badge" style="background-color: #6366f1;">
                                    <i class="fa fa-dot-circle-o"></i>
                                </div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="timeline-title" style="margin: 0; font-weight: 600; color: #1e293b;">{{ $history->action_type }}</h4>
                                        <p style="margin: 4px 0 0 0;"><small class="text-muted"><i class="fa fa-clock-o"></i> {{ is_string($history->created_at) ? $history->created_at : $history->created_at->diffForHumans() }} by {{ $history->user ? $history->user->full_name : 'System' }}</small></p>
                                    </div>
                                    <div class="timeline-body" style="margin-top: 10px; color: #334155; font-size: 13.5px;">
                                        <p style="margin: 0 0 8px 0;">{{ $history->remarks }}</p>
                                        @if($history->old_value || $history->new_value)
                                            <div style="background: #f8fafc; border-radius: 4px; padding: 6px 10px; border-left: 3px solid #cbd5e1; font-size: 12px; font-family: monospace;">
                                                @if($history->old_value)
                                                    <div><span style="color: #64748b;">Previous:</span> {{ $history->old_value }}</div>
                                                @endif
                                                @if($history->new_value)
                                                    <div><span style="color: #64748b;">Current:</span> {{ $history->new_value }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px 0; color: #94a3b8;">
                        <i class="fa fa-history" style="font-size: 40px; margin-bottom: 15px; display: block;"></i>
                        No history logs registered for this asset.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('additionalCSS')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    /* Elegant Vertical Timeline Styles */
    .timeline {
        position: relative;
        padding: 10px 0 20px;
        list-style: none;
    }
    .timeline:before {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 20px;
        width: 2px;
        content: "";
        background-color: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-item:after {
        clear: both;
        content: " ";
        display: table;
    }
    .timeline-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 10;
        width: 22px;
        height: 22px;
        color: #fff;
        line-height: 22px;
        text-align: center;
        border-radius: 50%;
        font-size: 10px;
    }
    .timeline-panel {
        position: relative;
        width: calc(100% - 50px);
        margin-left: 50px;
        padding: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05);
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        #qrCard, #qrCard * {
            visibility: visible;
        }
        #qrCard {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
        }
        #qrCard button {
            display: none;
        }
    }
</style>
@endsection

@section('additionalJS')
<script>
    function printQR() {
        window.print();
    }
</script>
@endsection
