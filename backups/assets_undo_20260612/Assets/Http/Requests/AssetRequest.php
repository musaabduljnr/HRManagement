<?php

namespace App\Modules\Assets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'            => ['required', 'max:255'],
            'category_id'     => ['required', 'exists:asset_categories,id'],
            'brand'           => ['nullable', 'max:255'],
            'model'           => ['nullable', 'max:255'],
            'serial_number'   => ['nullable', 'max:255'],
            'description'     => ['nullable'],
            'status'          => ['required', 'in:available,assigned,maintenance,retired,lost'],
            'condition'       => ['required', 'in:excellent,good,fair,poor'],
            'purchase_date'   => ['nullable', 'date'],
            'purchase_cost'   => ['nullable', 'numeric', 'min:0'],
            'vendor'          => ['nullable', 'max:255'],
            'warranty_expiry' => ['nullable', 'date'],
            'location'        => ['nullable', 'max:255'],
            'notes'           => ['nullable'],
            'image'           => ['nullable', 'image', 'max:2048'],
        ];
    }
}
