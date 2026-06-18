<?php

namespace App\Models;

use App\Services\TokenService;
use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModuleGroup extends Model
{
    use HasFactory, TracksUser;

    protected $table = 'module_group'; // because model name differs from table

    const CREATED_AT = 'created_dt_tm';
    const UPDATED_AT = 'updated_dt_tm';

    protected $fillable = [
        'module_group_name',
        'module_group_code',
        'module_group_order',
        'panel_type',
        'created_by',
        'updated_by', 
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $prefix = 'M-GRP-';

            $tokenService = app(TokenService::class);

            $employeeId = $prefix . $tokenService->getTokenByCode($prefix);

            $model->employee_id = $employeeId;

        });
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'module_group', 'module_group_code')
                    ->orderBy('module_order');
    }
}
