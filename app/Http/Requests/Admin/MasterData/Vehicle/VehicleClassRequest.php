<?php

namespace App\Http\Requests\Admin\MasterData\Vehicle;

use Illuminate\Foundation\Http\FormRequest;

class VehicleClassRequest extends FormRequest
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
            'element_order' => 'nullable|integer|min:0',
            'is_active' => 'required|in:0,1',
            'element_bn' => [
                'nullable',
                'string',
                'max:200',
                'regex:/^[\x{0980}-\x{09FF}\s]+$/u' // এটি শুধুমাত্র বাংলা ক্যারেক্টার এবং স্পেস এলাউ করবে
            ],
        ];
    }

    public function messages(): array {
        return [
            'element.required' => 'The Vehicle class field is mandatory.',
            'is_active.required' => 'Please select a status.',
        ];
    }
}
