<?php

namespace App\Modules\Pim\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeContactDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'street_1' => ['required'],
            'city' => ['required'],
            'zip' => ['required'],
            'country' => ['required'],
            'phone1' => ['required'],
            'emergency_contact_name' => ['nullable', 'string'],
            'emergency_contact_phone' => ['nullable', 'string'],
            'emergency_contact_relationship' => ['nullable', 'string']
        ];

        return $rules;
    }
}
