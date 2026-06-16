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
            'asset_id'               => ['required', 'exists:assets,id'],
            'user_id'                => ['required', 'exists:users,id'],
            'assigned_date'          => ['required', 'date'],
            'expected_return_date'   => ['nullable', 'date', 'after_or_equal:assigned_date'],
            'condition_at_assignment' => ['required', 'in:excellent,good,fair,poor'],
            'notes'                  => ['nullable'],
        ];
    }
}
