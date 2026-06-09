<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HrPolicy;

class HrAssistantController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (\Gate::denies('use-assistant')) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $policies = HrPolicy::all();
        $suggestedQuestions = [
            "What is the company leave policy?",
            "Can I work remotely?",
            "What are the code of conduct guidelines?"
        ];
        $current = 'assistant';
        return view('admin.assistant', compact('policies', 'suggestedQuestions', 'current'));
    }

    public function ask(Request $request)
    {
        $this->validate($request, [
            'question' => 'required|string'
        ]);

        $service = new \App\Services\HrAssistantService();
        $response = $service->ask($request->input('question'));

        return response()->json($response);
    }
}
