<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyFile extends BaseModel
{
    protected $table = 'company_file';

    protected $fillable = [
        'company',
        'original_name',
        'file_name',
        'file_type',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
