<?php

namespace App\Http\Requests\Admin\Place;

use Illuminate\Foundation\Http\FormRequest;

class PlaceRequest extends FormRequest
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
            'title_bn'                    => 'nullable|string|max:250',
            'address'                     => 'nullable|string',
            'address_bn'                  => 'nullable|string',

            'place_email'              => 'nullable|email|max:100',
            'website'                     => 'nullable|string|max:100',

            'place_mobile'                => 'required|string|max:20',
            'place_land_phone'            => 'nullable|string|max:20',

            'division'                    => 'nullable|integer',
            'district'                    => 'nullable|integer',
            'upozilla'                    => 'nullable|integer',
            'postal_code'                 => 'nullable|integer',

            'latitude'                    => 'nullable|string|max:50',
            'longitude'                   => 'nullable|string|max:50',

            'place_display_code'          => 'nullable|string|max:100',

            'primary_contact_person'      => 'nullable|string|max:200',
            'primary_contact_designation'=> 'nullable|string|max:100',
            'primary_contact_mobile'      => 'nullable|string|max:20',
            'primary_contact_email'       => 'nullable|email|max:100',

            'second_contact_person'       => 'nullable|string|max:200',
            'second_contact_designation'  => 'nullable|string|max:100',
            'second_contact_mobile'       => 'nullable|string|max:20',
            'second_contact_email'        => 'nullable|email|max:100',
        ];
    }

    public function messages()
    {
        return [
            'title.required'        => 'Place title is required.',
            'place_mobile.required'=> 'Mobile number is required.',
            'workshop_email.email' => 'Invalid email format.',
        ];
    }
}
