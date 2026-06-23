<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class BoarderInvoiceTemplate extends BaseModel
{
    protected $table = 'boarder_invoice_template';

    protected $fillable = [
        'boarder',
        'item_head',
        'quantity',
        'unit_price',
        'template_type',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];
}
