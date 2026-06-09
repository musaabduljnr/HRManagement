@extends('layouts.main_employee')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">
                Payslip Details
                <button onclick="window.print()" class="btn btn-xs btn-default pull-right">
                    <i class="fa fa-print"></i> Print Payslip
                </button>
            </div>
            <div style="padding: 30px;" id="payslip-print-area">
                <div class="row">
                    <div class="col-xs-6">
                        <h2>{{ config('app.name', 'HRM') }}</h2>
                        <p>100 Enterprise Way, Suite 500<br>Silicon Valley, CA 94043</p>
                    </div>
                    <div class="col-xs-6 text-right">
                        <h3>PAYSLIP</h3>
                        <p><strong>Payslip Number:</strong> {{ $payslip->payslip_number }}<br>
                        <strong>Month:</strong> {{ $record->payroll_month }}<br>
                        <strong>Status:</strong> <span class="label label-success">PAID</span></p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-xs-6">
                        <h4><strong>Employee Details</strong></h4>
                        <p><strong>Name:</strong> {{ $record->user->first_name }} {{ $record->user->last_name }}<br>
                        <strong>Email:</strong> {{ $record->user->email }}<br>
                        <strong>Department:</strong> {{ $record->user->department ? $record->user->department->name : 'N/A' }}<br>
                        <strong>Job Title:</strong> {{ $record->user->jobTitle ? $record->user->jobTitle->name : 'N/A' }}</p>
                    </div>
                    <div class="col-xs-6 text-right">
                        <h4><strong>Payment Info</strong></h4>
                        <p><strong>Payment Method:</strong> Bank Transfer<br>
                        <strong>Date Paid:</strong> {{ $record->updated_at->format('Y-m-d') }}</p>
                    </div>
                </div>
                
                <br>
                
                <table class="table table-bordered">
                    <thead>
                        <tr style="background-color: #f9f9f9;">
                            <th>Earnings / Allowances</th>
                            <th class="text-right">Amount</th>
                            <th>Deductions</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="text-right">${{ number_format($record->base_salary, 2) }}</td>
                            <td>Taxes & Deductions</td>
                            <td class="text-right">${{ number_format($record->deductions, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Other Allowances</td>
                            <td class="text-right">${{ number_format($record->allowances, 2) }}</td>
                            <td></td>
                            <td class="text-right">-</td>
                        </tr>
                        <tr>
                            <td>Bonuses</td>
                            <td class="text-right">${{ number_format($record->bonuses, 2) }}</td>
                            <td></td>
                            <td class="text-right">-</td>
                        </tr>
                        <tr style="font-weight: bold; background-color: #f5f5f5;">
                            <td>Total Earnings</td>
                            <td class="text-right">${{ number_format($record->base_salary + $record->allowances + $record->bonuses, 2) }}</td>
                            <td>Total Deductions</td>
                            <td class="text-right">${{ number_format($record->deductions, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="row">
                    <div class="col-xs-6 col-xs-offset-6 text-right">
                        <div style="border-top: 2px solid #ddd; padding-top: 10px; margin-top: 10px;">
                            <h3><strong>Net Salary: ${{ number_format($record->net_salary, 2) }}</strong></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <p><a href="{{ route('employee.payroll.index') }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back to History</a></p>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #payslip-print-area, #payslip-print-area * {
        visibility: visible;
    }
    #payslip-print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
@endsection
