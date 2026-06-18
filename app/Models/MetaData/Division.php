<?php

namespace App\Models\MetaData;

use App\Models\BaseModel;

class Division extends BaseModel
{
    protected $table = 'divisions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'division_en_name',
        'division_bn_name',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm'
    ];
}
