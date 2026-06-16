<?php

namespace App\Modules\Assets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'        => ['required', 'max:255'],
            'description' => ['nullable'],
            'icon'        => ['nullable', 'max:100'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }
}
