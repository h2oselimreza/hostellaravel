<?php

namespace App\Http\Requests\Admin\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // 1. Check if we are updating by looking for your route parameter
        // (e.g., if your route is /admin/vendors/{vendor_code})
        $vendorCode = $this->route('vendor'); 

        $uniqueRule = Rule::unique('vendor')->where(fn ($query) => 
            $query->where('title', $this->title)
                  ->where('vendor_mobile', $this->vendor_mobile)
        );

        // 2. If it's an update request, tell Laravel to ignore the current record
        if ($vendorCode) {
            $uniqueRule->ignore($vendorCode, 'vendor_code'); 
            // Note: If your route passes an ID instead of the code, use: $uniqueRule->ignore($vendorId)
        }

        return [
            'title' => 'required|string|max:255',
            'vendor_mobile' => [
                'required',
                'string',
                'max:20',
                $uniqueRule // 3. Pass the dynamic unique rule here
            ],
            'vendor_email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'vendor_land_phone' => 'nullable|string|max:20',
            'division' => 'nullable|integer',
            'district' => 'nullable|integer',
            'upozilla' => 'nullable|integer',
            'postal_code' => 'nullable|integer|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'primary_contact_person' => 'nullable|string|max:255',
            'primary_contact_designation' => 'nullable|string|max:255',
            'primary_contact_mobile' => 'nullable|string|max:20',
            'primary_contact_email' => 'nullable|email|max:255',
            'second_contact_person' => 'nullable|string|max:255', 
            'second_contact_designation' => 'nullable|string|max:255',
            'second_contact_mobile' => 'nullable|string|max:20',
            'second_contact_email' => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'vendor_mobile.unique' => 'A vendor with this title and mobile number already exists.',
        ];
    }
}
