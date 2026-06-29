<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Admin\Vendor;
use App\Repositories\CommonRepository;
use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

class ExpenseReportController extends Controller
{
    public function index(CommonRepository $commonRepository)
    {

        $costHeads = $commonRepository->getCostHead(1);

        $vendors = Vendor::where('is_active',1)->get();

        return view('admin.report.expense.expense-report', compact(
            'costHeads',
            'vendors'
        ));
    }

    public function expenseReportDetails(Request $request, ReportRepository $reportRepository)
    {

        $arr = [
            'expenseHeadCode' => $request->costHeadStr,
            'vendorCode'      => $request->vendorStr,
            'fromDate'        => $request->fromDate,
            'toDate'          => $request->toDate,
        ];

        if (
            ($arr['expenseHeadCode'] != '' || $arr['vendorCode'] != '') &&
            ($arr['fromDate'] && $arr['toDate'])
        ) {

            // Head Wise Report
            if ($arr['expenseHeadCode'] != '' && $arr['vendorCode'] == '') {

                $expenseDetails = $reportRepository->expenseReportDetails($arr);
                //dd($expenseDetails);
                $arr['flag'] = 'headwise';

                $categoryDetails = $reportRepository->getExpCategoryWiseDetails($arr);

                return view('admin.report.expense.expHeadSummaryReportView', compact(
                    'expenseDetails',
                    'categoryDetails'
                ))->with([
                    'fromDate'   => $arr['fromDate'],
                    'toDate'     => $arr['toDate'],
                    'reportType' => 'headwise',
                ]);
            }

            // Vendor Wise Report
            if ($arr['expenseHeadCode'] == '' && $arr['vendorCode'] != '') {

                $arr['flag'] = 'vendorwise';

                $vendorDetails = $reportRepository->getExpVendorWiseDetails($arr);

                return view('admin.report.expense.expVendorSummaryReportView', compact(
                    'vendorDetails'
                ))->with([
                    'fromDate'   => $arr['fromDate'],
                    'toDate'     => $arr['toDate'],
                    'reportType' => 'vendorwise',
                ]);
            }

            // Head + Vendor Wise Report
            if ($arr['expenseHeadCode'] != '' && $arr['vendorCode'] != '') {

                $expenseDetails = $reportRepository->getExpHeadVendorWiseDetails($arr);

                $categoryDetails = $reportRepository->getExpCategoryVendorDetails($arr);
                
                return view('admin.report.expense.expHeadVendorReportView', compact(
                    'expenseDetails',
                    'categoryDetails'
                ))->with([
                    'fromDate' => $arr['fromDate'],
                    'toDate'   => $arr['toDate'],
                ]);
            }
        }

        return redirect()->route('admin.report.expense-report')->with('error', 'Data has not been selected.');
    }
}
