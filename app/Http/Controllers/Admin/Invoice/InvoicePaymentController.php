<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Admin\InvoiceSummary;
use App\Repositories\CommonRepository;
use App\Repositories\InvoiceRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoicePaymentController extends Controller
{
    public function index(InvoiceRepository $invoiceRepository)
    {
        $arr['invoiceType'] = config('constants.INV_TYPE_GENERAL');
        $data = $invoiceRepository->getInvoiceSummary($arr);

        return view('admin.invoice.invoice-payment.index', compact('data'));
    }

    public function show($invoiceNo, InvoiceRepository $invoiceRepository,CommonRepository $commonRepository){

        if (empty($invoiceNo)) {
            return redirect()->route('admin.invoice.payment.index')->with('error','Invoice not found.');
        }

        // Invoice Summary
        $arr['invoiceNo'] = $invoiceNo;
        $arr['invoiceType'] = config('constants.INV_TYPE_GENERAL');
        $invoiceSummary = $invoiceRepository->getInvoiceSummary($arr);

        if (!$invoiceSummary) {
            return redirect()->route('admin.invoice.index')->with("error",'invoice summary not found.');
        }

        // Invoice Details
        $invoiceDetails = $invoiceRepository->getInvoiceDetails($arr);

        // Invoice Files
        $invoiceFiles = $invoiceRepository->getInvoiceFiles($arr);

        // Item Heads
        $itemHeads = $commonRepository->getItemHead(1);

        // Boarders
        $boarders = $invoiceRepository->getBoarderList();
    
        return view('admin.invoice.invoice-payment.show', [
            'invoiceSummary'      => $invoiceSummary,
            'invoiceDetails'      => $invoiceDetails,
            'invoiceFiles'        => $invoiceFiles,
            'invoiceNo'           => $invoiceNo,
            'itemHeads'           => $itemHeads,
            'boarders'            => $boarders,
        ]);
    }

    public function InvoicePayment(Request $request, InvoiceRepository $invoiceRepository)
    {

        $invoiceNo = $request->invoiceNo;

        if (empty($invoiceNo)) {
            return redirect()->route('admin.invoice.index')->with('error','Invoice no is not found.');
        }

        $arr['invoiceNo'] = $invoiceNo;
        $arr['invoiceType'] = config('constants.INV_TYPE_GENERAL');
        $invoice = $invoiceRepository->getInvoiceSummary($arr);

        if (!$invoice) {
            return redirect()->route('admin.invoice.index')->with('error','Record not found.');
        }

        $paymentArr = [];

        $paymentArr['invoice_no']     = $invoiceNo;
        $paymentArr['invoice_amount'] = (float) $invoice[0]->invoice_amount;
        $paymentArr['discount']       = (float) $request->discount;
        $paymentArr['discount_type']  = (int) $request->discountType;
        $paymentArr['paid_amount']    = (float) $request->paidAmount;
        $paymentArr['payment_dt_tm']  = Carbon::now();

        // Calculate total amount
        if ($paymentArr['discount_type'] == 1) {

            // Fixed discount
            $paymentArr['total_amount'] = $paymentArr['invoice_amount'] - $paymentArr['discount'];

        } elseif ($paymentArr['discount_type'] == 2) {

            // Percentage discount
            $paymentArr['total_amount'] = $paymentArr['invoice_amount'] - (($paymentArr['invoice_amount'] * $paymentArr['discount']) / 100);

        } else {

            $paymentArr['total_amount'] = $paymentArr['invoice_amount'];
        }

        // Payment Status
        if ($paymentArr['paid_amount'] == $paymentArr['total_amount']) {

            $paymentArr['is_paid'] = 1;

        } elseif ($paymentArr['paid_amount'] == 0) {

            $paymentArr['is_paid'] = 0;

        } else {

            // Partial Payment
            $paymentArr['is_paid'] = 2;
        }

        DB::beginTransaction();

        try {

            $invoice = InvoiceSummary::where('invoice_no', $request->invoiceNo)->first();

            if ($invoice) {
                $invoice->update($paymentArr);
            }

            DB::commit();

            return redirect()
                ->route('admin.invoice.payment.index') ->with('success', 'Successfully paid!');

        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function testInvoice($invoiceNo, InvoiceRepository $invoiceRepository) {

        $arr['invoiceNo'] = $invoiceNo;
        $arr['invoiceType'] = config('constants.INV_TYPE_GENERAL');
        $invoiceSummary = $invoiceRepository->getInvoiceSummary($arr);
        $invoiceDetails = $invoiceRepository->getInvoiceDetails($arr);

        return view('admin.invoice.invoice-payment.test-invoice',compact('invoiceSummary','invoiceDetails'));
    }
}
