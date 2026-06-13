<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetRepositoryInterface as AssetRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetAssignmentRepositoryInterface as AssetAssignmentRepository;
use Carbon\Carbon;

class AssetsDashboardController extends Controller
{
    private $assetRepository;
    private $assetAssignmentRepository;

    public function __construct(AssetRepository $assetRepository, AssetAssignmentRepository $assetAssignmentRepository)
    {
        $this->assetRepository = $assetRepository;
        $this->assetAssignmentRepository = $assetAssignmentRepository;
    }

    public function index()
    {
        $totalAssets = $this->assetRepository->getCollection([])->count();
        $assignedAssets = $this->assetRepository->getCollection([['key' => 'current_status', 'operator' => '=', 'value' => 'Assigned']])->count();
        $availableAssets = $this->assetRepository->getCollection([['key' => 'current_status', 'operator' => '=', 'value' => 'Available']])->count();
        $maintenanceAssets = $this->assetRepository->getCollection([['key' => 'current_status', 'operator' => '=', 'value' => 'Under Maintenance']])->count();
        
        $today = Carbon::today()->format('Y-m-d');
        $overdueAssets = $this->assetAssignmentRepository->getCollection([
            ['key' => 'status', 'operator' => '=', 'value' => 'Active'],
            ['key' => 'expected_return_date', 'operator' => '<', 'value' => $today]
        ])->count();

        return view('assets::dashboard', compact('totalAssets', 'assignedAssets', 'availableAssets', 'maintenanceAssets', 'overdueAssets'));
    }
}
