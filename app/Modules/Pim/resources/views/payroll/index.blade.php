@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="custom-panel">
            <div class="custom-panel-heading">Payroll Management</div>
            <div style="padding: 15px;">
                <div class="row">
                    <!-- Filter Month Form -->
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('payroll.index') }}" class="form-inline">
                            <div class="form-group">
                                <label for="month" style="margin-right: 10px;">Select Month:</label>
                                <select name="month" id="month" class="form-control" onchange="this.form.submit()">
                                    @foreach($availableMonths as $m)
                                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <!-- Generate Payroll Form -->
                    <div class="col-md-6 text-right">
                        <form method="POST" action="{{ route('payroll.store') }}" class="form-inline">
                            {{ csrf_field() }}
                            <input type="hidden" name="month" value="{{ $month }}">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-refresh"></i> Generate Payroll for {{ $month }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Month</th>
                        <th>Base Salary</th>
                        <th>Allowances</th>
                        <th>Deductions</th>
                        <th>Bonuses</th>
                        <th>Net Salary</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->user->first_name }} {{ $record->user->last_name }}</td>
                            <td>{{ $record->payroll_month }}</td>
                            <td>${{ number_format($record->base_salary, 2) }}</td>
                            <td>${{ number_format($record->allowances, 2) }}</td>
                            <td>${{ number_format($record->deductions, 2) }}</td>
                            <td>${{ number_format($record->bonuses, 2) }}</td>
                            <td><strong>${{ number_format($record->net_salary, 2) }}</strong></td>
                            <td>
                                @if($record->status == 'paid')
                                    <span class="label label-success">Paid</span>
                                @else
                                    <span class="label label-warning">Draft</span>
                                @endif
                            </td>
                            <td>
                                @if($record->status == 'draft')
                                    <form method="POST" action="{{ route('payroll.pay', $record->id) }}" style="display:inline;">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-xs btn-primary">
                                            <i class="fa fa-money"></i> Mark as Paid
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('payroll.payslip', $record->id) }}" class="btn btn-xs btn-default">
                                        <i class="fa fa-file-text-o"></i> View Payslip
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No payroll records generated for {{ $month }} yet. Click "Generate Payroll" above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
