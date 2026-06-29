<?php

namespace App\Repositories;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReportRepository
{
    public function expenseReportDetails(array $arr)
    {
        $expenseArr = explode(',', $arr['expenseHeadCode']);

        return DB::table('expense_detail')
            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,
                SUM(expense_detail.quantity) as total_quantity,
                expense_detail.unit_name,
                expense_detail.unit_price,
                expense_detail.adjust,
                SUM(expense_detail.amount) / SUM(expense_detail.quantity) as average_unit_price,
                COUNT(expense_detail.id) as total_tran,
                cost_heads.cost_head as expense_head_name,
                expense_detail.expense_head,
                cost_heads.cost_category,
                cost_categories.category_name
            ")
            ->join('expense_summary', 'expense_summary.expense_no', '=', 'expense_detail.expense_no')
            ->join('cost_heads', 'cost_heads.cost_head_code', '=', 'expense_detail.expense_head')
            ->join('cost_categories', 'cost_categories.category_code', '=', 'cost_heads.cost_category')
            ->whereDate('expense_summary.expense_date', '>=', $arr['fromDate'])
            ->whereDate('expense_summary.expense_date', '<=', $arr['toDate'])
            ->whereIn('expense_detail.expense_head', $expenseArr)
            ->groupBy(
                'expense_detail.expense_head',
                'expense_detail.unit_name',
                'expense_detail.unit_price',
                'expense_detail.adjust',
                'cost_heads.cost_head',
                'cost_heads.cost_category',
                'cost_categories.category_name'
            )
            ->orderBy('cost_categories.category_name')
            ->orderBy('expense_detail.expense_head')
            ->get();
    }

    public function getExpCategoryWiseDetails(array $arr)
    {
        $expenseArr = explode(',', $arr['expenseHeadCode']);

        return DB::table('expense_detail')
            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,
                cost_heads.cost_category,
                cost_categories.category_name
            ")
            ->join('expense_summary', 'expense_summary.expense_no', '=', 'expense_detail.expense_no')
            ->join('cost_heads', 'cost_heads.cost_head_code', '=', 'expense_detail.expense_head')
            ->join('cost_categories', 'cost_categories.category_code', '=', 'cost_heads.cost_category')
            ->whereDate('expense_summary.expense_date', '>=', $arr['fromDate'])
            ->whereDate('expense_summary.expense_date', '<=', $arr['toDate'])
            ->whereIn('expense_detail.expense_head', $expenseArr)
            ->groupBy(
                'cost_categories.category_code',
                'cost_heads.cost_category',
                'cost_categories.category_name'
            )
            ->orderBy('cost_categories.category_name', 'ASC')
            ->get();
    }


    public function getExpVendorWiseDetails(array $arr)
    {
        $vendorArr = explode(',', $arr['vendorCode']);

        return DB::table('expense_summary')
            ->selectRaw("
                SUM(expense_summary.total_amount) as total_expense,
                vendor.title as vendor_title
            ")
            ->join('vendor', 'vendor.vendor_code', '=', 'expense_summary.vendor')
            ->whereDate('expense_summary.expense_date', '>=', $arr['fromDate'])
            ->whereDate('expense_summary.expense_date', '<=', $arr['toDate'])
            ->whereIn('expense_summary.vendor', $vendorArr)
            ->groupBy(
                'expense_summary.vendor',
                'vendor.title'
            )
            ->orderBy('vendor.title', 'ASC')
            ->get();
    }

    public function getExpHeadVendorWiseDetails(array $arr)
    {
        $vendorArr = explode(',', $arr['vendorCode']);
        $expenseArr = explode(',', $arr['expenseHeadCode']);

        return DB::table('expense_detail')
            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,
                SUM(expense_detail.quantity) as total_quantity,
                SUM(expense_detail.amount) / SUM(expense_detail.quantity) as average_unit_price,
                COUNT(expense_detail.id) as total_tran,
                expense_summary.vendor,
                expense_detail.adjust,
                vendor.title as vendor_title,
                cost_heads.cost_head as expense_head_name,
                cost_heads.unit_name,
                expense_detail.expense_head,
                cost_heads.cost_category,
                cost_categories.category_name
            ")
            ->join('expense_summary', 'expense_summary.expense_no', '=', 'expense_detail.expense_no')
            ->join('vendor', 'vendor.vendor_code', '=', 'expense_summary.vendor')
            ->join('cost_heads', 'cost_heads.cost_head_code', '=', 'expense_detail.expense_head')
            ->join('cost_categories', 'cost_categories.category_code', '=', 'cost_heads.cost_category')
            ->whereDate('expense_summary.expense_date', '>=', $arr['fromDate'])
            ->whereDate('expense_summary.expense_date', '<=', $arr['toDate'])
            ->whereIn('expense_summary.vendor', $vendorArr)
            ->whereIn('expense_detail.expense_head', $expenseArr)
            ->groupBy(
                'expense_summary.vendor',
                'expense_detail.expense_head',
                'expense_detail.adjust',
                'vendor.title',
                'cost_heads.cost_head',
                'cost_heads.unit_name',
                'cost_heads.cost_category',
                'cost_categories.category_name'
            )
            ->orderBy('expense_summary.vendor', 'ASC')
            ->orderBy('cost_categories.category_name', 'ASC')
            ->orderBy('expense_detail.expense_head', 'ASC')
            ->get();
    }

    public function getExpCategoryVendorDetails(array $arr)
    {
        $vendorArr = explode(',', $arr['vendorCode']);
        $expenseArr = explode(',', $arr['expenseHeadCode']);

        return DB::table('expense_detail')
            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,
                cost_heads.cost_category,
                cost_categories.category_name
            ")
            ->join('expense_summary', 'expense_summary.expense_no', '=', 'expense_detail.expense_no')
            ->join('vendor', 'vendor.vendor_code', '=', 'expense_summary.vendor')
            ->join('cost_heads', 'cost_heads.cost_head_code', '=', 'expense_detail.expense_head')
            ->join('cost_categories', 'cost_categories.category_code', '=', 'cost_heads.cost_category')
            ->whereDate('expense_summary.expense_date', '>=', $arr['fromDate'])
            ->whereDate('expense_summary.expense_date', '<=', $arr['toDate'])
            ->whereIn('expense_summary.vendor', $vendorArr)
            ->whereIn('expense_detail.expense_head', $expenseArr)
            ->groupBy(
                'cost_categories.category_code',
                'cost_heads.cost_category',
                'cost_categories.category_name'
            )
            ->orderBy('cost_categories.category_name', 'ASC')
            ->get();
    }
}