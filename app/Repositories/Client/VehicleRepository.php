<?php

namespace App\Repositories\Client;

use App\Models\Client\Vehicle;
use App\Models\Client\VehicleAssignDetail;
use App\Models\Client\VehicleBookingSummary;
use App\Models\Company;
use App\Models\CustomerEmployee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleRepository
{

    public function getVehicleInfo($arr)
    {
        $query = DB::table('vehicles')
            ->select(
                'vehicles.*',
                'vehicle_type_tb.element as vehicle_type_name',
                'brand_tb.element as brand_name',
                'driver_tb.employee_name as driver_name',
                'driver_tb.employee_id as driver_id',
                'driver_tb.primary_mobile as driver_mobile_no',
                'brand_model_tb.element as brand_model_name',
                'vehicle_class_tb.element as vehicle_class_name',
                'vehicle_color_tb.element as color_name',
                'vehicle_group_tb.element as vehicle_group_name'
            )
            ->leftJoin('common_table as vehicle_type_tb', 'vehicle_type_tb.element_code', '=', 'vehicles.vehicle_type')
            ->leftJoin('common_table as brand_tb', 'brand_tb.element_code', '=', 'vehicles.brand')
            ->leftJoin('common_table as brand_model_tb', 'brand_model_tb.element_code', '=', 'vehicles.brand_model')
            ->leftJoin('common_table as vehicle_class_tb', 'vehicle_class_tb.element_code', '=', 'vehicles.vehicle_class')
            ->leftJoin('common_table as vehicle_color_tb', 'vehicle_color_tb.element_code', '=', 'vehicles.color')
            ->leftJoin('common_table as vehicle_group_tb', 'vehicle_group_tb.element_code', '=', 'vehicles.vehicle_group')
            ->leftJoin('customer_employee as driver_tb', 'driver_tb.employee_id', '=', 'vehicles.driver_id')
            ->where('vehicles.company', $arr['companyCode']);

        if (isset($arr['bulkFlag']) && $arr['bulkFlag'] == 0) {
            $query->where('vehicles.vehicle_id', $arr['vehicleId']);
        }

        if (isset($arr['isActiveFlag']) && $arr['isActiveFlag'] == 1) {
            $query->where('vehicles.is_active', 1);
        } elseif (isset($arr['isActiveFlag']) && $arr['isActiveFlag'] == 2) {
            $query->where('vehicles.is_active', 0);
        }

        return $query->get();
    }

    public function getVehicleFiles(array $arr)
    {
        $query = DB::table('vehicle_files')
            ->where('is_active', 1)
            ->orderByDesc('id');

        // Apply condition only when bulkFlag = 0
        if (isset($arr['bulkFlag']) && $arr['bulkFlag'] == 0) {
            $query->where('vehicle', $arr['vehicleId']);
        }

        return $query->get()->toArray();
    }

    public function getPullVehicleDetails($vehicleId, $company)
    {
        return Vehicle::where('vehicle_id', $vehicleId)
            ->where('company', $company)
            ->where('is_active', 1)
            ->first();
    }

    public function getVehicleBookingSummary($bookingNo, $companyCode)
    {
        return DB::table('vehicle_booking_summary')
            ->where('company', $companyCode)
            ->where('booking_no', $bookingNo)
            ->get();
    }

    public function assignEmployee(array $data, string $vehicleId, string $company, $driver = null): bool
    {
        return DB::transaction(function () use ($data, $vehicleId, $company, $driver) {

            $bookingNo = $data['booking_no'] ?? null;

            // Remove unwanted fields
            unset($data['booking_no']);

            // 1. Update Vehicle
            $vehicle = Vehicle::where('vehicle_id', $vehicleId)->first();
            $vehicle->update($data);

            // 2. Prepare Assign Details
            $assignDetails = [
                'reference_no'        => $data['pull_detail_ref_no'] ?? null,
                'company'             => $company,
                'vehicle'             => $vehicleId,
                'driver'              => $driver ?? null,
                'assign_dt_tm'        => $data['pull_receive_date'] ?? null,
                'assign_type'         => $data['assign_type'] ?? null,
                'emp_name'            => $data['pull_emp_name'] ?? null,
                'emp_designation'     => $data['pull_designation'] ?? null,
                'emp_department'      => $data['pull_department'] ?? null,
                'emp_id_no'           => $data['pull_id_no'] ?? null,
                'route'               => $data['pull_route'] ?? null,
                'current_location'    => $data['pull_current_location'] ?? null,
                'booking_no'          => $bookingNo,
                'remarks'             => $data['pull_remarks'] ?? null,
                'created_by'          => $data['updated_by'] ?? null,
                'created_dt_tm'       => $data['updated_dt_tm'] ?? now(),
            ];

            // 3. Insert Assign Details
            VehicleAssignDetail::create($assignDetails);

            return true;
        });
    }

    public function updateTripStatusVehicleBooking($bookingNo, $status)
    {
        DB::table('vehicle_booking_summary')
            ->where('booking_no', $bookingNo)
            ->update([
                'trip_status'   => $status,
                'updated_by'    => Auth::user()->user_id ?? null,
                'updated_dt_tm' => now(),
            ]);
    }

    public function updateTripStatusVacantVehicleBooking($vehicle, $companyCode)
    {
        return DB::transaction(function () use ($vehicle, $companyCode) {

            // 1. Find matching booking
            $booking = VehicleBookingSummary::where('vehicle', $vehicle)
                ->where('company', $companyCode)
                ->where('status', config('constants.BOOKING_REQ_APPROVE_STATUS'))
                ->where('trip_status', config('constants.TRIP_STATUS_START'))
                ->first();

            // 2. If no record found
            if (!$booking) {
                return false;
            }

            // 3. Update booking status
            VehicleBookingSummary::where('vehicle', $vehicle)
                ->where('company', $companyCode)
                ->where('status', config('constants.BOOKING_REQ_APPROVE_STATUS'))
                ->where('trip_status', config('constants.TRIP_STATUS_START'))
                ->update([
                    'trip_status'   => config('constants.TRIP_STATUS_END'),
                    'updated_by'    => Auth::user()->user_id ?? null,
                    'updated_dt_tm' => Carbon::now(),
                ]);

            // 4. Return original record (same behavior as CI)
            return $booking;
        });
    }


    public function getCompanyGeneralInfo(array $arr)
    {
        $query = DB::table('corporate_companies')
            ->select([
                'corporate_companies.*',
                'divisions.division_en_name',
                'divisions.division_bn_name',
                'districts.district_en_name',
                'districts.district_bn_name',
                'upazilas.upozilla_en_name',
                'upazilas.upozilla_bn_name',
                'package.package_name',
                'package.package_details',
                'vts_com_tb.element as vts_company_title',
            ])
            ->leftJoin('divisions', 'divisions.id', '=', 'corporate_companies.division')
            ->leftJoin('districts', 'districts.id', '=', 'corporate_companies.district')
            ->leftJoin('upazilas', 'upazilas.id', '=', 'corporate_companies.upozilla')
            ->leftJoin('common_table as vts_com_tb', 'vts_com_tb.element_code', '=', 'corporate_companies.vts_company')
            ->leftJoin('package', 'package.package_code', '=', 'corporate_companies.package');

        // Condition
        if (($arr['bulkFlag'] ?? 0) == 0) {
            $query->where('corporate_companies.company_code', $arr['companyCode']);
        }

        return $query->get()->toArray();
    }
    
    public function getVehicleInfoForLocation(array $arr)
    {
        return DB::table('vehicles')

            ->select(
                'vehicles.*',

                'vehicle_type_tb.element as vehicle_type_name',
                'brand_tb.element as brand_name',

                'driver_tb.employee_name as driver_name',
                'driver_tb.employee_id as driver_id',
                'driver_tb.primary_mobile as driver_mobile_no',

                'brand_model_tb.element as brand_model_name',
                'vehicle_class_tb.element as vehicle_class_name',
                'vehicle_color_tb.element as color_name'
            )
            ->leftJoin(
                'common_table as vehicle_type_tb',
                'vehicle_type_tb.element_code',
                '=',
                'vehicles.vehicle_type'
            )
            ->leftJoin(
                'common_table as brand_tb',
                'brand_tb.element_code',
                '=',
                'vehicles.brand'
            )
            ->leftJoin(
                'common_table as brand_model_tb',
                'brand_model_tb.element_code',
                '=',
                'vehicles.brand_model'
            )
            ->leftJoin(
                'common_table as vehicle_class_tb',
                'vehicle_class_tb.element_code',
                '=',
                'vehicles.vehicle_class'
            )
            ->leftJoin(
                'common_table as vehicle_color_tb',
                'vehicle_color_tb.element_code',
                '=',
                'vehicles.color'
            )
            ->leftJoin(
                'customer_employee as driver_tb',
                'driver_tb.employee_id',
                '=',
                'vehicles.driver_id'
            )
            ->where('vehicles.company', $arr['companyCode'])
            ->whereIn(
                'vehicles.communication_code',
                $arr['communicationCodeArr']
            )
            ->where('vehicles.is_active', 1)
            ->get()
            ->toArray();
    }

}