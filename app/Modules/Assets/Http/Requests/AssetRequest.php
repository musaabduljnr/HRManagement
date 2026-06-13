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
            'asset_name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'current_status' => 'required|in:Available,Assigned,Under Maintenance,Damaged,Lost,Retired',
            'condition' => 'required|in:Excellent,Good,Fair,Poor,Damaged',
            'notes' => 'nullable|string',
            'image_file' => 'nullable|image|max:2048',
        ];
    }
}
