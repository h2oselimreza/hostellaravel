<?php

namespace App\Models;

use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory, TracksUser;
    
    const CREATED_AT = 'created_dt_tm';
    const UPDATED_AT = 'updated_dt_tm';
}
