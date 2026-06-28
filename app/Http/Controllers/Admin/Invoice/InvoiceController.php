<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(InvoiceRepository $invoiceRepository){
        $arr['invoiceType'] = config('constants.INV_TYPE_GENERAL');
        $data = $invoiceRepository->getInvoiceSummary($arr);
        return view('admin.invoice.index',compact('data'));
    }
}
