<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Http\Controllers\Controller;
use App\Models\Admin\ExpenseDetail;
use App\Models\Admin\ExpenseFile;
use App\Models\Admin\ExpenseSummary;
use App\Models\Admin\Vendor;
use App\Repositories\Client\ExpenseRepository;
use App\Repositories\CommonRepository;
use App\Services\GenerateMonthlyToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(ExpenseRepository $expenseRepository){
        $arr['expenseNo'] = "";
        $arr['expenseType'] = config('constants.EXP_TYPE_GENERAL');
        $data = $expenseRepository->getExpenseSummary($arr);
        return view('admin.expense.index',compact('data'));
    }

    public function create(CommonRepository $commonRepository){
        $data['costHeads'] = $commonRepository->getCostHead(1);
        $data['vendors'] = Vendor::where('is_active',1)->get();
        return view('admin.expense.create',$data);
    }

    public function store(Request $request, GenerateMonthlyToken $generateMonthlyToken)
    {
        $detailsArr = [];
        $insertFileArr = [];
        $totalAmount = 0;

        $expenseNo = config('constants.EXPENSE_NO') .$generateMonthlyToken->get_month_token(config('constants.EXPENSE_NO'));

        $takenExpenseCount = $request->takenExpenseCount;

        for ($j = 1; $j <= $takenExpenseCount; $j++) {

            $expenseHead = trim($request->input('expenseHeadCode' . $j));

            if ($expenseHead) {

                $detail = [];

                $detail['expense_no'] = $expenseNo;
                $detail['expense_head'] = $expenseHead;
                $detail['quantity'] = (float) $request->input('quantity' . $j);
                $detail['unit_name'] = trim($request->input('unitName' . $j));
                $detail['unit_price'] = (float) $request->input('unitPrice' . $j);
                $detail['adjust'] = (float) $request->input('adjust' . $j);

                $postedAmount = (float) $request->input('amount' . $j);

                $detail['amount'] =
                    ($detail['quantity'] * $detail['unit_price']) +
                    $detail['adjust'];

                if ($detail['amount'] != $postedAmount) {
                    return redirect()
                        ->back()
                        ->with('error', 'Amount mismatch detected.');
                }

                $detail['remarks'] = $request->input('remarks' . $j);

                $detail['created_by'] = Auth::user()->user_id;
                $detail['created_dt_tm'] = Carbon::now();
                $detail['updated_by'] = Auth::user()->user_id;
                $detail['updated_dt_tm'] = Carbon::now();

                $totalAmount += $detail['amount'];

                $detailsArr[] = $detail;
            }
        }

        $summaryArr = [
            'expense_title' => trim($request->expenseTitle),
            'vendor'        => $request->vendor ?: null,
            'expense_type'  => config('constants.EXP_TYPE_GENERAL'),
            'expense_date'  => $request->expenseDate,
            'expense_no'    => $expenseNo,
            'total_amount'  => $totalAmount,
            'created_by'    => Auth::user()->user_id,
            'created_dt_tm' => Carbon::now(),
            'updated_by'    => Auth::user()->user_id,
            'updated_dt_tm' => Carbon::now(),
        ];

        if ($summaryArr['vendor']) {

            $summaryArr['guest_name'] = null;
            $summaryArr['guest_mobile'] = null;
            $summaryArr['is_guest'] = 0;

        } else {

            $summaryArr['guest_name'] = $request->guestName;
            $summaryArr['guest_mobile'] = $request->guestMobile;
            $summaryArr['is_guest'] = 1;
        }

        if (
            empty($summaryArr['expense_title']) ||
            empty($summaryArr['expense_date']) ||
            empty($detailsArr)
        ) {
            return redirect()
                ->route('admin.expense.general-expense-list');
        }

        if (
            is_null($summaryArr['vendor']) &&
            is_null($summaryArr['guest_name'])
        ) {
            return redirect()
                ->route('admin.expense.general-expense-list');
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Upload Files
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('expenseFile')) {

                foreach ($request->file('expenseFile') as $file) {

                    $fileName = Str::uuid() . '.' .$file->getClientOriginalExtension();

                    // Upload file
                    $file->storeAs(
                        ExpenseFile::IMAGE_PATH,
                        $fileName,
                        'public'
                    );

                    $insertFileArr[] = [
                        'expense_no'    => $expenseNo,
                        'original_name' => $file->getClientOriginalName(),
                        'file_name'     => $fileName,
                        'created_by'    => Auth::user()->user_id,
                        'created_dt_tm' => Carbon::now(),
                        'updated_by'    => Auth::user()->user_id,
                        'updated_dt_tm' => Carbon::now(),
                    ];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Save Summary
            |--------------------------------------------------------------------------
            */

            ExpenseSummary::create($summaryArr);

            /*
            |--------------------------------------------------------------------------
            | Save Details
            |--------------------------------------------------------------------------
            */

            ExpenseDetail::insert($detailsArr);

            /*
            |--------------------------------------------------------------------------
            | Save Files
            |--------------------------------------------------------------------------
            */

            if (!empty($insertFileArr)) {
                ExpenseFile::insert($insertFileArr);
            }

            DB::commit();

            return redirect()
                ->route('admin.expense.index')
                ->with('success', 'Expense has been added successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->route('admin.expense.index')
                ->with('error', $e->getMessage());
        }
    }

    public function edit(string $expenseNo, ExpenseRepository $expenseRepository, CommonRepository $commonRepository)
    {
        $arr = [
            'expenseNo'   => $expenseNo,
            'expenseType' => config('constants.EXP_TYPE_GENERAL'),
        ];

        $expenseSummary = $expenseRepository->getExpenseSummary($arr);

        if (empty($expenseSummary) || $expenseSummary->isEmpty()) {
            return redirect()->route('admin.expense.general-expense-list');
        }

        $expenseDetails = $expenseRepository->getExpenseDetails($arr);
        $expenseFiles   = $expenseRepository->getExpenseFiles($arr);
        $costHeads      = $commonRepository->getCostHead(1);
        $vendors = Vendor::where('is_active',1)->get();

        return view('admin.expense.edit', compact('expenseSummary','expenseDetails','expenseFiles','costHeads','expenseNo','vendors'));
    }

    public function update(Request $request, $expenseNo)
    {
        DB::beginTransaction();

        try {

            $detailsArr = [];
            $totalAmount = 0;

            $takenExpenseCount = $request->takenExpenseCount;

            // Delete old details
            ExpenseDetail::where('expense_no', $expenseNo)->delete();

            for ($j = 1; $j <= $takenExpenseCount; $j++) {

                $expenseHead = $request->input('expenseHeadCode' . $j);

                if ($expenseHead) {

                    $quantity = (float)$request->input('quantity' . $j);
                    $unitPrice = (float)$request->input('unitPrice' . $j);
                    $adjust = (float)$request->input('adjust' . $j);
                    $amount = (float)$request->input('amount' . $j);

                    $calculatedAmount = ($quantity * $unitPrice) + $adjust;

                    if ($calculatedAmount != $amount) {
                        return back()->with('error', 'Invalid amount calculation.');
                    }

                    $detailsArr[] = [
                        'expense_no'    => $expenseNo,
                        'expense_head'  => (string) $expenseHead,
                        'quantity'      => $quantity,
                        'unit_name'     => $request->input('unitName' . $j),
                        'unit_price'    => $unitPrice,
                        'adjust'        => $adjust,
                        'amount'        => $calculatedAmount,
                        'remarks'       => $request->input('remarks' . $j),
                        'created_by'    => Auth::user()->user_id,
                        'created_dt_tm' => Carbon::now(),
                        'updated_by'    => Auth::user()->user_id,
                        'updated_dt_tm' => Carbon::now(),
                    ];

                    $totalAmount += $calculatedAmount;
                }
            }

            ExpenseDetail::insert($detailsArr);

            // Summary Update
            $summaryData = [
                'expense_title' => $request->expenseTitle,
                'expense_date'  => $request->expenseDate,
                'vendor'        => $request->vendor,
                'total_amount'  => $totalAmount,
                'updated_by'    => Auth::user()->user_id,
                'updated_dt_tm' => Carbon::now(),
            ];

            if ($request->vendor) {

                $summaryData['guest_name'] = null;
                $summaryData['guest_mobile'] = null;
                $summaryData['is_guest'] = 0;

            } else {

                $summaryData['guest_name'] = $request->guestName;
                $summaryData['guest_mobile'] = $request->guestMobile;
                $summaryData['is_guest'] = 1;
            }

            ExpenseSummary::where('expense_no', $expenseNo)
                ->update($summaryData);

            /*
            |--------------------------------------------------------------------------
            | Upload New Files
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('expenseFile')) {

                foreach ($request->file('expenseFile') as $file) {

                    if (!$file) {
                        continue;
                    }

                    $fileName = Str::uuid() . '.' .
                        $file->getClientOriginalExtension();

                    $file->storeAs(
                        ExpenseFile::IMAGE_PATH,
                        $fileName,
                        'public'
                    );

                    ExpenseFile::create([
                        'expense_no'    => $expenseNo,
                        'original_name' => $file->getClientOriginalName(),
                        'file_name'     => $fileName,
                        'created_by'    => Auth::user()->user_id,
                        'created_dt_tm' => Carbon::now(),
                        'updated_by'    => Auth::user()->user_id,
                        'updated_dt_tm' => Carbon::now(),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Expense updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
