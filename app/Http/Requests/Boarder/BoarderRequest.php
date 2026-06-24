<?php

namespace App\Http\Requests\Boarder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BoarderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $boarderId = $this->route('boarderId');

        return [
            // Required Fields
            'boarder_name'   => ['required', 'string', 'max:255'],
            'primary_mobile' => [
                'required',
                'string',
                'max:20',
                Rule::unique('boarder', 'primary_mobile')
                    ->ignore($boarderId, 'boarder_id'),
            ],
            
            'seatCode' => $this->isMethod('post')
            ? ['required', 'string', 'max:50']
            : ['nullable', 'string', 'max:50'],

            'email'          => ['required', 'email'],

            // Optional Fields
            'gender'                         => ['nullable', 'string', 'max:50'],
            'religion'                       => ['nullable', 'string', 'max:100'],
            'nationality'                    => ['nullable', 'string', 'max:100'],
            'dob'                            => ['nullable', 'date'],
            'blood_group'                    => ['nullable', 'string', 'max:20'],
            'marital_status'                 => ['nullable', 'string', 'max:50'],

            'anniversary'                    => ['nullable', 'date'],
            'spouse_name'                    => ['nullable', 'string', 'max:255'],
            'spouse_occupation'              => ['nullable', 'string', 'max:255'],
            'spouse_office_address'          => ['nullable', 'string'],
            'spouse_contact'                 => ['nullable', 'string', 'max:20'],

            'secendary_mobile'               => ['nullable', 'string', 'max:20'],
            'emer_contact_name'              => ['nullable', 'string', 'max:255'],
            'emer_contact_address'           => ['nullable', 'string'],
            'emer_contact_relation'          => ['nullable', 'string', 'max:100'],
            'emer_conatct_mobile'            => ['nullable', 'string', 'max:20'],

            'present_address'                => ['nullable', 'string'],
            'national_id'                    => ['nullable', 'string', 'max:100'],

            'father_name'                    => ['nullable', 'string', 'max:255'],
            'father_occupation'              => ['nullable', 'string', 'max:255'],
            'father_office_address'          => ['nullable', 'string'],
            'father_contact'                 => ['nullable', 'string', 'max:20'],

            'mother_name'                    => ['nullable', 'string', 'max:255'],
            'mother_occupation'              => ['nullable', 'string', 'max:255'],
            'mother_office_address'          => ['nullable', 'string'],
            'mother_contact'                 => ['nullable', 'string', 'max:20'],

            'guardian_name'                  => ['nullable', 'string', 'max:255'],
            'guardian_contact'               => ['nullable', 'string', 'max:20'],
            'guardian_relation'              => ['nullable', 'string', 'max:100'],
            'guardian_house_address'         => ['nullable', 'string'],

            'boarder_tnt_phone'              => ['nullable', 'string', 'max:50'],
            'boarder_permanent_address'      => ['nullable', 'string'],

            'passport_no'                    => ['nullable', 'string', 'max:100'],
            'passport_expiry_date'           => ['nullable', 'date'],

            'driving_license_no'             => ['nullable', 'string', 'max:100'],
            'driving_license_expiry_date'    => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'boarder_name.required' => 'Boarder name is required.',
            'primary_mobile.required' => 'Mobile number is required.',
            'seatCode.required' => 'Seat is required.',
        ];
    }
}
