<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\CustomerEmployee;
use Illuminate\Support\Facades\DB;

class CRMCallLogRepository
{

    public function getCallLog($arr)
    {
        $query = DB::table('call_center_log')
            ->select(
                'call_center_log.*',
                'call_reason.title as reason_title',
                'customer_feedback.title as feedback_title',
                'common_table.element as call_type_title',
                'employee.employee_name as created_by_name'
            )
            ->leftJoin('call_reason', 'call_reason.reason_code', '=', 'call_center_log.call_reason')
            ->leftJoin('customer_feedback', 'customer_feedback.feedback_code', '=', 'call_center_log.customer_feedback')
            ->join('common_table', 'common_table.element_code', '=', 'call_center_log.call_type')
            ->join('employee', 'employee.employee_id', '=', 'call_center_log.created_by');

        // historyDate condition
        if (!empty($arr['historyDate'])) {
            $query->whereDate('call_center_log.call_start_dt_tm', $arr['historyDate'])
                ->orderBy('call_center_log.call_start_dt_tm', 'DESC');
        }

        // taskDate condition
        if (!empty($arr['taskDate'])) {
            $query->whereIn('call_center_log.next_call_status', ['1', '3'])
                ->whereDate('call_center_log.next_call_dt_tm', $arr['taskDate'])
                ->orderBy('call_center_log.next_call_dt_tm', 'ASC');
        }

        // logId condition
        if (!empty($arr['logId'])) {
            $query->where('call_center_log.log_id', $arr['logId']);
        }

        return $query->get();
    }

    public function changeNextCallStatus($updateArr, $logId)
    {
        DB::table('call_center_log')
            ->where('log_id', $logId)
            ->update($updateArr);
    }


    public function getIndividualCustomer($companyCode)
    {
        return DB::table('corporate_companies')
            ->select(
                'title as name',
                'company_code as customer_id',
                'company_mobile as mobile',
                'address'
            )
            ->where('company_type', config('constants.INDIVIDUAL_CUST'))
            ->where('company_code', $companyCode)
            ->get();
    }

    public function getCallLeads($leadCode = null)
    {
        $query = DB::table('call_leads');

        if (!empty($leadCode)) {
            $query->where('lead_code', $leadCode);
        }

        return $query->get();
    }

    public function getCustomerFeedback($feedbackCode = null, $isActiveFlag = 1)
    {
        $query = DB::table('customer_feedback')
            ->select('customer_feedback.*', 'common_table.element as call_type_name')
            ->join('common_table', 'common_table.element_code', '=', 'customer_feedback.call_type');

        // filter by feedbackCode
        if (!empty($feedbackCode)) {
            $query->where('feedback_code', $feedbackCode);
        }

        // is_active logic
        if ($isActiveFlag == 1) {
            $query->where('customer_feedback.is_active', 1);
        } elseif ($isActiveFlag == 2) {
            $query->where('customer_feedback.is_active', 0);
        }

        // ordering
        $query->orderBy('customer_feedback.call_type')
            ->orderBy('customer_feedback.feedback_order');

        return $query->get();
    }

    public function updateForNextCallLog($updateArr, $logId)
    {
        $row = DB::table('call_center_log')
            ->where('log_id', $logId)
            ->first();

        if (!$row) {
            return 2; // fake request
        }

        $nextCallStatus = $row->next_call_status;

        if ($nextCallStatus == config('constants.HOLD_NEXT_CALL')) {

            $nextCallFlagDtTm = strtotime($row->next_call_flag_dt_tm);
            $currentTime = strtotime(date('y-m-d H:i:s'));

            $minute = round(abs($nextCallFlagDtTm - $currentTime) / 60, 2);

            if ($minute > config('constants.CALL_UNLOCK_MINUITE')) {

                DB::table('call_center_log')
                    ->where('log_id', $logId)
                    ->update($updateArr);

                return 1;
            } else {
                return 3; // one user has engaged with call
            }
        }

        if ($nextCallStatus == config('constants.HAVE_NEXT_CALL')) {

            DB::table('call_center_log')
                ->where('log_id', $logId)
                ->update($updateArr);

            return 1;
        }

        return 2; // fake request
    }

    public function addCallLog($insertArr, $previousLogUpdateArr, $previousLogId, $leadCode, $leadUpdateArr)
    {
        // Insert main call log
        DB::table('call_center_log')->insert($insertArr);

        // Update previous log if exists
        if ($previousLogId) {
            DB::table('call_center_log')
                ->where('log_id', $previousLogId)
                ->update($previousLogUpdateArr);
        }

        // Update lead if exists
        if ($leadCode) {
            DB::table('call_leads')
                ->where('lead_code', $leadCode)
                ->update($leadUpdateArr);
        }

        return 1;
    }

