<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetAssignmentRepositoryInterface as AssetAssignmentRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetRepositoryInterface as AssetRepository;
use App\Modules\Pim\Repositories\Interfaces\EmployeeRepositoryInterface as EmployeeRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetHistoryRepositoryInterface as AssetHistoryRepository;
use App\Modules\Assets\Http\Requests\AssetAssignmentRequest;
use Carbon\Carbon;
use Datatables;
use Illuminate\Http\Request;

class AssetAssignmentsController extends Controller
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

    public function index()
    {
        return view('assets::assignments.index');
    }

    public function getDatatable()
    {
        return Datatables::of($this->assetAssignmentRepository->getCollection([], ['id', 'asset_id', 'employee_id', 'issue_date', 'expected_return_date', 'actual_return_date', 'status']))
            ->editColumn('asset_id', function ($assignment) {
                return $assignment->asset ? $assignment->asset->asset_code . ' - ' . $assignment->asset->asset_name : 'N/A';
            })
            ->editColumn('employee_id', function ($assignment) {
                return $assignment->employee ? $assignment->employee->first_name . ' ' . $assignment->employee->last_name : 'N/A';
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
            ->addColumn('actions', function ($assignment) {
                $status = strtolower($assignment->status);
                $returnUrl = $status == 'active' ? route('assets.assignments.return', $assignment->id) : null;
                return view('includes._datatable_actions', [
                    'deleteUrl' => route('assets.assignments.destroy', $assignment->id),
                    'editUrl' => route('assets.assignments.edit', $assignment->id),
                    'approveUrl' => $returnUrl // Map return URL to the approve action button dynamically
                ]);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create()
    {
        $assets = $this->assetRepository->getCollection([['key' => 'current_status', 'operator' => '=', 'value' => 'Available']])
            ->mapWithKeys(function ($asset) {
                return [$asset->id => $asset->asset_code . ' - ' . $asset->asset_name];
            })->toArray();

        $employees = $this->employeeRepository->pluckName()->toArray();
        return view('assets::assignments.create', compact('assets', 'employees'));
    }

    public function store(AssetAssignmentRequest $request)
    {
        $data = $request->all();
        $data['assigned_by'] = auth()->id();

        // Create assignment
        $assignment = $this->assetAssignmentRepository->create($data);

        // Update asset status
        $this->assetRepository->update($request->asset_id, ['current_status' => 'Assigned']);

        // Log history
        $this->assetHistoryRepository->create([
            'asset_id' => $request->asset_id,
            'action_type' => 'Assigned',
            'performed_by' => auth()->id(),
            'new_value' => 'Employee: ' . $assignment->employee->first_name . ' ' . $assignment->employee->last_name,
            'remarks' => 'Asset checked out to employee. Expected Return: ' . ($request->expected_return_date ?: 'None')
        ]);

        $request->session()->flash('success', 'Asset assigned successfully.');
        return redirect()->route('assets.assignments.index');
    }

    public function edit($id)
    {
        $assignment = $this->assetAssignmentRepository->getById($id);
        $assets = $this->assetRepository->getCollection()
            ->mapWithKeys(function ($asset) {
                return [$asset->id => $asset->asset_code . ' - ' . $asset->asset_name];
            })->toArray();

        $employees = $this->employeeRepository->pluckName()->toArray();
        $breadcrumb = ['title' => 'Assignment #' . $assignment->id, 'id' => $assignment->id];
        return view('assets::assignments.edit', compact('assignment', 'assets', 'employees', 'breadcrumb'));
    }

    public function update($id, AssetAssignmentRequest $request)
    {
        $assignment = $this->assetAssignmentRepository->getById($id);
        $oldStatus = $assignment->status;

        $data = $request->all();
        $this->assetAssignmentRepository->update($id, $data);

        // Update asset status based on new assignment status
        if ($oldStatus != $request->status) {
            $assetStatus = 'Available';
            if ($request->status == 'Active') {
                $assetStatus = 'Assigned';
            } elseif ($request->status == 'Lost') {
                $assetStatus = 'Lost';
            }

            $this->assetRepository->update($assignment->asset_id, ['current_status' => $assetStatus]);

            // Log history
            $this->assetHistoryRepository->create([
                'asset_id' => $assignment->asset_id,
                'action_type' => 'Assignment Updated',
                'performed_by' => auth()->id(),
                'old_value' => $oldStatus,
                'new_value' => $request->status,
                'remarks' => 'Assignment status updated via form edit. Notes: ' . ($request->assignment_notes ?: 'None')
            ]);
        }

        $request->session()->flash('success', 'Assignment updated successfully.');
        return redirect()->route('assets.assignments.index');
    }

    public function returnForm($id)
    {
        $assignment = $this->assetAssignmentRepository->getById($id);
        $conditions = [
            'Excellent' => 'Excellent',
            'Good' => 'Good',
            'Fair' => 'Fair',
            'Damaged' => 'Damaged',
            'Lost' => 'Lost'
        ];
        return view('assets::assignments.return', compact('assignment', 'conditions'));
    }

    public function processReturn($id, Request $request)
    {
        $this->validate($request, [
            'actual_return_date' => 'required|date',
            'condition' => 'required|string',
            'assignment_notes' => 'nullable|string'
        ]);

        $assignment = $this->assetAssignmentRepository->getById($id);
        
        $this->assetAssignmentRepository->update($id, [
            'status' => 'Returned',
            'actual_return_date' => $request->actual_return_date,
            'assignment_notes' => $request->assignment_notes
        ]);

        // Update asset status and condition
        $assetStatus = 'Available';
        if ($request->condition == 'Damaged') {
            $assetStatus = 'Damaged';
        } elseif ($request->condition == 'Lost') {
            $assetStatus = 'Lost';
        }

        $this->assetRepository->update($assignment->asset_id, [
            'current_status' => $assetStatus,
            'condition' => $request->condition
        ]);

        // Log history
        $this->assetHistoryRepository->create([
            'asset_id' => $assignment->asset_id,
            'action_type' => 'Returned',
            'performed_by' => auth()->id(),
            'old_value' => 'Employee: ' . $assignment->employee->first_name . ' ' . $assignment->employee->last_name,
            'new_value' => $assetStatus,
            'remarks' => 'Asset checked in / returned to inventory. Condition: ' . $request->condition . '. Remarks: ' . ($request->assignment_notes ?: 'None')
        ]);

        $request->session()->flash('success', 'Asset checked in successfully.');
        
        if ($request->has('redirect_to_employee')) {
            return redirect()->route('pim.employees.assets.index', $assignment->employee_id);
        }

        return redirect()->route('assets.assignments.index');
    }

    public function destroy($id, Request $request)
    {
        $assignment = $this->assetAssignmentRepository->getById($id);
        // Revert asset status to Available if it was active
        if ($assignment->status == 'Active') {
            $this->assetRepository->update($assignment->asset_id, ['current_status' => 'Available']);
        }
        $this->assetAssignmentRepository->delete($id);
        $request->session()->flash('success', 'Assignment record deleted.');
        return redirect()->route('assets.assignments.index');
    }
}
