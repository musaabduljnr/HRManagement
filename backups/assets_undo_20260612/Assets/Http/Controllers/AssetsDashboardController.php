<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\AssetRepository;

class AssetsDashboardController extends Controller
{
    private $assets;

    public function __construct(AssetRepository $assets)
    {
        $this->assets = $assets;
    }

    public function index()
    {
        $statistics = $this->assets->getDashboardCounts();

        return view('assets::dashboard.index', compact('statistics'));
    }
}
