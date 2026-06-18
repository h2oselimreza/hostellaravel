<?php

namespace App\Models\MetaData;

use App\Models\BaseModel;

class District extends BaseModel
{
    protected $table = 'districts';

    protected $primaryKey = 'id';

    protected $fillable = [
        'division',
        'district_en_name',
        'district_bn_name',
        'latitude',
        'longitude',
        'website',
        'is_active',
        'created_by',
        'updated_by',
        'created_dt_tm',
        'updated_dt_tm'
    ];

    protected $appends = ['division_relation'];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division');
    }

    public function getDivisionRelationAttribute()
    {
        return $this->division()->first();
    }

    public function upozillas()
    {
        return $this->hasMany(Upozilla::class, 'district');
    }
}
