<?php

namespace App\Http\Requests\Client\Pool\VehicleAssign; // Fixed Casing

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Instead of just true, check actual permissions here
        return true;
    }

    public function rules(): array
    {
        // dd(auth()->user()?->customerEmployee?->company);
        return [
            // 'vehicle' => [
            //     'required',
            //     // Security: Ensure the vehicle exists AND belongs to this company
            //     Rule::exists('vehicles', 'id')->where(function ($query) {
            //         $query->where('company', 1);
            //     }),
            // ],
            'assignType' => [
                'required',
                'string',
                Rule::in([
                    config('constants.ASSIGN_PERSON'),
                    config('constants.ASSIGN_ENROUTE'),
                ]),
            ],
            'personName'   => 'required|string|max:255',
            'receiveDate'  => 'nullable|date',
            'designation'  => 'nullable|string|max:100',
            'department'   => 'nullable|string|max:100',
            'idNo'         => 'nullable|string|max:50',
            'route'        => 'nullable|string',
            'location'     => 'nullable|string',
            'notes'        => 'nullable|string|max:1000',
            'bookingNo'    => 'nullable|string|exists:vehicle_booking_summary,booking_no',
            'route_json'   => 'nullable|json',
        ];
    }

    /**
     * Custom error messages (Optional but professional)
     */
    public function messages(): array
    {
        return [
            'vehicle.exists' => 'The selected vehicle is invalid or does not belong to your company.',
            'route_json.json' => 'The route data must be a valid JSON string.',
        ];
    }
}
