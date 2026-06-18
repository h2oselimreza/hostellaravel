<?php

namespace App\Http\Requests\Admin\MasterData;

use Illuminate\Foundation\Http\FormRequest;

class CorporateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
                // 🔹 Basic Info
            'enrolled_apprv_code' => 'nullable|string|max:50',

            'title' => 'required|string|max:250',
            'package' => 'nullable|string|max:50',
            'membership_card' => 'nullable|string|max:20',

            // 🔹 Contact Info
            'address' => 'nullable|string',
            'company_email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:100',

            'company_mobile' => 'required|string|max:20',
            'company_land_phone' => 'nullable|string|max:20',

            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // 🔹 Location
            'division' => 'required|integer|exists:divisions,id',
            'district' => 'nullable|integer|exists:districts,id',
            'upozilla' => 'nullable|integer|exists:upazilas,id',

            'postal_code' => 'required|integer',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',

            // 🔹 Primary Contact
            'primary_contact_person' => 'nullable|string|max:200',
            'primary_contact_designation' => 'nullable|string|max:100',
            'primary_contact_mobile' => 'nullable|string|max:20',
            'primary_contact_email' => 'nullable|email|max:100',

            // 🔹 Secondary Contact
            'second_contact_person' => 'nullable|string|max:200',
            'second_contact_designation' => 'nullable|string|max:100',
            'second_contact_mobile' => 'nullable|string|max:20',
            'second_contact_email' => 'nullable|email|max:100',

            // 🔹 VTS Info
            'vts_company' => 'nullable|string|max:50',
            'vts_app_key' => 'nullable|string|max:100',
            'map_api_key' => 'nullable|string|max:100',

            // 🔹 Others
            'rm_id' => 'nullable|string|max:50',
            // 'status' => 'required|integer|in:0,1',
        ];
    }
}
