<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Room extends BaseModel
{
    protected $table = 'hst_room';

    public const IMAGE_PATH = 'assets/admin/images/room';

    protected $fillable = [
        'room_code',
        'floor',
        'title',
        'description',
        'room_image',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];

    public function floorInfo()
    {
        return $this->belongsTo(
            Floor::class,
            'floor',       // floor table column
            'floor_code'   // building table column
        );
    }
}
