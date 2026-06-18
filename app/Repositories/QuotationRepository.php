<?php

namespace App\Repositories;

use App\Models\Company;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationRepository
{
    public function getQuotationRequest(array $arr): array
    {
        $query = DB::table('quotation_req_summary')
            ->select(
                'quotation_req_summary.*',
                'corporate_companies.title as company_name'
            )
            ->join(
                'corporate_companies',
                'corporate_companies.company_code',
                '=',
                'quotation_req_summary.customer'
            );

        if (
            isset($arr['customerType']) &&
            $arr['customerType'] == config('constants.CORPORATE_CUST')
        ) {
            $query->where(
                'quotation_req_summary.customer',
                $arr['companyCode']
            );
        }

        if (
            isset($arr['status']) &&
            $arr['status'] != config('constants.REQ_QUOT_ALL_STATUS')
        ) {
            $query->where(
                'quotation_req_summary.status',
                $arr['status']
            );
        }

        $result = $query
            ->orderBy('quotation_req_summary.created_dt_tm', 'DESC')
            ->get();

        return $result->toArray();
    }

    public function checkFirstQuotReqExist(array $arr): int
    {
        $query = DB::table('quotation_req_summary');

        if (
            isset($arr['customerType']) &&
            $arr['customerType'] == config('constants.CORPORATE_CUST')
        ) {
            $query->where('customer', $arr['companyCode']);
        }

        $query->where('request_no', $arr['requestNo']);

        if ($query->count() > 0) {
            return 1;
        }

        return 0;
    }

    public function getQuotationReqSummary(string $requestNo): array
    {
        return DB::table('quotation_req_summary')
            ->select(
                'quotation_req_summary.*',
                'corporate_companies.title as company_name',
                'employee.employee_name as rm_name',
                'employee.email as rm_email',
                'employee.primary_mobile as rm_mobile'
            )
            ->leftJoin(
                'employee',
                'employee.employee_id',
                '=',
                'quotation_req_summary.rm_id'
            )
            ->join(
                'corporate_companies',
                'corporate_companies.company_code',
                '=',
                'quotation_req_summary.customer'
            )
            ->where('quotation_req_summary.request_no', $requestNo)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }

    public function getQuotationReqDetails(string $requestNo): array
    {
        return DB::table('quotation_req_detail')
            ->select(
                'quotation_req_detail.*',
                'service_variants.service_variant_name',
                'vehicles.registration_no'
            )
            ->leftJoin(
                'service_variants',
                'service_variants.variant_code',
                '=',
                'quotation_req_detail.service_veriant'
            )
            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'quotation_req_detail.vehicle'
            )
            ->where('quotation_req_detail.request_no', $requestNo)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }

    public function getRequestedVehicle(string $requestNo): array
    {
        return DB::table('quotation_req_detail')
            ->select(
                'quotation_req_detail.vehicle',
                'vehicles.registration_no'
            )
            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'quotation_req_detail.vehicle'
            )
            ->where('quotation_req_detail.request_no', $requestNo)
            ->distinct()
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }

    public function getQuotationSummary(array $summaryArr)
    {
        $query = DB::table('quotation_res_summary')
            ->select(
                'quotation_res_summary.*',
                'workshops.title as workshop_name',
                'quotation_req_summary.status as request_status',
                'quotation_req_summary.approved_quotation_no'
            )
            ->join(
                'workshops',
                'workshops.workshop_code',
                '=',
                'quotation_res_summary.workshop'
            )
            ->join(
                'quotation_req_summary',
                'quotation_req_summary.request_no',
                '=',
                'quotation_res_summary.request_no'
            );

        if (
            isset($summaryArr['bulkFlag']) &&
            $summaryArr['bulkFlag'] == 0
        ) {
            $query->where(
                'quotation_res_summary.request_no',
                $summaryArr['requestNo']
            );
        } elseif (
            isset($summaryArr['bulkFlag']) &&
            $summaryArr['bulkFlag'] == 1
        ) {
            // where in
            // Keep empty because original CodeIgniter logic is empty
        }

        $query->where(
            'quotation_req_summary.customer',
            $summaryArr['companyCode']
        );

        return $query->get();
    }

    public function requestChangeStatus(string $requestNo, int $changeStatus): int
    {
        $updateData = [];

        $requestInfo = DB::table('quotation_req_summary')
            ->where('request_no', $requestNo)
            ->first();

        if (!$requestInfo) {
            return 2;
        }

        $requestCurrentStatus = $requestInfo->status;

        if ($changeStatus == config('constants.REQ_PROCCESSING_STATUS')) {

            if ($requestCurrentStatus == config('constants.REQ_PENDING_STATUS')) {

                $updateData['status'] = config('constants.REQ_PROCCESSING_STATUS');
                $updateData['reject_reason'] = null;
                $updateData['updated_by'] = Auth::user()->user_id ?? Auth::id();
                $updateData['updated_dt_tm'] = now();
                $updateData['updated_by_type'] = config('constants.P_ADMIN');

            } else {
                return 2;
            }
        }

        if (!empty($updateData)) {

            DB::table('quotation_req_summary')
                ->where('request_no', $requestNo)
                ->update($updateData);

            return 1;
        }

        return 2;
    }

    public function checkQuotReqExist(array $arr): int
    {
        $query = DB::table('quotation_req_summary');

        if ($arr['customerType'] == config('constants.CORPORATE_CUST')) {
            $query->where('customer', $arr['companyCode']);
        }

        $query->where('request_no', $arr['requestNo']);
        $query->where('status', config('constants.REQ_PROCCESSING_STATUS'));

        return $query->exists() ? 1 : 0;
    }

    public function checkQuotReqStatus($requestNo)
    {
        $row = DB::table('quotation_req_summary')
            ->where('request_no', $requestNo)
            ->first();

        return $row->status ?? null;
    }

    public function editQuotationRequest(
        array $quotationSummaryArr,
        array $quotationDetailInsertArr,
        array $quotationDetailUpdateArr,
        string $requestNo,
        array $deleteArr,
        string $updateDtTm
    ) {
        try {

            return DB::transaction(function () use (
                $quotationSummaryArr,
                $quotationDetailInsertArr,
                $quotationDetailUpdateArr,
                $requestNo,
                $deleteArr,
                $updateDtTm
            ) {
                //dd($$quotationSummaryArr, $quotationDetailInsertArr, $quotationDetailUpdateArr);
                // ================= CHECK CONCURRENT UPDATE =================
                $current = DB::table('quotation_req_summary')
                    ->where('request_no', $requestNo)
                    ->value('updated_dt_tm');

                if ($current != $updateDtTm) {
                    return 3;
                }

                // ================= COLLECT PRODUCT DETAIL NOS =================
                $productReqDetailNoArr = [];

                if (!empty($deleteArr['productDelteStr'])) {

                    $results = DB::table('quotation_req_detail')
                        ->select('request_details_no')
                        ->whereIn('id', explode(',', $deleteArr['productDelteStr']))
                        ->get();

                    foreach ($results as $result) {
                        $productReqDetailNoArr[] = $result->request_details_no;
                    }
                }

                // ================= VEHICLE BASED DETAILS =================
                if (!empty($deleteArr['vehicleDeleteStr'])) {

                    $results = DB::table('quotation_req_detail')
                        ->select('request_details_no')
                        ->where('request_no', $requestNo)
                        ->whereIn('vehicle', explode(',', $deleteArr['vehicleDeleteStr']))
                        ->get();

                    foreach ($results as $result) {
                        $productReqDetailNoArr[] = $result->request_details_no;
                    }
                }

                // ================= DELETE FROM RES DETAIL =================
                if (!empty($productReqDetailNoArr)) {

                    DB::table('quotation_res_detail')
                        ->whereIn('req_detail_no', $productReqDetailNoArr)
                        ->delete();
                }

                // ================= DELETE SERVICE RES DETAIL =================
                if (!empty($deleteArr['serviceDeleteStr'])) {

                    DB::table('quotation_res_detail')
                        ->whereIn('req_detail_no', explode(',', $deleteArr['serviceDeleteStr']))
                        ->delete();
                }

                // ================= DELETE FROM REQUEST DETAIL =================

                if (!empty($deleteArr['vehicleDeleteStr'])) {

                    DB::table('quotation_req_detail')
                        ->where('request_no', $requestNo)
                        ->whereIn('vehicle', explode(',', $deleteArr['vehicleDeleteStr']))
                        ->delete();
                }

                if (!empty($deleteArr['serviceDeleteStr'])) {

                    DB::table('quotation_req_detail')
                        ->whereIn('request_details_no', explode(',', $deleteArr['serviceDeleteStr']))
                        ->delete();
                }

                if (!empty($deleteArr['productDelteStr'])) {

                    DB::table('quotation_req_detail')
                        ->whereIn('id', explode(',', $deleteArr['productDelteStr']))
                        ->delete();
                }

                // ================= UPDATE SUMMARY =================
                DB::table('quotation_req_summary')
                    ->where('request_no', $requestNo)
                    ->update($quotationSummaryArr);

                // ================= INSERT NEW DETAILS =================
                if (!empty($quotationDetailInsertArr)) {
                    DB::table('quotation_req_detail')->insert($quotationDetailInsertArr);
                }

                // ================= UPDATE DETAILS =================
                // if (!empty($quotationDetailUpdateArr)) {
                //     DB::table('quotation_req_detail')
                //         ->upsert($quotationDetailUpdateArr, ['request_details_no']);
                // }

                if (!empty($quotationDetailUpdateArr)) {

                    foreach ($quotationDetailUpdateArr as $row) {

                        DB::table('quotation_req_detail')
                            ->where('request_details_no', $row['request_details_no'])
                            ->update([
                                'vehicle' => $row['vehicle'] ?? null,
                                'service_veriant' => $row['service_veriant'] ?? null,
                                'quantity' => $row['quantity'] ?? null,
                                'request_type' => $row['request_type'] ?? null,
                                'product_variant' => $row['product_variant'] ?? null,
                                'product_display_name' => $row['product_display_name'] ?? null,
                                'unit_name' => $row['unit_name'] ?? null,
                                'updated_by' => $row['updated_by'] ?? null,
                                'updated_dt_tm' => $row['updated_dt_tm'] ?? null,
                            ]);
                    }
                }

                return 1;
            });

        } catch (Exception $e) {
            dd($e->getMessage());
            return 0; // or log error if needed
        }
    }

    public function checkAddQuot(array $arr): int
    {
        $query = DB::table('quotation_req_summary');

        if ($arr['customerType'] == config('constants.CORPORATE_CUST')) {
            $query->where('customer', $arr['companyCode']);
        }

        $query->where('request_no', $arr['requestNo']);
        $query->where('status', config('constants.REQ_PROCCESSING_STATUS'));
        $query->orWhere('status', config('constants.REQ_QUOT_SUB_STATUS'));

        return $query->exists() ? 1 : 0;
    }

    public function checkQuotationDuplicate(array $arr)
    {
        $query = DB::table('quotation_res_summary');

        if ($arr['addEditFlag'] == 'edit') {
            $query->whereNotIn('id', [$arr['summaryId']]);
        }

        $query->where('request_no', $arr['requestNo']);
        $query->where('workshop', $arr['workshopCode']);

        if ($query->exists()) {
            return 0;
        }

        return 1;
    }

    public function checkReqContengency(string $updateDtTm, string $requestNo)
    {
        $exists = DB::table('quotation_req_summary')
            ->where('updated_dt_tm', $updateDtTm)
            ->where('request_no', $requestNo)
            ->exists();

        if ($exists) {
            return 1;
        }
        return 0;
    }

    public function addNewQuotation(
        array $detailInsertArr,
        array $summayInsertArr
    ) {
        if ($detailInsertArr) {

            DB::table('quotation_res_detail')
                ->insert($detailInsertArr);

            DB::table('quotation_res_summary')
                ->insert($summayInsertArr);

            return 4;
        }

        return 2;
    }

    public function checkQuotationExits(
        string $quotationNo,
        string $editPrintFlag
    ): int {

        $query = DB::table('quotation_res_summary')
            ->where('quotation_no', $quotationNo);

        if ($editPrintFlag === 'edit') {
            $query->where('status', config('constants.QUO_DRAFT_STATUS'));
        }

        $count = $query->count();

        if ($count > 0) {
            return 1;
        }

        return 0;
    }

    public function checkReviseQuotation(string $quotationNo): int
    {
        $count = DB::table('quotation_res_summary')
            ->where('quotation_no', $quotationNo)
            ->where('status', config('constants.QUO_SEND_STATUS'))
            ->count();

        if ($count > 0) {
            return 1;
        }

        return 0;
    }

    public function getQuotationNoInfo(string $quotationNo)
    {
        return DB::table('quotation_res_summary')
            ->select([
                'quotation_res_summary.request_no',
                'quotation_req_summary.customer as company_code',
                'corporate_companies.title as company_name',
                'corporate_companies.address',
                'corporate_companies.rm_id',
                'employee.employee_name as rm_name',
                'employee.email as rm_email',
                'employee.primary_mobile as rm_mobile',
            ])
            ->join(
                'quotation_req_summary',
                'quotation_req_summary.request_no',
                '=',
                'quotation_res_summary.request_no'
            )
            ->join(
                'corporate_companies',
                'corporate_companies.company_code',
                '=',
                'quotation_req_summary.customer'
            )
            ->leftJoin(
                'employee',
                'employee.employee_id',
                '=',
                'corporate_companies.rm_id'
            )
            ->where('quotation_res_summary.quotation_no', $quotationNo)
            ->first();
    }

    public function getQuotReqDetailsWithPrice(
        string $requestNo,
        string $quotationNo
    ): array {

        $quoResDetailTempTable = 'quotation_res_detail_' . reference_no();

        DB::statement("
            CREATE TEMPORARY TABLE {$quoResDetailTempTable}
            SELECT *
            FROM quotation_res_detail
            WHERE quotation_no = ?
        ", [$quotationNo]);

        $results = DB::table('quotation_req_detail')
            ->select([
                'quotation_req_detail.*',
                'service_variants.service_variant_name',
                'vehicles.registration_no',
                DB::raw("{$quoResDetailTempTable}.unit_price"),
                DB::raw("{$quoResDetailTempTable}.id as res_detail_tb_id"),
                DB::raw("{$quoResDetailTempTable}.quotation_no"),
            ])
            ->leftJoin(
                'service_variants',
                'service_variants.variant_code',
                '=',
                'quotation_req_detail.service_veriant'
            )
            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'quotation_req_detail.vehicle'
            )
            ->leftJoin(
                $quoResDetailTempTable,
                "{$quoResDetailTempTable}.req_detail_no",
                '=',
                'quotation_req_detail.request_details_no'
            )
            ->where('quotation_req_detail.request_no', $requestNo)
            ->get()
            ->toArray();

        return $results;
    }

    public function getQuotResSummary(string $quotationNo)
    {
        return DB::table('quotation_res_summary')
            ->select([
                'quotation_res_summary.*',
                'workshops.title as workshop_name',
                'workshops.address',
            ])
            ->join(
                'workshops',
                'workshops.workshop_code',
                '=',
                'quotation_res_summary.workshop'
            )
            ->where('quotation_res_summary.quotation_no', $quotationNo)
            ->first();
    }

    public function checkQuotationSentStatus($referenceQuoNo)
    {
        $exists = DB::table('quotation_res_summary')
            ->where('quotation_no', $referenceQuoNo)
            ->where('status', config('constants.QUO_SEND_STATUS'))
            ->exists();

        return $exists ? 1 : 0;
    }

    public function checkResContengency($resUpdateDtTm, $quotationNo)
    {
        $exists = DB::table('quotation_res_summary')
            ->where('updated_dt_tm', $resUpdateDtTm)
            ->where('quotation_no', $quotationNo)
            ->exists();

        return $exists ? 1 : 0;
    }

    public function checkQuotationExist($summaryId, $quotationNo)
    {
        $exists = DB::table('quotation_res_summary')
            ->where('id', $summaryId)
            ->where('quotation_no', $quotationNo)
            ->where('status', config('constants.QUO_DRAFT_STATUS'))
            ->exists();

        return $exists ? 1 : 0;
    }

    public function editQuotation($detailInsertArr, $detailUpdateArr, $summayUpdateArr, $summaryId)
    {
        $summaryExists = DB::table('quotation_res_summary')
            ->where('id', $summaryId)
            ->exists();

        if (!$summaryExists) {
            return 6;
        }

        // update summary
        DB::table('quotation_res_summary')
            ->where('id', $summaryId)
            ->update($summayUpdateArr);

        // batch insert details
        if (!empty($detailInsertArr)) {
            DB::table('quotation_res_detail')
                ->insert($detailInsertArr);
        }

        if (!empty($detailUpdateArr)) {
            foreach ($detailUpdateArr as $row) {
                DB::table('quotation_res_detail')
                    ->where('id', $row['id'])
                    ->update([
                        //'description' => $row['description'],
                        //'quantity' => $row['quantity'],
                        'unit_price' => $row['unit_price'],
                        //'amount' => $row['amount'],
                        'updated_dt_tm' => Carbon::now(),
                    ]);
            }
        }

        return 7;
    }

    public function checkQuotPrint(array $arr): int
    {
        $query = DB::table('quotation_req_summary');

        if (
            isset($arr['customerType']) &&
            $arr['customerType'] == config('constants.CORPORATE_CUST')
        ) {
            $query->where('customer', $arr['companyCode']);
        }

        $exists = $query->where('request_no', $arr['requestNo'])
            ->whereNotIn('status', (array) config('constants.REQ_DRAFT_STATUS'))
            ->exists();

        return $exists ? 1 : 0;
    }

    public function quotationSend(string $quotationNo, string $requestNo): int
    {
        $quotationExists = DB::table('quotation_res_summary')
            ->where('quotation_no', $quotationNo)
            ->where('request_no', $requestNo)
            ->where('status', config('constants.QUO_DRAFT_STATUS'))
            ->exists();

        if (!$quotationExists) {
            return 2;
        }

        $requestExists = DB::table('quotation_req_summary')
            ->where('request_no', $requestNo)
            ->where(function ($query) {
                $query->where('status', config('constants.REQ_PROCCESSING_STATUS'))
                    ->orWhere('status', config('constants.REQ_QUOT_SUB_STATUS'));
            })
            ->exists();

        if (!$requestExists) {
            return 2;
        }

        $createUpdateUser = Auth::user()->user_id;
        $dateTime = Carbon::now();

        DB::table('quotation_res_summary')
            ->where('quotation_no', $quotationNo)
            ->update([
                'status'           => config('constants.QUO_SEND_STATUS'),
                'updated_by'       => $createUpdateUser,
                'updated_dt_tm'    => $dateTime,
                'quo_sending_date' => $dateTime,
            ]);

        DB::table('quotation_req_summary')
            ->where('request_no', $requestNo)
            ->update([
                'status'          => config('constants.REQ_QUOT_SUB_STATUS'),
                'updated_by'      => $createUpdateUser,
                'updated_dt_tm'   => $dateTime,
                'updated_by_type' => config('constants.P_ADMIN'),
            ]);

        return 1;
    }

    public function checkCorpQuotationExits(string $quotationNo, string $companyCode): int
    {
        $exists = DB::table('quotation_res_summary')
            ->select('quotation_res_summary.id')
            ->join(
                'quotation_req_summary',
                'quotation_req_summary.request_no',
                '=',
                'quotation_req_summary.request_no'
            )
            ->where('quotation_res_summary.quotation_no', $quotationNo)
            ->where('quotation_req_summary.customer', $companyCode)
            ->whereNotIn(
                'quotation_res_summary.status',
                (array) config('constants.QUO_DRAFT_STATUS')
            )
            ->exists();

        return $exists ? 1 : 0;
    }

    public function rejectQuotation($requestNo, $rejectReason)
    {
        $requestSummary = DB::table('quotation_req_summary')
            ->where('request_no', $requestNo)
            ->first();

        if (!$requestSummary) {
            return 3;
        }

        $requestCurrentStatus = $requestSummary->status;

        if (
            $requestCurrentStatus == config('constants.REQ_PENDING_STATUS') ||
            $requestCurrentStatus == config('constants.REQ_PROCCESSING_STATUS')
        ) {
            $updateData = [
                'status'          => config('constants.REQ_REJECT_STATUS'),
                'reject_reason'   => $rejectReason,
                'updated_by'      => Auth::user()->user_id,
                'updated_dt_tm'   => Carbon::now(),
                'updated_by_type' => config('constants.P_ADMIN'),
            ];
        } else {
            return 3;
        }

        DB::table('quotation_req_summary')
            ->where('request_no', $requestNo)
            ->update($updateData);

        return 1;
    }

}