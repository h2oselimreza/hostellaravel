<?php

namespace App\Models\MetaData;

use App\Models\BaseModel;

class Upozilla extends BaseModel
{
    protected $table = 'upazilas';

    protected $primaryKey = 'id';

    protected $fillable = [
        'district',
        'upozilla_en_name',
        'upozilla_bn_name',
        'is_active',
        'created_by',
        'updated_by',
        'created_dt_tm',
        'updated_dt_tm'
    ];
}
