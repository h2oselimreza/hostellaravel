<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
    public function rules(): array
    {
        return [
        // Basic Information
        'employee_name' => 'required|string|max:250',
        'employee_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'designation' => 'nullable|string|max:200',
        'first_joining_date' => 'nullable|date_format:Y-m-d',
        'gender' => 'nullable|string|max:10',
        'religion' => 'nullable|string|max:30',
        'nationality' => 'nullable|string|max:100',
        'dob' => 'required|date_format:Y-m-d',
        'blood_group' => 'nullable|string|max:10',
        'marital_status' => 'nullable|string|max:20',
        'anniversary' => 'nullable|date_format:Y-m-d',

        // Spouse
        'spouse_name' => 'nullable|string|max:250',
        'spouse_occupation' => 'nullable|string|max:250',
        'spouse_contact' => 'nullable|string|max:30',
        'spouse_office_address' => 'nullable|string|max:500',

        // Contact
        'primary_mobile' => 'required|string|max:30',
        'secendary_mobile' => 'nullable|string|max:30',
        'employee_tnt_phone' => 'nullable|string|max:30',
        'email' => 'nullable|email|max:250',
        'present_address' => 'nullable|string|max:500',
        'employee_permanent_address' => 'nullable|string|max:500',

        // Emergency
        'emer_contact_name' => 'nullable|string|max:250',
        'emer_contact_relation' => 'nullable|string|max:250',
        'emer_conatct_mobile' => 'nullable|string|max:30',
        'emer_contact_address' => 'nullable|string|max:500',

        // NID & Personal
        'national_id' => 'nullable|string|max:50',
        'father_name' => 'nullable|string|max:250',
        'father_occupation' => 'nullable|string|max:250',
        'father_office_address' => 'nullable|string|max:500',
        'father_contact' => 'nullable|string|max:30',
        'mother_name' => 'nullable|string|max:250',
        'mother_occupation' => 'nullable|string|max:250',
        'mother_office_address' => 'nullable|string|max:500',
        'mother_contact' => 'nullable|string|max:30',

        // Guardian
        'guardian_name' => 'nullable|string|max:200',
        'guardian_contact' => 'nullable|string|max:30',
        'guardian_relation' => 'nullable|string|max:250',
        'guardian_house_address' => 'nullable|string|max:500',

        // Previous Organization
        'last_organization' => 'nullable|string|max:250',
        'last_org_address' => 'nullable|string|max:500',
        'last_org_designation' => 'nullable|string|max:250',
        'last_org_from_date' => 'nullable|date_format:Y-m-d',
        'last_org_to_date' => 'nullable|date_format:Y-m-d|after_or_equal:last_org_from_date',

        // Passport & Driving
        'passport_no' => 'nullable|string|max:100',
        'passposrt_expiry_date' => 'nullable|date_format:Y-m-d',
        'driving_license_no' => 'nullable|string|max:100',
        'driving_license_expiry_date' => 'nullable|date_format:Y-m-d',

        // System Fields (NOT NULL in DB)
        // 'is_active' => 'required|integer',
        // 'system_user' => 'required|integer',
        // 'emp_type' => 'required|string|max:50',
        // 'emp_category' => 'required|string|max:50',
        // 'emp_station' => 'required|string|max:50',
        // 'grade' => 'required|string|max:50',
        // 'department' => 'required|string|max:50',
        // 'reporting_to' => 'required|string|max:50',
        // 'work_shift' => 'required|string|max:50',
        // 'birthday_sms_status' => 'required|integer',
        // 'anniversary_sms_status' => 'required|integer',
        ];
    }
}
