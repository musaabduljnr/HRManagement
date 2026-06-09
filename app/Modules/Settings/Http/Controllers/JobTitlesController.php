<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Repositories\Interfaces\JobTitleRepositoryInterface as JobTitleRepository;
use App\Modules\Settings\Http\Requests\JobTitleRequest;
use Illuminate\Http\Request;
use Datatables;

class JobTitlesController extends Controller
{
    private $jobTitleRepository;

    public function __construct(JobTitleRepository $jobTitleRepository)
    {
        $this->jobTitleRepository = $jobTitleRepository;
    }

    public function index()
    {
        return view('settings::job_titles.index');
    }

    public function getDatatable()
    {
        return Datatables::of($this->jobTitleRepository->getQry([], ['id', 'name', 'description']))
            ->addColumn('actions', function($jobTitle){
                return view('includes._datatable_actions', [
                    'deleteUrl' => route('settings.job_titles.destroy', $jobTitle->id), 
                    'editUrl' => route('settings.job_titles.edit', $jobTitle->id)
                ]);
            })
            ->make();
    }

    public function create()
    {
        return view('settings::job_titles.create');
    }

    public function store(JobTitleRequest $request)
    {
        $jobTitleData = $this->jobTitleRepository->create($request->all());
        $request->session()->flash('success', 'Job title created successfully.');
        return redirect()->route('settings.job_titles.edit', $jobTitleData->id);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $jobTitle = $this->jobTitleRepository->getById($id);
        return view('settings::job_titles.edit', ['jobTitle' => $jobTitle, 'breadcrumb' => ['title' => $jobTitle->name, 'id' => $jobTitle->id]]);
    }

    public function update($id, JobTitleRequest $request)
    {
        $jobTitleData = $this->jobTitleRepository->update($id, $request->all());
        $request->session()->flash('success', 'Job title updated successfully.');
        return redirect()->route('settings.job_titles.edit', $jobTitleData->id);
    }

    public function destroy($id, Request $request)
    {
        $this->jobTitleRepository->delete($id);
        $request->session()->flash('success', 'Job title deleted successfully.');
        return redirect()->route('settings.job_titles.index');
    }
}
