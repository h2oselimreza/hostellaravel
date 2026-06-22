<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class SeatFile extends BaseModel
{
    protected $table = 'hst_seat_file';

    public const IMAGE_PATH = 'assets/admin/images/seat';


    protected $fillable = [
        'seat',
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
