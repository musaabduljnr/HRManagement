@extends('layouts.main_employee')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{ route('employee.assets.index') }}" class="btn btn-default pull-right">Back to My Assets</a>
    </div>
</div>

<div class="row" style="margin-top: 15px;">
    {{-- Left: QR Code Card --}}
    <div class="col-md-5">
        <div class="custom-panel" id="qrCard" style="text-align: center; padding: 20px; margin-bottom: 20px;">
            <div class="custom-panel-heading" style="text-align: left; margin: -20px -20px 20px -20px;">My Asset QR Label</div>
            <div style="background: #fff; padding: 15px; display: inline-block; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <img src="{{ $asset->qr_code_url }}" alt="QR Code Link" style="width: 150px; height: 150px;">
                <div style="margin-top: 10px; font-weight: 600; color: #1e293b; font-size: 13px;">{{ $asset->asset_code }}</div>
                <div style="font-size: 11px; color: #64748b;">Asset Tag (QR Code)</div>
            </div>
            <div style="margin-top: 15px;">
                <button onclick="printQR()" class="btn btn-default btn-sm"><i class="fa fa-print"></i> Print Label</button>
            </div>
        </div>
    </div>

    {{-- Right: Asset details --}}
    <div class="col-md-7">
        <div class="custom-panel" style="margin-bottom: 20px;">
            <div class="custom-panel-heading">Asset Information: {{ $asset->asset_name }}</div>
            <div style="padding: 15px; text-align: center; border-bottom: 1px solid #e2e8f0;">
                @if($asset->image)
                    <img src="{{ route('storage', ['path' => $asset->image]) }}" alt="Asset Photo" style="max-height: 150px; max-width: 100%; border-radius: 6px; border: 1px solid #e2e8f0; padding: 4px; margin-bottom: 10px;">
                @endif
                <h4 style="margin: 0; font-weight: 700; color: #1e293b;">{{ $asset->asset_name }}</h4>
                <p style="margin: 3px 0 0 0; font-size: 12.5px; color: #64748b;">Serial: <code>{{ $asset->serial_number ?: 'N/A' }}</code></p>
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
                    <th style="color: #64748b;">Condition (Issued)</th>
                    <td><span class="label label-default">{{ $asset->condition }}</span></td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Date Checked Out</th>
                    <td>{{ is_string($assignment->issue_date) ? $assignment->issue_date : $assignment->issue_date->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Expected Return Date</th>
                    <td>{{ $assignment->expected_return_date ? (is_string($assignment->expected_return_date) ? $assignment->expected_return_date : $assignment->expected_return_date->format('Y-m-d')) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Assigned By</th>
                    <td>{{ $assignment->assigner ? $assignment->assigner->first_name . ' ' . $assignment->assigner->last_name : 'System' }}</td>
                </tr>
                <tr>
                    <th style="color: #64748b;">Assignment Notes</th>
                    <td>{{ $assignment->assignment_notes ?: 'No notes attached.' }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

@section('additionalCSS')
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
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
