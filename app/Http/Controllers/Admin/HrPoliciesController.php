<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\HrPolicy;
use App\ActivityLog;
use Illuminate\Http\Request;

class HrPoliciesController extends Controller
{
    /**
     * Display a listing of all HR policies.
     */
    public function index()
    {
        $policies = HrPolicy::orderBy('category')->orderBy('title')->get();
        $current = 'hr_policies';
        return view('admin.hr_policies.index', compact('policies', 'current'));
    }

    /**
     * Show the form for creating a new policy.
     */
    public function create()
    {
        $current = 'hr_policies';
        $categories = $this->getCategories();
        return view('admin.hr_policies.create', compact('current', 'categories'));
    }

    /**
     * Store a newly created policy in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        $policy = HrPolicy::create($request->only('title', 'content', 'category'));

        ActivityLog::log(
            'HR Policy Created',
            'HR Policy "' . $policy->title . '" (Category: ' . $policy->category . ') was created.'
        );

        return redirect()->route('hr_policies.edit', $policy->id)
            ->with('success', 'HR Policy "' . $policy->title . '" created successfully.');
    }

    /**
     * Show the form for editing the specified policy.
     */
    public function edit($id)
    {
        $policy = HrPolicy::findOrFail($id);
        $current = 'hr_policies';
        $categories = $this->getCategories();
        $breadcrumb = ['title' => $policy->title, 'id' => $policy->id];
        return view('admin.hr_policies.edit', compact('policy', 'current', 'categories', 'breadcrumb'));
    }

    /**
     * Update the specified policy in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        $policy = HrPolicy::findOrFail($id);
        $policy->update($request->only('title', 'content', 'category'));

        ActivityLog::log(
            'HR Policy Updated',
            'HR Policy "' . $policy->title . '" was updated.'
        );

        return redirect()->route('hr_policies.edit', $policy->id)
            ->with('success', 'HR Policy updated successfully.');
    }

    /**
     * Remove the specified policy from storage.
     */
    public function destroy(Request $request, $id)
    {
        $policy = HrPolicy::findOrFail($id);
        $title = $policy->title;
        $policy->delete();

        ActivityLog::log(
            'HR Policy Deleted',
            'HR Policy "' . $title . '" was deleted.'
        );

        return redirect()->route('hr_policies.index')
            ->with('success', 'HR Policy "' . $title . '" deleted.');
    }

    /**
     * Default policy categories.
     */
    private function getCategories(): array
    {
        return [
            'General'           => 'General',
            'Code of Conduct'   => 'Code of Conduct',
            'Leave & Absence'   => 'Leave & Absence',
            'Compensation'      => 'Compensation',
            'Health & Safety'   => 'Health & Safety',
            'IT & Data'         => 'IT & Data',
            'Recruitment'       => 'Recruitment',
            'Performance'       => 'Performance',
            'Disciplinary'      => 'Disciplinary',
        ];
    }
}
