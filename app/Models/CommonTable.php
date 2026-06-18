<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonTable extends Model
{
    protected $table = 'common_table';

    public $timestamps = false; // Because no created_at / updated_at

    protected $fillable = [
        'element',
        'element_code',
        'depend_on_element',
        'type',
        'is_active',
        'element_order',
        'element_bn',
        'company',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'element_order' => 'integer',
    ];
}
