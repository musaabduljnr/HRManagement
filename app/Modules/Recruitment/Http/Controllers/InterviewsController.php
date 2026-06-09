<?php

namespace App\Modules\Recruitment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Recruitment\Repositories\Interfaces\InterviewRepositoryInterface as InterviewRepository;
use App\Modules\Recruitment\Models\CandidateApplication;
use Illuminate\Http\Request;
use Datatables;

class InterviewsController extends Controller
{
    private $interviewRepository;

    public function __construct(InterviewRepository $interviewRepository)
    {
        $this->interviewRepository = $interviewRepository;
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
        return view('recruitment::interviews.index', compact('current'));
    }

    public function getDatatable()
    {
        $query = $this->interviewRepository->getQry([], ['id', 'candidate_id', 'interview_date', 'interviewer_name', 'status']);
        
        return Datatables::of($query)
            ->addColumn('candidate', function($record) {
                $app = CandidateApplication::with('user')->find($record->candidate_id);
                return $app ? $app->user->first_name . ' ' . $app->user->last_name : 'N/A';
            })
            ->addColumn('actions', function($record) {
                return view('includes._datatable_actions', [
                    'deleteUrl' => route('recruitment.interviews.destroy', $record->id),
                    'editUrl' => route('recruitment.interviews.edit', $record->id)
                ]);
            })
            ->make(true);
    }

    public function create()
    {
        $candidates = CandidateApplication::with('user')->get()->pluck('user.full_name', 'id');
        $current = 'recruitment';
        return view('recruitment::interviews.create', compact('candidates', 'current'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'candidate_id' => 'required|integer',
            'interview_date' => 'required',
            'interviewer_name' => 'nullable|string|max:255',
            'status' => 'required|in:Scheduled,Completed,Cancelled',
            'notes' => 'nullable'
        ]);

        $this->interviewRepository->create($request->all());

        // Update candidate status to Interviewing if it was just Applied/Shortlisted
        $app = CandidateApplication::find($request->input('candidate_id'));
        if ($app && in_array($app->status, ['Applied', 'Shortlisted'])) {
            $app->status = 'Interviewing';
            $app->save();
        }

        $request->session()->flash('success', 'Interview successfully scheduled.');
        return redirect()->route('recruitment.interviews.index');
    }

    public function edit($id)
    {
        $interview = $this->interviewRepository->getById($id);
        $candidates = CandidateApplication::with('user')->get()->pluck('user.full_name', 'id');
        $breadcrumb = ['title' => 'Edit Interview #' . $interview->id, 'id' => $interview->id];
        $current = 'recruitment';
        
        // format date for datetime-local input
        $interview->interview_date = date('Y-m-d\TH:i', strtotime($interview->interview_date));
        
        return view('recruitment::interviews.edit', compact('interview', 'candidates', 'breadcrumb', 'current'));
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'candidate_id' => 'required|integer',
            'interview_date' => 'required',
            'interviewer_name' => 'nullable|string|max:255',
            'status' => 'required|in:Scheduled,Completed,Cancelled',
            'notes' => 'nullable'
        ]);

        $this->interviewRepository->update($id, $request->all());

        $request->session()->flash('success', 'Interview successfully updated.');
        return redirect()->route('recruitment.interviews.index');
    }

    public function destroy($id, Request $request)
    {
        $this->interviewRepository->delete($id);
        $request->session()->flash('success', 'Interview successfully deleted.');
        return redirect()->route('recruitment.interviews.index');
    }
}
