<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends BaseModel
{
    protected $table = 'expense_detail';

    protected $fillable = [
        'expense_no',
        'expense_head',
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
