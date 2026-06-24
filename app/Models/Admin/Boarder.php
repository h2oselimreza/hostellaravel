<?php

namespace App\Models\Admin;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Boarder extends BaseModel
{
    protected $table = 'boarder';

    public const FILE_PATH = 'assets/admin/files/boarder';

    protected $fillable = [
        'boarder_id',
        'boarder_name',
        'boarder_image',
        'designation',
        'first_joining_date',
        'gender',
        'religion',
        'nationality',
        'dob',
        'blood_group',
        'marital_status',
        'spouse_name',
        'spouse_occupation',
        'spouse_contact',
        'primary_mobile',
        'secendary_mobile',
        'emer_contact_name',
        'emer_contact_relation',
        'email',
        'emer_conatct_mobile',
        'emer_contact_address',
        'present_address',
        'national_id',
        'father_name',
        'father_occupation',
        'father_office_address',
        'father_contact',
        'mother_name',
        'mother_occupation',
        'mother_office_address',
        'mother_contact',
        'guardian_name',
        'guardian_contact',
        'guardian_relation',
        'guardian_house_address',
        'spouse_office_address',
        'boarder_tnt_phone',
        'boarder_permanent_address',
        'last_organization',
        'last_org_address',
        'last_org_designation',
        'last_org_from_date',
        'last_org_to_date',
        'passport_no',
        'passposrt_expiry_date',
        'driving_license_no',
        'driving_license_expiry_date',
        'anniversary',
        'last_invoice_date',
        'has_template',
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
        'system_user',
    ];
}
