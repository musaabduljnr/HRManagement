<?php

namespace App\Modules\Assets\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\AssetAssignmentRepository;
use Illuminate\Support\Facades\Auth;

class AssetsController extends Controller
{
    private $assignments;

    public function __construct(AssetAssignmentRepository $assignments)
    {
        $this->assignments = $assignments;
    }

    public function index()
    {
        $assignments = $this->assignments->getActiveForEmployee(Auth::id());

        return view('assets::employee.assets.index', compact('assignments'));
    }

    public function show($id)
    {
        $assignment = $this->assignments->getById($id);

        return view('assets::employee.assets.show', compact('assignment'));
    }
}
