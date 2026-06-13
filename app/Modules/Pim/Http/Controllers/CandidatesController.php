<?php

namespace App\Modules\Pim\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Pim\Repositories\Interfaces\CandidateRepositoryInterface as CandidateRepository;
use App\Modules\Pim\Http\Requests\CandidateRequest;
use Illuminate\Http\Request;
use Datatables;

class CandidatesController extends Controller
{
    private $candidateRepository;

    public function __construct(CandidateRepository $candidateRepository)
    {
        $this->candidateRepository = $candidateRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pim::candidates.index');
    }

    /**
     * Return data for the resource list
     * 
     * @return \Illuminate\Http\Response
     */
    public function getDatatable()
    {
        return Datatables::of($this->candidateRepository->getCollection(
                [['key' => 'role', 'operator' => '=', 'value' => $this->candidateRepository->model::USER_ROLE_CANDIDATE]], 
                ['id', 'first_name', 'last_name', 'email']))
            ->addColumn('actions', function($employee){
                return view('includes._datatable_actions', [
                    'deleteUrl' => route('pim.candidates.destroy', $employee->id), 
                    'editUrl' => route('pim.candidates.edit', $employee->id)
                ]);
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * Marks a candidate as featured for easier access
     * 
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function makeFeatured($id, Request $request)
    {
        $candidate = $this->candidateRepository->getById($id);
        $featured = !$candidate->featured;
        $candidate->featured = $featured;
        $candidate->save();
        if($request->ajax()){
            return ['isFeatured' => (int)$featured];
        }
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jobOpenings = \App\Modules\Recruitment\Models\JobOpening::where('status', 'Open')->pluck('title', 'id');
        $application = null;
        return view('pim::candidates.create', compact('jobOpenings', 'application'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Modules\Pim\Http\Requests\CandidateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CandidateRequest $request)
    {
        $employeeData = $request->all();
        $employeeData['role'] = $this->candidateRepository->model::USER_ROLE_CANDIDATE;
        $employeeData = $this->candidateRepository->create($employeeData);
        
        $appData = [
            'user_id' => $employeeData->id,
            'job_opening_id' => $request->input('job_opening_id'),
            'status' => $request->input('status', 'Applied'),
            'notes' => $request->input('app_notes')
        ];
        if ($request->hasFile('resume')) {
            $path = $request->resume->store('uploads/resumes');
            $appData['resume_path'] = $path;
        }
        \App\Modules\Recruitment\Models\CandidateApplication::create($appData);

        $request->session()->flash('success', trans('app.pim.candidates.store_success'));
        return redirect()->route('pim.candidates.edit', $employeeData->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  integer  unique identifier for the resource
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  integer  unique identifier for the resource
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = $this->candidateRepository->getById($id);
        if($employee->role == $this->candidateRepository->model::USER_ROLE_EMPLOYEE) {
            return redirect()->route('pim.employees.edit', $id);
        }
        $jobOpenings = \App\Modules\Recruitment\Models\JobOpening::pluck('title', 'id');
        $application = \App\Modules\Recruitment\Models\CandidateApplication::where('user_id', $id)->first();

        return view('pim::candidates.edit', [
            'employee' => $employee,
            'jobOpenings' => $jobOpenings,
            'application' => $application,
            'breadcrumb' => [
                'title' => $employee->first_name.' '.$employee->last_name,
                'id' => $employee->id
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  integer  unique identifier for the resource
     * @param  \App\Modules\Pim\Http\Requests\CandidateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, CandidateRequest $request)
    {
        $employeeData = $this->candidateRepository->update($id, $request->all());
        
        $application = \App\Modules\Recruitment\Models\CandidateApplication::firstOrCreate(['user_id' => $id]);
        $appData = [
            'job_opening_id' => $request->input('job_opening_id'),
            'status' => $request->input('status'),
            'notes' => $request->input('app_notes')
        ];
        if ($request->hasFile('resume')) {
            $path = $request->resume->store('uploads/resumes');
            $appData['resume_path'] = $path;
        }
        $application->update($appData);

        $request->session()->flash('success', trans('app.pim.candidates.update_success'));
        return redirect()->route('pim.candidates.edit', $employeeData->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  unique identifier for the resource
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $this->candidateRepository->delete($id);
        $request->session()->flash('success', trans('app.pim.candidates.delete_success'));
        return redirect()->route('pim.candidates.index');
    }

    public function convertToEmployee($id, Request $request)
    {
        $candidateUser = $this->candidateRepository->getById($id);
        if ($candidateUser->role != $this->candidateRepository->model::USER_ROLE_CANDIDATE) {
            return redirect()->back()->with('error', 'User is not a candidate.');
        }

        $plainPassword = str_random(10);
        
        $candidateUser->role = $this->candidateRepository->model::USER_ROLE_EMPLOYEE;
        $candidateUser->password = bcrypt($plainPassword);
        $candidateUser->save();

        $application = \App\Modules\Recruitment\Models\CandidateApplication::where('user_id', $id)->first();
        if ($application) {
            $application->status = 'Hired';
            $application->save();
        }

        \Log::info("Candidate {$candidateUser->full_name} converted to employee. Credentials - Email: {$candidateUser->email}, Password: {$plainPassword}");

        $request->session()->flash('success', "Candidate successfully converted to Employee. Temp password: {$plainPassword}");
        return redirect()->route('pim.employees.edit', $id);
    }
}