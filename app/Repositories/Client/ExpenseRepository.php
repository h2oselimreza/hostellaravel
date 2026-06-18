<?php

namespace App\Repositories\Client;

use App\Models\Client\Vehicle;
use App\Models\Client\VehicleAssignDetail;
use App\Models\Client\VehicleBookingSummary;
use App\Models\Company;
use App\Models\CustomerEmployee;
use Illuminate\Support\Facades\DB;
use File;

class ExpenseRepository
{


    public function getExpenseSummary(array $arr, int $perPage = 10)
    {
        return DB::table('expense_summary as es')
        ->select('es.*', 'cv.title as vendor_title')
        ->leftJoin('corporate_vendor as cv', 'cv.vendor_code', '=', 'es.vendor')

        ->where('es.company', $arr['company'])

        ->when(!empty($arr['expenseType']), fn($q) =>
            $q->where('es.expense_type', $arr['expenseType'])
        )

        ->when(!empty($arr['expenseNo']), fn($q) =>
            $q->where('es.expense_no', $arr['expenseNo'])
        )

        ->when(!empty($arr['title']), fn($q) =>
            $q->where('es.expense_title', 'LIKE', '%' . $arr['title'] . '%')
        )

        ->when(!empty($arr['fromDate']), fn($q) =>
            $q->whereDate('es.expense_date', '>=', $arr['fromDate'])
        )

        ->when(!empty($arr['toDate']), fn($q) =>
            $q->whereDate('es.expense_date', '<=', $arr['toDate'])
        )

        ->orderByDesc('es.created_dt_tm')

        ->paginate($perPage);
    }

    public function getExpenseSummaryList(array $arr)
    {
        return DB::table('expense_summary as es')
        ->select('es.*', 'cv.title as vendor_title')
        ->leftJoin('corporate_vendor as cv', 'cv.vendor_code', '=', 'es.vendor')

        ->where('es.company', $arr['company'])

        ->when(!empty($arr['expenseType']), fn($q) =>
            $q->where('es.expense_type', $arr['expenseType'])
        )

        ->when(!empty($arr['expenseNo']), fn($q) =>
            $q->where('es.expense_no', $arr['expenseNo'])
        )

        ->when(!empty($arr['title']), fn($q) =>
            $q->where('es.expense_title', 'LIKE', '%' . $arr['title'] . '%')
        )

        ->when(!empty($arr['fromDate']), fn($q) =>
            $q->where('es.expense_date', '>=', $arr['fromDate'])
        )

        ->when(!empty($arr['toDate']), fn($q) =>
            $q->where('es.expense_date', '<=', $arr['toDate'])
        )

        ->orderByDesc('es.created_dt_tm')

        ->paginate(10);
    }

    public function addExpense($summaryArr, $detailsArr, $insertFileArr)
    {
        if (!$detailsArr || count($detailsArr) == 0) {
            return 2;
        }

        DB::table('expense_summary')->insert($summaryArr);

        DB::table('expense_detail')->insert($detailsArr);

        if ($insertFileArr && count($insertFileArr) > 0) {
            DB::table('expense_files')->insert($insertFileArr);
        }

        return 1;
    }

