<?php

namespace App\Modules\Employee\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetAssignmentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class EmployeeAssetsController extends Controller
{
    private $assignmentRepository;

    public function __construct(AssetAssignmentRepositoryInterface $assignmentRepository)
    {
        $this->assignmentRepository = $assignmentRepository;
    }

    public function index()
    {
        $employeeId = Auth::user()->id;
        $activeAssignments = $this->assignmentRepository->getActiveForEmployee($employeeId);
        $historyAssignments = $this->assignmentRepository->getHistoryForEmployee($employeeId);

        return view('employee.assets::index', compact('activeAssignments', 'historyAssignments'));
    }
}
