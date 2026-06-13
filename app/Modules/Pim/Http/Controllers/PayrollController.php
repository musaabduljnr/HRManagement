<?php

namespace App\Modules\Pim\Http\Controllers;

use App\Http\Controllers\Controller;
use App\ActivityLog;
use App\Modules\Pim\Models\PayrollRecord;
use App\Modules\Pim\Models\Payslip;
use App\Modules\Pim\Models\CurrentSalary;
use App\Modules\Pim\Models\Salary;
use App\Modules\Pim\Models\SalarySalaryComponent;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        if (\Gate::denies('manage-payroll')) {
            abort(403);
        }
        $month = $request->input('month', date('Y-m'));
        $records = PayrollRecord::with('user')->where('payroll_month', $month)->get();

        $availableMonths = PayrollRecord::select('payroll_month')
            ->distinct()
            ->orderBy('payroll_month', 'desc')
            ->pluck('payroll_month')
            ->toArray();
            
        if (!in_array(date('Y-m'), $availableMonths)) {
            array_unshift($availableMonths, date('Y-m'));
        }
        
        $current = 'payroll';
        return view('pim::payroll.index', compact('records', 'month', 'availableMonths', 'current'));
    }

    public function store(Request $request)
    {
        if (\Gate::denies('manage-payroll')) {
            abort(403);
        }
        $this->validate($request, [
            'month' => 'required|date_format:Y-m'
        ]);
        
        $month = $request->input('month');

        $employees = \App\User::whereIn('role', [
            \App\User::USER_ROLE_EMPLOYEE,
            \App\User::USER_ROLE_HR_MANAGER,
            \App\User::USER_ROLE_PAYROLL_MANAGER,
            \App\User::USER_ROLE_DEPT_MANAGER
        ])->get();

        if ($employees->isEmpty()) {
            return redirect()->back()->with('error', 'No active employees found to generate payroll.');
        }

        $generated = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Skip if this employee already has a record for the month
            $alreadyExists = PayrollRecord::where('user_id', $employee->id)
                ->where('payroll_month', $month)
                ->exists();
            if ($alreadyExists) {
                $skipped++;
                continue;
            }
            $generated++;
            $config = CurrentSalary::where('user_id', $employee->id)->orderBy('id', 'desc')->first();
            $baseSalary = $config ? $config->amount : 1000.00;

            $allowances = 0.00;
            $deductions = 0.00;
            $latestSalary = Salary::where('user_id', $employee->id)->orderBy('payment_date', 'desc')->first();
            if ($latestSalary) {
                $components = SalarySalaryComponent::where('salary_id', $latestSalary->id)->get();
                foreach ($components as $comp) {
                    $salaryComponent = \App\Modules\Settings\Models\SalaryComponent::find($comp->salary_component_id);
                    if ($salaryComponent) {
                        if ($salaryComponent->type == \App\Modules\Settings\Models\SalaryComponent::TYPE_EARNING) {
                            $allowances += $comp->value;
                        } elseif ($salaryComponent->type == \App\Modules\Settings\Models\SalaryComponent::TYPE_DEDUCTION) {
                            $deductions += $comp->value;
                        }
                    }
                }
            }

            $netSalary = $baseSalary + $allowances - $deductions;

            PayrollRecord::create([
                'user_id' => $employee->id,
                'payroll_month' => $month,
                'base_salary' => $baseSalary,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'bonuses' => 0.00,
                'net_salary' => $netSalary,
                'status' => 'draft'
            ]);
        }

        if ($generated === 0) {
            return redirect()->route('payroll.index', ['month' => $month])->with('error', 'All active employees already have payroll records for ' . $month . '. No new records created.');
        }

        $message = 'Payroll generated for ' . $month . ': ' . $generated . ' new record(s) created.';
        if ($skipped > 0) {
            $message .= ' ' . $skipped . ' employee(s) already had records and were skipped.';
        }

        ActivityLog::log(
            'Payroll Generated',
            'Payroll for ' . $month . ' generated: ' . $generated . ' record(s) created, ' . $skipped . ' skipped.'
        );

        return redirect()->route('payroll.index', ['month' => $month])->with('success', $message);
    }

    public function markAsPaid($id)
    {
        if (\Gate::denies('manage-payroll')) {
            abort(403);
        }
        $record = PayrollRecord::findOrFail($id);
        if ($record->status == 'paid') {
            return redirect()->back()->with('error', 'Payroll record is already paid.');
        }

        $record->status = 'paid';
        $record->save();

        $payslipNum = 'PAY-' . str_replace('-', '', $record->payroll_month) . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT);
        
        Payslip::create([
            'payroll_record_id' => $record->id,
            'payslip_number'    => $payslipNum,
            'pdf_path'          => null
        ]);

        ActivityLog::log(
            'Payroll Paid',
            'Payslip ' . $payslipNum . ' generated for ' . $record->user->first_name . ' ' . $record->user->last_name . ' (' . $record->payroll_month . ').'
        );

        return redirect()->back()->with('success', 'Payroll record marked as PAID. Payslip ' . $payslipNum . ' generated.');
    }

    public function payslip($id)
    {
        if (\Gate::denies('manage-payroll')) {
            abort(403);
        }
        $record = PayrollRecord::with('user')->findOrFail($id);
        $payslip = Payslip::where('payroll_record_id', $record->id)->first();
        if (!$payslip) {
            abort(404, 'Payslip not generated yet.');
        }
        $current = 'payroll';
        return view('pim::payroll.payslip', compact('record', 'payslip', 'current'));
    }

    public function updateBonus($id, Request $request)
    {
        if (\Gate::denies('manage-payroll')) {
            abort(403);
        }
        $this->validate($request, [
            'bonuses' => 'required|numeric|min:0',
        ]);

        $record = PayrollRecord::findOrFail($id);

        if ($record->status === 'paid') {
            return redirect()->back()->with('error', 'Cannot edit a paid payroll record.');
        }

        $record->bonuses = $request->input('bonuses');
        $record->net_salary = $record->base_salary + $record->allowances + $record->bonuses - $record->deductions;
        $record->save();

        return redirect()->back()->with('success', 'Bonus updated for ' . $record->user->first_name . ' ' . $record->user->last_name . '. Net salary recalculated.');
    }
}
