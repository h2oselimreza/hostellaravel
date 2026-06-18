<?php

namespace App\Http\Requests\Admin\MasterData\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class VehicleTypeRequest extends FormRequest
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
    public function rules(): array {
        return [
            'element' => 'required|string|max:255',
            'element_order' => 'nullable|integer',
            'is_active' => 'required|in:0,1',
        ];
    }

    public function messages(): array {
        return [
            'is_active.required' => 'Please select a status.',
        ];
    }
}
