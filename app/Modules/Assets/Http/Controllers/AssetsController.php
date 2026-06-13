<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetRepositoryInterface as AssetRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetCategoryRepositoryInterface as AssetCategoryRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetHistoryRepositoryInterface as AssetHistoryRepository;
use App\Modules\Assets\Http\Requests\AssetRequest;
use Datatables;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
    private $assetRepository;
    private $assetCategoryRepository;
    private $assetHistoryRepository;

    public function __construct(
        AssetRepository $assetRepository,
        AssetCategoryRepository $assetCategoryRepository,
        AssetHistoryRepository $assetHistoryRepository
    ) {
        $this->assetRepository = $assetRepository;
        $this->assetCategoryRepository = $assetCategoryRepository;
        $this->assetHistoryRepository = $assetHistoryRepository;
    }

    public function index()
    {
        return view('assets::list.index');
    }

    public function getDatatable()
    {
        return Datatables::of($this->assetRepository->getCollection([], ['id', 'asset_code', 'asset_name', 'category_id', 'brand', 'model', 'serial_number', 'current_status', 'condition']))
            ->editColumn('category_id', function ($asset) {
                return $asset->category ? $asset->category->name : 'N/A';
            })
            ->editColumn('current_status', function ($asset) {
                $status = strtolower($asset->current_status);
                $classes = [
                    'available' => 'label-success',
                    'assigned' => 'label-primary',
                    'under maintenance' => 'label-warning',
                    'damaged' => 'label-danger',
                    'lost' => 'label-danger',
                    'retired' => 'label-default'
                ];
                $class = isset($classes[$status]) ? $classes[$status] : 'label-default';
                return '<span class="label ' . $class . '">' . $asset->current_status . '</span>';
            })
            ->addColumn('actions', function ($asset) {
                return view('includes._datatable_actions', [
                    'showUrl' => route('assets.list.show', $asset->id),
                    'editUrl' => route('assets.list.edit', $asset->id),
                    'deleteUrl' => route('assets.list.destroy', $asset->id)
                ]);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create()
    {
        $categories = $this->assetCategoryRepository->getCollection([['key' => 'status', 'operator' => '=', 'value' => 'Active']])->pluck('name', 'id');
        return view('assets::list.create', compact('categories'));
    }

    public function store(AssetRequest $request)
    {
        $data = $request->except(['image_file']);
        if ($request->hasFile('image_file')) {
            $path = $request->image_file->store('uploads/assets');
            $data['image'] = $path;
        }

        $asset = $this->assetRepository->create($data);

        // Log history
        $this->assetHistoryRepository->create([
            'asset_id' => $asset->id,
            'action_type' => 'Created',
            'performed_by' => auth()->id(),
            'remarks' => 'Asset registered in database.'
        ]);

        $request->session()->flash('success', 'Asset created successfully.');
        return redirect()->route('assets.list.index');
    }

    public function show($id)
    {
        $asset = $this->assetRepository->getById($id);
        $histories = $asset->histories()->with('user')->get();
        $breadcrumb = ['title' => $asset->asset_code . ' (' . $asset->asset_name . ')', 'id' => $asset->id];
        return view('assets::list.show', compact('asset', 'histories', 'breadcrumb'));
    }

    public function edit($id)
    {
        $asset = $this->assetRepository->getById($id);
        $categories = $this->assetCategoryRepository->getCollection([['key' => 'status', 'operator' => '=', 'value' => 'Active']])->pluck('name', 'id');
        $breadcrumb = ['title' => $asset->asset_code, 'id' => $asset->id];
        return view('assets::list.edit', compact('asset', 'categories', 'breadcrumb'));
    }

    public function update($id, AssetRequest $request)
    {
        $asset = $this->assetRepository->getById($id);
        $oldStatus = $asset->current_status;
        $oldCondition = $asset->condition;

        $data = $request->except(['image_file']);
        if ($request->hasFile('image_file')) {
            $path = $request->image_file->store('uploads/assets');
            $data['image'] = $path;
        }

        $this->assetRepository->update($id, $data);
        $newAsset = $this->assetRepository->getById($id);

        // Log history on status change
        if ($oldStatus != $newAsset->current_status) {
            $this->assetHistoryRepository->create([
                'asset_id' => $id,
                'action_type' => 'Status Updated',
                'performed_by' => auth()->id(),
                'old_value' => $oldStatus,
                'new_value' => $newAsset->current_status,
                'remarks' => 'Asset status changed directly via edit form.'
            ]);
        }

        // Log history on condition change
        if ($oldCondition != $newAsset->condition) {
            $this->assetHistoryRepository->create([
                'asset_id' => $id,
                'action_type' => 'Condition Updated',
                'performed_by' => auth()->id(),
                'old_value' => $oldCondition,
                'new_value' => $newAsset->condition,
                'remarks' => 'Asset condition changed directly via edit form.'
            ]);
        }

        $request->session()->flash('success', 'Asset updated successfully.');
        return redirect()->route('assets.list.index');
    }

    public function destroy($id, Request $request)
    {
        $this->assetRepository->delete($id);
        $request->session()->flash('success', 'Asset deleted successfully.');
        return redirect()->route('assets.list.index');
    }
}
