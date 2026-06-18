<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Building extends BaseModel
{
    protected $table = 'hst_building';

    public const IMAGE_PATH = 'assets/admin/images/building';
    
    protected $fillable = [
        'building_code',
        'title',
        'address',
        'building_image',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
