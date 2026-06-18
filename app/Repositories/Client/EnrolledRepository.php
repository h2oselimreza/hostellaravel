<?php

namespace App\Repositories\Client;

use App\Models\Admin\MasterData\ServiceVariant;
use App\Models\Client\HomeServiceAppDetail;
use App\Models\Client\HomeServiceAppSummaryGen;
use App\Models\CorporateCompany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class EnrolledRepository
{

    public function getIndividualUsers(
        string $companyCode,
        ?string $individualCompanyCode = null
    ): Collection {

        $query = DB::table('enrolled_corp_indv')

            ->select([
                'enrolled_corp_indv.*',
                'customer_employee.employee_name',
                'customer_employee.employee_image',
                'customer_employee.primary_mobile',
            ])

            ->join(
                'customer_employee',
                'customer_employee.employee_id',
                '=',
                'enrolled_corp_indv.indv_employee'
            )

            ->where(
                'enrolled_corp_indv.corp_company',
                $companyCode
            );

        if ($individualCompanyCode) {

            $query->where(
                'enrolled_corp_indv.indv_company',
                $individualCompanyCode
            );
        }

        return $query->get();
    }

    public function getValidUserInfo(
        string $mobileNo,
        string $approvalCode
    ): array {

        $flag = $this->checkEnrolledUserExists($mobileNo);

        if (!$flag) {

            return [
                'flag' => 3, // already Exists
            ];
        }

        $row = DB::table('customer_employee')

            ->select([
                'customer_employee.employee_id',
                'customer_employee.employee_name',
                'customer_employee.primary_mobile',
                'customer_employee.email',
                'customer_employee.employee_image',
                'customer_employee.company',
            ])
            ->join(
                'users',
                'users.user_id',
                '=',
                'customer_employee.employee_id'
            )
            ->join(
                'corporate_companies',
                'corporate_companies.company_code',
                '=',
                'customer_employee.company'
            )
            ->where('users.username', $mobileNo)
            ->where(
                'corporate_companies.enrolled_apprv_code',
                $approvalCode
            )
            ->where('users.is_active', 1)
            ->first();
        if ($row) {

            return [
                'flag' => 1,
                'personName' => $row->employee_name,
                'mobileNo' => $row->primary_mobile,
                'email' => $row->email ?? "",
                'image' => $row->employee_image ?? "",
                'indvCompanyCode' => $row->company,
                'indvEmployeeId' => $row->employee_id,
            ];
        }

        return [
            'flag' => 2, // mobile no and approval code not matched
        ];
    }


    public function checkEnrolledUserExists(string $mobileNo): int
    {
        $exists = DB::table('enrolled_corp_indv')
            ->select('enrolled_corp_indv.id')
            ->join(
                'customer_employee',
                'customer_employee.company',
                '=',
                'enrolled_corp_indv.indv_company'
            )
            ->join(
                'users',
                'users.user_id',
                '=',
                'customer_employee.employee_id'
            )
            ->where(
                'enrolled_corp_indv.corp_company',
                Auth::user()->customerEmployee->company,
            )
            ->where(
                'users.username',
                $mobileNo
            )

            ->exists();
        return $exists ? 0 : 1;
    }

    public function addIndividualUser(
        array $insertArr,
        array $updateArr
    ): int {

        DB::table('enrolled_corp_indv')
            ->insert($insertArr);

        DB::table('corporate_companies')
            ->where(
                'company_code',
                $updateArr['company_code']
            )
            ->update($updateArr);

        return 1;
    }

    public function getCorporateCompanies(
        string $individualCompanyCode,
        ?string $companyCode = null
    ) {
        $query = DB::table('enrolled_corp_indv')
            ->select(
                'enrolled_corp_indv.*',
                'corporate_companies.title as company_name',
                'corporate_companies.company_code',
                'customer_employee.employee_name'
            )
            ->join(
                'corporate_companies',
                'corporate_companies.company_code',
                '=',
                'enrolled_corp_indv.corp_company'
            )
            ->join(
                'customer_employee',
                'customer_employee.company',
                '=',
                'enrolled_corp_indv.indv_company'
            )
            ->where(
                'customer_employee.emp_type',
                'system_manager'
            )

            ->where(
                'enrolled_corp_indv.indv_company',
                $individualCompanyCode
            );

        if (!empty($companyCode)) {
            $query->where(
                'enrolled_corp_indv.corp_company',
                $companyCode
            );
        }

        return $query->get();
    }

    public function generateApprovalCode(
        string $companyCode,
        array $updateArr
    ) {
        $updated = DB::table('corporate_companies')
            ->where('company_code', $companyCode)
            ->update($updateArr);
        return $updated ? 1 : 0;
    }

    public function updateUserInfo(
        array $updateArr,
        string $companyCode,
        string $individualCompanyCode
    ) {
        DB::table('enrolled_corp_indv')
            ->where(
                'corp_company',
                $companyCode
            )
            ->where(
                'indv_company',
                $individualCompanyCode
            )
            ->update($updateArr);

        return 1;
    }
}