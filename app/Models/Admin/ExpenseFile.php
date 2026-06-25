<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class ExpenseFile extends BaseModel
{
    protected $table = 'expense_files';

    public const IMAGE_PATH = 'assets/admin/files/expense';

    protected $fillable = [
        'expense_no',
        'original_name',
        'file_name',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
