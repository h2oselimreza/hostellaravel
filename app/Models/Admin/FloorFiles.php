<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class FloorFiles extends BaseModel
{
    protected $table = 'hst_floor_file';

    public const IMAGE_PATH = 'assets/admin/images/floor';

    protected $fillable = [
        'floor',
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