    public function getExpenseTakenVehicle($arr)
    {
        return DB::table('expense_detail')
            ->select(
                'expense_detail.vehicle',
                'vehicles.registration_no'
            )
            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'expense_detail.vehicle'
            )
            ->where('expense_detail.expense_no', $arr['expenseNo'])
            ->distinct()
            ->get();
    }

    public function getExpenseDetails($arr)
    {
        return DB::table('expense_detail')
            ->select(
                'expense_detail.*',
                'cost_heads.cost_head as expense_head_name'
            )
            ->join(
                'cost_heads',
                'cost_heads.cost_head_code',
                '=',
                'expense_detail.expense_head'
            )
            ->where('expense_detail.expense_no', $arr['expenseNo'])
            ->get();
    }

    public function getExpenseFiles($arr)
    {
        return DB::table('expense_files')
            ->where('expense_no', $arr['expenseNo'])
            ->get();
    }

    public function editExpense($summaryArr, $detailsUpdateArr, $detailsInsertArr, $detailTableIdArr, $insertFileArr, $expenseNo, $deleteArr, $updateDtTm, $companyCode)
    {
        return DB::transaction(function () use ($summaryArr, $detailsUpdateArr, $detailsInsertArr, $detailTableIdArr, $insertFileArr, $expenseNo, $deleteArr, $updateDtTm, $companyCode) {
            // 1. Contingency Check (Optimistic Locking)
            $summary = DB::table('expense_summary')
                ->where('expense_no', $expenseNo)
                ->where('updated_dt_tm', $updateDtTm)
                ->where('company', $companyCode)
                ->first();
            if (!$summary) return 3;

            // 2. Detail ID existence check
            $dbDetailIds = DB::table('expense_detail')
                ->where('expense_no', $expenseNo)
                ->pluck('id')
                ->toArray();

            foreach ($detailTableIdArr as $id) {
                if (!in_array($id, $dbDetailIds)) return 4;
            }

            // 3. Deletion Logic
            if (!empty($deleteArr['vehicleStr'])) {
                DB::table('expense_detail')
                    ->where('expense_no', $expenseNo)
                    ->whereIn('vehicle', explode(',', $deleteArr['vehicleStr']))
                    ->delete();
            }

            if (!empty($deleteArr['expenseHeadStr'])) {
                DB::table('expense_detail')
                    ->where('expense_no', $expenseNo)
                    ->whereIn('id', explode(',', $deleteArr['expenseHeadStr']))
                    ->delete();
            }

            if (!empty($deleteArr['fileStr'])) {
                $files = DB::table('expense_files')
                    ->whereIn('id', explode(',', $deleteArr['fileStr']))
                    ->where('expense_no', $expenseNo)
                    ->get();

                foreach ($files as $file) {
                    $path = public_path('assets/files/expense/' . $file->file_name);
                    if (File::exists($path)) File::delete($path);
                }

                DB::table('expense_files')
                    ->whereIn('id', explode(',', $deleteArr['fileStr']))
                    ->where('expense_no', $expenseNo)
                    ->delete();
            }

            // 4. Batch Updates & Inserts
            DB::table('expense_summary')->where('expense_no', $expenseNo)->update($summaryArr);

            if (!empty($detailsUpdateArr)) {
                foreach ($detailsUpdateArr as $data) {
                    $id = $data['id'];
                    unset($data['id']);
                    DB::table('expense_detail')->where('id', $id)->update($data);
                }
            }

            if (!empty($detailsInsertArr)) {
                DB::table('expense_detail')->insert($detailsInsertArr);
            }

            if (!empty($insertFileArr)) {
                DB::table('expense_files')->insert($insertFileArr);
            }

            return 5;
        });
    }

    public function deleteExpense($expenseNo, $companyCode)
    {
        $expenseSummary = DB::table('expense_summary')
            ->where('expense_no', $expenseNo)
            ->where('company', $companyCode)
            ->first();

        if (empty($expenseSummary)) {
            return 2;
        }

        DB::table('expense_summary')
            ->where('expense_no', $expenseNo)
            ->delete();

        DB::table('expense_detail')
            ->where('expense_no', $expenseNo)
            ->delete();

        //--------- remove file from folder ----------------//

        $results = DB::table('expense_files')
            ->select('file_name')
            ->where('expense_no', $expenseNo)
            ->get();

        foreach ($results as $result) {

            $file = public_path('assets/client/files/expense/' . $result->file_name);

            if (file_exists($file)) {
                unlink($file);
            }
        }

        DB::table('expense_files')
            ->where('expense_no', $expenseNo)
            ->delete();

        return 1;
    }

}