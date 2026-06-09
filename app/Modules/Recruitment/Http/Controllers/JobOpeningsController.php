<?php

namespace App\Modules\Recruitment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Recruitment\Repositories\Interfaces\JobOpeningRepositoryInterface as JobOpeningRepository;
use App\Modules\Settings\Repositories\Interfaces\DepartmentRepositoryInterface as DepartmentRepository;
use Illuminate\Http\Request;
use Datatables;

class JobOpeningsController extends Controller
{
    private $jobOpeningRepository;

    public function __construct(JobOpeningRepository $jobOpeningRepository)
    {
        $this->jobOpeningRepository = $jobOpeningRepository;
        $this->middleware(function ($request, $next) {
            if (\Gate::denies('manage-recruitment')) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $current = 'recruitment';
        return view('recruitment::job_openings.index', compact('current'));
    }

    public function getDatatable()
    {
        $query = $this->jobOpeningRepository->getQry([], ['id', 'title', 'status', 'department_id']);
        
        return Datatables::of($query)
            ->addColumn('department', function($record) {
                $dep = \App\Modules\Settings\Models\Department::find($record->department_id);
                return $dep ? $dep->name : 'N/A';
            })
            ->addColumn('actions', function($record) {
                return view('includes._datatable_actions', [
                    'deleteUrl' => route('recruitment.job-openings.destroy', $record->id),
                    'editUrl' => route('recruitment.job-openings.edit', $record->id)
                ]);
            })
            ->make(true);
    }

    public function create(DepartmentRepository $departmentRepository)
    {
        $departments = $departmentRepository->getAll()->pluck('name', 'id');
        $current = 'recruitment';
        return view('recruitment::job_openings.create', compact('departments', 'current'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required',
            'status' => 'required|in:Open,Closed,Draft',
            'department_id' => 'nullable|integer'
        ]);

        $this->jobOpeningRepository->create($request->all());

        $request->session()->flash('success', 'Job opening successfully created.');
        return redirect()->route('recruitment.job-openings.index');
    }

    public function edit($id, DepartmentRepository $departmentRepository)
    {
        $jobOpening = $this->jobOpeningRepository->getById($id);
        $departments = $departmentRepository->getAll()->pluck('name', 'id');
        $breadcrumb = ['title' => 'Edit ' . $jobOpening->title, 'id' => $jobOpening->id];
        $current = 'recruitment';
        return view('recruitment::job_openings.edit', compact('jobOpening', 'departments', 'breadcrumb', 'current'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required',
            'status' => 'required|in:Open,Closed,Draft',
            'department_id' => 'nullable|integer'
        ]);

        $this->jobOpeningRepository->update($id, $request->all());

        $request->session()->flash('success', 'Job opening successfully updated.');
        return redirect()->route('recruitment.job-openings.index');
    }

    public function destroy($id, Request $request)
    {
        $this->jobOpeningRepository->delete($id);
        $request->session()->flash('success', 'Job opening successfully deleted.');
        return redirect()->route('recruitment.job-openings.index');
    }
}
