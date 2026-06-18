<?php

namespace App\Http\Requests\Admin\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberShipCardRequest extends FormRequest
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
        $id = $this->route('membership_card');
        return [
            'card_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('membership_card', 'card_number')->ignore($id),
            ],
            'validity_month' => 'required|numeric',
        ];
    }
}
