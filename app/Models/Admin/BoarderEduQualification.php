<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class BoarderEduQualification extends BaseModel
{
    protected $table = 'boarder_edu_qualification';

    protected $fillable = [
        'boarder_id',
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
}
