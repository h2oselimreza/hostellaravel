<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubModules extends Model
{
    use HasFactory;
    protected $table = 'sub_modules'; // adjust if your table name is different

    public $timestamps = false; 

    protected $fillable = [
        'module',
        'sub_module_name',
        'sub_module_code',
        'panel_type',
    ];
}
