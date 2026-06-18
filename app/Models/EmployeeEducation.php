<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    protected $table = 'customer_emp_edu_qual';

    public $timestamps = false;

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
}
