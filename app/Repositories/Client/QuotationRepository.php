<?php

namespace App\Repositories\Client;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

class QuotationRepository
{
    public function getQoutationReqest(array $arr): array
    {
        $query = DB::table('quotation_req_summary as qrs')
            ->select(
                'qrs.*',
                'cc.title as company_name'
            )
            ->join(
                'corporate_companies as cc',
                'cc.company_code',
                '=',
                'qrs.customer'
            );

        if (
            isset($arr['customerType']) &&
            $arr['customerType'] == config('constants.CORPORATE_CUST')
        ) {

            $query->where('qrs.customer', $arr['companyCode']);
        }

        if (
            isset($arr['status']) &&
            $arr['status'] != config('constants.REQ_QUOT_ALL_STATUS')
        ) {

            $query->where('qrs.status', $arr['status']);
        }

        return $query
            ->orderByDesc('qrs.created_dt_tm')
            ->get()
            ->toArray();
    }

    public function addQuotationRequest(
        array $quotationSummaryArr,
        array $quotationDetailInsertArr
    ): int {

        DB::transaction(function () use (
            $quotationSummaryArr,
            $quotationDetailInsertArr
        ) {

            DB::table('quotation_req_summary')
                ->insert($quotationSummaryArr);

            DB::table('quotation_req_detail')
                ->insert($quotationDetailInsertArr);
        });

        return 1;
    }

    public function checkFirstQuotReqExist(array $arr): int
    {
        $query = DB::table('quotation_req_summary');

        if (($arr['customerType'] ?? null) == config('constants.CORPORATE_CUST')) {
            $query->where('customer', $arr['companyCode']);
        }

        $query->where('request_no', $arr['requestNo']);

        return $query->exists() ? 1 : 0;
    }

    public function getQuotationReqSummary(string $requestNo)
    {
        return DB::table('quotation_req_summary as qrs')
            ->select(
                'qrs.*',
                'cc.title as company_name',
                'e.employee_name as rm_name',
                'e.email as rm_email',
                'e.primary_mobile as rm_mobile'
            )
            ->leftJoin('employee as e', 'e.employee_id', '=', 'qrs.rm_id')
            ->join('corporate_companies as cc', 'cc.company_code', '=', 'qrs.customer')
            ->where('qrs.request_no', $requestNo)
            ->get();
    }

    public function getQuotationReqDetails(string $requestNo)
    {
        return DB::table('quotation_req_detail as qrd')
            ->select(
                'qrd.*',
                'sv.service_variant_name',
                'v.registration_no'
            )
            ->leftJoin(
                'service_variants as sv',
                'sv.variant_code',
                '=',
                'qrd.service_veriant'
            )
            ->join(
                'vehicles as v',
                'v.vehicle_id',
                '=',
                'qrd.vehicle'
            )
            ->where('qrd.request_no', $requestNo)
            ->get();
    }

    public function getRequestedVehicle(string $requestNo)
    {
        return DB::table('quotation_req_detail as qrd')
            ->select(
                'qrd.vehicle',
                'v.registration_no'
            )
            ->join(
                'vehicles as v',
                'v.vehicle_id',
                '=',
                'qrd.vehicle'
            )
            ->where('qrd.request_no', $requestNo)
            ->distinct()
            ->get();
    }

