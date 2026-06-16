<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;

class AssetAssignmentsController extends Controller
{
    public function index()
    {
        return view('assets::assignments.index');
    }
}
