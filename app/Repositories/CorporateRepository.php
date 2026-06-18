<?php 

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CorporateRepository
{
    public function getCompanyGeneralInfo(array $arr)
    {
        $query = DB::table('corporate_companies')
            ->select(
                'corporate_companies.*',
                'divisions.division_en_name',
                'divisions.division_bn_name',
                'districts.district_en_name',
                'districts.district_bn_name',
                'upazilas.upozilla_en_name',
                'upazilas.upozilla_bn_name',
                'package.package_name',
                'package.package_details',
                'vts_com_tb.element as vts_company_title'
            )

            ->leftJoin(
                'divisions',
                'divisions.id',
                '=',
                'corporate_companies.division'
            )

            ->leftJoin(
                'districts',
                'districts.id',
                '=',
                'corporate_companies.district'
            )

            ->leftJoin(
                'upazilas',
                'upazilas.id',
                '=',
                'corporate_companies.upozilla'
            )

            ->leftJoin(
                'common_table as vts_com_tb',
                'vts_com_tb.element_code',
                '=',
                'corporate_companies.vts_company'
            )

            ->leftJoin(
                'package',
                'package.package_code',
                '=',
                'corporate_companies.package'
            );

        if (($arr['bulkFlag'] ?? 0) == 0) {
            $query->where(
                'corporate_companies.company_code',
                $arr['companyCode']
            );
        }

        return $query->get();
    }
}