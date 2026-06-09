<?php

namespace App\Modules\Employee\Salary\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Pim\Models\PayrollRecord;
use App\Modules\Pim\Models\Payslip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Datatables;

class EmployeePayrollController extends Controller
{
    public function index()
    {
        $current = 'employee.payroll';
        return view('employee.salary::payroll.index', compact('current'));
    }

    public function getDatatable()
    {
        $records = PayrollRecord::where('user_id', Auth::id())
            ->where('status', 'paid')
            ->select(['id', 'payroll_month', 'base_salary', 'allowances', 'deductions', 'bonuses', 'net_salary']);

        return Datatables::of($records)
            ->addColumn('actions', function($record) {
                return '<a href="'.route('employee.payroll.show', $record->id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View Payslip</a>';
            })
            ->make(true);
    }

    public function show($id)
    {
        $record = PayrollRecord::with('user')->findOrFail($id);
        if ($record->user_id != Auth::id()) {
            abort(403, 'Unauthorized.');
        }
        if ($record->status != 'paid') {
            abort(404, 'Payslip not available.');
        }
        $payslip = Payslip::where('payroll_record_id', $record->id)->first();
        $current = 'employee.payroll';
        return view('employee.salary::payroll.show', compact('record', 'payslip', 'current'));
    }
}
