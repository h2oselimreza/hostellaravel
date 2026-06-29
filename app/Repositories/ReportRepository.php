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

    public function incomeReportDetails(array $arr)
    {
        $itemArr = explode(',', $arr['itemHeadCode']);

        $query = DB::table('invoice_detail')
            ->selectRaw("
                SUM(invoice_detail.amount) as total_income,
                SUM(invoice_detail.quantity) as total_quantity,
                invoice_detail.unit_name,
                invoice_detail.unit_price,
                invoice_detail.adjust,
                SUM(invoice_detail.amount) / SUM(invoice_detail.quantity) as average_unit_price,
                COUNT(invoice_detail.id) as total_tran,
                item_heads.item_head as item_head_name,
                invoice_detail.item_head,
                item_heads.item_category,
                item_categories.category_name
            ")
            ->join('invoice_summary', 'invoice_summary.invoice_no', '=', 'invoice_detail.invoice_no')
            ->join('item_heads', 'item_heads.item_head_code', '=', 'invoice_detail.item_head')
            ->join('item_categories', 'item_categories.category_code', '=', 'item_heads.item_category');

        // Date Filter
        if ($arr['dateType'] == 'invoiceDate') {
            $query->whereDate('invoice_summary.invoice_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_date', '<=', $arr['toDate']);
        } elseif ($arr['dateType'] == 'dueDate') {
            $query->whereDate('invoice_summary.invoice_due_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_due_date', '<=', $arr['toDate']);
        }

        // Payment Status Filter
        if ($arr['status'] == config('constants.UNPAID') || $arr['status'] == config('constants.PAID')) {
            $query->where('invoice_summary.is_paid', $arr['status']);
        }

        return $query
            ->whereIn('invoice_detail.item_head', $itemArr)
            ->groupBy(
                'invoice_detail.item_head',
                'invoice_detail.unit_name',
                'invoice_detail.unit_price',
                'invoice_detail.adjust',
                'item_heads.item_head',
                'item_heads.item_category',
                'item_categories.category_name'
            )
            ->orderBy('item_categories.category_name', 'ASC')
            ->orderBy('invoice_detail.item_head', 'ASC')
            ->get();
    }


    public function getIncomeCategoryWiseDetails(array $arr)
    {
        $itemArr = explode(',', $arr['itemHeadCode']);

        $query = DB::table('invoice_detail')
            ->selectRaw("
                SUM(invoice_detail.amount) as total_income,
                item_heads.item_category,
                item_categories.category_name
            ")
            ->join('invoice_summary', 'invoice_summary.invoice_no', '=', 'invoice_detail.invoice_no')
            ->join('item_heads', 'item_heads.item_head_code', '=', 'invoice_detail.item_head')
            ->join('item_categories', 'item_categories.category_code', '=', 'item_heads.item_category');

        // Date Filter
        if ($arr['dateType'] === 'invoiceDate') {
            $query->whereDate('invoice_summary.invoice_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_date', '<=', $arr['toDate']);
        } elseif ($arr['dateType'] === 'dueDate') {
            $query->whereDate('invoice_summary.invoice_due_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_due_date', '<=', $arr['toDate']);
        }

        // Payment Status Filter
        if ($arr['status'] == config('constants.PAID') || $arr['status'] == config('constants.UNPAID')) {
            $query->where('invoice_summary.is_paid', $arr['status']);
        }

        return $query
            ->whereIn('invoice_detail.item_head', $itemArr)
            ->groupBy(
                'item_categories.category_code',
                'item_heads.item_category',
                'item_categories.category_name'
            )
            ->orderBy('item_categories.category_name', 'ASC')
            ->get();
    }

    public function getIncomeBoarderWiseDetails(array $arr)
    {
        $boarderArr = explode(',', $arr['boarderId']);

        $query = DB::table('invoice_summary')
            ->selectRaw("
                SUM(invoice_summary.invoice_amount) as total_income,
                boarder.boarder_name
            ")
            ->join('boarder', 'boarder.boarder_id', '=', 'invoice_summary.boarder');

        // Date Filter
        if ($arr['dateType'] === 'invoiceDate') {
            $query->whereDate('invoice_summary.invoice_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_date', '<=', $arr['toDate']);
        } elseif ($arr['dateType'] === 'dueDate') {
            $query->whereDate('invoice_summary.invoice_due_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_due_date', '<=', $arr['toDate']);
        }

        // Payment Status Filter
        if ($arr['status'] == config('constants.PAID') || $arr['status'] == config('constants.UNPAID')) {
            $query->where('invoice_summary.is_paid', $arr['status']);
        }

        return $query
            ->whereIn('invoice_summary.boarder', $boarderArr)
            ->groupBy(
                'invoice_summary.boarder',
                'boarder.boarder_name'
            )
            ->orderBy('boarder.boarder_name', 'ASC')
            ->get();
    }

    public function getIncomeHeadBoarderWiseDetails(array $arr)
    {
        $boarderArr = explode(',', $arr['boarderId']);
        $itemArr = explode(',', $arr['itemHeadCode']);

        $query = DB::table('invoice_detail')
            ->selectRaw("
                SUM(invoice_detail.amount) as total_income,
                SUM(invoice_detail.quantity) as total_quantity,
                SUM(invoice_detail.amount) / SUM(invoice_detail.quantity) as average_unit_price,
                COUNT(invoice_detail.id) as total_tran,
                invoice_summary.boarder,
                invoice_detail.adjust,
                boarder.boarder_name,
                item_heads.item_head as item_head_name,
                item_heads.unit_name,
                invoice_detail.item_head,
                item_heads.item_category,
                item_categories.category_name
            ")
            ->join('invoice_summary', 'invoice_summary.invoice_no', '=', 'invoice_detail.invoice_no')
            ->join('boarder', 'boarder.boarder_id', '=', 'invoice_summary.boarder')
            ->join('item_heads', 'item_heads.item_head_code', '=', 'invoice_detail.item_head')
            ->join('item_categories', 'item_categories.category_code', '=', 'item_heads.item_category');

        // Date Filter
        if ($arr['dateType'] === 'invoiceDate') {
            $query->whereDate('invoice_summary.invoice_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_date', '<=', $arr['toDate']);
        } elseif ($arr['dateType'] === 'dueDate') {
            $query->whereDate('invoice_summary.invoice_due_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_due_date', '<=', $arr['toDate']);
        }

        // Payment Status Filter
        if ($arr['status'] == config('constants.PAID') || $arr['status'] == config('constants.UNPAID')) {
            $query->where('invoice_summary.is_paid', $arr['status']);
        }

        return $query
            ->whereIn('invoice_summary.boarder', $boarderArr)
            ->whereIn('invoice_detail.item_head', $itemArr)
            ->groupBy(
                'invoice_summary.boarder',
                'invoice_detail.item_head',
                'invoice_detail.adjust',
                'boarder.boarder_name',
                'item_heads.item_head',
                'item_heads.unit_name',
                'item_heads.item_category',
                'item_categories.category_name'
            )
            ->orderBy('invoice_summary.boarder', 'ASC')
            ->orderBy('item_categories.category_name', 'ASC')
            ->orderBy('invoice_detail.item_head', 'ASC')
            ->get();
    }

    public function getIncomeCategoryBoarderDetails(array $arr)
    {
        $boarderArr = explode(',', $arr['boarderId']);
        $itemArr = explode(',', $arr['itemHeadCode']);

        $query = DB::table('invoice_detail')
            ->selectRaw("
                SUM(invoice_detail.amount) as total_income,
                item_heads.item_category,
                item_categories.category_name
            ")
            ->join('invoice_summary', 'invoice_summary.invoice_no', '=', 'invoice_detail.invoice_no')
            ->join('boarder', 'boarder.boarder_id', '=', 'invoice_summary.boarder')
            ->join('item_heads', 'item_heads.item_head_code', '=', 'invoice_detail.item_head')
            ->join('item_categories', 'item_categories.category_code', '=', 'item_heads.item_category');

        // Date Filter
        if ($arr['dateType'] === 'invoiceDate') {
            $query->whereDate('invoice_summary.invoice_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_date', '<=', $arr['toDate']);
        } elseif ($arr['dateType'] === 'dueDate') {
            $query->whereDate('invoice_summary.invoice_due_date', '>=', $arr['fromDate'])
                ->whereDate('invoice_summary.invoice_due_date', '<=', $arr['toDate']);
        }

        // Payment Status Filter
        if ($arr['status'] == config('constants.PAID') || $arr['status'] == config('constants.UNPAID')) {
            $query->where('invoice_summary.is_paid', $arr['status']);
        }

        return $query
            ->whereIn('invoice_summary.boarder', $boarderArr)
            ->whereIn('invoice_detail.item_head', $itemArr)
            ->groupBy(
                'item_categories.category_code',
                'item_heads.item_category',
                'item_categories.category_name'
            )
            ->orderBy('item_categories.category_name', 'ASC')
            ->get();
    }
}