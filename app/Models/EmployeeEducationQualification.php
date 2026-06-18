<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducationQualification extends Model
{
    protected $table = 'emp_edu_qualification';

    protected $primaryKey = 'id';

    public $timestamps = false; // IMPORTANT (because using custom datetime fields)

    protected $fillable = [
        'employee_id',
        'level_of_education',
        'exam_degree',
        'institute_name',
        'education_board',
        'qualification_result',
        'cgpa_marks',
        'scale',
        'passing_year',
        'duration',
        'major_group',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $user = auth()->user()->user_id ?? 'system';

            $model->created_by = $user;
            $model->updated_by = $user;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user()->user_id ?? 'system';
        });

        // static::created(function ($group) {

        //     $prefix = \DB::table('token')
        //                 ->where('code', 'BLK-')
        //                 ->value('code') ?? 'BLK-';

        //     $group->block_code =
        //         $prefix . str_pad($group->id, 5, '0', STR_PAD_LEFT);

        //     $group->saveQuietly();
        // });
    }
}
