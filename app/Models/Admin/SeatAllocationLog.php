<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class SeatAllocationLog extends BaseModel
{
    protected $table = 'seat_allocation_log';

    protected $fillable = [
        'seat',
        'boarder',
        'allocated_dt_tm',
        'log_type',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
