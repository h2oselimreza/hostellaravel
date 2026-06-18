<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleRequest extends FormRequest
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
        $moduleId = $this->route('module'); 
        // works for both create (null) and update

        return [
            'module_group' => 'required|string|max:20',
            'modules_name' => 'required|string|max:200',
            'module_url' => [
                'required',
                'string',
                'max:300',
                Rule::unique('modules', 'module_url')->ignore($moduleId),
            ],
            'module_order' => 'nullable|integer',
            'panel_type' => 'nullable|string|max:30',
        ];
    }
}
