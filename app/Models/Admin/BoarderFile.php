<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class BoarderFile extends BaseModel
{
    protected $table = 'boarder_file';

    protected $fillable = [
        'boarder',
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
