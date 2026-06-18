<?php

namespace App\Repositories\Client;

use App\Models\Company;
use App\Models\CorporateCompany;
use App\Models\CustomerEmployee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeRepository
{

public function getClientEmployeeProfile($employeeId = null, array $employeeIdArr = [], $flag = null, $companyCode)
{
    return DB::table('customer_employee')
        // Joins with Aliases
        ->leftJoin('common_table as occupation_father_tb', 'occupation_father_tb.element_code', '=', 'customer_employee.father_occupation')
        ->leftJoin('common_table as occupation_mother_tb', 'occupation_mother_tb.element_code', '=', 'customer_employee.mother_occupation')
        ->leftJoin('common_table as occupation_spouse_tb', 'occupation_spouse_tb.element_code', '=', 'customer_employee.spouse_occupation')
        ->leftJoin('common_table as emer_con_rel_tb', 'emer_con_rel_tb.element_code', '=', 'customer_employee.emer_contact_relation')
        ->leftJoin('common_table as guardian_rel_tb', 'guardian_rel_tb.element_code', '=', 'customer_employee.guardian_relation')
        ->leftJoin('common_table as emp_type_tb', 'emp_type_tb.element_code', '=', 'customer_employee.emp_type')
        ->leftJoin('users', 'users.user_id', '=', 'customer_employee.employee_id')
        ->leftJoin('user_group', 'user_group.id', '=', 'users.user_group')

        // Selections
        ->select([
            'customer_employee.*',
            'occupation_father_tb.element as father_occupation_name',
            'occupation_mother_tb.element as mother_occupation_name',
            'occupation_spouse_tb.element as spouse_occupation_name',
            'emer_con_rel_tb.element as emer_contact_relation_name',
            'emp_type_tb.element as emp_type_name',
            'guardian_rel_tb.element_code as guardian_relation_name',
            'user_group.group_name',
            'users.is_reset','emp_type_tb.element as emp_type_name'
        ])

        // Mandatory Filtering
        ->where('customer_employee.company', $companyCode)

        // Conditional Filtering (The Laravel Expert Way)
        ->when($flag === null, function ($query) {
            return $query->where('customer_employee.is_active', 1);
        })
        ->when($employeeId, function ($query) use ($employeeId) {
            return $query->where('customer_employee.employee_id', $employeeId);
        }, function ($query) use ($employeeIdArr) {
            // This runs if $employeeId is null/false (the 'else' case)
            return $query->when(!empty($employeeIdArr), function ($q) use ($employeeIdArr) {
                return $q->whereIn('customer_employee.id', $employeeIdArr);
            });
        })
        ->get()
        ->toArray();
    }

    public function getEmpPersonalInfo($employeeId = null, $employeeIdArr = [], $flag = null, $companyCode)
    {
        $query = DB::table('customer_employee')
            ->select(
                'customer_employee.*',
                'occupation_father_tb.element as father_occupation_name',
                'occupation_mother_tb.element as mother_occupation_name',
                'occupation_spouse_tb.element as spouse_occupation_name',
                'emer_con_rel_tb.element as emer_contact_relation_name',
                'emp_type_tb.element as emp_type_name',
                'guardian_rel_tb.element_code as guardian_relation_name',
                'user_group.group_name',
                'users.is_reset'
            )
            ->leftJoin('common_table as occupation_father_tb', 'occupation_father_tb.element_code', '=', 'customer_employee.father_occupation')
            ->leftJoin('common_table as occupation_mother_tb', 'occupation_mother_tb.element_code', '=', 'customer_employee.mother_occupation')
            ->leftJoin('common_table as occupation_spouse_tb', 'occupation_spouse_tb.element_code', '=', 'customer_employee.spouse_occupation')
            ->leftJoin('common_table as emer_con_rel_tb', 'emer_con_rel_tb.element_code', '=', 'customer_employee.emer_contact_relation')
            ->leftJoin('common_table as guardian_rel_tb', 'guardian_rel_tb.element_code', '=', 'customer_employee.guardian_relation')
            ->leftJoin('common_table as emp_type_tb', 'emp_type_tb.element_code', '=', 'customer_employee.emp_type')
            ->leftJoin('users', 'users.user_id', '=', 'customer_employee.employee_id')
            ->leftJoin('user_group', 'user_group.id', '=', 'users.user_group')
            ->where('customer_employee.company', $companyCode);

        if ($flag === null) {
            $query->where('customer_employee.is_active', 1);
        }

        if ($employeeId) {
            $query->where('customer_employee.employee_id', $employeeId);
        } elseif (!empty($employeeIdArr)) {
            $query->whereIn('customer_employee.id', $employeeIdArr);
        }

        return $query->get();
    }

    public function changeEmployeeStatus($employeeId, $companyCode, $status)
    {

        $employee = CustomerEmployee::where('employee_id', $employeeId)
            ->where('company', $companyCode)
            ->first();

        if (!$employee) {
            return 2;
        }

        if ($status == 0) {

            User::where('user_id', $employeeId)
                ->update([
                    'is_active'     => 0,
                    'updated_by'    => Auth::user()->user_id,
                    'updated_dt_tm' => Carbon::now(),
                ]);

            $employee->update([
                'is_active'  => 0,
                'system_user'=> 0,
            ]);

        } else {

            $company = CorporateCompany::where('company_code', $companyCode)
                ->select('is_active')
                ->first();

            if (!$company || $company->is_active == 0) {
                return 3;
            }

            $employee->update([
                'is_active' => 1,
            ]);
        }

        return 1;
    }


    public function getEmpEducationalDetails($employeeId = null, $employeeAutoIdArr = [])
    {
        $queryEmployeeId = null;

        if (!empty($employeeAutoIdArr)) {
            $queryEmployeeId = DB::table('customer_employee')
                ->select('employee_id')
                ->whereIn('id', $employeeAutoIdArr);
        }

        $query = DB::table('customer_emp_edu_qual')
            ->select(
                'customer_emp_edu_qual.*',
                'education_level_tb.element as education_level',
                'exam_title_tb.element as exam_title',
                'education_board_tb.element as education_board_name',
                'quali_result_tb.element as quali_result_name'
            )
            ->leftJoin(
                'common_table as education_level_tb',
                'education_level_tb.element_code',
                '=',
                'customer_emp_edu_qual.level_of_education'
            )
            ->leftJoin(
                'common_table as exam_title_tb',
                'exam_title_tb.element_code',
                '=',
                'customer_emp_edu_qual.exam_degree'
            )
            ->leftJoin(
                'common_table as education_board_tb',
                'education_board_tb.element_code',
                '=',
                'customer_emp_edu_qual.education_board'
            )
            ->leftJoin(
                'common_table as quali_result_tb',
                'quali_result_tb.element_code',
                '=',
                'customer_emp_edu_qual.qualification_result'
            );

        if (!empty($employeeId)) {
            $query->where('customer_emp_edu_qual.employee_id', $employeeId);
        }

        if (!empty($employeeAutoIdArr)) {
            $query->whereIn(
                'customer_emp_edu_qual.employee_id',
                $queryEmployeeId
            );
        }

        return $query->get()->toArray();
    }

    public function getEmpWorkingDetails($employeeId = null, $employeeAutoIdArr = [])
    {
        $queryEmployeeId = null;

        if (!empty($employeeAutoIdArr)) {
            $queryEmployeeId = DB::table('customer_employee')
                ->select('employee_id')
                ->whereIn('id', $employeeAutoIdArr);
        }

        $query = DB::table('customer_emp_working_exp');

        if (!empty($employeeId)) {
            $query->where('employee_id', $employeeId);
        }

        if (!empty($employeeAutoIdArr)) {
            $query->whereIn('employee_id', $queryEmployeeId);
        }

        return $query
            ->orderBy('from_date', 'DESC')
            ->get()
            ->toArray();
    }

}