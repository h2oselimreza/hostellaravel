<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\CustomerEmployee;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getFouCountValue()
    {
        $resultArr = [];

        $resultArr['workshopCount'] = DB::table('workshops')
            ->where('is_active', 1)
            ->count();

        $resultArr['companyCount'] = DB::table('corporate_companies')
            ->where('company_type', config('constants.CORPORATE_CUST'))
            ->where('is_active', 1)
            ->count();

        $resultArr['homeServiceCount'] = DB::table('home_service_app_summary_gen')
            ->count();

        $resultArr['appointmentCount'] = DB::table('appointment_summary')
            ->count();

        return $resultArr;
    }

    public function getStatusInfo()
    {
        $result = [];

        $result['quotationRequests'] = DB::table('quotation_req_summary')
            ->selectRaw('COUNT(id) as status_count, status')
            ->where('status', '!=', config('constants.REQ_DRAFT_STATUS'))
            ->groupBy('status')
            ->get()
            ->toArray();

        $result['appointmentServices'] = DB::table('appointment_summary')
            ->selectRaw('COUNT(id) as status_count, status')
            ->groupBy('status')
            ->get()
            ->toArray();

        $result['homeServices'] = DB::table('home_service_app_summary_gen')
            ->selectRaw('COUNT(id) as status_count, status')
            ->groupBy('status')
            ->get()
            ->toArray();

        return $result;
    }
}