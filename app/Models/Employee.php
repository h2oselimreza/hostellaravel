<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TracksUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Employee extends Model
{
    use HasFactory, TracksUser;
    protected $table = 'employee';

    public $timestamps = false; // because you use custom datetime fields

    protected $fillable = [
        'employee_id',
        'employee_name',
        'employee_image',
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
        'employee_tnt_phone',
        'employee_permanent_address',
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
        'is_active',
        'created_by',
        'created_dt_tm',
        'updated_by',
        'updated_dt_tm',
        'system_user',
        'emp_type',
        'emp_category',
        'emp_station',
        'grade',
        'department',
        'reporting_to',
        'work_shift',
        'birthday_sms_status',
        'anniversary_sms_status'
    ];

    public static function getEmployeeCustomBulkMsgData(array $arr)
    {
        $employeeQuery = Employee::query()
            ->select('primary_mobile as mob1')
            ->where('is_active', 1);

        // If specific employees selected
        if (!empty($arr['employeeIdStr'])) {
            $employeeIds = explode(',', $arr['employeeIdStr']);
            $employeeQuery->whereIn('id', $employeeIds);
        } 
        // Otherwise, filter by designation
        elseif (!empty($arr['designation'])) {
            $designations = explode(',', $arr['designation']);
            $employeeQuery->whereIn('designation', $designations);
        }

        return $employeeQuery->get()->toArray();
    }

    public static function getEmployeeDetails($arr, $flag = 0)
    {
        $query = self::select(
                'employee.*',
                'designation_tb.element as designation_name'
            )
            ->leftJoin(
                'common_table as designation_tb',
                'designation_tb.element_code',
                '=',
                'employee.designation'
            )
            ->where('employee.is_active', 1);

        if (!empty($arr['designation'])) {
            $query->whereIn('employee.designation', explode(',', $arr['designation']));
        }

        if ($arr['cardType'] == 'anniversary') {

            $date = Carbon::parse($arr['anniversaryDate']);

            $query->whereDay('employee.anniversary', $date->day)
                ->whereMonth('employee.anniversary', $date->month);

        } elseif ($arr['cardType'] == 'birthday') {

            $date = Carbon::parse($arr['anniversaryDate']);

            $query->whereDay('employee.dob', $date->day)
                ->whereMonth('employee.dob', $date->month);
        }

        if ($flag == 1) {
            $query->whereIn('employee.id',$arr);
        }

        return $query->get()->toArray();
    }

    public static function getEmpPersonalInfo($employeeId = null, $employeeIdArr = [], $flag = null, $employeeAutoIdArr = [])
    {
        $query = DB::table('employee')
            ->select(
                'employee.*',
                'occupation_father_tb.element as father_occupation_name',
                'occupation_mother_tb.element as mother_occupation_name',
                'occupation_spouse_tb.element as spouse_occupation_name',
                'designation_tb.element as designation_name'
            )
            ->leftJoin('common_table as occupation_father_tb', 'occupation_father_tb.element_code', '=', 'employee.father_occupation')
            ->leftJoin('common_table as occupation_mother_tb', 'occupation_mother_tb.element_code', '=', 'employee.mother_occupation')
            ->leftJoin('common_table as occupation_spouse_tb', 'occupation_spouse_tb.element_code', '=', 'employee.spouse_occupation')
            ->leftJoin('common_table as designation_tb', 'designation_tb.element_code', '=', 'employee.designation');

        if ($flag == 1) {
            $query->where('employee.is_active', 1);
        }

        if ($employeeId) {
            $query->where('employee.employee_id', $employeeId);
        } elseif (!empty($employeeIdArr)) {
            $query->whereIn('employee.employee_id', $employeeIdArr);
        } elseif (!empty($employeeAutoIdArr)) {
            $query->whereIn('employee.id', $employeeAutoIdArr);
        }

        return $query->get()->toArray();
    }
}
