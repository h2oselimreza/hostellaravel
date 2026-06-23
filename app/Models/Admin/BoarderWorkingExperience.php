<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class BoarderWorkingExperience extends BaseModel
{
    protected $table = 'boarder_working_experience';

    public $timestamps = false;

    protected $fillable = [
        'boarder_id',
        'institution_name',
        'institution_type',
        'address',
        'designation',
        'department',
        'from_date',
        'to_date',
        'is_continued',
        'responsibilites',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];

    protected $casts = [
        'from_date'      => 'date',
        'to_date'        => 'date',
        'is_continued'   => 'boolean',
    ];
}
