<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class SeatType extends BaseModel
{
    protected $table = 'hst_seat_type';

    protected $fillable = [
        'title',
        'seat_type_code',
        'description',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
