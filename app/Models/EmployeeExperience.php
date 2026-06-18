<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeExperience extends Model
{
    protected $table = 'customer_emp_working_exp';

    public $timestamps = false;

    protected $fillable = [
        'employee_id',
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
}
