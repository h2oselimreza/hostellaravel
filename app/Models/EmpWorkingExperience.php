<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmpWorkingExperience extends Model
{
    use HasFactory;

    // If table name is not the plural of model name
    protected $table = 'emp_working_experience';


    // Disable Laravel timestamps if your table uses custom datetime fields
    public $timestamps = false;

    // Fillable fields for mass assignment
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

    // Optionally, cast date fields
    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'created_dt_tm' => 'datetime',
        'updated_dt_tm' => 'datetime',
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
    }
}
