<?php

namespace App\Repositories\Client;

use Illuminate\Support\Facades\DB;

class ReportRepository
{

    public function expenseReportDetails(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vehicleArr = array_filter(explode(',', $arr['vehicleIdStr']));
        $expenseArr = array_filter(explode(',', $arr['expenseHeadCode']));

        $query = DB::table('expense_detail')
            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,
                SUM(expense_detail.quantity) as total_quantity,

                ANY_VALUE(expense_detail.vehicle) as vehicle,
                ANY_VALUE(vehicles.registration_no) as registration_no,
                ANY_VALUE(vehicles.purchase_date) as purchase_date,
                ANY_VALUE(vehicles.purchase_cost) as purchase_cost,
                ANY_VALUE(vehicles.dpy) as dpy,

                ANY_VALUE(vehicles.purchase_cost - (
                    TIMESTAMPDIFF(MONTH, vehicles.purchase_date, CURRENT_DATE)
                    * (vehicles.purchase_cost * vehicles.dpy) / (100 * 12)
                )) as present_book_value,

                ANY_VALUE(vehicle_type_tb.element) as vehicle_type_name,
                ANY_VALUE(brand_tb.element) as brand_name,
                ANY_VALUE(brand_model_tb.element) as brand_model_name,

                ANY_VALUE(expense_detail.unit_name) as unit_name,
                ANY_VALUE(expense_detail.unit_price) as unit_price,
                ANY_VALUE(expense_detail.adjust) as adjust,

                SUM(expense_detail.amount) / NULLIF(SUM(expense_detail.quantity), 0) as average_unit_price,
                COUNT(expense_detail.id) as total_tran,

                ANY_VALUE(cost_heads.cost_head) as expense_head_name,
                ANY_VALUE(expense_detail.expense_head) as expense_head,
                ANY_VALUE(cost_heads.cost_category) as cost_category,
                ANY_VALUE(cost_categories.category_name) as category_name,

                (MAX(expense_detail.odometer_mileage) - MIN(expense_detail.odometer_mileage)) as total_mileage
            ")
            ->join('expense_summary', 'expense_summary.expense_no', '=', 'expense_detail.expense_no')
            ->join('vehicles', 'vehicles.vehicle_id', '=', 'expense_detail.vehicle')
            ->leftJoin('common_table as vehicle_type_tb', 'vehicle_type_tb.element_code', '=', 'vehicles.vehicle_type')
            ->leftJoin('common_table as brand_tb', 'brand_tb.element_code', '=', 'vehicles.brand')
            ->leftJoin('common_table as brand_model_tb', 'brand_model_tb.element_code', '=', 'vehicles.brand_model')
            ->join('cost_heads', 'cost_heads.cost_head_code', '=', 'expense_detail.expense_head')
            ->join('cost_categories', 'cost_categories.category_code', '=', 'cost_heads.cost_category')
            ->where('expense_summary.company', $company)
            ->whereDate('expense_summary.expense_date', '>=', $fromDate)
            ->whereDate('expense_summary.expense_date', '<=', $toDate);

        if ($arr['vehicleIdStr'] != "" && $arr['expenseHeadCode'] == "") {
            $query->whereIn('expense_detail.vehicle', $vehicleArr);
            $query->groupBy([
                'expense_detail.vehicle',
                'vehicles.registration_no',
                'vehicles.purchase_date',
                'vehicles.purchase_cost',
                'vehicles.dpy',
                'vehicle_type_tb.element',
                'brand_tb.element',
                'brand_model_tb.element'
            ]);
        } elseif ($arr['vehicleIdStr'] == "" && $arr['expenseHeadCode'] != "") {
            $query->whereIn('expense_detail.expense_head', $expenseArr);
            $query->groupBy([
                'expense_detail.expense_head',
                'cost_heads.cost_head',
                'cost_heads.cost_category',
                'cost_categories.category_name'
            ]);
            $query->orderBy('cost_categories.category_name', 'ASC')
                ->orderBy('expense_detail.expense_head', 'ASC');
        } elseif ($arr['vehicleIdStr'] != "" && $arr['expenseHeadCode'] != "") {
            $query->whereIn('expense_detail.vehicle', $vehicleArr);
            $query->whereIn('expense_detail.expense_head', $expenseArr);
            $query->groupBy([
                'expense_detail.vehicle',
                'expense_detail.expense_head',
                'vehicles.registration_no',
                'vehicles.purchase_date',
                'vehicles.purchase_cost',
                'vehicles.dpy',
                'vehicle_type_tb.element',
                'brand_tb.element',
                'brand_model_tb.element',
                'cost_heads.cost_head',
                'cost_heads.cost_category',
                'cost_categories.category_name'
            ]);
            $query->orderBy('expense_detail.vehicle', 'ASC')
                ->orderBy('cost_categories.category_name', 'ASC')
                ->orderBy('expense_detail.expense_head', 'ASC');
        }

        return $query->get();
    }

