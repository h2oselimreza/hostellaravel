<?php

namespace App\Repositories\Client;

use Illuminate\Support\Facades\DB;

class GenHomeServiceRepository
{

    public function getHomeServiceList(array $arr = [])
    {
        return DB::table('home_service_app_summary_gen as hs')
            ->select(
                'hs.*',
                'cc.title as company_name',
                'cc.company_type',
                'e.employee_name as assigned_employee_name',
                'e.primary_mobile as assigned_employee_mobile'
            )
            ->join('corporate_companies as cc', 'cc.company_code', '=', 'hs.company')
            ->leftJoin('employee as e', 'e.employee_id', '=', 'hs.assign_emp')

            // Company filter
            ->when(!empty($arr['companyCode']), function ($query) use ($arr) {
                $query->where('hs.company', $arr['companyCode']);
            })

            // Status filter
            ->when(
                isset($arr['status']) && $arr['status'] != config('constants.APPOINTMENT_ALL'),
                function ($query) use ($arr) {
                    $query->where('hs.status', $arr['status']);
                }
            )

            // Assign employee filter
            ->when(isset($arr['assignEmpFlag']), function ($query) use ($arr) {
                if ($arr['assignEmpFlag'] == 2) {
                    $query->whereNull('hs.assign_emp');
                } elseif ($arr['assignEmpFlag'] == 3) {
                    $query->whereNotNull('hs.assign_emp');
                }
            })

            ->orderByDesc('hs.created_dt_tm')
            ->get()
            ->toArray();
    }

    public function getAppointmentSummary($appointmentNo, $companyCode = null)
    {
        $query = DB::table('home_service_app_summary_gen as hs')
            ->select(
                'hs.*',
                'e.employee_name as assigned_employee_name',
                'e.primary_mobile as assigned_employee_mobile'
            )
            ->leftJoin('employee as e', 'e.employee_id', '=', 'hs.assign_emp')
            ->where('hs.appointment_no', $appointmentNo);

        if (!empty($companyCode)) {
            $query->where('hs.company', $companyCode);
        }

        $result = $query->first();

        return $result ?: 0;
    }

    public function getAppoinmentDetail($appointmentNo)
    {
        return DB::table('home_service_app_detail_gen as hd')
            ->select(
                'hd.*',
                'sv.service_variant_name',
                'sv.unit_name'
            )
            ->join('service_variants as sv', 'sv.variant_code', '=', 'hd.service_variant')
            ->where('hd.appointment_no', $appointmentNo)
            ->get()
            ->map(function ($row) {
                return (array) $row;
            })
            ->toArray();
    }


    public function getDistinctService(array $variantArr)
    {
        return DB::table('service_variants as sv')
            ->select('sv.service', 's.service_name')
            ->join('services as s', 's.service_code', '=', 'sv.service')
            ->where('sv.variant_type', $variantArr['variantType'])
            ->where('sv.is_active', 1)
            ->where('s.is_active', 1)
            ->distinct()
            ->get()
            ->map(function ($row) {
                return (array) $row;
            })
            ->toArray();
    }

    public function getHomeService(array $arr, $isActiveFlag = 1)
    {
        return DB::table('service_variants as sv')
            ->select(
                'sv.variant_code',
                'sv.service_variant_name',
                'sv.variant_type',
                'sv.default_variant',
                'sv.service',
                'sv.unit_price',
                'sv.unit_name',
                's.service_name'
            )
            ->join('services as s', 's.service_code', '=', 'sv.service')
            ->where('sv.variant_type', $arr['variantType'])
            ->where('sv.is_active', 1)
            ->where('s.is_active', 1)
            // ->orderBy('sv.service', 'ASC') // intentionally kept commented (same as original)
            ->orderBy('sv.service_variant_name', 'ASC')
            ->get()
            ->map(function ($row) {
                return (array) $row;
            })
            ->toArray();
    }

    public function proccessHomeService(array $arr, $appointmentNo)
    {
        return DB::table('home_service_app_summary_gen')
            ->where('appointment_no', $appointmentNo)
            ->update($arr);
    }

    public function editHomeService(array $finalArr)
    {
        return DB::transaction(function () use ($finalArr) {

            // 1. Update summary (same as CI where + update)
            DB::table('home_service_app_summary_gen')
                ->where('appointment_no', $finalArr['appointmentNo'])
                ->where('company', $finalArr['company'])
                ->update($finalArr['summaryArr']);

            // 2. Insert batch (same as insert_batch)
            if (!empty($finalArr['insertDetailArr'])) {
                DB::table('home_service_app_detail_gen')
                    ->insert($finalArr['insertDetailArr']);
            }

            // 3. Update batch (same as update_batch)
            if (!empty($finalArr['updateDetailArr'])) {
                foreach ($finalArr['updateDetailArr'] as $row) {

                    DB::table('home_service_app_detail_gen')
                        ->where('id', $row['id'])
                        ->update([
                            'unit_price'    => $row['unit_price'],
                            'quantity'      => $row['quantity'],
                            'total_amount'  => $row['total_amount'],
                            'updated_by'    => $row['updated_by'],
                            'updated_type'  => $row['updated_type'],
                            'updated_dt_tm' => $row['updated_dt_tm'],
                        ]);
                }
            }

            // 4. Delete (same as where_in + delete)
            if (!empty($finalArr['deleteDetailArr'])) {
                DB::table('home_service_app_detail_gen')
                    ->where('appointment_no', $finalArr['appointmentNo'])
                    ->whereIn('service_variant', $finalArr['deleteDetailArr'])
                    ->delete();
            }

            return true;
        });
    }
}