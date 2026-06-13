<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetAssignmentRepositoryInterface as AssetAssignmentRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetRepositoryInterface as AssetRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetHistoryRepositoryInterface as AssetHistoryRepository;
use App\Modules\Pim\Repositories\Interfaces\EmployeeRepositoryInterface as EmployeeRepository;
use Carbon\Carbon;
use Datatables;
use Illuminate\Http\Request;

class EmployeeAssetsController extends Controller
{
    private $assetAssignmentRepository;
    private $assetRepository;
    private $employeeRepository;
    private $assetHistoryRepository;

    public function __construct(
        AssetAssignmentRepository $assetAssignmentRepository,
        AssetRepository $assetRepository,
        EmployeeRepository $employeeRepository,
        AssetHistoryRepository $assetHistoryRepository
    ) {
        $this->assetAssignmentRepository = $assetAssignmentRepository;
        $this->assetRepository = $assetRepository;
        $this->employeeRepository = $employeeRepository;
        $this->assetHistoryRepository = $assetHistoryRepository;
    }

    public function index($employeeId)
    {
        $employee = $this->employeeRepository->getById($employeeId);
        $breadcrumb = ['parent_id' => $employee->id, 'parent_title' => $employee->first_name . ' ' . $employee->last_name];
        return view('assets::employee_assets.index', compact('employee', 'breadcrumb'));
    }

    public function getDatatable($employeeId)
    {
        $collection = $this->assetAssignmentRepository->getCollection([
            ['key' => 'employee_id', 'operator' => '=', 'value' => $employeeId]
        ], ['id', 'asset_id', 'issue_date', 'expected_return_date', 'actual_return_date', 'status']);

        return Datatables::of($collection)
            ->editColumn('asset_id', function ($assignment) {
                return $assignment->asset ? $assignment->asset->asset_code . ' - ' . $assignment->asset->asset_name : 'N/A';
            })
            ->editColumn('status', function ($assignment) {
                $status = strtolower($assignment->status);
                $classes = [
                    'active' => 'label-primary',
                    'returned' => 'label-success',
                    'replaced' => 'label-info',
                    'lost' => 'label-danger'
                ];
                $class = isset($classes[$status]) ? $classes[$status] : 'label-default';
                return '<span class="label ' . $class . '">' . $assignment->status . '</span>';
            })
            ->addColumn('actions', function ($assignment) use ($employeeId) {
                $status = strtolower($assignment->status);
                $deleteUrl = $status == 'active' ? route('pim.employees.assets.destroy', [$employeeId, $assignment->id]) : null;
                return view('includes._datatable_actions', [
                    'deleteUrl' => $deleteUrl // Using Delete URL to perform check-in / return
                ]);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create($employeeId)
    {
        $employee = $this->employeeRepository->getById($employeeId);
        $assets = $this->assetRepository->getCollection([['key' => 'current_status', 'operator' => '=', 'value' => 'Available']])
            ->mapWithKeys(function ($asset) {
                return [$asset->id => $asset->asset_code . ' - ' . $asset->asset_name];
            })->toArray();
        $breadcrumb = ['parent_id' => $employee->id, 'parent_title' => $employee->first_name . ' ' . $employee->last_name];
        return view('assets::employee_assets.create', compact('employee', 'assets', 'breadcrumb'));
    }

    public function store($employeeId, Request $request)
    {
        $this->validate($request, [
            'asset_id' => 'required|exists:assets,id',
            'issue_date' => 'required|date'
        ]);

        $employee = $this->employeeRepository->getById($employeeId);

        $assignmentData = [
            'asset_id' => $request->asset_id,
            'employee_id' => $employeeId,
            'issue_date' => $request->issue_date,
            'expected_return_date' => $request->expected_return_date,
            'assigned_by' => auth()->id(),
            'assignment_notes' => $request->assignment_notes,
            'status' => 'Active'
        ];

        // Create assignment
        $this->assetAssignmentRepository->create($assignmentData);

        // Update asset status
        $this->assetRepository->update($request->asset_id, ['current_status' => 'Assigned']);

        // Log history
        $this->assetHistoryRepository->create([
            'asset_id' => $request->asset_id,
            'action_type' => 'Assigned',
            'performed_by' => auth()->id(),
            'new_value' => 'Employee: ' . $employee->first_name . ' ' . $employee->last_name,
            'remarks' => 'Asset assigned via PIM employee profile.'
        ]);

        $request->session()->flash('success', 'Asset checked out to employee successfully.');
        return redirect()->route('pim.employees.assets.index', $employeeId);
    }

    public function destroy($employeeId, $id, Request $request)
    {
        $assignment = $this->assetAssignmentRepository->getById($id);
        
        $this->assetAssignmentRepository->update($id, [
            'status' => 'Returned',
            'actual_return_date' => Carbon::today()->format('Y-m-d')
        ]);

        $this->assetRepository->update($assignment->asset_id, ['current_status' => 'Available']);

        // Log history
        $this->assetHistoryRepository->create([
            'asset_id' => $assignment->asset_id,
            'action_type' => 'Returned',
            'performed_by' => auth()->id(),
            'old_value' => 'Employee: ' . $assignment->employee->first_name . ' ' . $assignment->employee->last_name,
            'new_value' => 'Available',
            'remarks' => 'Asset checked in via PIM employee profile.'
        ]);

        $request->session()->flash('success', 'Asset checked in successfully.');
        return redirect()->route('pim.employees.assets.index', $employeeId);
    }
}
