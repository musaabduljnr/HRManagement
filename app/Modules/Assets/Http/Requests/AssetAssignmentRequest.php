<?php

namespace App\Modules\Assets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetAssignmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'employee_id' => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:issue_date',
            'actual_return_date' => 'nullable|date|after_or_equal:issue_date',
            'received_by' => 'nullable|string|max:255',
            'assignment_notes' => 'nullable|string',
            'status' => 'required|in:Active,Returned,Replaced,Lost',
        ];
    }
}
