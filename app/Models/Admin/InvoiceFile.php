<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class InvoiceFile extends BaseModel
{
    protected $table = 'invoice_files';

    public const IMAGE_PATH = 'assets/admin/files/invoice';

    protected $fillable = [
        'invoice_no',
        'original_name',
        'file_name',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
