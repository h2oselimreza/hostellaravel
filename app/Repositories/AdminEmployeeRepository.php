<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\CustomerEmployee;
use Illuminate\Support\Facades\DB;

class AdminEmployeeRepository
{

    public function getEmpPersonalInfo($employeeId = null, $employeeIdArr = [], $flag = null)
    {
        $query = DB::table('employee')

            ->select(
                'employee.*',
                'occupation_father_tb.element as father_occupation_name',
                'occupation_mother_tb.element as mother_occupation_name',
                'occupation_spouse_tb.element as spouse_occupation_name',
                'emer_con_rel_tb.element as emer_contact_relation_name',
                'guardian_rel_tb.element_code as guardian_relation_name',
                'designation_tb.element as designation_name',
                'user_group.group_name',
                'user_group.id as group_id',
                'users.is_reset'
            )

            ->leftJoin('common_table as occupation_father_tb', 'occupation_father_tb.element_code', '=', 'employee.father_occupation')
            ->leftJoin('common_table as occupation_mother_tb', 'occupation_mother_tb.element_code', '=', 'employee.mother_occupation')
            ->leftJoin('common_table as occupation_spouse_tb', 'occupation_spouse_tb.element_code', '=', 'employee.spouse_occupation')
            ->leftJoin('common_table as emer_con_rel_tb', 'emer_con_rel_tb.element_code', '=', 'employee.emer_contact_relation')
            ->leftJoin('common_table as guardian_rel_tb', 'guardian_rel_tb.element_code', '=', 'employee.guardian_relation')
            ->leftJoin('common_table as designation_tb', 'designation_tb.element_code', '=', 'employee.designation')
            ->leftJoin('users', 'users.user_id', '=', 'employee.employee_id')
            ->leftJoin('user_group', 'user_group.id', '=', 'users.user_group');

        // if ($flag == null)
        if ($flag === null) {
            $query->where('employee.is_active', 1);
        }

        if (!empty($employeeId)) {
            $query->where('employee.employee_id', $employeeId);

        } elseif (!empty($employeeIdArr)) {
            $query->whereIn('employee.employee_id', $employeeIdArr);
        }

        return $query->get()->toArray();
    }

}