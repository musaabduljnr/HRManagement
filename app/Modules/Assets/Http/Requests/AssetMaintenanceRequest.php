<?php

namespace App\Modules\Assets\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetMaintenanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'asset_id' => 'required|exists:assets,id',
            'maintenance_type' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'service_provider' => 'nullable|string|max:255',
            'maintenance_date' => 'required|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:maintenance_date',
        ];
    }
}
