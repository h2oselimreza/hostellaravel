<?php

namespace App\Repositories;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IndividualCustomerRepository
{
    public function changeCompanyStatus($companyCode, $statusFlag)
    {
        return DB::transaction(function () use ($companyCode, $statusFlag) {

            $authUserId = Auth::user()->user_id ?? null;

            // ================= ACTIVE =================
            if ($statusFlag == 1) {

                DB::table('corporate_companies')
                    ->where('company_code', $companyCode)
                    ->update([
                        'is_active'      => 1,
                        'updated_by'     => $authUserId,
                        'updated_dt_tm'  => Carbon::now(),
                    ]);

                return 1;
            }

            // ================= INACTIVE =================
            DB::table('corporate_companies')
                ->where('company_code', $companyCode)
                ->update([
                    'is_active'      => 0,
                    'updated_by'     => $authUserId,
                    'updated_dt_tm'  => Carbon::now(),
                ]);

            // Get system users
            $systemUserArr = DB::table('customer_employee')
                ->where('system_user', 1)
                ->where('company', $companyCode)
                ->pluck('employee_id')
                ->toArray();

            if (!empty($systemUserArr)) {

                DB::table('customer_employee')
                    ->where('company', $companyCode)
                    ->update([
                        'updated_by'     => $authUserId,
                        'updated_dt_tm'  => Carbon::now(),
                        'system_user'    => 0,
                    ]);

                DB::table('users')
                    ->whereIn('user_id', $systemUserArr)
                    ->update([
                        'is_active'      => 0,
                        'updated_by'     => $authUserId,
                        'updated_dt_tm'  => Carbon::now(),
                    ]);
            }

            return 1;
        });
    }

    public function getCardAssignedIndividualAcc($companyType = null)
    {
        return DB::table('corporate_companies')
            ->select(
                'corporate_companies.*',
                'employee.employee_name as rm_name',
                'package.package_name',
                'user_group.group_name as user_group_name',
                'membership_card.validity_month',
                'membership_card.activation_dt_tm',
                'membership_card.valid_dt_tm'
            )

            ->leftJoin('employee', 'employee.employee_id', '=', 'corporate_companies.rm_id')
            ->leftJoin('package', 'package.package_code', '=', 'corporate_companies.package')

            ->join('customer_employee', 'customer_employee.company', '=', 'corporate_companies.company_code')
            ->join('users', 'users.user_id', '=', 'customer_employee.employee_id')
            ->join('user_group', 'user_group.id', '=', 'users.user_group')

            ->join('membership_card', 'membership_card.card_number', '=', 'corporate_companies.membership_card')

            ->where('corporate_companies.is_active', 1)
            ->where('corporate_companies.company_type', $companyType)
            ->where('membership_card.is_active', config('constants.CARD_ACTIVE'))
            ->get();
    }
}