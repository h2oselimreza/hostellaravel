<?php

namespace App\Http\Controllers\Admin\Boarder;

use App\Http\Controllers\Controller;
use App\Models\Admin\Boarder;
use App\Repositories\BoarderRepository;
use App\Repositories\CommonRepository;
use App\Services\GenerateMonthlyToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoarderInvoiceController extends Controller
{
    // public function edit($boarderId){
    //     $data = Boarder::where('boarder_id', $boarderId)->first();
    //     return view("admin.boarder.add-boarder.invoice.index",compact("data"));
    // }

    public function edit(
        $boarderId,
        CommonRepository $commonRepository, 
        BoarderRepository $boarderRepository)
    {
        $disableFlag = '';

        $data = Boarder::where('boarder_id', $boarderId)->first();

        $boarderDetails = $boarderRepository->getBoarderPersonalInfo($boarderId, null, 1);

        if (!$boarderId || !$boarderDetails) {
            return redirect()->route('admin.boarder-enrollment.create');
        }

        $itemHeads = $commonRepository->getItemHead(1);

        $templateDetails = $boarderRepository->getInvoiceTemplate($boarderId);

        $boarderId = $boarderId;

        $itemHeadInfo = $boarderRepository->getAdmissionHead(config('constants.ADMISSION_FEE_HEAD_CODE'));

        $admissionFeeInfo = $boarderRepository->getAdmissionFee($boarderId);

        $boarderDetails = $boarderDetails;

        if (
            !empty($admissionFeeInfo) &&
            isset($admissionFeeInfo[0]) &&
            $admissionFeeInfo->is_paid != 0
        ) {
            $disableFlag = 'disabled';
        }

        return view(
            'admin.boarder.add-boarder.invoice.index',
            compact('disableFlag','data','itemHeads','templateDetails','boarderId','itemHeadInfo','admissionFeeInfo','boarderDetails')
        );
    }

    public function updateBoarderInvoiceInfo(Request $request)
    {
        $boarderId = $request->boarderId;
        $takenItemCount = (int) $request->takenItemCount;

        $templateUpdateArr = [];
        $templateInsertArr = [];
        $templateTableIdArr = [];

        $boarderPersonal = [
            'has_template' => 1,
        ];

        $deleteHeadStr = $request->deleteHeadStr;

        for ($j = 1; $j <= $takenItemCount; $j++) {

            if (!$request->filled("itemHeadCode{$j}")) {
                continue;
            }

            $detailTableId = (int) $request->input("detailTableId{$j}");

            if ($detailTableId > 0) {

                $templateUpdateArr[] = [
                    'id'            => $detailTableId,
                    'item_head'     => trim($request->input("itemHeadCode{$j}")),
                    'quantity'      => trim($request->input("quantity{$j}")),
                    'unit_price'    => trim($request->input("unitPrice{$j}")),
                    'updated_by'    => Auth::user()->user_id,
                    'updated_dt_tm' => Carbon::now(),
                ];

                $templateTableIdArr[] = $detailTableId;

            } else {

                $templateInsertArr[] = [
                    'boarder'       => $boarderId,
                    'item_head'     => trim($request->input("itemHeadCode{$j}")),
                    'quantity'      => trim($request->input("quantity{$j}")),
                    'unit_price'    => trim($request->input("unitPrice{$j}")),
                    'created_by'    => Auth::user()->user_id,
                    'created_dt_tm' => Carbon::now(),
                    'updated_by'    => Auth::user()->user_id,
                    'updated_dt_tm' => Carbon::now(),
                ];
            }
        }

        if (empty($templateInsertArr) && empty($templateUpdateArr)) {
            $boarderPersonal['has_template'] = 0;
        }

        DB::beginTransaction();

        try {

            // Existing IDs
            $detailIdDbArr = DB::table('boarder_invoice_template')
                ->where('boarder', $boarderId)
                ->pluck('id')
                ->toArray();

            foreach ($templateTableIdArr as $id) {
                if (!in_array($id, $detailIdDbArr)) {
                    DB::rollBack();

                    return redirect()->back()
                        ->with('error', 'Invalid template detail.');
                }
            }

            // Delete
            if (!empty($deleteHeadStr)) {

                DB::table('boarder_invoice_template')
                    ->where('boarder', $boarderId)
                    ->whereIn('id', explode(',', $deleteHeadStr))
                    ->delete();
            }

            // Update (Equivalent to CodeIgniter update_batch)
            foreach ($templateUpdateArr as $row) {

                DB::table('boarder_invoice_template')
                    ->where('id', $row['id'])
                    ->where('boarder', $boarderId)
                    ->update([
                        'item_head'     => $row['item_head'],
                        'quantity'      => $row['quantity'],
                        'unit_price'    => $row['unit_price'],
                        'updated_by'    => $row['updated_by'],
                        'updated_dt_tm' => $row['updated_dt_tm'],
                    ]);
            }

            // Insert
            if (!empty($templateInsertArr)) {

                DB::table('boarder_invoice_template')
                    ->insert($templateInsertArr);
            }

            // Update boarder
            DB::table('boarder')
                ->where('boarder_id', $boarderId)
                ->update($boarderPersonal);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Updated successfully');

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function updateBoarderAdmissionFee(Request $request, GenerateMonthlyToken $generateMonthlyToken)
    {
        $invoiceNo = $request->invoiceNo;
        $boarderId = $request->boarderId;

        $admissionFeeInfo = DB::table('invoice_summary')
            ->join('invoice_detail', 'invoice_detail.invoice_no', '=', 'invoice_summary.invoice_no')
            ->where('invoice_detail.item_head', config('constants.ADMISSION_FEE_HEAD_CODE'))
            ->where('invoice_summary.boarder', $boarderId)
            ->where('invoice_summary.is_admission_invoice', 1)
            ->select('invoice_summary.*')
            ->first();

        $invoiceAmount = $request->unitPrice;

        $summaryArr = [
            'boarder' => $boarderId,
            'invoice_date' => $request->invoiceDate,
            'invoice_due_date' => $request->invoiceDueDate,
            'invoice_amount' => $invoiceAmount,
            'total_amount' => $invoiceAmount,
        ];

        $detailArr = [
            'unit_price' => $invoiceAmount,
            'amount' => $invoiceAmount,
        ];

        DB::beginTransaction();

        try {

            if ($admissionFeeInfo && $admissionFeeInfo->is_paid == 0) {

                $summaryArr['updated_by'] = Auth::user()->user_id;
                $summaryArr['updated_dt_tm'] = Carbon::now();

                $detailArr['updated_by'] = Auth::user()->user_id;
                $detailArr['updated_dt_tm'] = Carbon::now();

                DB::table('invoice_summary')
                    ->where('invoice_no', $invoiceNo)
                    ->where('boarder', $boarderId)
                    ->where('is_admission_invoice', 1)
                    ->update($summaryArr);

                DB::table('invoice_detail')
                    ->where('invoice_no', $invoiceNo)
                    ->where('item_head', config('constants.ADMISSION_FEE_HEAD_CODE'))
                    ->update($detailArr);

            } else {

                $summaryArr = array_merge($summaryArr, [
                    'invoice_no' => config('constants.INVOICE_NO') . $generateMonthlyToken->get_month_token(config('constants.INVOICE_NO')),
                    'reference_no' => reference_no(),
                    'invoice_title' => config('constants.ADMISSION_FEE_INV_TITLE'),

                    'boarder_name' => $request->boarderName,
                    'boarder_primary_mobile' => $request->boarderPrimaryMobile,
                    'boarder_address' => $request->boarderAddress ?: 'N/A',
                    'boarder_email' => $request->boarderEmail,
                    'boarder_city' => 'N/A',
                    'boarder_postcode' => 'N/A',

                    'is_admission_invoice' => 1,
                    'discount' => 0.00,
                    'paid_amount' => 0.00,

                    'created_by' => Auth::user()->user_id,
                    'created_dt_tm' => now(),
                    'updated_by' => Auth::user()->user_id,
                    'updated_dt_tm' => now(),
                ]);

                $detailArr = array_merge($detailArr, [
                    'invoice_no' => $summaryArr['invoice_no'],
                    'item_head' => config('constants.ADMISSION_FEE_HEAD_CODE'),
                    'item_category' => $request->itemCategory,
                    'category_name' => $request->itemCategoryName,
                    'head_name' => $request->itemHeadName,
                    'quantity' => 1,
                    'unit_name' => $request->unitName,

                    'created_by' => Auth::user()->user_id,
                    'created_dt_tm' => now(),
                    'updated_by' => Auth::user()->user_id,
                    'updated_dt_tm' => now(),
                ]);
                
                DB::table('invoice_summary')->insert($summaryArr);

                DB::table('invoice_detail')->insert($detailArr);
            }

            DB::commit();

            return redirect()
                ->back()
                ->withInput()
                ->with('success', 'Admission fee updated successfully.');

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
