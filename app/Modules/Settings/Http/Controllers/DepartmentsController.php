<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Repositories\Interfaces\DepartmentRepositoryInterface as DepartmentRepository;
use App\Modules\Settings\Http\Requests\DepartmentRequest;
use Illuminate\Http\Request;
use Datatables;

class DepartmentsController extends Controller
{
    private $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function index()
    {
        return view('settings::departments.index');
    }

    public function getDatatable()
    {
        return Datatables::of($this->departmentRepository->getQry([], ['id', 'name', 'description']))
            ->addColumn('actions', function($department){
                return view('includes._datatable_actions', [
                    'deleteUrl' => route('settings.departments.destroy', $department->id), 
                    'editUrl' => route('settings.departments.edit', $department->id)
                ]);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create()
    {
        return view('settings::departments.create');
    }

    public function store(DepartmentRequest $request)
    {
        $departmentData = $this->departmentRepository->create($request->all());
        $request->session()->flash('success', 'Department created successfully.');
        return redirect()->route('settings.departments.edit', $departmentData->id);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $department = $this->departmentRepository->getById($id);
        return view('settings::departments.edit', ['department' => $department, 'breadcrumb' => ['title' => $department->name, 'id' => $department->id]]);
    }

    public function update($id, DepartmentRequest $request)
    {
        $departmentData = $this->departmentRepository->update($id, $request->all());
        $request->session()->flash('success', 'Department updated successfully.');
        return redirect()->route('settings.departments.edit', $departmentData->id);
    }

    public function destroy($id, Request $request)
    {
        $this->departmentRepository->delete($id);
        $request->session()->flash('success', 'Department deleted successfully.');
        return redirect()->route('settings.departments.index');
    }
}
