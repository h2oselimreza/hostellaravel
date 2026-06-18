<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class BuildingFile extends BaseModel
{
    protected $table = 'building_files';

    public const IMAGE_PATH = 'assets/admin/images/building';

    protected $fillable = [
        'building',
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
