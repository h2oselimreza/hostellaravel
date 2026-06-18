<?php

namespace App\Http\Requests\Client\Employee;

use Illuminate\Foundation\Http\FormRequest;

class ClientEmployeeRequest extends FormRequest
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
        $id = $this->route('id');

        return [

            // Required
            'employee_name' => 'required|string|max:255',
            'primary_mobile' => 'required|string|max:20',
            'driving_license_no' => 'required|string|max:100',

            // Optional
            'national_id' => 'nullable|string|max:50',
            'gender' => 'nullable|in:male,female',
            'religion' => 'nullable|in:Islam,Hindu,Christian,Buddhist',
            'nationality' => 'nullable|string|max:100',

            'dob' => 'nullable|date|before:today',
            'blood_group' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',

            'marital_status' => 'nullable|in:Single,Married',
            'anniversary' => 'nullable|date|required_if:marital_status,Married',

            'passport_no' => 'nullable|string|max:100',
            'passposrt_expiry_date' => 'nullable|date',

            'driving_license_expiry_date' => 'nullable|date',

            'secendary_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:customer_employee,email,' . $id . ',employee_id',

            'present_address' => 'nullable|string|max:500',
            'employee_permanent_address' => 'nullable|string|max:500',
            'employee_tnt_phone' => 'nullable|string|max:20',

            'father_name' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:100',
            'father_office_address' => 'nullable|string|max:255',
            'father_contact' => 'nullable|string|max:20',

            'mother_name' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:100',
            'mother_office_address' => 'nullable|string|max:255',
            'mother_contact' => 'nullable|string|max:20',

            'guardian_name' => 'nullable|string|max:255',
            'guardian_relation' => 'nullable|string|max:100',
            'guardian_house_address' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',

            'spouse_name' => 'nullable|string|max:255',
            'spouse_occupation' => 'nullable|string|max:100',
            'spouse_office_address' => 'nullable|string|max:255',
            'spouse_contact' => 'nullable|string|max:20',

            'emer_contact_name' => 'nullable|string|max:255',
            'emer_conatct_mobile' => 'nullable|string|max:20',
            'emer_contact_relation' => 'nullable|string|max:100',
            'emer_contact_address' => 'nullable|string|max:255',

            'ref_one_name' => 'nullable|string|max:255',
            'ref_one_mobile' => 'nullable|string|max:20',
            'ref_one_email' => 'nullable|email|max:255',
            'ref_one_address' => 'nullable|string|max:255',

            'ref_two_name' => 'nullable|string|max:255',
            'ref_two_mobile' => 'nullable|string|max:20',
            'ref_two_email' => 'nullable|email|max:255',
            'ref_two_address' => 'nullable|string|max:255',
        ];
    }
}
