<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentSmsDetails extends Model
{
    protected $table = 'sent_sms_details';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'mobile_no',
        'message_body',
        'created_by',
        'created_type',
        'created_dt_tm',
    ];
}