    public function getEditCallLog(string $logId)
    {
        return DB::table('call_center_log')
            ->select(
                'call_center_log.*',
                'call_reason.title as reason_title',
                'customer_feedback.title as feedback_title',
                'common_table.element as call_type_title'
            )
            ->leftJoin('call_reason', 'call_reason.reason_code', '=', 'call_center_log.call_reason')
            ->leftJoin('customer_feedback', 'customer_feedback.feedback_code', '=', 'call_center_log.customer_feedback')
            ->join('common_table', 'common_table.element_code', '=', 'call_center_log.call_type')
            ->where('call_center_log.log_id', $logId)
            ->first();
    }

    public function editCallLog(array $updateArr)
    {
        DB::table('call_center_log')
            ->where('log_id', $updateArr['log_id'])
            ->update($updateArr);

        return 1;
    }

    public function removeCallLog($logId)
    {
        $exists = DB::table('call_center_log')
            ->where('log_id', $logId)
            ->first();

        if (!$exists) {
            return 2;
        }

        $childExists = DB::table('call_center_log')
            ->where('ref_log_id', $logId)
            ->exists();

        if ($childExists) {
            return 3;
        }

        DB::table('call_center_log')
            ->where('log_id', $logId)
            ->delete();

        return 1;
    }

    public function getCustomer($customerType)
    {
        return DB::table('corporate_companies')
            ->select('title', 'company_code', 'company_mobile')
            ->where('company_type', $customerType)
            ->where('is_active', 1)
            ->get();
    }


    public function getIndividualAccList($arr)
    {
        // ----------- BASIC SEARCH -----------
        if (
            $arr['fromDate'] == "" &&
            $arr['toDate'] == "" &&
            $arr['serviceCodeArr'] == []
        ) {
            //dd($arr);
            $query = DB::table('corporate_companies')
                ->select('corporate_companies.*')
                ->where('corporate_companies.company_type', config('constants.INDIVIDUAL_CUST'));

            if ($arr['customerId'] != "") {
                $query->where('corporate_companies.company_code', 'like', '%' . $arr['customerId'] . '%');
            }

            if ($arr['customerName'] != "") {
                $query->where('corporate_companies.title', 'like', '%' . $arr['customerName'] . '%');
            }

            if ($arr['customerMobile'] != "") {
                $query->where('corporate_companies.company_mobile', 'like', '%' . $arr['customerMobile'] . '%');
            }

            return $query->get();
        }

        // ----------- ADVANCE SEARCH -----------
        $query = DB::table('home_service_app_detail_gen')
            ->select(
                'home_service_app_detail_gen.appointment_no',
                'home_service_app_detail_gen.service_variant',
                'home_service_app_summary_gen.company',
                'home_service_app_summary_gen.final_date',
                'corporate_companies.title',
                'corporate_companies.company_code',
                'corporate_companies.address',
                'corporate_companies.company_mobile',
                'corporate_companies.is_active',
                'service_variants.service_variant_name'
            )
            ->leftJoin(
                'home_service_app_summary_gen',
                'home_service_app_summary_gen.appointment_no',
                '=',
                'home_service_app_detail_gen.appointment_no'
            )
            ->join(
                'corporate_companies',
                'corporate_companies.company_code',
                '=',
                'home_service_app_summary_gen.company'
            )
            ->join(
                'service_variants',
                'service_variants.variant_code',
                '=',
                'home_service_app_detail_gen.service_variant'
            )
            ->where('corporate_companies.company_type', config('constants.INDIVIDUAL_CUST'))
            ->where('home_service_app_summary_gen.status', config('constants.APPOINTMENT_CASH_COLLECT'));

        if ($arr['customerId'] != "") {
            $query->where('corporate_companies.company_code', 'like', '%' . $arr['customerId'] . '%');
        }

        if ($arr['customerName'] != "") {
            $query->where('corporate_companies.title', 'like', '%' . $arr['customerName'] . '%');
        }

        if ($arr['customerMobile'] != "") {
            $query->where('corporate_companies.company_mobile', 'like', '%' . $arr['customerMobile'] . '%');
        }

        if ($arr['fromDate'] != "" && $arr['toDate'] != "") {
            $query->where('home_service_app_summary_gen.final_date', '>=', $arr['fromDate'])
                ->where('home_service_app_summary_gen.final_date', '<=', $arr['toDate']);
        }

        if ($arr['serviceCodeArr'] != []) {
            $query->whereIn('home_service_app_detail_gen.service_variant', $arr['serviceCodeArr']);
        }

        $query->orderBy('home_service_app_summary_gen.final_date', 'DESC');

        return $query->get();
    }

    public function addCallLeadsList($callLeadsArr, $mobileNoArr)
    {
        // Check existing mobile numbers
        $results = DB::table('call_leads')
            ->whereIn('mobile', $mobileNoArr)
            ->get()
            ->toArray();

        if (count($results) > 0) {
            return [
                'result' => 4,
                'mobileNumberExist' => $results[0]->mobile
            ];
        }

        // Insert batch
        DB::table('call_leads')->insert($callLeadsArr);

        return [
            'result' => 1
        ];
    }

}