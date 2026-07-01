<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceService
{
    protected InvoiceRepository $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function sendInvoiceMail(string $invoiceNo): bool
    {
        $arr = [
            'invoiceNo'   => $invoiceNo,
            'invoiceType' => config('constants.INV_TYPE_GENERAL'),
        ];

        $invoiceSummary = $this->invoiceRepository->getInvoiceSummary($arr);
        $invoiceDetails = $this->invoiceRepository->getInvoiceDetails($arr);

        if ($invoiceSummary->isEmpty()) {
            return false;
        }

        try {
            Mail::to($invoiceSummary[0]->boarder_email)
                ->send(new InvoiceMail($invoiceSummary, $invoiceDetails));

            DB::table('invoice_summary')
                ->where('invoice_no', $invoiceNo)
                ->where('invoice_type', config('constants.INV_TYPE_GENERAL'))
                ->update([
                    'mail_send_status' => 1,
                ]);

            Log::channel('mail')->info('Invoice mail sent successfully.', [
                'invoice_no' => $invoiceNo,
                'email' => $invoiceSummary[0]->boarder_email,
            ]);

            return true;

        } catch (\Throwable $e) {

            Log::channel('mail')->error('Invoice mail failed.', [
                'invoice_no' => $invoiceNo,
                'email'      => $invoiceSummary[0]->boarder_email,
                'error'      => $e->getMessage(),
            ]);

            return false;
        }
    }
}