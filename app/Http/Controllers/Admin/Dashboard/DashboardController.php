<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\CommonRepository;
use App\Repositories\DashboardRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(
        DashboardRepository $dashboardRepository,
        CommonRepository $commonRepository
        )
    {
        // $data['countInformation'] = $dashboardRepository->getFouCountValue();
        // $data['companies'] = $commonRepository->getCompanyList(1,config('constants.CORPORATE_CUST'));
        // $data['statusInformation'] = $dashboardRepository->getStatusInfo();

        return view('dashboard');
    }
}
