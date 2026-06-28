<?php

namespace App\Repositories;

use App\Models\Admin\InvoiceSummary;
use App\Models\Company;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceRepository
{
    public function getInvoiceSummary(array $data)
    {
        $query = InvoiceSummary::query();

        if (!empty($data['invoiceType'])) {
            $query->where('invoice_type', $data['invoiceType']);
        }

        if (!empty($data['invoiceNo'])) {
            $query->where('invoice_no', $data['invoiceNo']);
        }

        if (!empty($data['referenceNo'])) {
            $query->where('reference_no', $data['referenceNo']);
        }

        return $query->orderByDesc('created_dt_tm')->get();
    }
}