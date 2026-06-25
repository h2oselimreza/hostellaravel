<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ExpenseSummary extends BaseModel
{
     protected $table = 'expense_summary';

    protected $fillable = [
        'expense_title',
        'expense_date',
        'expense_no',
        'total_amount',
        'vendor',
        'guest_name',
        'guest_mobile',
        'is_guest',
        'expense_type',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
