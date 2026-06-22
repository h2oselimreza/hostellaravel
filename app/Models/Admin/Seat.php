<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Seat extends BaseModel
{
    //use HasRelationships;

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

    public function seatType()
    {
        return $this->belongsTo(
            SeatType::class,
            'seat_type',       // main table column
            'seat_type_code'   // join table column
        );
    }

    public function roomInfo()
    {
        return $this->belongsTo(
            Room::class,
            'room',       // floor table column
            'room_code'   // building table column
        );
    }
}