    public function editQuotationRequest(
        array $quotationSummaryArr, 
        array $quotationDetailInsertArr, 
        array $quotationDetailUpdateArr, 
        string $requestNo, 
        array $deleteArr, 
        string $updateDtTm
    ): int {
        try {
            
            DB::beginTransaction();

            // 1. Optimistic locking check
            $row = DB::table('quotation_req_summary')
                ->select('updated_dt_tm')
                ->where('request_no', $requestNo)
                ->first();

            // If row doesn't exist or timestamp has changed, roll back and abort with status 3
            if (!$row || $row->updated_dt_tm != $updateDtTm) {
                DB::rollBack();
                return 3;
            }

            $productReqDetailNoArr = [];

            // Helper to safely parse comma-separated IDs into a clean array, filtering out empty values
            $parseIds = function ($str) {
                return array_filter(explode(',', trim($str ?? '')), 'strlen');
            };

            // 2. Fetch tracking detail numbers from product delete string
            $productDeleteIds = $parseIds($deleteArr['productDelteStr'] ?? '');
            if (!empty($productDeleteIds)) {
                $productDetails = DB::table('quotation_req_detail')
                    ->whereIn('id', $productDeleteIds)
                    ->pluck('request_details_no')
                    ->toArray();
                    
                $productReqDetailNoArr = array_merge($productReqDetailNoArr, $productDetails);
            }

            // 3. Fetch tracking detail numbers from vehicle delete string
            $vehicleDeleteIds = $parseIds($deleteArr['vehicleDeleteStr'] ?? '');
            if (!empty($vehicleDeleteIds)) {
                $vehicleDetails = DB::table('quotation_req_detail')
                    ->whereIn('vehicle', $vehicleDeleteIds)
                    ->where('request_no', $requestNo)
                    ->pluck('request_details_no')
                    ->toArray();
                    
                $productReqDetailNoArr = array_merge($productReqDetailNoArr, $vehicleDetails);
            }

            // 4. Delete related responses from quotation_res_detail
            if (!empty($productReqDetailNoArr)) {
                DB::table('quotation_res_detail')
                    ->whereIn('req_detail_no', $productReqDetailNoArr)
                    ->delete();
            }

            $serviceDeleteIds = $parseIds($deleteArr['serviceDeleteStr'] ?? '');

            if (!empty($serviceDeleteIds)) {
                DB::table('quotation_res_detail')
                    ->whereIn('req_detail_no', $serviceDeleteIds)
                    ->delete();
            }

            // 5. Delete records from quotation_req_detail based on your exact conditions
            if (!empty($vehicleDeleteIds)) {
                DB::table('quotation_req_detail')
                    ->whereIn('vehicle', $vehicleDeleteIds)
                    ->where('request_no', $requestNo)
                    ->delete();
            }

            if (!empty($serviceDeleteIds)) {
                DB::table('quotation_req_detail')
                    ->whereIn('request_details_no', $serviceDeleteIds)
                    ->delete();
            }

            if (!empty($productDeleteIds)) {
                DB::table('quotation_req_detail')
                    ->whereIn('id', $productDeleteIds)
                    ->delete();
            }

            // 6. Update the quotation summary table
            DB::table('quotation_req_summary')
                ->where('request_no', $requestNo)
                ->update($quotationSummaryArr);

            // 7. Handle batch insertions
            if (!empty($quotationDetailInsertArr)) {
                DB::table('quotation_req_detail')->insert($quotationDetailInsertArr);
            }

            // 8. Handle batch updates (Replaces CodeIgniter's update_batch)
            if (!empty($quotationDetailUpdateArr)) {
                foreach ($quotationDetailUpdateArr as $updateRow) {
                    $key = $updateRow['request_details_no'];
                    // Unset the tracking key so it isn't included in the SQL SET statement
                    unset($updateRow['request_details_no']);

                    DB::table('quotation_req_detail')
                        ->where('request_details_no', $key)
                        ->update($updateRow);
                }
            }

            // Commit all executions safely if everything went through without a hitch
            DB::commit();
            return 1;

        } catch (Exception $e) {

            DB::rollBack();
            
            // Log the exception details so you can review issues in your storage/logs/laravel.log files
            logger()->error('Quotation edit failure: ' . $e->getMessage(), [
                'request_no' => $requestNo,
                'trace' => $e->getTraceAsString()
            ]);

            // Return a zero or error flag status to indicate internal exception failure
            return 0; 
        }
    }

    public function checkQuotReqStatus(string $requestNo): ?int
    {
        return DB::table('quotation_req_summary')
            ->where('request_no', $requestNo)
            ->value('status');
    }

    public function approveQuotation(
        string $quotationNo,
        string $requestNo,
        string $companyCode,
        ?string $referenceNo
    ): int {
        $exists = DB::table('quotation_res_summary')
            ->join(
                'quotation_req_summary',
                'quotation_req_summary.request_no',
                '=',
                'quotation_res_summary.request_no'
            )
            ->where('quotation_res_summary.status', config('constants.QUO_SEND_STATUS'))
            ->where('quotation_req_summary.status', config('constants.REQ_QUOT_SUB_STATUS'))
            ->where('quotation_res_summary.quotation_no', $quotationNo)
            ->where('quotation_res_summary.request_no', $requestNo)
            ->where('quotation_req_summary.customer', $companyCode)
            ->exists();

        if (!$exists) {
            return 2;
        }

        $companyInfo = DB::table('corporate_companies')
            ->select('rm_id')
            ->where('company_code', $companyCode)
            ->first();

        $createUpdateUser = Auth::user()->user_id;
        $dateTime = Carbon::now();

        DB::transaction(function () use (
            $quotationNo,
            $requestNo,
            $referenceNo,
            $companyInfo,
            $createUpdateUser,
            $dateTime
        ) {

            DB::table('quotation_req_summary')
                ->where('request_no', $requestNo)
                ->update([
                    'approved_quotation_no' => $quotationNo,
                    'status'                => config('constants.REQ_QUOT_APPV_CUS_STATUS'),
                    'reference_no'          => $referenceNo,
                    'updated_by'            => $createUpdateUser,
                    'updated_dt_tm'         => $dateTime,
                    'updated_by_type'       => config('constants.P_ADMIN'),
                    'rm_id'                 => $companyInfo?->rm_id,
                ]);

            DB::table('quotation_res_summary')
                ->where('quotation_no', $quotationNo)
                ->update([
                    'status'         => config('constants.QUO_APPROV_STATUS'),
                    'updated_by'     => $createUpdateUser,
                    'updated_dt_tm'  => $dateTime,
                ]);
        });

        return 1;
    }
}