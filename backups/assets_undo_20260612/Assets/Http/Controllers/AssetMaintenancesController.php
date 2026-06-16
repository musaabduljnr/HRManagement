<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;

class AssetMaintenancesController extends Controller
{
    public function index()
    {
        return view('assets::maintenances.index');
    }
}
