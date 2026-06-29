<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

class LossProfitReportController extends Controller
{

    public function index() {
        return view('admin.report.loss-profit.index');
    }

    public function lossProfitReportDetails(Request $request, ReportRepository $reportRepository)
    {

        $arr = [
            'fromDate' => $request->fromDate,
            'toDate'   => $request->toDate,
        ];

        if ($arr['fromDate'] && $arr['toDate']) {

            $incomeDetails = $reportRepository->getIncomeLossProfitDetails($arr);

            $expenseDetails = $reportRepository->getExpLossProfitDetails($arr);

            return view('admin.report.loss-profit.lossProfitHeadReportView', compact(
                'incomeDetails',
                'expenseDetails'
            ))->with([
                'fromDate' => $arr['fromDate'],
                'toDate'   => $arr['toDate'],
            ]);
        }

        return redirect()->route('admin.loss.profit.report')->with('error', "Please fill in all required fields.");
    }
}
