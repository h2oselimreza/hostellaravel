<?php

namespace App\Repositories\Client;

use App\Models\Admin\MasterData\ServiceVariant;
use App\Models\Client\HomeServiceAppDetail;
use App\Models\Client\HomeServiceAppSummaryGen;
use App\Models\CorporateCompany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeServiceRepository
{
    public function getHomeServiceList(array $arr)
    {
        $query = DB::table('home_service_app_summary_gen as hs')
            ->select(
                'hs.*',
                'cc.title as company_name',
                'cc.company_type',
                'e.employee_name as assigned_employee_name',
                'e.primary_mobile as assigned_employee_mobile'
            )
            ->join('corporate_companies as cc', 'cc.company_code', '=', 'hs.company')
            ->leftJoin('employee as e', 'e.employee_id', '=', 'hs.assign_emp');

        // Filter by company
        if (!empty($arr['companyCode'])) {
            $query->where('hs.company', $arr['companyCode']);
        }

        // Filter by status
        if (isset($arr['status']) && $arr['status'] != config('constants.APPOINTMENT_ALL')) {
            $query->where('hs.status', $arr['status']);
        }

        // Assign employee filter
        if (isset($arr['assignEmpFlag'])) {
            if ($arr['assignEmpFlag'] == 2) {
                // Not assigned
                $query->whereNull('hs.assign_emp');
            } elseif ($arr['assignEmpFlag'] == 3) {
                // Assigned
                $query->whereNotNull('hs.assign_emp');
            }
        }

        // Order
        $query->orderBy('hs.created_dt_tm', 'DESC');

        return $query->get()->toArray();
    }

    public function getDistinctService($variantArr)
    {
        return DB::table('service_variants as sv')
            ->select('sv.service', 's.service_name')
            ->join('services as s', 's.service_code', '=', 'sv.service')
            ->where('sv.variant_type', $variantArr['variantType'] ?? null)
            ->where('sv.is_active', 1)
            ->where('s.is_active', 1)
            ->distinct()
            ->get();
    }

    public function getHomeService($arr, $isActiveFlag = 1)
    {
        return ServiceVariant::query()
        ->select(
            'service_variants.variant_code',
            'service_variants.service_variant_name',
            'service_variants.variant_type',
            'service_variants.default_variant',
            'service_variants.service',
            'service_variants.unit_price',
            'service_variants.unit_name',
            'services.service_name'
        )
        ->join('services', 'services.service_code', '=', 'service_variants.service')
        ->where('service_variants.variant_type', $arr['variantType'] ?? null)
        ->where(['service_variants.is_active' => 1, 'services.is_active' => 1])
        ->orderBy('service_variants.service_variant_name', 'ASC')
        ->get();
    }

    public function getSingleCompanyInfo($companyCode)
    {
        if (!$companyCode) {
            return null;
        }
        return CorporateCompany::where('company_code', $companyCode)->first();
    }

    public function deleteHomeService($appointmentNo, $company)
    {
        if (!$appointmentNo) {
            return 2;
        }

        $summary = HomeServiceAppSummaryGen::where('appointment_no', $appointmentNo)
            ->where('company', $company)
            ->first();

        if (!$summary) {
            return 2;
        }

        if ($summary->status != config('constants.APPOINTMENT_PENDING')) {
            return 2;
        }
        DB::beginTransaction();
        try {

            // ✅ Delete summary
            HomeServiceAppSummaryGen::where('appointment_no', $appointmentNo)
                ->where('company', $company)
                ->delete();

            // ✅ Delete details
            HomeServiceAppDetail::where('appointment_no', $appointmentNo)
                ->delete();

            DB::commit();

            return 1;

        } catch (\Exception $e) {
            DB::rollBack();
            return 2;
        }
    }

    public function getHomeServiceEmployee($employeeId = null, array $employeeIdArr = [], $flag = null)
    {
        $query = DB::table('employee')
            ->select([
                'employee.*',
                'occupation_father_tb.element as father_occupation_name',
                'occupation_mother_tb.element as mother_occupation_name',
                'occupation_spouse_tb.element as spouse_occupation_name',
                'emer_con_rel_tb.element as emer_contact_relation_name',
                'guardian_rel_tb.element_code as guardian_relation_name',
                'designation_tb.element as designation_name',
                'user_group.group_name'
            ])
            ->leftJoin('common_table as occupation_father_tb', 'occupation_father_tb.element_code', '=', 'employee.father_occupation')
            ->leftJoin('common_table as occupation_mother_tb', 'occupation_mother_tb.element_code', '=', 'employee.mother_occupation')
            ->leftJoin('common_table as occupation_spouse_tb', 'occupation_spouse_tb.element_code', '=', 'employee.spouse_occupation')
            ->leftJoin('common_table as emer_con_rel_tb', 'emer_con_rel_tb.element_code', '=', 'employee.emer_contact_relation')
            ->leftJoin('common_table as guardian_rel_tb', 'guardian_rel_tb.element_code', '=', 'employee.guardian_relation')
            ->leftJoin('common_table as designation_tb', 'designation_tb.element_code', '=', 'employee.designation')
            ->leftJoin('users', 'users.user_id', '=', 'employee.employee_id')
            ->leftJoin('user_group', 'user_group.id', '=', 'users.user_group');

        $userGroupBlockListArr = config('constants.USERGROUP_BLOCKLIST');

        $currentUserGroup = Auth::user()->user_group ?? null;

        if (!in_array($currentUserGroup, $userGroupBlockListArr)) {
            $query->whereNotIn('user_group.id', $userGroupBlockListArr);
        }

        // Flag check for active status
        if ($flag === null) {
            $query->where('employee.is_active', 1);
        }

        // Employee filtering logic
        if ($employeeId) {
            $query->where('employee.employee_id', $employeeId);
        } elseif (!empty($employeeIdArr)) {
            $query->whereIn('employee.employee_id', $employeeIdArr);
        }

        // Result returning as array to maintain compatibility
        return $query->get();
    }

    public function getEmpPerInfo(array $whereArr)
    {
        if (empty($whereArr['empId'])) {
            return [];
        }

        $result = DB::table('employee')
            ->select('employee_id', 'employee_name')
            ->where('employee_id', $whereArr['empId'])
            ->first();

        return $result;
    }

    public function getEmpHomeServiceList(array $arr)
    {
        if (empty($arr['empId'])) {
            return [];
        }

        $result = DB::table('home_service_app_summary_gen as hs')
            ->select(
                'hs.*',
                'cc.title as company_name',
                'cc.company_type'
            )
            ->join('corporate_companies as cc', 'cc.company_code', '=', 'hs.company')
            ->where('hs.assign_emp', $arr['empId'])
            ->orderBy('hs.created_dt_tm', 'DESC')
            ->get();

        return $result;
    }

    public function getEmpAppointmentSummary(array $whereArr)
    {
        if (empty($whereArr['empId']) || empty($whereArr['appointmentNo'])) {
            return 0;
        }

        $result = DB::table('home_service_app_summary_gen as hs')
            ->select(
                'hs.*',
                'e.employee_name as assigned_employee_name',
                'e.primary_mobile as assigned_employee_mobile'
            )
            ->leftJoin('employee as e', 'e.employee_id', '=', 'hs.assign_emp')
            ->where('hs.assign_emp', $whereArr['empId'])
            ->where('hs.appointment_no', $whereArr['appointmentNo'])
            ->first();

        return $result ? $result : 0;
    }

    public function getAppoinmentDetail($appointmentNo)
    {
        if (empty($appointmentNo)) {
            return [];
        }

        $result = DB::table('home_service_app_detail_gen as hd')
            ->select(
                'hd.*',
                'sv.service_variant_name',
                'sv.unit_name'
            )
            ->join('service_variants as sv', 'sv.variant_code', '=', 'hd.service_variant')
            ->where('hd.appointment_no', $appointmentNo)
            ->get();

        return $result;
    }

    public function startEmpHomeService(array $updateArr, array $whereArr)
    {
        if (empty($whereArr['appointmentNo'])) {
            return false;
        }

        return DB::table('home_service_app_summary_gen')
            ->where('appointment_no', $whereArr['appointmentNo'])
            ->update($updateArr);
    }

    public function getAppointmentSummary($appointmentNo, $companyCode = null)
    {
        $query = DB::table('home_service_app_summary_gen')
            ->select(
                'home_service_app_summary_gen.*',
                'employee.employee_name as assigned_employee_name',
                'employee.primary_mobile as assigned_employee_mobile'
            )
            ->leftJoin('employee', 'employee.employee_id', '=', 'home_service_app_summary_gen.assign_emp')
            ->where('home_service_app_summary_gen.appointment_no', $appointmentNo);

        if ($companyCode) {
            $query->where('home_service_app_summary_gen.company', $companyCode);
        }

        $result = $query->first();

        if ($result) {
            return $result;
        }

        return 0;
    }


    public function editHomeService(array $finalArr)
    {
        DB::transaction(function () use ($finalArr) {

            // Update summary
            DB::table('home_service_app_summary_gen')
                ->where('appointment_no', $finalArr['appointmentNo'])
                ->where('company', $finalArr['company'])
                ->update($finalArr['summaryArr']);

            // Insert batch
            if (!empty($finalArr['insertDetailArr'])) {
                DB::table('home_service_app_detail_gen')
                    ->insert($finalArr['insertDetailArr']);
            }

            // Update batch (no direct updateBatch in Laravel → loop)
            if (!empty($finalArr['updateDetailArr'])) {
                foreach ($finalArr['updateDetailArr'] as $row) {
                    DB::table('home_service_app_detail_gen')
                        ->where('id', $row['id'])
                        ->update($row);
                }
            }

            // Delete records
            if (!empty($finalArr['deleteDetailArr'])) {
                DB::table('home_service_app_detail_gen')
                    ->where('appointment_no', $finalArr['appointmentNo'])
                    ->whereIn('service_variant', $finalArr['deleteDetailArr'])
                    ->delete();
            }

        });
    }

    public function rejectEmpHomeService(array $updateArr, array $whereArr)
    {
        DB::table('home_service_app_summary_gen')
            ->where('appointment_no', $whereArr['appointmentNo'])
            ->update($updateArr);
    }


    public function completeEmpHomeService(array $updateArr, array $whereArr)
    {
        DB::table('home_service_app_summary_gen')
            ->where('appointment_no', $whereArr['appointmentNo'])
            ->update($updateArr);
    }

    public function getHomeServiceCalendarValue($arr)
    {
        $query = DB::table('home_service_app_summary_gen')
            ->selectRaw("
                CONCAT(appointment_no, ' ', name, ' (', mobile, ')') as title,
                '#964545' as color,
                final_date as start,
                (final_date + INTERVAL 1 DAY) as end
            ")
            ->where('status', config('constants.APPOINTMENT_ACCEPT'))
            ->where('final_date', '>=', $arr['fromDate'])
            ->where('final_date', '<=', $arr['toDate']);

        return $query->get();
    }

}