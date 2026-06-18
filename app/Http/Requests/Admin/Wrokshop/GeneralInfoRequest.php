<?php

namespace App\Http\Requests\Admin\Wrokshop;

use Illuminate\Foundation\Http\FormRequest;

class GeneralInfoRequest extends FormRequest
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
            'title'                       => 'required|string|max:250',
            'address'                     => 'required|string',
            'workshop_email'              => 'required|email|max:100',
            'website'                     => 'required|url|max:100',
            'workshop_mobile'             => 'required|string|max:20',
            'workshop_land_phone'         => 'required|string|max:20',
            'profile_image'               => 'nullable|image|max:2048',
            
            
            'division'                    => 'required|integer|exists:divisions,id',
            'district'                    => 'required|integer|exists:districts,id',
            'upozilla'                    => 'required|integer|exists:upazilas,id',
            'postal_code'                 => 'required|integer',
            'latitude'                    => 'required|string|max:50',
            'longitude'                   => 'required|string|max:50',

            
            'primary_contact_person'      => 'required|string|max:200',
            'primary_contact_designation' => 'required|string|max:100',
            'primary_contact_mobile'      => 'required|string|max:20',
            'primary_contact_email'       => 'required|email|max:100',
            
            'second_contact_person'       => 'required|string|max:200',
            'second_contact_designation'  => 'required|string|max:100',
            'second_contact_mobile'       => 'required|string|max:20',
            'second_contact_email'        => 'required|email|max:100',
        ];
    }
}
