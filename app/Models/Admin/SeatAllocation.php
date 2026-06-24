<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class SeatAllocation extends BaseModel
{
    protected $table = 'seat_allocation';

    protected $fillable = [
        'seat',
        'boarder',
        'allocated_dt_tm',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];

}
