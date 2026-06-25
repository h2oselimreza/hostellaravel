<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends BaseModel
{
    protected $table = 'invoice_detail';

    protected $fillable = [
        'invoice_no',
        'item_head',
        'item_category',
        'category_name',
        'head_name',
        'quantity',
        'unit_name',
        'unit_price',
        'adjust',
        'amount',
        'remarks',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
