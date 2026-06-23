<?php

namespace App\Models\Admin;

use App\Models\BaseModel;

class Vendor extends BaseModel
{
    protected $table = 'vendor';

    public const FILE_PATH = 'assets/admin/files/vendor';


    protected $fillable = [
        'vendor_code',
        'title',
        'address',
        'vendor_email',
        'website',
        'vendor_mobile',
        'vendor_land_phone',
        'profile_image',
        'division',
        'district',
        'upozilla',
        'postal_code',
        'latitude',
        'longitude',
        'primary_contact_person',
        'primary_contact_designation',
        'primary_contact_mobile',
        'primary_contact_email',
        'second_contact_person',
        'second_contact_designation',
        'second_contact_mobile',
        'second_contact_email',
        'is_active',
        'status',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
    ];
}
