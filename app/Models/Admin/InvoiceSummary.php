<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class InvoiceSummary extends BaseModel
{
    protected $table = 'invoice_summary';

    protected $fillable = [
        'invoice_no',
        'reference_no',
        'val_id',
        'invoice_title',
        'invoice_date',
        'invoice_due_date',

        'boarder',
        'boarder_name',
        'boarder_email',
        'boarder_address',
        'boarder_city',
        'boarder_postcode',
        'boarder_primary_mobile',

        'guest_name',
        'guest_mobile',
        'guest_email',
        'guest_address',
        'guest_city',
        'guest_postcode',

        'is_guest',
        'invoice_type',

        'invoice_amount',
        'total_amount',

        'discount',
        'discount_type',

        'paid_amount',
        'payment_dt_tm',
        'payment_method',

        'is_paid',
        'tran_status_history',

        'mail_send_status',
        'is_admission_invoice',

        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];

}
