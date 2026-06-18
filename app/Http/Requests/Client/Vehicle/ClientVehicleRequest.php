<?php

namespace App\Http\Requests\Client\Vehicle;

use App\Services\Client\PackageCheckService;
use DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientVehicleRequest extends FormRequest
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
        $vehicleId = $this->route('vehicle'); // for update
        $companyCode = auth()->user()?->customerEmployee?->company;

        return [
            'registration_no' => [
                'required',
                'string',
                'max:100',
                Rule::unique('vehicles', 'registration_no')
                    ->where(function ($query) use ($companyCode) {
                        return $query->where('company', $companyCode);
                    })
                    ->ignore($vehicleId, 'id'),
            ],

            'vehicle_type' => ['required', 'string', 'max:50'],
            'brand' => ['required', 'string', 'max:50'],
            'brand_model' => ['required', 'string', 'max:50'],
            'vehicle_class' => ['required', 'string', 'max:50'],
            'vehicle_cc' => ['nullable', 'string', 'max:50'],
            'manufacturing_year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . ((int) date('Y') + 1),
            ],
            'manufacturing_country' => ['nullable', 'string', 'max:100'],
            'engine_no' => ['nullable', 'string', 'max:30'],
            'chasis_no' => ['nullable', 'string', 'max:30'],
            'color' => ['nullable', 'string', 'max:50'],
            'vehicle_group' => ['nullable', 'string', 'max:50'],
            'communication_code' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $companyCode = auth()->user()?->customerEmployee?->company;
            $vehicleId   = $this->route('vehicle');
            $isEdit      = !empty($vehicleId);

            // =========================
            // ✅ PACKAGE CHECK
            // =========================
            $packageService = app(PackageCheckService::class);

            $check = $packageService->check(
                PackageCheckService::PACKAGE_VEHICLE_COUNT,
                $companyCode
            );

            if ($check['success'] == 0 && !$isEdit) {
                $validator->errors()->add(
                    'registration_no',
                    'Vehicle limit exceeded for your package.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'registration_no.required' => 'Registration No is required',
            'registration_no.unique'   => 'Registration already exists',

            'vehicle_type.required' => 'Vehicle Type is required',
            'brand.required'        => 'Brand is required',
            'vehicle_class.required'=> 'Vehicle Class is required',
        ];
    }
}
