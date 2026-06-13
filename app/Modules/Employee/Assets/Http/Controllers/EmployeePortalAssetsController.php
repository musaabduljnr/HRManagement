<?php

namespace App\Modules\Employee\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetAssignmentRepositoryInterface as AssetAssignmentRepository;
use Illuminate\Http\Request;

class EmployeePortalAssetsController extends Controller
{
    private $assetAssignmentRepository;

    public function __construct(AssetAssignmentRepository $assetAssignmentRepository)
    {
        $this->assetAssignmentRepository = $assetAssignmentRepository;
    }

    public function index()
    {
        $assignments = $this->assetAssignmentRepository->getCollection([
            ['key' => 'employee_id', 'operator' => '=', 'value' => auth()->id()],
            ['key' => 'status', 'operator' => '=', 'value' => 'Active']
        ]);
        return view('employee.assets::index', compact('assignments'));
    }

    public function show($id)
    {
        $assignment = $this->assetAssignmentRepository->getCollection([
            ['key' => 'id', 'operator' => '=', 'value' => $id],
            ['key' => 'employee_id', 'operator' => '=', 'value' => auth()->id()]
        ])->first();

        if (!$assignment) {
            abort(403, 'Unauthorized access.');
        }

        $asset = $assignment->asset;
        return view('employee.assets::show', compact('asset', 'assignment'));
    }
}
