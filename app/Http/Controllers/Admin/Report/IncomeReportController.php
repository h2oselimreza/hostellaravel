<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Repositories\CommonRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

class IncomeReportController extends Controller
{

    public function index(CommonRepository $commonRepository, InvoiceRepository $invoiceRepository)
    {
        $itemHeads = $commonRepository->getItemHead(1);

        $boarders = $invoiceRepository->getBoarderList(1);

        return view('admin.report.income-report.index', compact(
            'itemHeads',
            'boarders'
        ));
    }

    public function incomeReportDetails(Request $request, ReportRepository $reportRepository)
    {

        $arr = [
            'itemHeadCode' => $request->itemHeadStr,
            'boarderId'    => $request->boarderStr,
            'fromDate'     => $request->fromDate,
            'toDate'       => $request->toDate,
            'status'       => $request->filled('status') ? (int) $request->status : null,
            'dateType'     => $request->dateType,
        ];

        if (
            ($arr['itemHeadCode'] != '' || $arr['boarderId'] != '') &&
            $arr['fromDate'] &&
            $arr['toDate'] &&
            $arr['dateType']
        ) {

            // Head Wise Report
            if ($arr['itemHeadCode'] != '' && $arr['boarderId'] == '') {

                $incomeDetails = $reportRepository->incomeReportDetails($arr);

                $arr['flag'] = 'headwise';

                $categoryDetails = $reportRepository->getIncomeCategoryWiseDetails($arr);
                
                return view('admin.report.income-report.incomeHeadSummaryReportView', compact(
                    'incomeDetails',
                    'categoryDetails'
                ))->with([
                    'fromDate'   => $arr['fromDate'],
                    'toDate'     => $arr['toDate'],
                    'reportType' => 'headwise',
                ]);
            }

            // Boarder Wise Report
            if ($arr['itemHeadCode'] == '' && $arr['boarderId'] != '') {

                $arr['flag'] = 'boarderwise';

                $boarderDetails = $reportRepository->getIncomeBoarderWiseDetails($arr);
                
                return view('admin.report.income-report.incomeBoarderSummaryReportView', compact(
                    'boarderDetails'
                ))->with([
                    'fromDate'   => $arr['fromDate'],
                    'toDate'     => $arr['toDate'],
                    'reportType' => 'boarderwise',
                ]);
            }

            // Head + Boarder Wise Report
            if ($arr['itemHeadCode'] != '' && $arr['boarderId'] != '') {

                $incomeDetails = $reportRepository->getIncomeHeadBoarderWiseDetails($arr);

                $categoryDetails = $reportRepository->getIncomeCategoryBoarderDetails($arr);
                
                return view('admin.report.income-report.incomeHeadBoarderReportView', compact(
                    'incomeDetails',
                    'categoryDetails'
                ))->with([
                    'fromDate' => $arr['fromDate'],
                    'toDate'   => $arr['toDate'],
                ]);
            }
        }

        return redirect()->route('admin.income.report')->with('error', "Please fill in all required fields.");
    }
}
