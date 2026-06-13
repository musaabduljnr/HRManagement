<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetRepositoryInterface as AssetRepository;
use App\Modules\Assets\Repositories\Interfaces\AssetCategoryRepositoryInterface as AssetCategoryRepository;
use Illuminate\Http\Request;

class AssetReportsController extends Controller
{
    private $assetRepository;
    private $assetCategoryRepository;

    public function __construct(AssetRepository $assetRepository, AssetCategoryRepository $assetCategoryRepository)
    {
        $this->assetRepository = $assetRepository;
        $this->assetCategoryRepository = $assetCategoryRepository;
    }

    public function index(Request $request)
    {
        $categories = $this->assetCategoryRepository->getCollection()->pluck('name', 'id')->toArray();
        
        $assets = $this->assetRepository->getCollection();
        if ($request->has('category_id') && $request->category_id != '') {
            $assets = $assets->where('category_id', $request->category_id);
        }
        if ($request->has('current_status') && $request->current_status != '') {
            $assets = $assets->where('current_status', $request->current_status);
        }
        if ($request->has('condition') && $request->condition != '') {
            $assets = $assets->where('condition', $request->condition);
        }

        return view('assets::reports.index', compact('categories', 'assets'));
    }

    public function export(Request $request)
    {
        $assets = $this->assetRepository->getCollection();
        if ($request->has('category_id') && $request->category_id != '') {
            $assets = $assets->where('category_id', $request->category_id);
        }
        if ($request->has('current_status') && $request->current_status != '') {
            $assets = $assets->where('current_status', $request->current_status);
        }
        if ($request->has('condition') && $request->condition != '') {
            $assets = $assets->where('condition', $request->condition);
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="assets_inventory_report_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($assets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Asset Code', 'Asset Name', 'Category', 'Brand', 'Model', 'Serial Number', 'Status', 'Condition', 'Purchase Cost', 'Purchase Date']);
            
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->asset_code,
                    $asset->asset_name,
                    $asset->category ? $asset->category->name : 'N/A',
                    $asset->brand ?: '',
                    $asset->model ?: '',
                    $asset->serial_number ?: '',
                    $asset->current_status,
                    $asset->condition,
                    $asset->purchase_cost ? '₦' . number_format($asset->purchase_cost, 2) : '',
                    $asset->purchase_date ? (is_string($asset->purchase_date) ? $asset->purchase_date : $asset->purchase_date->format('Y-m-d')) : ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
