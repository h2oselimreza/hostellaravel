<?php

namespace App\Models\MetaData;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Package extends BaseModel
{
    protected $table = 'package';
    protected $fillable = [
        'package_code',
        'package_name',
        'package_details',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
