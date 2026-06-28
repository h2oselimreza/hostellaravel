<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Admin\InvoiceDetail;
use App\Models\Admin\InvoiceFile;
use App\Models\Admin\InvoiceSummary;
use App\Repositories\CommonRepository;
use App\Repositories\InvoiceRepository;
use App\Services\GenerateMonthlyToken;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index(InvoiceRepository $invoiceRepository){
        $arr['invoiceType'] = config('constants.INV_TYPE_GENERAL');
        $data = $invoiceRepository->getInvoiceSummary($arr);
        return view('admin.invoice.index',compact('data'));
    }

    public function create(
        CommonRepository $commonRepository,
        InvoiceRepository $invoiceRepository,
        ){
        $itemHeads = $commonRepository->getItemHead(1);
        $boarders = $invoiceRepository->getBoarderList();
        return view('admin.invoice.create',compact('itemHeads','boarders'));
    }

   public function store(Request $request, GenerateMonthlyToken $generateMonthlyToken)
    {

        $invoiceNo = config('constants.INVOICE_NO') . $generateMonthlyToken->get_month_token(config('constants.INVOICE_NO'));

        $detailsArr = [];
        $invoiceAmount = 0;

        $takenItemCount = $request->takenItemCount;

        for ($j = 1; $j <= $takenItemCount; $j++) {

            $itemCategory = trim($request->input("itemCategoryCode{$j}"));
            $itemHead = trim($request->input("itemHeadCode{$j}"));

            if ($itemCategory && $itemHead) {

                $quantity = (float)$request->input("quantity{$j}");
                $unitPrice = (float)$request->input("unitPrice{$j}");
                $adjust = (float)$request->input("adjust{$j}");
                $amount = (float)$request->input("amount{$j}");

                $calculatedAmount = ($quantity * $unitPrice) + $adjust;

                if ($calculatedAmount != $amount) {
                    return redirect()->route('admin.invoice.create');
                }

                $detailsArr[] = [
                    'invoice_no' => $invoiceNo,
                    'item_category' => $itemCategory,
                    'item_head' => $itemHead,
                    'category_name' => trim($request->input("itemCategoryName{$j}")),
                    'head_name' => trim($request->input("itemHeadName{$j}")),
                    'quantity' => $quantity,
                    'unit_name' => trim($request->input("unitName{$j}")),
                    'unit_price' => $unitPrice,
                    'adjust' => $adjust,
                    'amount' => $calculatedAmount,
                    'remarks' => $request->input("remarks{$j}"),
                    'created_by' => Auth::user()->user_id,
                    'updated_by' => Auth::user()->user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];

                $invoiceAmount += $calculatedAmount;
            }
        }

        $summaryArr = [
            'invoice_title' => trim($request->invoiceTitle),
            'invoice_type' => config('constants.INV_TYPE_GENERAL'),
            'invoice_date' => $request->invoiceDate,
            'invoice_due_date' => $request->invoiceDueDate,
            'invoice_no' => $invoiceNo,
            'reference_no' => reference_no(),
            'invoice_amount' => $invoiceAmount,
            'total_amount' => $invoiceAmount,
            'is_guest' => (int)$request->isGuest,
            'created_by' => Auth::user()->user_id,
            'updated_by' => Auth::user()->user_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'boarder' => null,
            'boarder_name' => null,
            'boarder_primary_mobile' => null,
            'boarder_address' => null,
            'boarder_email' => null,
            'boarder_city' => null,
            'boarder_postcode' => null,
            'discount' => 0.00,
            'paid_amount' => 0.00,

            'guest_name' => null,
            'guest_mobile' => null,
            'guest_address' => null,
            'guest_email' => null,
            'guest_city' => null,
            'guest_postcode' => null,
        ];

        if ($summaryArr['is_guest'] == 0) {

            $summaryArr['boarder'] = $request->boarderIdHidden;
            $summaryArr['boarder_name'] = $request->boarderNameHidden;
            $summaryArr['boarder_primary_mobile'] = $request->boarderPrimaryMobileHidden;
            $summaryArr['boarder_address'] = $request->boarderAddressHidden ?: 'N/A';
            $summaryArr['boarder_email'] = $request->boarderEmailHidden;
            $summaryArr['boarder_city'] = 'N/A';
            $summaryArr['boarder_postcode'] = 'N/A';

        } else {

            $summaryArr['guest_name'] = $request->guestName;
            $summaryArr['guest_mobile'] = $request->guestMobile;
            $summaryArr['guest_address'] = $request->guestAddress ?: 'N/A';
            $summaryArr['guest_email'] = $request->guestEmail;
            $summaryArr['guest_city'] = 'N/A';
            $summaryArr['guest_postcode'] = 'N/A';
        }

        if (
            empty($summaryArr['invoice_title']) ||
            empty($summaryArr['invoice_date']) ||
            empty($summaryArr['invoice_due_date']) ||
            empty($detailsArr)
        ) {
            return redirect()->route('admin.invoice.index');
        }

        if ($summaryArr['is_guest'] == 0 && empty($summaryArr['boarder'])) {
            return redirect()->route('admin.invoice.index');
        }

        if ($summaryArr['is_guest'] == 1 && empty($summaryArr['guest_name'])) {
            return redirect()->route('admin.invoice.index');
        }

        DB::beginTransaction();

        try {

            // Save Invoice Summary
            $invoice = InvoiceSummary::create($summaryArr);

            // Save Invoice Details
            foreach ($detailsArr as $detail) {
                InvoiceDetail::create($detail);
            }

            // Upload Files
            if ($request->hasFile('invoiceFile')) {

                foreach ($request->file('invoiceFile') as $file) {

                    if (!$file->isValid()) {
                        throw new \Exception('Invalid file upload.');
                    }

                    $fileName = reference_no() . '.' . $file->getClientOriginalExtension();

                    // Save file
                    $file->storeAs(
                        InvoiceFile::IMAGE_PATH,
                        $fileName,
                        'public'
                    );

                    InvoiceFile::create([
                        'invoice_no'    => $invoiceNo,
                        'original_name' => $file->getClientOriginalName(),
                        'file_name'     => $fileName,
                        'created_by'    => Auth::id(),
                        'created_dt_tm' => now(),
                        'updated_by'    => Auth::id(),
                        'updated_dt_tm' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.invoice.index')
                ->with('success', "Invoice has been created successfully.");

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->route('admin.invoice.index', 2)
                ->with('error', $e->getMessage());
        }
    }

    public function edit($invoiceNo, InvoiceRepository $invoiceRepository, CommonRepository $commonRepository)
    {

        if (empty($invoiceNo)) {
            return redirect()->route('admin.invoice.index')->with('error','Invoice number not found.');
        }

        // Invoice Summary
        $arr['invoiceNo'] = $invoiceNo;
        $arr['invoiceType'] = config('constants.INV_TYPE_GENERAL');
        $invoiceSummary = $invoiceRepository->getInvoiceSummary($arr);

        if (!$invoiceSummary) {
            return redirect()->route('admin.invoice.index')->with("error",'invoice summary not found.');
        }

        $arr['invoiceNo'] = $invoiceNo;
        $arr['invoiceType'] = config('constants.INV_TYPE_GENERAL');
        // Invoice Details
        $invoiceDetails = $invoiceRepository->getInvoiceDetails($arr);

        // Invoice Files
        $invoiceFiles = $invoiceRepository->getInvoiceFiles($arr);

        // Item Heads
        $itemHeads = $commonRepository->getItemHead(1);

        // Boarders
        $boarders = $invoiceRepository->getBoarderList();

        return view('admin.invoice.edit', compact(
            'invoiceSummary',
            'invoiceDetails',
            'invoiceFiles',
            'itemHeads',
            'boarders',
            'invoiceNo'
        ));
    }

    public function update(Request $request, $invoiceNo)
    {
        DB::beginTransaction();

        try {

            $invoice = InvoiceSummary::where('invoice_no', $invoiceNo)->firstOrFail();

            $detailsArr = [];
            $invoiceAmount = 0;

            $takenItemCount = $request->takenItemCount;

            for ($j = 1; $j <= $takenItemCount; $j++) {

                $itemCategory = trim($request->input("itemCategoryCode{$j}"));
                $itemHead     = trim($request->input("itemHeadCode{$j}"));

                if ($itemCategory && $itemHead) {

                    $quantity   = (float)$request->input("quantity{$j}");
                    $unitPrice  = (float)$request->input("unitPrice{$j}");
                    $adjust     = (float)$request->input("adjust{$j}");
                    $amount     = (float)$request->input("amount{$j}");

                    $calculatedAmount = ($quantity * $unitPrice) + $adjust;

                    if ($calculatedAmount != $amount) {
                        return redirect()->back()->with('error', 'Invalid amount.');
                    }

                    $detailsArr[] = [
                        'invoice_no'    => $invoiceNo,
                        'item_category' => $itemCategory,
                        'item_head'     => $itemHead,
                        'category_name' => trim($request->input("itemCategoryName{$j}")),
                        'head_name'     => trim($request->input("itemHeadName{$j}")),
                        'quantity'      => $quantity,
                        'unit_name'     => trim($request->input("unitName{$j}")),
                        'unit_price'    => $unitPrice,
                        'adjust'        => $adjust,
                        'amount'        => $calculatedAmount,
                        'remarks'       => $request->input("remarks{$j}"),
                        'created_by'    => $invoice->created_by,
                        'updated_by'    => Auth::user()->user_id,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now(),
                    ];

                    $invoiceAmount += $calculatedAmount;
                }
            }

            $summaryArr = [

                'invoice_title'  => trim($request->invoiceTitle),
                'invoice_date'   => $request->invoiceDate,
                'invoice_due_date' => $request->invoiceDueDate,

                'invoice_amount' => $invoiceAmount,
                'total_amount'   => $invoiceAmount,

                'is_guest'       => (int)$request->isGuest,

                'updated_by'     => Auth::user()->user_id,
                'updated_at'     => Carbon::now(),

                'boarder' => null,
                'boarder_name' => null,
                'boarder_primary_mobile' => null,
                'boarder_address' => null,
                'boarder_email' => null,
                'boarder_city' => null,
                'boarder_postcode' => null,

                'guest_name' => null,
                'guest_mobile' => null,
                'guest_address' => null,
                'guest_email' => null,
                'guest_city' => null,
                'guest_postcode' => null,
            ];

            if ($summaryArr['is_guest'] == 0) {

                $summaryArr['boarder'] = $request->boarderIdHidden;
                $summaryArr['boarder_name'] = $request->boarderNameHidden;
                $summaryArr['boarder_primary_mobile'] = $request->boarderPrimaryMobileHidden;
                $summaryArr['boarder_address'] = $request->boarderAddressHidden ?: 'N/A';
                $summaryArr['boarder_email'] = $request->boarderEmailHidden;
                $summaryArr['boarder_city'] = 'N/A';
                $summaryArr['boarder_postcode'] = 'N/A';

            } else {

                $summaryArr['guest_name'] = $request->guestName;
                $summaryArr['guest_mobile'] = $request->guestMobile;
                $summaryArr['guest_address'] = $request->guestAddress ?: 'N/A';
                $summaryArr['guest_email'] = $request->guestEmail;
                $summaryArr['guest_city'] = 'N/A';
                $summaryArr['guest_postcode'] = 'N/A';
            }

            // Update Summary
            $invoice->update($summaryArr);

            // Delete old details
            InvoiceDetail::where('invoice_no', $invoiceNo)->delete();

            // Insert new details
            foreach ($detailsArr as $detail) {
                InvoiceDetail::create($detail);
            }

            /*
            |--------------------------------------------------------------------------
            | Files
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('invoiceFile')) {

                // Delete old files
                $oldFiles = InvoiceFile::where('invoice_no', $invoiceNo)->get();

                foreach ($oldFiles as $oldFile) {

                    Storage::disk('public')->delete(
                        InvoiceFile::IMAGE_PATH.'/'.$oldFile->file_name
                    );

                    $oldFile->delete();
                }

                // Upload new files
                foreach ($request->file('invoiceFile') as $file) {

                    if (!$file->isValid()) {
                        throw new Exception('Invalid file upload.');
                    }

                    $fileName = reference_no().'.'.$file->getClientOriginalExtension();

                    $file->storeAs(
                        InvoiceFile::IMAGE_PATH,
                        $fileName,
                        'public'
                    );

                    InvoiceFile::create([
                        'invoice_no'    => $invoiceNo,
                        'original_name' => $file->getClientOriginalName(),
                        'file_name'     => $fileName,
                        'created_by'    => Auth::id(),
                        'created_dt_tm' => now(),
                        'updated_by'    => Auth::id(),
                        'updated_dt_tm' => now(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.invoice.index')
                ->with('success', 'Invoice has been updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($invoiceNo)
    {

        if (empty($invoiceNo)) {
            return response()->json([
                'status' => 2,
                'message' => 'Invoice number is required.'
            ]);
        }

        DB::beginTransaction();

        try {

            $invoice = InvoiceSummary::where('invoice_no', $invoiceNo)->first();

            if (!$invoice) {
                DB::rollBack();

                return response()->json([
                    'status' => 2,
                    'message' => 'Invoice not found.'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Files From Storage
            |--------------------------------------------------------------------------
            */

            $files = InvoiceFile::where('invoice_no', $invoiceNo)->get();

            foreach ($files as $file) {

                Storage::disk('public')->delete(
                    InvoiceFile::IMAGE_PATH . '/' . $file->file_name
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Database Records
            |--------------------------------------------------------------------------
            */

            InvoiceFile::where('invoice_no', $invoiceNo)->delete();
            InvoiceDetail::where('invoice_no', $invoiceNo)->delete();
            $invoice->delete();

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Invoice deleted successfully.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 0,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
