<?php

namespace App\Modules\Assets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetReturnRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'actual_return_date' => ['required', 'date'],
            'condition' => ['required', 'in:Excellent,Good,Fair,Poor,Damaged'],
            'assignment_notes' => ['nullable', 'string'],
        ];
    }
}
