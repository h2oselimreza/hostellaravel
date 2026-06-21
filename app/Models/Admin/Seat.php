<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Seat extends BaseModel
{
    protected $table = 'hst_seat';

    public const IMAGE_PATH = 'assets/admin/images/seat';

    protected $fillable = [
        'seat_code',
        'seat_type',
        'room',
        'title',
        'description',
        'seat_image',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
