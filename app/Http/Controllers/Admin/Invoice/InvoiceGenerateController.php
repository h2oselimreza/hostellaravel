<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Repositories\BoarderRepository;
use App\Repositories\InvoiceRepository;
use App\Services\GenerateMonthlyToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceGenerateController extends Controller
{
    public function index(BoarderRepository $boarderRepository)
    {

        // Active Boarders
        $data = $boarderRepository->getBoarders(['isActive' => 1,]);

        // // Message
        // $msgFlag = (int) $request->query('msg');

        // $msg = '';
        // $msgType = '';

        // if ($msgFlag === 1) {
        //     $msg = 'Successfully Generated...!';
        //     $msgType = 'success';
        // } elseif ($msgFlag === 2) {
        //     $msg = 'Not Generated...!';
        //     $msgType = 'danger';
        // }

        return view('admin.invoice.invoice-generate.index', compact(
            'data'
        ));
    }

    public function doGenerate(
        Request $request,
        InvoiceRepository $invoiceRepository,
        GenerateMonthlyToken $generateMonthlyToken
        )
    {
        $request->validate([
            'invoiceTitle'   => 'required|string|max:255',
            'invoiceDate'    => 'required|date',
            'invoiceDueDate' => 'required|date',
            'boarderIdStr'   => 'required|string',
        ]);

        $boarderIds = collect(explode(',', $request->boarderIdStr))
            ->map(function ($item) {
                $parts = explode('-', $item);
                return $parts[1] ?? null;
            })
            ->filter()
            ->values()
            ->toArray();

        if (empty($boarderIds)) {
            return redirect()->route('admin.invoice-generate');
        }

        $boarderDetails = $invoiceRepository->getBoarderTemplateDetails($boarderIds);

        if ($boarderDetails->isEmpty()) {
            return redirect()->route('admin.invoice.generate')
                ->with('error', 'No data found.');
        }

        $summaryBatch = [];
        $detailsBatch = [];
        $lastInvoiceUpdates = [];

        $currentBoarder = null;
        $invoiceNo = '';
        $invoiceAmount = 0;
        $summary = [];

        foreach ($boarderDetails as $detail) {

            if ($currentBoarder != $detail->boarder_id) {

                if (!empty($summary)) {
                    $summaryBatch[] = $summary;
                }

                $currentBoarder = $detail->boarder_id;
                $invoiceNo = config('constants.INVOICE_NO') . $generateMonthlyToken->get_month_token(config('constants.INVOICE_NO'));
                $invoiceAmount = 0;

                $summary = [
                    'invoice_title'            => $request->invoiceTitle,
                    'boarder'                  => $detail->boarder_id,
                    'boarder_name'             => $detail->boarder_name,
                    'boarder_primary_mobile'   => $detail->primary_mobile,
                    'boarder_address'          => $detail->present_address,
                    'boarder_email'            => $detail->email,
                    'boarder_city'             => 'N/A',
                    'boarder_postcode'         => 'N/A',
                    'is_guest'                 => 0,
                    'invoice_type'             => config('constants.INV_TYPE_GENERAL'),
                    'discount'                 => 0,
                    'paid_amount'              => 0,
                    'invoice_date'             => $request->invoiceDate,
                    'invoice_due_date'         => $request->invoiceDueDate,
                    'invoice_no'               => $invoiceNo,
                    'reference_no'             => reference_no(),
                    'created_by'               => Auth::user()->user_id,
                    'created_dt_tm'               => Carbon::now(),
                    'updated_by'               => Auth::user()->user_id,
                    'updated_dt_tm'               => Carbon::now(),
                ];

                $lastInvoiceUpdates[] = [
                    'boarder_id'        => $detail->boarder_id,
                    'last_invoice_date' => $request->invoiceDate,
                    'updated_by'        => Auth::user()->user_id,
                    'updated_dt_tm'        => Carbon::now(),
                ];
            }

            $amount = $detail->quantity * $detail->unit_price;

            $detailsBatch[] = [
                'item_category' => $detail->item_category,
                'item_head'     => $detail->item_head,
                'invoice_no'    => $invoiceNo,
                'category_name' => $detail->category_name,
                'head_name'     => $detail->item_head_name,
                'quantity'      => $detail->quantity,
                'unit_name'     => $detail->unit_name,
                'unit_price'    => $detail->unit_price,
                'adjust'        => 0,
                'amount'        => $amount,
                'remarks'       => null,
                'created_by'    => Auth::user()->user_id,
                'created_dt_tm'    => Carbon::now(),
                'updated_by'    => Auth::user()->user_id,
                'updated_dt_tm'    => Carbon::now(),
            ];

            $invoiceAmount += $amount;

            $summary['invoice_amount'] = $invoiceAmount;
            $summary['total_amount'] = $invoiceAmount;
        }

        if (!empty($summary)) {
            $summaryBatch[] = $summary;
        }

        DB::transaction(function () use (
            $summaryBatch,
            $detailsBatch,
            $lastInvoiceUpdates,
            $invoiceRepository,
        ) {
            $invoiceRepository->doGenerate(
                $summaryBatch,
                $detailsBatch,
                $lastInvoiceUpdates
            );
        });

        return redirect()
            ->route('admin.invoice.generate')->with('success', 'Invoice has been generated successfully.');
    }
}
