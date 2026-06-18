<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberOfficeRequest extends FormRequest
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
            "member_type"=> "nullable",
            "first_introduced_by"=> "nullable",
            "second_introduced_by"=> "nullable",
            "first_joining_date"=> "required|date_format:Y-m-d",
        ];
    }
}
