<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Module extends Model
{
     use HasFactory, Notifiable;
     public $timestamps = false;
    protected $fillable = [
        'module_group',
        'modules_name',
        'module_url',
        'module_order',
        'panel_type',
    ];

    // public static function getModulesByUserGroup($userGroup)
    // {
    //     return self::select('id', 'modules_name', 'module_url', 'module_group')
    //                 ->join('user_group_module', 'modules.id', '=', 'user_group_module.module_id')
    //                 ->where('user_group_module.user_group', $userGroup)
    //                 ->orderBy('module_group')
    //                 ->get();
    // }
}
