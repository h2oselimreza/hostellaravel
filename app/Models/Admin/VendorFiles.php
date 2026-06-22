<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class VendorFiles extends BaseModel
{
    protected $table = 'vendor_file';

    public const FILE_PATH = 'assets/admin/files/vendor';

    protected $fillable = [
        'vendor',
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
