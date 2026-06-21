<?php

namespace App\Models\Admin;

use App\Models\BaseModel;

class Floor extends BaseModel
{
    protected $table = 'hst_floor';

    public const IMAGE_PATH = 'assets/admin/images/floor';

    protected $fillable = [
        'floor_code',
        'building',
        'title',
        'floor_image',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];

    public function buildingInfo()
    {
        return $this->belongsTo(
            Building::class,
            'building',       // floor table column
            'building_code'   // building table column
        );
    }
}
