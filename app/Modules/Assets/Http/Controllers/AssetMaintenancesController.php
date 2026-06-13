<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetMaintenanceRepositoryInterface as AssetMaintenanceRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetRepositoryInterface as AssetRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetHistoryRepositoryInterface as AssetHistoryRepository;
use App\Modules\Assets\Http\Requests\AssetMaintenanceRequest;
use Datatables;
use Illuminate\Http\Request;

class AssetMaintenancesController extends Controller
{
    private $assetMaintenanceRepository;
    private $assetRepository;
    private $assetHistoryRepository;

    public function __construct(
        AssetMaintenanceRepository $assetMaintenanceRepository,
        AssetRepository $assetRepository,
        AssetHistoryRepository $assetHistoryRepository
    ) {
        $this->assetMaintenanceRepository = $assetMaintenanceRepository;
        $this->assetRepository = $assetRepository;
        $this->assetHistoryRepository = $assetHistoryRepository;
    }

    public function index()
    {
        return view('assets::maintenances.index');
    }

    public function getDatatable()
    {
        return Datatables::of($this->assetMaintenanceRepository->getCollection([], ['id', 'asset_id', 'maintenance_type', 'cost', 'service_provider', 'maintenance_date']))
            ->editColumn('asset_id', function ($maintenance) {
                return $maintenance->asset ? $maintenance->asset->asset_code . ' - ' . $maintenance->asset->asset_name : 'N/A';
            })
            ->editColumn('cost', function ($maintenance) {
                return '₦' . number_format($maintenance->cost, 2);
            })
            ->addColumn('actions', function ($maintenance) {
                return view('includes._datatable_actions', [
                    'deleteUrl' => route('assets.maintenances.destroy', $maintenance->id),
                    'editUrl' => route('assets.maintenances.edit', $maintenance->id)
                ]);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create()
    {
        $assets = $this->assetRepository->getCollection()
            ->mapWithKeys(function ($asset) {
                return [$asset->id => $asset->asset_code . ' - ' . $asset->asset_name];
            })->toArray();
        return view('assets::maintenances.create', compact('assets'));
    }

    public function store(AssetMaintenanceRequest $request)
    {
        $data = $request->all();
        $maintenance = $this->assetMaintenanceRepository->create($data);

        // Update asset status
        $this->assetRepository->update($request->asset_id, ['current_status' => 'Under Maintenance']);

        // Log history
        $this->assetHistoryRepository->create([
            'asset_id' => $request->asset_id,
            'action_type' => 'Maintenance',
            'performed_by' => auth()->id(),
            'remarks' => 'Sent to maintenance: ' . $request->maintenance_type . ' by ' . ($request->service_provider ?: 'unknown provider') . '. Cost: ₦' . number_format($request->cost, 2)
        ]);

        $request->session()->flash('success', 'Maintenance record created and asset set to "Under Maintenance".');
        return redirect()->route('assets.maintenances.index');
    }

    public function edit($id)
    {
        $maintenance = $this->assetMaintenanceRepository->getById($id);
        $assets = $this->assetRepository->getCollection()
            ->mapWithKeys(function ($asset) {
                return [$asset->id => $asset->asset_code . ' - ' . $asset->asset_name];
            })->toArray();
        $breadcrumb = ['title' => 'Log #' . $maintenance->id, 'id' => $maintenance->id];
        return view('assets::maintenances.edit', compact('maintenance', 'assets', 'breadcrumb'));
    }

    public function update($id, AssetMaintenanceRequest $request)
    {
        $data = $request->all();
        $this->assetMaintenanceRepository->update($id, $data);

        $request->session()->flash('success', 'Maintenance record updated successfully.');
        return redirect()->route('assets.maintenances.index');
    }

    public function destroy($id, Request $request)
    {
        $maintenance = $this->assetMaintenanceRepository->getById($id);
        // Change status back to Available
        $this->assetRepository->update($maintenance->asset_id, ['current_status' => 'Available']);

        $this->assetMaintenanceRepository->delete($id);
        $request->session()->flash('success', 'Maintenance record deleted.');
        return redirect()->route('assets.maintenances.index');
    }
}
