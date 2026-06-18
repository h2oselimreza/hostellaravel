<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
            "member_name"=> "required",
            'dob' => 'required|date_format:Y-m-d',
            "primary_mobile"=> "required",
            'passport_expiry_date' => 'nullable|date_format:Y-m-d',
            'driving_license_expiry_date' => 'nullable|date_format:Y-m-d',
            'gender'=> 'nullable',
            'national_id'=> 'nullable',
            "society_block"=> "required",
            "society_road"=> "required",
            "society_plot"=> "required",
            "society_flat"=> "required",
            'member_bangla_name'=>'nullable',
            'religion'=> 'nullable',
            'nationality'=>'nullable',
            'blood_group'=>'nullable',
            'marital_status'=>'nullable',
            'anniversary'=>'nullable|date_format:Y-m-d',
            'member_occupation'=>'nullable',
            'institution_name'=>'nullable',
            'passport_no'=>'nullable',
            'driving_license_no'=>'nullable',
            'secendary_mobile'=>'nullable',
            'member_tnt_phone'=>'nullable',
            'email'=>'nullable',
            'present_address'=>'nullable',
            'member_permanent_address'=>'nullable',
            'father_name'=>'nullable',
            'father_occupation'=>'nullable',
            'father_office_address'=>'nullable',
            'father_contact'=>'nullable',
            'mother_name'=>'nullable',
            'mother_occupation'=>'nullable',
            'mother_office_address' => 'nullable',
            'mother_contact'=>'nullable',
            'spouse_name'=>'nullable',
            'spouse_occupation'=>'nullable',
            'spouse_office_address'=>'nullable',
            'spouse_contact'=>'nullable',
            'emer_contact_name'=>'nullable',
            'emer_conatct_mobile'=>'nullable',
            'emer_contact_relation'=>'nullable',
            'emer_contact_address'=>'nullable',
        ];
    }
}
