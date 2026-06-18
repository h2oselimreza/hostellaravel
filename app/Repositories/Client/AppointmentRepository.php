<?php

namespace App\Repositories\Client;

use App\Models\Admin\Workshop\WorkshopFile;
use App\Models\Admin\Workshop\WorkshopVehicleType;
use App\Models\Client\AppointmentSummary;
use App\Models\Client\WorkshopTimeSchedule;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentRepository
{

    public function getAppointmentList(array $arr)
    {
        return DB::table('appointment_summary')
            ->select(
                'appointment_summary.*',
                'workshops.title as workshop_name',
                'corporate_companies.title as company_name',
                'corporate_companies.company_type'
            )
            ->join('workshops', 'workshops.workshop_code', '=', 'appointment_summary.workshop')
            ->join('corporate_companies', 'corporate_companies.company_code', '=', 'appointment_summary.company')
            
            ->when(!empty($arr['companyCode']), function ($query) use ($arr) {
                $query->where('appointment_summary.company', $arr['companyCode']);
            })

            ->when(isset($arr['status']) && $arr['status'] != config('constants.APPOINTMENT_ALL'), function ($query) use ($arr) {
                $query->where('appointment_summary.status', $arr['status']);
            })

            ->orderByDesc('appointment_summary.created_dt_tm')
            ->get()
            ->toArray();
    }

    public function getDistinctService(array $variantArr)
    {
        return DB::table('service_variants')
        ->select('service_variants.service', 'services.service_name')
        ->join('services', 'services.service_code', '=', 'service_variants.service')
        ->where('service_variants.variant_type', $variantArr['variantType'])
        ->where('service_variants.is_active', 1)
        ->where('services.is_active', 1)
        ->distinct()
        ->get()
        ->toArray();
    }


    public function getWorkshopService(array $arr, $isActiveFlag = 1): array
    {
        $query = DB::table('workshop_service')
            ->join('service_variants', 'service_variants.variant_code', '=', 'workshop_service.service_variant')
            ->join('services', 'services.service_code', '=', 'service_variants.service')
            ->select([
                'workshop_service.workshop',
                DB::raw('workshop_service.service_variant as variant_code'),
                'service_variants.service_variant_name',
                'service_variants.variant_type',
                'service_variants.default_variant',
                'service_variants.service',
                'services.service_name',
            ])
            ->orderBy('service_variants.service', 'ASC');

        // Conditional filters (prevents undefined index issues)
        if (!empty($arr['workshopCode'] ?? null)) {
            $query->where('workshop_service.workshop', $arr['workshopCode']);
        }

        if (!empty($arr['variantType'] ?? null)) {
            $query->where('service_variants.variant_type', $arr['variantType']);
        }

        // Active flags (kept same logic, but flexible)
        if ($isActiveFlag !== null) {
            $query->where('service_variants.is_active', $isActiveFlag)
                ->where('services.is_active', $isActiveFlag);
        }

        return $query->get()->toArray(); // same as CI result_array()
    }

    public function getWorkshopList($searchArr, $serviceVarArr)
    {
        $workshopArr = [];

        /*
        |---------------------------------------
        | STEP 1: Filter workshops by service variants
        |---------------------------------------
        */

        if (!empty($serviceVarArr)) {

            $results = DB::table('workshop_service')
                ->selectRaw('COUNT(id) as id_count, workshop')
                ->whereIn('service_variant', $serviceVarArr)
                ->groupBy('workshop')
                ->havingRaw('COUNT(id) = ?', [count($serviceVarArr)])
                ->get();

            foreach ($results as $result) {
                $workshopArr[] = $result->workshop;
            }
        }

        /*
        |---------------------------------------
        | STEP 2: If filter exists but no match
        |---------------------------------------
        */

        if (!empty($serviceVarArr) && empty($workshopArr)) {
            return collect(); // empty result
        }

        /*
        |---------------------------------------
        | STEP 3: Main workshop query
        |---------------------------------------
        */

        $query = DB::table('workshops')
            ->select(
                'workshops.*',
                'divisions.division_en_name',
                'divisions.division_bn_name',
                'districts.district_en_name',
                'districts.district_bn_name',
                'upazilas.upozilla_en_name',
                'upazilas.upozilla_bn_name'
            )
            ->leftJoin('divisions', 'divisions.id', '=', 'workshops.division')
            ->leftJoin('districts', 'districts.id', '=', 'workshops.district')
            ->leftJoin('upazilas', 'upazilas.id', '=', 'workshops.upozilla');

        /*
        |---------------------------------------
        | STEP 4: Filters (area-wise)
        |---------------------------------------
        */

        if (!empty($searchArr['division'])) {
            $query->where('workshops.division', $searchArr['division']);
        }

        if (!empty($searchArr['district'])) {
            $query->where('workshops.district', $searchArr['district']);
        }

        if (!empty($searchArr['upozilla'])) {
            $query->where('workshops.upozilla', $searchArr['upozilla']);
        }

        /*
        |---------------------------------------
        | STEP 5: Workshop filter from service match
        |---------------------------------------
        */

        if (!empty($workshopArr)) {
            $query->whereIn('workshops.workshop_code', $workshopArr);
        }

        /*
        |---------------------------------------
        | STEP 6: Active only
        |---------------------------------------
        */

        $query->where('workshops.is_active', 1);

        return $query->get();
    }

    public function getWorkshopInfo($workshopCode)
    {
        $arr = [];

        /* ---------------- TIME SCHEDULE ---------------- */
        $arr['timeShedule'] = WorkshopTimeSchedule::query()
            ->select(
                'workshop_time_schedule.start_time',
                'workshop_time_schedule.end_time',
                'workshop_time_schedule.weekend_status',
                'workshop_time_schedule.week_day',
                'common_table.element as weekday_name',
                'common_table.element_order'
            )
            ->join('common_table', 'common_table.element_code', '=', 'workshop_time_schedule.week_day')
            ->where('workshop_time_schedule.workshop', $workshopCode)
            ->where('common_table.type', 'week_day')
            ->orderBy('common_table.element_order', 'ASC')
            ->get()
            ->toArray();

        /* ---------------- VEHICLE TYPE ---------------- */
        $arr['serviceVehicle'] = WorkshopVehicleType::query()
            ->select(
                'workshop_vehicle_type.vehicle_type',
                'common_table.element as vehicle_type_name'
            )
            ->join('common_table', 'common_table.element_code', '=', 'workshop_vehicle_type.vehicle_type')
            ->where('workshop_vehicle_type.workshop', $workshopCode)
            ->where('common_table.type', 'vehicle_type')
            ->orderBy('common_table.element_order', 'ASC')
            ->get()
            ->toArray();

        /* ---------------- SERVICE ---------------- */
        $variantArr = [
            'variantType' => config('constants.APPOINTMENT_SER'),
            'workshopCode' => $workshopCode,
        ];

        $arr['distinctService'] = $this->getDistinctService($variantArr);
        $arr['allService'] = $this->getWorkshopService($variantArr, 1);
        $arr['otherImage'] = $this->getWorkshopOtherImage($workshopCode, 1, null);
        return $arr; 
    }

    /* ---------------- WORKSHOP IMAGES ---------------- */
    public function getWorkshopOtherImage($workshopCode, $flag, $previousLimit = null)
    {
        $query = WorkshopFile::query()
            ->select('file_name')
            ->where('workshop', $workshopCode)
            ->where('file_type', config('constants.OTHER_IMAGE'))
            ->where('is_active', 1);

        if ($flag == 1) {
            $query->limit(4);
        } elseif ($flag == 2) {
            $query->limit(4)->offset($previousLimit);
        }

        return $query->get()->map(function ($item) {
            return asset('assets/images/workshop/' . $item->file_name);
        })->toArray();
    }

    public function addNewAppointment($summaryArr, $detailArr, $serviceVarCodeArr)
    {
        return DB::transaction(function () use ($summaryArr, $detailArr, $serviceVarCodeArr) {

            $dbServiceVariantArr = DB::table('workshop_service')
                ->where('workshop', $summaryArr['workshop'])
                ->pluck('service_variant')
                ->toArray();

            if (empty($dbServiceVariantArr)) {
                return 2;
            }

            $diffCount = count(array_diff($serviceVarCodeArr, $dbServiceVariantArr));

            if ($diffCount > 0) {
                return 2;
            }

            DB::table('appointment_summary')->insert($summaryArr);

            if (!empty($detailArr)) {
                DB::table('appointment_detail')->insert($detailArr);
            }

            return 1;
        });
    }

    public function getAssignWorkshopService(array $arr, $isActiveFlag = 1)
    {
        $workshopCode = $arr['workshopCode'] ?? null;
        $variantType  = $arr['variantType'] ?? null;

        if (!$workshopCode || !$variantType) {
            return [];
        }

        // Subquery instead of temporary table
        $workshopServiceSub = DB::table('workshop_service')
            ->select('id', 'service_variant')
            ->where('workshop', $workshopCode);

        $query = DB::table('service_variants')
            ->select([
                'service_variants.*',
                'services.service_name',
                'service_categories.category_name',
                'service_categories.parent_category_str',
                'ws.service_variant as workshop_ser_var',
                'ws.id as workshop_service_id',
            ])
            ->leftJoinSub($workshopServiceSub, 'ws', function ($join) {
                $join->on('ws.service_variant', '=', 'service_variants.variant_code');
            })
            ->join('services', 'services.service_code', '=', 'service_variants.service')
            ->join('service_categories', 'service_categories.category_code', '=', 'services.service_category');

        // Active filter
        if ($isActiveFlag == 1) {
            $query->where('service_variants.is_active', 1);
        } elseif ($isActiveFlag == 2) {
            $query->where('service_variants.is_active', 0);
        }

        $query->where('services.is_active', 1)
            ->where('service_categories.is_active', 1)
            ->where('service_variants.variant_type', $variantType)
            ->orderBy('service_variants.service', 'ASC');

        return $query->get()->toArray();
    }

    public function singleWorkshopDetails($workshopCode)
    {
        $result = DB::table('workshops')
        ->join('divisions', 'divisions.id', '=', 'workshops.division')
        ->join('districts', 'districts.id', '=', 'workshops.district')
        ->join('upazilas', 'upazilas.id', '=', 'workshops.upozilla')
        ->where('workshops.workshop_code', $workshopCode)
        ->where('workshops.is_active', 1)
        ->select(
            'workshops.*',
            'divisions.division_en_name',
            'divisions.division_bn_name',
            'districts.district_en_name',
            'districts.district_bn_name',
            'upazilas.upozilla_en_name',
            'upazilas.upozilla_bn_name'
        )
        ->first();
        return $result ?? 0;        
    }

    public function getAppointmentSummary($appointmentNo, $companyCode)
    {
        return DB::table('appointment_summary')
            ->join('workshops', 'workshops.workshop_code', '=', 'appointment_summary.workshop')
            ->where('appointment_summary.company', $companyCode)
            ->where('appointment_summary.appointment_no', $appointmentNo)
            ->select('appointment_summary.*', 'workshops.title as workshop_name')
            ->first();
    }

    public function getAppoinmentedVehicle($appointmentNo)
    {
        return DB::table('appointment_detail')
            ->join('vehicles', 'vehicles.vehicle_id', '=', 'appointment_detail.vehicle')
            ->where('appointment_detail.appointment_no', $appointmentNo)
            ->distinct()
            ->select('appointment_detail.vehicle', 'vehicles.registration_no')
            ->get()
            ->toArray();
    }


    public function getAppoinmentDetail($appointmentNo)
    {
        return DB::table('appointment_detail')
            ->join('service_variants', 'service_variants.variant_code', '=', 'appointment_detail.service_variant')
            ->where('appointment_detail.appointment_no', $appointmentNo)
            ->select('appointment_detail.*', 'service_variants.service_variant_name')
            ->get()
            ->toArray();
    }

    public function appointmentChangeStatus(string $appointmentNo, string $status): int
    {
        $appointment = DB::table('appointment_summary')
            ->where('appointment_no', $appointmentNo)
            ->first();

        if (!$appointment) {
            return 2;
        }

        $dbStatus = $appointment->status;

        if ($status == config('constants.APPOINTMENT_PROCCESSING')) {
            if ($dbStatus != config('constants.APPOINTMENT_PENDING')) {
                return 2;
            }
        }

        if ($status == config('constants.APPOINTMENT_COMPLETE')) {
            if ($dbStatus != config('constants.APPOINTMENT_ACCEPT')) {
                return 2;
            }
        }
        
        if ($status == config('constants.APPOINTMENT_ACCEPT')) {
            if ($dbStatus != config('constants.APPOINTMENT_PROCCESSING')) {
                return 2;
            }

            if ($appointment->final_date === null || $appointment->appointment_time === null) {
                return 3;
            }
        }

        DB::table('appointment_summary')
            ->where('appointment_no', $appointmentNo)
            ->update([
                'status'       => $status,
                'updated_type' => config('constants.P_ADMIN'),
            ]);

        return 1;
    }

    public function setConfirmDateTime($appointmentNo, $confirmDate, $confirmTime): int
    {
        // Fetch appointment
        $appointment = DB::table('appointment_summary')
            ->where('appointment_no', $appointmentNo)
            ->first();

        // Not found
        if (!$appointment) {
            return 2;
        }

        // Status validation (must be processing)
        if ($appointment->status != config('constants.APPOINTMENT_PROCCESSING')) {
            return 2;
        }

        // Update data
        $data = [
            'final_date'       => $confirmDate,
            'appointment_time' => $confirmTime,
            'updated_type'     => config('constants.P_ADMIN'),
        ];

        // Update query
        DB::table('appointment_summary')
            ->where('appointment_no', $appointmentNo)
            ->update($data);

        return 1;
    }
}