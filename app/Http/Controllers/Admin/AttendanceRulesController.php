<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\AttendanceRule;
use App\ActivityLog;
use App\User;
use App\Modules\Settings\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceRulesController extends Controller
{
    /**
     * Display a listing of all attendance rules.
     */
    public function index()
    {
        $rules = AttendanceRule::orderBy('rule_name')->get();
        $current = 'settings';
        return view('admin.attendance_rules.index', compact('rules', 'current'));
    }

    /**
     * Show the form for creating a new rule.
     */
    public function create()
    {
        $current = 'settings';
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $employees = User::whereIn('role', [
            User::USER_ROLE_EMPLOYEE,
            User::USER_ROLE_HR_MANAGER,
            User::USER_ROLE_PAYROLL_MANAGER,
            User::USER_ROLE_DEPT_MANAGER
        ])->get()->mapWithKeys(function($user) {
            return [$user->id => $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')'];
        });

        $workingDaysList = [
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
            'Saturday' => 'Saturday',
            'Sunday' => 'Sunday'
        ];

        return view('admin.attendance_rules.create', compact('current', 'departments', 'employees', 'workingDaysList'));
    }

    /**
     * Store a newly created rule in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'rule_name' => 'required|string|max:255',
            'shift_name' => 'required|string|max:255',
            'applies_to' => 'required|in:all_employees,branch,department,selected_employees',
            'working_days' => 'required|array',
            'check_in_start_time' => 'required',
            'check_in_cutoff_time' => 'required',
            'grace_period_minutes' => 'nullable|integer|min:0',
            'office_latitude' => 'nullable|numeric',
            'office_longitude' => 'nullable|numeric',
            'allowed_radius_meters' => 'nullable|integer|min:0',
            'minimum_work_duration_minutes' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['company_id'] = 1; // Default company
        $data['created_by'] = Auth::id();
        $data['working_days'] = json_encode($request->input('working_days'));
        $data['selfie_required'] = $request->has('selfie_required');
        $data['checkout_selfie_required'] = $request->has('checkout_selfie_required');
        $data['device_lock_required'] = $request->has('device_lock_required');
        $data['auto_mark_absent'] = $request->has('auto_mark_absent');
        $data['auto_mark_missed_checkout'] = $request->has('auto_mark_missed_checkout');
        $data['check_out_enabled'] = $request->has('check_out_enabled');

        // Context-specific nullification
        if ($data['applies_to'] !== 'department') {
            $data['department_id'] = null;
        }
        if ($data['applies_to'] !== 'selected_employees') {
            $data['employee_ids'] = null;
        } else {
            $data['employee_ids'] = $request->input('employee_ids') ? json_encode($request->input('employee_ids')) : null;
        }

        if (!$data['check_out_enabled']) {
            $data['check_out_start_time'] = null;
            $data['check_out_cutoff_time'] = null;
            $data['minimum_work_duration_minutes'] = null;
        }

        // Clean up remaining empty string fields
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        $rule = AttendanceRule::create($data);

        ActivityLog::log(
            'Attendance Rule Created',
            'Attendance Rule "' . $rule->rule_name . '" (' . $rule->shift_name . ') was created.'
        );

        return redirect()->route('attendance_rules.index')
            ->with('success', 'Attendance Rule "' . $rule->rule_name . '" created successfully.');
    }

    /**
     * Show the form for editing the specified rule.
     */
    public function edit($id)
    {
        $rule = AttendanceRule::findOrFail($id);
        
        // Ensure json-decoded arrays for view bind
        $rule->working_days = is_string($rule->working_days) ? json_decode($rule->working_days, true) : $rule->working_days;
        $rule->employee_ids = is_string($rule->employee_ids) ? json_decode($rule->employee_ids, true) : $rule->employee_ids;

        $current = 'settings';
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $employees = User::whereIn('role', [
            User::USER_ROLE_EMPLOYEE,
            User::USER_ROLE_HR_MANAGER,
            User::USER_ROLE_PAYROLL_MANAGER,
            User::USER_ROLE_DEPT_MANAGER
        ])->get()->mapWithKeys(function($user) {
            return [$user->id => $user->first_name . ' ' . $user->last_name . ' (' . $user->email . ')'];
        });

        $workingDaysList = [
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday',
            'Saturday' => 'Saturday',
            'Sunday' => 'Sunday'
        ];

        $breadcrumb = ['title' => $rule->rule_name, 'id' => $rule->id];

        return view('admin.attendance_rules.edit', compact('rule', 'current', 'departments', 'employees', 'workingDaysList', 'breadcrumb'));
    }

    /**
     * Update the specified rule in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'rule_name' => 'required|string|max:255',
            'shift_name' => 'required|string|max:255',
            'applies_to' => 'required|in:all_employees,branch,department,selected_employees',
            'working_days' => 'required|array',
            'check_in_start_time' => 'required',
            'check_in_cutoff_time' => 'required',
            'grace_period_minutes' => 'nullable|integer|min:0',
            'office_latitude' => 'nullable|numeric',
            'office_longitude' => 'nullable|numeric',
            'allowed_radius_meters' => 'nullable|integer|min:0',
            'minimum_work_duration_minutes' => 'nullable|integer|min:0',
        ]);

        $rule = AttendanceRule::findOrFail($id);
        $data = $request->all();
        $data['working_days'] = json_encode($request->input('working_days'));
        $data['selfie_required'] = $request->has('selfie_required');
        $data['checkout_selfie_required'] = $request->has('checkout_selfie_required');
        $data['device_lock_required'] = $request->has('device_lock_required');
        $data['auto_mark_absent'] = $request->has('auto_mark_absent');
        $data['auto_mark_missed_checkout'] = $request->has('auto_mark_missed_checkout');
        $data['check_out_enabled'] = $request->has('check_out_enabled');

        // Context-specific nullification
        if ($data['applies_to'] !== 'department') {
            $data['department_id'] = null;
        }
        if ($data['applies_to'] !== 'selected_employees') {
            $data['employee_ids'] = null;
        } else {
            $data['employee_ids'] = $request->input('employee_ids') ? json_encode($request->input('employee_ids')) : null;
        }

        if (!$data['check_out_enabled']) {
            $data['check_out_start_time'] = null;
            $data['check_out_cutoff_time'] = null;
            $data['minimum_work_duration_minutes'] = null;
        }

        // Clean up remaining empty string fields
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        $rule->update($data);

        ActivityLog::log(
            'Attendance Rule Updated',
            'Attendance Rule "' . $rule->rule_name . '" was updated.'
        );

        return redirect()->route('attendance_rules.index')
            ->with('success', 'Attendance Rule updated successfully.');
    }

    /**
     * Remove the specified rule from storage.
     */
    public function destroy($id)
    {
        $rule = AttendanceRule::findOrFail($id);
        $name = $rule->rule_name;
        $rule->delete();

        ActivityLog::log(
            'Attendance Rule Deleted',
            'Attendance Rule "' . $name . '" was deleted.'
        );

        return redirect()->route('attendance_rules.index')
            ->with('success', 'Attendance Rule "' . $name . '" deleted.');
    }
}