    public function getExpCategoryWiseDetails(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vehicleArr = explode(',', $arr['vehicleIdStr']);
        $expenseArr = explode(',', $arr['expenseHeadCode']);

        $query = DB::table('expense_detail')

            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,
                cost_heads.cost_category,
                cost_categories.category_name
            ")

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )

            ->join(
                'cost_heads',
                'cost_heads.cost_head_code',
                '=',
                'expense_detail.expense_head'
            )

            ->join(
                'cost_categories',
                'cost_categories.category_code',
                '=',
                'cost_heads.cost_category'
            )

            ->where(
                'expense_summary.company',
                $company
            )

            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )

            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            );

        /*
        | Vehicle Filter
        */
        if ($arr['flag'] == "headvehiclewise") {

            $query->whereIn(
                'expense_detail.vehicle',
                $vehicleArr
            );
        }

        /*
        | Expense Head Filter
        */
        $query->whereIn(
            'expense_detail.expense_head',
            $expenseArr
        );

        /*
        | Group & Order
        */
        $query->groupBy(
            'cost_categories.category_code',
            'cost_heads.cost_category',
            'cost_categories.category_name'
        );

        $query->orderBy(
            'cost_categories.category_name',
            'ASC'
        );

        return $query->get();
    }

    public function getExpVendorWiseDetails(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vendorArr = explode(',', $arr['vendorCode']);

        $query = DB::table('expense_summary')

            ->selectRaw("
                SUM(expense_summary.total_amount) as total_expense,
                corporate_vendor.title as vendor_title
            ")

            ->join(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )

            ->where(
                'expense_summary.company',
                $company
            )

            ->where(
                'corporate_vendor.company',
                $company
            )

            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )

            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            )

            ->whereIn(
                'expense_summary.vendor',
                $vendorArr
            )

            ->groupBy(
                'expense_summary.vendor',
                'corporate_vendor.title'
            )

            ->orderBy(
                'corporate_vendor.title',
                'ASC'
            );

        return $query->get();
    }

    public function getExpHeadVendorWiseDetails(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vendorArr  = explode(',', $arr['vendorCode']);
        $expenseArr = explode(',', $arr['expenseHeadCode']);

        $query = DB::table('expense_detail')

            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,

                SUM(expense_detail.quantity) as total_quantity,

                SUM(expense_detail.amount) /
                SUM(expense_detail.quantity)
                    as average_unit_price,

                COUNT(expense_detail.id) as total_tran,

                expense_summary.vendor,
                expense_detail.vehicle,
                expense_detail.adjust,

                corporate_vendor.title as vendor_title,

                cost_heads.cost_head as expense_head_name,
                cost_heads.unit_name,

                expense_detail.expense_head,

                cost_heads.cost_category,
                cost_categories.category_name
            ")

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )

            ->join(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )

            ->join(
                'cost_heads',
                'cost_heads.cost_head_code',
                '=',
                'expense_detail.expense_head'
            )

            ->join(
                'cost_categories',
                'cost_categories.category_code',
                '=',
                'cost_heads.cost_category'
            )

            ->where(
                'expense_summary.company',
                $company
            )

            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )

            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            )

            ->where(
                'cost_heads.company',
                $company
            )

            ->where(
                'corporate_vendor.company',
                $company
            )

            ->whereIn(
                'expense_summary.vendor',
                $vendorArr
            )

            ->whereIn(
                'expense_detail.expense_head',
                $expenseArr
            )

            ->groupBy(
                'expense_summary.vendor',
                'expense_detail.expense_head',

                'expense_detail.vehicle',
                'expense_detail.adjust',

                'corporate_vendor.title',

                'cost_heads.cost_head',
                'cost_heads.unit_name',

                'cost_heads.cost_category',
                'cost_categories.category_name'
            )

            ->orderBy(
                'expense_summary.vendor',
                'ASC'
            )

            ->orderBy(
                'cost_categories.category_name',
                'ASC'
            )

            ->orderBy(
                'expense_detail.expense_head',
                'ASC'
            );

        return $query->get();
    }


    public function getExpCategoryVendorDetails(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vendorArr  = explode(',', $arr['vendorCode']);
        $expenseArr = explode(',', $arr['expenseHeadCode']);

        $query = DB::table('expense_detail')

            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,
                cost_heads.cost_category,
                cost_categories.category_name
            ")

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )

            ->join(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )

            ->join(
                'cost_heads',
                'cost_heads.cost_head_code',
                '=',
                'expense_detail.expense_head'
            )

            ->join(
                'cost_categories',
                'cost_categories.category_code',
                '=',
                'cost_heads.cost_category'
            )

            ->where(
                'expense_summary.company',
                $company
            )

            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )

            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            )

            ->where(
                'corporate_vendor.company',
                $company
            )

            ->whereIn(
                'expense_summary.vendor',
                $vendorArr
            )

            ->whereIn(
                'expense_detail.expense_head',
                $expenseArr
            )

            ->groupBy(
                'cost_categories.category_code',
                'cost_heads.cost_category',
                'cost_categories.category_name'
            )

            ->orderBy(
                'cost_categories.category_name',
                'ASC'
            );

        return $query->get();
    }

    public function getExpVehicleVendorWiseDetails(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vehicleArr = explode(',', $arr['vehicleIdStr']);
        $vendorArr  = explode(',', $arr['vendorCode']);

        $query = DB::table('expense_detail')

            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,

                expense_detail.vehicle,

                vehicles.registration_no,
                vehicles.purchase_date,
                vehicles.purchase_cost,
                vehicles.dpy,

                purchase_cost - (
                    TIMESTAMPDIFF(
                        MONTH,
                        purchase_date,
                        CURRENT_DATE
                    ) * (purchase_cost * dpy) / (100 * 12)
                ) as present_book_value,

                vehicle_type_tb.element as vehicle_type_name,
                brand_tb.element as brand_name,
                brand_model_tb.element as brand_model_name,

                expense_summary.vendor,
                corporate_vendor.title as vendor_title
            ")

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )

            ->join(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )

            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'expense_detail.vehicle'
            )

            ->leftJoin(
                'common_table as vehicle_type_tb',
                'vehicle_type_tb.element_code',
                '=',
                'vehicles.vehicle_type'
            )

            ->leftJoin(
                'common_table as brand_tb',
                'brand_tb.element_code',
                '=',
                'vehicles.brand'
            )

            ->leftJoin(
                'common_table as brand_model_tb',
                'brand_model_tb.element_code',
                '=',
                'vehicles.brand_model'
            )

            ->where(
                'expense_summary.company',
                $company
            )

            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )

            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            )

            ->where(
                'corporate_vendor.company',
                $company
            )

            ->whereIn(
                'expense_detail.vehicle',
                $vehicleArr
            )

            ->whereIn(
                'expense_summary.vendor',
                $vendorArr
            )

            ->groupBy(
                'expense_detail.vehicle',
                'expense_summary.vendor',

                'vehicles.registration_no',
                'vehicles.purchase_date',
                'vehicles.purchase_cost',
                'vehicles.dpy',

                'vehicle_type_tb.element',
                'brand_tb.element',
                'brand_model_tb.element',

                'corporate_vendor.title'
            )

            ->orderBy(
                'expense_detail.vehicle',
                'ASC'
            )

            ->orderBy(
                'corporate_vendor.title',
                'ASC'
            );

        return $query->get();
    }

    public function getExpVehicleVendorWiseSummary(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vehicleArr = explode(',', $arr['vehicleIdStr']);
        $vendorArr  = explode(',', $arr['vendorCode']);

        $query = DB::table('expense_detail')

            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,

                expense_summary.vendor,

                corporate_vendor.title as vendor_title
            ")

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )

            ->join(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )

            ->where(
                'expense_summary.company',
                $company
            )

            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )

            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            )

            ->where(
                'corporate_vendor.company',
                $company
            )

            ->whereIn(
                'expense_detail.vehicle',
                $vehicleArr
            )

            ->whereIn(
                'expense_summary.vendor',
                $vendorArr
            )

            ->groupBy(
                'expense_summary.vendor',
                'corporate_vendor.title'
            )

            ->orderBy(
                'corporate_vendor.title',
                'ASC'
            );

        return $query->get();
    }

    public function getHeadVendorVehicleWiseDetails(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vendorArr  = explode(',', $arr['vendorCode']);
        $expenseArr = explode(',', $arr['expenseHeadCode']);
        $vehicleArr = explode(',', $arr['vehicleIdStr']);

        $query = DB::table('expense_detail')

            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,

                SUM(expense_detail.quantity) as total_quantity,

                SUM(expense_detail.amount) /
                SUM(expense_detail.quantity)
                    as average_unit_price,

                COUNT(expense_detail.id) as total_tran,

                expense_summary.vendor,
                expense_detail.vehicle,
                expense_detail.adjust,

                vehicles.registration_no,
                vehicles.purchase_date,
                vehicles.purchase_cost,
                vehicles.dpy,

                purchase_cost - (
                    TIMESTAMPDIFF(
                        MONTH,
                        purchase_date,
                        CURRENT_DATE
                    ) * (purchase_cost * dpy) / (100 * 12)
                ) as present_book_value,

                vehicle_type_tb.element as vehicle_type_name,
                brand_tb.element as brand_name,
                brand_model_tb.element as brand_model_name,

                corporate_vendor.title as vendor_title,

                cost_heads.cost_head as expense_head_name,
                cost_heads.unit_name,

                expense_detail.expense_head,

                cost_heads.cost_category,
                cost_categories.category_name
            ")

            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'expense_detail.vehicle'
            )

            ->leftJoin(
                'common_table as vehicle_type_tb',
                'vehicle_type_tb.element_code',
                '=',
                'vehicles.vehicle_type'
            )

            ->leftJoin(
                'common_table as brand_tb',
                'brand_tb.element_code',
                '=',
                'vehicles.brand'
            )

            ->leftJoin(
                'common_table as brand_model_tb',
                'brand_model_tb.element_code',
                '=',
                'vehicles.brand_model'
            )

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )

            ->join(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )

            ->join(
                'cost_heads',
                'cost_heads.cost_head_code',
                '=',
                'expense_detail.expense_head'
            )

            ->join(
                'cost_categories',
                'cost_categories.category_code',
                '=',
                'cost_heads.cost_category'
            )

            ->where(
                'expense_summary.company',
                $company
            )

            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )

            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            )

            ->where(
                'cost_heads.company',
                $company
            )

            ->where(
                'corporate_vendor.company',
                $company
            )

            ->where(
                'vehicles.company',
                $company
            )

            ->whereIn(
                'expense_summary.vendor',
                $vendorArr
            )

            ->whereIn(
                'expense_detail.expense_head',
                $expenseArr
            )

            ->whereIn(
                'expense_detail.vehicle',
                $vehicleArr
            );

        /*
        | Vehicle Wise Report
        */
        if ($arr['reportType'] == 'vehicleWise') {

            $query->groupBy(
                'expense_detail.vehicle',
                'expense_summary.vendor',
                'expense_detail.expense_head',

                'expense_detail.adjust',

                'vehicles.registration_no',
                'vehicles.purchase_date',
                'vehicles.purchase_cost',
                'vehicles.dpy',

                'vehicle_type_tb.element',
                'brand_tb.element',
                'brand_model_tb.element',

                'corporate_vendor.title',

                'cost_heads.cost_head',
                'cost_heads.unit_name',

                'cost_heads.cost_category',
                'cost_categories.category_name'
            );

            $query->orderBy(
                'expense_detail.vehicle',
                'ASC'
            );

            $query->orderBy(
                'corporate_vendor.title',
                'ASC'
            );

            $query->orderBy(
                'cost_categories.category_name',
                'ASC'
            );

            $query->orderBy(
                'expense_detail.expense_head',
                'ASC'
            );
        }

        /*
        | Vendor Wise Report
        */
        else if ($arr['reportType'] == 'vendorWise') {

            $query->groupBy(
                'expense_summary.vendor',
                'expense_detail.vehicle',
                'expense_detail.expense_head',

                'expense_detail.adjust',

                'vehicles.registration_no',
                'vehicles.purchase_date',
                'vehicles.purchase_cost',
                'vehicles.dpy',

                'vehicle_type_tb.element',
                'brand_tb.element',
                'brand_model_tb.element',

                'corporate_vendor.title',

                'cost_heads.cost_head',
                'cost_heads.unit_name',

                'cost_heads.cost_category',
                'cost_categories.category_name'
            );

            $query->orderBy(
                'corporate_vendor.title',
                'ASC'
            );

            $query->orderBy(
                'expense_detail.vehicle',
                'ASC'
            );

            $query->orderBy(
                'cost_categories.category_name',
                'ASC'
            );

            $query->orderBy(
                'expense_detail.expense_head',
                'ASC'
            );
        }

        return $query->get();
    }

    public function getHeadVendorVehicleWiseSummary(array $arr)
    {
        $fromDate = $arr['fromDate'];
        $toDate   = $arr['toDate'];
        $company  = $arr['company'];

        $vendorArr  = explode(',', $arr['vendorCode']);
        $expenseArr = explode(',', $arr['expenseHeadCode']);
        $vehicleArr = explode(',', $arr['vehicleIdStr']);

        $query = DB::table('expense_detail')

            ->selectRaw("
                SUM(expense_detail.amount) as total_expense,

                SUM(expense_detail.quantity) as total_quantity,

                SUM(expense_detail.amount) /
                SUM(expense_detail.quantity)
                    as average_unit_price,

                COUNT(expense_detail.id) as total_tran,

                expense_summary.vendor,
                expense_detail.vehicle,
                expense_detail.adjust,

                vehicles.registration_no,

                corporate_vendor.title as vendor_title,

                cost_heads.cost_head as expense_head_name,
                cost_heads.unit_name,

                expense_detail.expense_head,

                cost_heads.cost_category,
                cost_categories.category_name
            ")

            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'expense_detail.vehicle'
            )

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )

            ->join(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )

            ->join(
                'cost_heads',
                'cost_heads.cost_head_code',
                '=',
                'expense_detail.expense_head'
            )

            ->join(
                'cost_categories',
                'cost_categories.category_code',
                '=',
                'cost_heads.cost_category'
            )

            ->where(
                'expense_summary.company',
                $company
            )

            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )

            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            )

            ->where(
                'cost_heads.company',
                $company
            )

            ->where(
                'corporate_vendor.company',
                $company
            )

            ->where(
                'vehicles.company',
                $company
            )

            ->whereIn(
                'expense_summary.vendor',
                $vendorArr
            )

            ->whereIn(
                'expense_detail.expense_head',
                $expenseArr
            )

            ->whereIn(
                'expense_detail.vehicle',
                $vehicleArr
            );

        /*
        | Vehicle Wise Summary
        */
        if ($arr['reportType'] == 'vehicleWise') {

            $query->groupBy(
                'expense_detail.vehicle',

                'expense_summary.vendor',
                'expense_detail.adjust',

                'vehicles.registration_no',

                'corporate_vendor.title',

                'cost_heads.cost_head',
                'cost_heads.unit_name',

                'expense_detail.expense_head',

                'cost_heads.cost_category',
                'cost_categories.category_name'
            );

            $query->orderBy(
                'expense_detail.vehicle',
                'ASC'
            );
        }

        /*
        | Vendor Wise Summary
        */
        else if ($arr['reportType'] == 'vendorWise') {

            $query->groupBy(
                'expense_summary.vendor',

                'expense_detail.vehicle',
                'expense_detail.adjust',

                'vehicles.registration_no',

                'corporate_vendor.title',

                'cost_heads.cost_head',
                'cost_heads.unit_name',

                'expense_detail.expense_head',

                'cost_heads.cost_category',
                'cost_categories.category_name'
            );

            $query->orderBy(
                'corporate_vendor.title',
                'ASC'
            );
        }

        return $query->get();
    }

    public function getVehicleLastProduct(array $arr)
    {
        $tempInsertResults = DB::table('stock_summary')
            ->selectRaw("
                stock_summary.stock_date,
                stock_details.variant,
                (
                    SUM(stock_details.debit_quantity)
                    -
                    SUM(stock_details.credit_quantity)
                ) as quantity
            ")
            ->join(
                'stock_details',
                'stock_details.stock_summary_id',
                '=',
                'stock_summary.stock_summary_id'
            )
            ->where('stock_summary.company', $arr['company'])
            ->where('stock_details.vehicle', $arr['vehicle'])
            ->where('stock_summary.stock_type', 'stock_out')
            ->where('stock_summary.is_active', 1)
            ->groupBy([
                'stock_summary.stock_date',
                'stock_details.variant'
            ])
            ->orderBy('stock_summary.stock_date', 'DESC')
            ->get()
            ->toArray();

        if ($tempInsertResults) {

            $stockTempTable = 'product_temp' . reference_no();

            // FIXED: Added explicit CHARACTER SET and COLLATE to match your product_variants table
            DB::statement("
                CREATE TEMPORARY TABLE IF NOT EXISTS `$stockTempTable` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `variant` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
                    `quantity` decimal(10,2) NOT NULL DEFAULT '0.00',
                    `stock_date` date NOT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");

            $insertData = [];

            foreach ($tempInsertResults as $row) {
                $insertData[] = [
                    'variant'    => $row->variant,
                    'quantity'   => $row->quantity,
                    'stock_date' => $row->stock_date,
                ];
            }

            DB::table($stockTempTable)->insert($insertData);

            return DB::table($stockTempTable)
                ->selectRaw("
                    MAX($stockTempTable.stock_date) as stock_date,
                    $stockTempTable.variant,
                    $stockTempTable.quantity,

                    product_variants.unit_name,
                    product_variants.variant_name,

                    products.product_name,

                    product_categories.category_name
                ")
                ->join(
                    'product_variants',
                    'product_variants.variant_code',
                    '=',
                    DB::raw("$stockTempTable.variant")
                )
                ->join(
                    'products',
                    'products.product_code',
                    '=',
                    'product_variants.product'
                )
                ->join(
                    'product_categories',
                    'product_categories.category_code',
                    '=',
                    'products.category'
                )
                ->groupBy([
                    "$stockTempTable.variant",
                    "$stockTempTable.quantity",
                    'product_variants.unit_name',
                    'product_variants.variant_name',
                    'products.product_name',
                    'product_categories.category_name'
                ])
                ->get();
        }

        return collect([]);
    }

    public function getStockInProductDetails(array $arr): array
    {
        return DB::table('stock_summary')
            ->select(
                DB::raw('ANY_VALUE(stock_summary.stock_summary_id) as stock_summary_id'),
                DB::raw('ANY_VALUE(stock_summary.stock_date) as stock_date'),
                
                DB::raw('SUM(stock_details.credit_quantity) as credit_quantity'),
                DB::raw('SUM(stock_details.debit_quantity) as debit_quantity'),
                
                'stock_details.variant',
                
                DB::raw('ANY_VALUE(product_variants.variant_name) as variant_name'),
                DB::raw('ANY_VALUE(product_variants.unit_name) as unit_name'),
                DB::raw('ANY_VALUE(products.product_name) as product_name'),
                DB::raw('ANY_VALUE(product_categories.category_name) as category_name')
            )
            ->join(
                'stock_details',
                'stock_details.stock_summary_id',
                '=',
                'stock_summary.stock_summary_id'
            )
            ->join(
                'product_variants',
                'product_variants.variant_code',
                '=',
                'stock_details.variant'
            )
            ->join(
                'products',
                'products.product_code',
                '=',
                'product_variants.product'
            )
            ->join(
                'product_categories',
                'product_categories.category_code',
                '=',
                'products.category'
            )
            ->where('stock_summary.is_active', 1)
            ->where('stock_summary.stock_type', 'stock_in')
            ->whereDate(
                'stock_summary.stock_date',
                '>=',
                $arr['fromDate']
            )
            ->whereDate(
                'stock_summary.stock_date',
                '<=',
                $arr['toDate']
            )
            ->where(
                'stock_summary.company',
                $arr['company']
            )
            ->whereIn(
                'stock_details.variant',
                explode(',', $arr['variantStr'])
            )
            ->groupBy('stock_details.variant')
            ->orderBy('stock_details.variant')
            ->get()
            ->toArray();
    }

    public function stockOutReportDetails(array $arr): array
    {
        $query = DB::table('stock_summary')
            ->select(
                DB::raw('ANY_VALUE(stock_summary.stock_summary_id) as stock_summary_id'),
                DB::raw('ANY_VALUE(stock_summary.stock_date) as stock_date'),

                DB::raw('SUM(stock_details.credit_quantity) as credit_quantity'),
                DB::raw('SUM(stock_details.debit_quantity) as debit_quantity'),

                DB::raw('ANY_VALUE(vehicles.registration_no) as registration_no'),
                DB::raw('ANY_VALUE(brand_tb.element) as brand_name'),
                DB::raw('ANY_VALUE(brand_model_tb.element) as brand_model_name'),

                'stock_details.vehicle',
                'stock_details.variant',
                DB::raw('ANY_VALUE(stock_details.id) as id'),

                DB::raw('ANY_VALUE(product_variants.variant_name) as variant_name'),
                DB::raw('ANY_VALUE(product_variants.unit_name) as unit_name'),

                DB::raw('ANY_VALUE(products.product_name) as product_name'),

                DB::raw('ANY_VALUE(product_categories.category_name) as category_name')
            )
            ->join(
                'stock_details',
                'stock_details.stock_summary_id',
                '=',
                'stock_summary.stock_summary_id'
            )
            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'stock_details.vehicle'
            )
            ->leftJoin(
                'common_table as brand_tb',
                'brand_tb.element_code',
                '=',
                'vehicles.brand'
            )
            ->leftJoin(
                'common_table as brand_model_tb',
                'brand_model_tb.element_code',
                '=',
                'vehicles.brand_model'
            )
            ->join(
                'product_variants',
                'product_variants.variant_code',
                '=',
                'stock_details.variant'
            )
            ->join(
                'products',
                'products.product_code',
                '=',
                'product_variants.product'
            )
            ->join(
                'product_categories',
                'product_categories.category_code',
                '=',
                'products.category'
            )
            ->where('stock_summary.is_active', 1)
            ->where(
                'stock_summary.company',
                $arr['company']
            )
            ->where(
                'stock_summary.stock_type',
                'stock_out'
            )
            ->whereDate(
                'stock_summary.stock_date',
                '>=',
                $arr['fromDate']
            )
            ->whereDate(
                'stock_summary.stock_date',
                '<=',
                $arr['toDate']
            )
            ->whereIn(
                'stock_details.variant',
                explode(',', $arr['variantStr'])
            );

        /*
        | Vehicle Wise Report
        */
        if (
            $arr['vehicleIdStr'] &&
            $arr['reportGroup'] == 'vehicleWise'
        ) {

            if ($arr['dateFlag']) {

                $query->whereIn(
                    'stock_details.vehicle',
                    explode(',', $arr['vehicleIdStr'])
                );

                $query->groupBy('stock_summary.stock_date');
                $query->groupBy('stock_details.vehicle');
                $query->groupBy('stock_details.variant');

                $query->orderBy('stock_summary.stock_date', 'ASC');
                $query->orderBy('stock_details.vehicle', 'ASC');
                $query->orderBy('stock_details.variant', 'ASC');

            } else {

                $query->whereIn(
                    'stock_details.vehicle',
                    explode(',', $arr['vehicleIdStr'])
                );

                $query->groupBy('stock_details.vehicle');
                $query->groupBy('stock_details.variant');

                $query->orderBy('stock_details.vehicle', 'ASC');
                $query->orderBy('stock_details.variant', 'ASC');
            }

        }

        /*
        | Product Wise Report
        */
        else if (
            $arr['vehicleIdStr'] &&
            $arr['reportGroup'] == 'productWise'
        ) {

            if ($arr['dateFlag']) {

                $query->whereIn(
                    'stock_details.vehicle',
                    explode(',', $arr['vehicleIdStr'])
                );

                $query->groupBy('stock_summary.stock_date');
                $query->groupBy('stock_details.variant');
                $query->groupBy('stock_details.vehicle');

                $query->orderBy('stock_summary.stock_date', 'ASC');
                $query->orderBy('stock_details.variant', 'ASC');
                $query->orderBy('stock_details.vehicle', 'ASC');

            } else {

                $query->whereIn(
                    'stock_details.vehicle',
                    explode(',', $arr['vehicleIdStr'])
                );

                $query->groupBy('stock_details.variant');
                $query->groupBy('stock_details.vehicle');

                $query->orderBy('stock_details.variant', 'ASC');
                $query->orderBy('stock_details.vehicle', 'ASC');
            }

        }

        /*
        | Summary Report
        */
        else if (!$arr['vehicleIdStr']) {

            $query->groupBy('stock_details.variant');
            $query->orderBy('stock_details.variant', 'ASC');
        }

        return $query
            ->get()
            ->toArray();
    }

    public function getExpenseHistoryData($fromDate, $toDate, $start, $end, $company): array
    {
        return DB::table('expense_detail')

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )

            ->leftJoin(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'expense_detail.vehicle'
            )

            ->join(
                'cost_heads',
                'cost_heads.cost_head_code',
                '=',
                'expense_detail.expense_head'
            )

            ->leftJoin(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )

            ->where('expense_summary.company', $company)

            ->whereDate('expense_summary.expense_date', '>=', $fromDate)

            ->whereDate('expense_summary.expense_date', '<=', $toDate)

            ->orderBy('expense_detail.expense_no', 'ASC')

            ->orderBy('expense_summary.expense_date', 'ASC')

            ->offset($start)

            ->limit($end)

            ->get()
            ->toArray();
    }

    public function getExpenseDetailsHistoryData(
        $fromDate,
        $toDate,
        $start,
        $end,
        $company,
        array $arr
    ): array {

        return DB::table('expense_detail')

            ->select(
                'expense_detail.*',
                'expense_summary.expense_title',
                'expense_summary.expense_date',
                DB::raw('expense_summary.guest_name as guest_vendor_title'),
                'vehicles.registration_no',
                DB::raw('cost_heads.cost_head as expense_title'),
                DB::raw('corporate_vendor.title as vendor_title')
            )

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )
            ->leftJoin(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'expense_detail.vehicle'
            )
            ->join(
                'cost_heads',
                'cost_heads.cost_head_code',
                '=',
                'expense_detail.expense_head'
            )
            ->leftJoin(
                'corporate_vendor',
                'corporate_vendor.vendor_code',
                '=',
                'expense_summary.vendor'
            )
            ->where(
                'expense_summary.company',
                $company
            )
            ->whereDate(
                'expense_summary.expense_date',
                '>=',
                $fromDate
            )
            ->whereDate(
                'expense_summary.expense_date',
                '<=',
                $toDate
            )
            ->when($arr['vehicleStr'], function ($query) use ($arr) {

                $query->whereIn(
                    'expense_detail.vehicle',
                    explode(',', $arr['vehicleStr'])
                );

            })
            ->when($arr['expenseHeadCode'], function ($query) use ($arr) {

                $query->whereIn(
                    'expense_detail.expense_head',
                    explode(',', $arr['expenseHeadCode'])
                );

            })
            ->when($arr['vendorCode'], function ($query) use ($arr) {

                $query->whereIn(
                    'expense_summary.vendor',
                    explode(',', $arr['vendorCode'])
                );

            })
            ->orderBy('expense_detail.expense_no', 'ASC')
            ->orderBy('expense_summary.expense_date', 'ASC')
            ->offset($start)
            ->limit($end)
            ->get()
            ->toArray();
    }
}