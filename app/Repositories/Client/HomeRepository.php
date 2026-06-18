<?php

namespace App\Repositories\Client;

use App\Models\Admin\MasterData\ServiceVariant;
use App\Models\Client\HomeServiceAppDetail;
use App\Models\Client\HomeServiceAppSummaryGen;
use App\Models\CorporateCompany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeRepository
{

    public function getSingleVehicleInfo(
        $vehicleId,
        $companyCode
    ) {

        $query = DB::table('vehicles')

            ->select(
                'vehicles.*',

                DB::raw('vehicle_type_tb.element as vehicle_type_name'),

                DB::raw('brand_tb.element as brand_name'),

                DB::raw('driver_tb.employee_name as driver_name'),

                DB::raw('driver_tb.primary_mobile as driver_mobile'),

                DB::raw('driver_tb.employee_id as driver_id'),

                DB::raw('brand_model_tb.element as brand_model_name'),

                DB::raw('vehicle_class_tb.element as vehicle_class_name'),

                DB::raw('vehicle_color_tb.element as color_name'),

                'driver_tb.driving_license_no',

                'driver_tb.driving_license_expiry_date'
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

            ->leftJoin(
                'common_table as vehicle_class_tb',
                'vehicle_class_tb.element_code',
                '=',
                'vehicles.vehicle_class'
            )

            ->leftJoin(
                'common_table as vehicle_color_tb',
                'vehicle_color_tb.element_code',
                '=',
                'vehicles.color'
            )

            ->leftJoin(
                'customer_employee as driver_tb',
                'driver_tb.employee_id',
                '=',
                'vehicles.driver_id'
            )

            ->where(
                'vehicles.vehicle_id',
                $vehicleId
            )

            ->where(
                'vehicles.company',
                $companyCode
            );

        /*
        | Existing Logic Preserved
        */
        if ($query->exists()) {
            return $query->first();
        }

        return $query->first();
    }

    public function getVehicleCostCategory(
        $companyCode,
        $year,
        $vehicleId
    ): array {

        return DB::table('expense_detail')

            ->select(
                DB::raw('SUM(expense_detail.amount) as total_expense'),

                'cost_heads.cost_category',

                'cost_categories.category_name'
            )
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
                'expense_detail.vehicle',
                $vehicleId
            )
            ->whereYear(
                'expense_summary.expense_date',
                $year
            )
            ->where(
                'expense_summary.company',
                $companyCode
            )
            ->groupBy(
                'cost_categories.category_code',
                'cost_heads.cost_category',
                'cost_categories.category_name'
            )
            ->orderBy(
                'cost_categories.category_name',
                'ASC'
            )

            ->get()
            ->toArray();
    }

    public function getVehicleCostMonth(
        $companyCode,
        $year,
        $vehicleId
    ): array {

        return DB::table('expense_detail')

            ->select(
                DB::raw('SUM(expense_detail.amount) as total_expense'),

                DB::raw('MONTH(expense_summary.expense_date) as month')
            )

            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )
            ->where(
                'expense_detail.vehicle',
                $vehicleId
            )
            ->whereYear(
                'expense_summary.expense_date',
                $year
            )
            ->where(
                'expense_summary.company',
                $companyCode
            )
            ->groupBy(
                DB::raw('MONTH(expense_summary.expense_date)')
            )
            ->orderBy(
                DB::raw('MONTH(expense_summary.expense_date)'),
                'ASC'
            )
            ->get()
            ->toArray();
    }

    public function getCostYear($companyCode): array
    {

        return DB::table('expense_summary')
            ->select(
                DB::raw('YEAR(expense_summary.expense_date) as year')
            )
            ->where(
                'expense_summary.company',
                $companyCode
            )
            ->distinct()
            ->orderBy(
                DB::raw('YEAR(expense_summary.expense_date)'),
                'DESC'
            )
            ->get()
            ->toArray();
    }

    public function getStatusInfo(): array
    {
        $result = [];

        $result['quotationRequests'] = DB::table('quotation_req_summary')
            ->selectRaw('COUNT(id) as status_count, status')
            ->where('status', '!=', config('constants.REQ_DRAFT_STATUS'))
            ->groupBy('status')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();

        $result['appointmentServices'] = DB::table('appointment_summary')
            ->selectRaw('COUNT(id) as status_count, status')
            ->groupBy('status')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();

        $result['homeServices'] = DB::table('home_service_app_summary_gen')
            ->selectRaw('COUNT(id) as status_count, status')
            ->groupBy('status')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();

        return $result;
    }

    public function getCostCategory(string $company, int $year): array
    {
        return DB::table('expense_detail')
            ->selectRaw('
                SUM(expense_detail.amount) as total_expense,
                cost_heads.cost_category,
                cost_categories.category_name
            ')
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
            ->whereYear('expense_summary.expense_date', $year)
            ->where('expense_summary.company', $company)
            ->groupBy('cost_categories.category_code')
            ->orderBy('cost_categories.category_name', 'ASC')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();
    }


    public function getCostMonth(string $companyCode, int $year): array
    {
        return DB::table('expense_detail')
            ->selectRaw('
                SUM(expense_detail.amount) as total_expense,
                MONTH(expense_summary.expense_date) as month
            ')
            ->join(
                'expense_summary',
                'expense_summary.expense_no',
                '=',
                'expense_detail.expense_no'
            )
            ->whereYear('expense_summary.expense_date', $year)
            ->where('expense_summary.company', $companyCode)
            ->groupByRaw('MONTH(expense_summary.expense_date)')
            ->orderByRaw('MONTH(expense_summary.expense_date) ASC')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();
    }

    public function getCompanyInfo(string $companyCode)
    {
        $companyInfo = DB::table('corporate_companies')
            ->select(
                'corporate_companies.rm_id',
                'employee.employee_name as rm_name',
                'employee.primary_mobile as rm_mobile',
                'employee.email as rm_email',
                'package.package_name',
                'corporate_companies.membership_card',
                'membership_card.valid_dt_tm'
            )
            ->leftJoin(
                'employee',
                'employee.employee_id',
                '=',
                'corporate_companies.rm_id'
            )
            ->leftJoin(
                'package',
                'package.package_code',
                '=',
                'corporate_companies.package'
            )
            ->leftJoin(
                'membership_card',
                'membership_card.card_number',
                '=',
                'corporate_companies.membership_card'
            )
            ->where('company_code', $companyCode)
            ->first();

        if (!$companyInfo) {
            return 0;
        }

        return $companyInfo;
    }

    public function getVehicleInfo(string $companyCode): array
    {
        return DB::table('vehicles')
            ->select([
                'vehicles.vehicle_id',
                'vehicles.registration_no',
                'vehicles.driver_id',
                'vehicles.pull_emp_name',
                'vehicles.assign_type',
                'brand_tb.element as brand_name',
                'brand_model_tb.element as brand_model_name',
                'vehicles.pull_designation',
                'customer_employee.employee_name as driver_name',
                'vehicles.tax_period_to_date',
                'vehicles.insurance_to_date',
                'vehicles.fitness_validity_todate',
                'vehicles.route_to_date',
            ])
            ->leftJoin(
                'customer_employee',
                'vehicles.driver_id',
                '=',
                'customer_employee.employee_id'
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
            ->where('vehicles.company', $companyCode)
            ->where('vehicles.is_active', 1)
            ->orderBy('vehicles.registration_no', 'ASC')
            ->get()
            ->toArray();
    }

    public function getTotalDriver(string $company): int
    {
        return (int) DB::table('customer_employee')
            ->where('company', $company)
            ->where('is_active', 1)
            ->where('emp_type', 'driver')
            ->count('id');
    }

    public function getDocVehicleInfo(
        string $companyCode,
        string $flag,
        string $fromDate,
        string $toDate
    ): array {
        
        $query = DB::table('vehicles')
            ->select([
                'vehicles.vehicle_id',
                'vehicles.registration_no',
                'vehicle_type_tb.element as vehicle_type_name',
                'vehicles.driver_id',
                'vehicles.pull_emp_name',
                'vehicles.assign_type',
                'brand_tb.element as brand_name',
                'brand_model_tb.element as brand_model_name',
                'vehicles.pull_designation',
                'customer_employee.employee_name as driver_name',
                'vehicles.tax_period_to_date',
                'vehicles.insurance_to_date',
                'vehicles.fitness_validity_todate',
                'vehicles.route_to_date',
            ])
            ->leftJoin(
                'customer_employee',
                'vehicles.driver_id',
                '=',
                'customer_employee.employee_id'
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
            ->leftJoin(
                'common_table as vehicle_type_tb',
                'vehicle_type_tb.element_code',
                '=',
                'vehicles.vehicle_type'
            )
            ->where('vehicles.company', $companyCode)
            ->where('vehicles.is_active', 1);

        if ($flag === 'fitness') {

            $query->where('vehicles.fitness_validity_todate', '>=', $fromDate)
                ->where('vehicles.fitness_validity_todate', '<=', $toDate)
                ->orderBy('vehicles.fitness_validity_todate', 'ASC');

        } elseif ($flag === 'taxToken') {

            $query->where('vehicles.tax_period_to_date', '>=', $fromDate)
                ->where('vehicles.tax_period_to_date', '<=', $toDate)
                ->orderBy('vehicles.tax_period_to_date', 'ASC');

        } elseif ($flag === 'insurance') {

            $query->where('vehicles.insurance_to_date', '>=', $fromDate)
                ->where('vehicles.insurance_to_date', '<=', $toDate)
                ->orderBy('vehicles.insurance_to_date', 'ASC');

        } elseif ($flag === 'routePermit') {

            $query->where('vehicles.route_to_date', '>=', $fromDate)
                ->where('vehicles.route_to_date', '<=', $toDate)
                ->orderBy('vehicles.route_to_date', 'ASC');
        }

        $vehicles = $query
            ->orderBy('vehicles.registration_no', 'ASC')
            ->get();

        return $vehicles
            ->map(fn ($vehicle) => (array) $vehicle)
            ->toArray();
    }

    public function getDocVehicleCount(
        string $companyCode,
        string $fromDate,
        string $toDate
    ): array {

        $response = [];

        // -------- Fitness Count -------- //
        $response['fitnessCount'] = DB::table('vehicles')
            ->where('company', $companyCode)
            ->where('is_active', 1)
            ->where('fitness_validity_todate', '>=', $fromDate)
            ->where('fitness_validity_todate', '<=', $toDate)
            ->count('id');

        // -------- Tax Token Count -------- //
        $response['taxTokenCount'] = DB::table('vehicles')
            ->where('company', $companyCode)
            ->where('is_active', 1)
            ->where('tax_period_to_date', '>=', $fromDate)
            ->where('tax_period_to_date', '<=', $toDate)
            ->count('id');

        // -------- Insurance Count -------- //
        $response['insuranceCount'] = DB::table('vehicles')
            ->where('company', $companyCode)
            ->where('is_active', 1)
            ->where('insurance_to_date', '>=', $fromDate)
            ->where('insurance_to_date', '<=', $toDate)
            ->count('id');

        // -------- Route Permit Count -------- //
        $response['routePermitCount'] = DB::table('vehicles')
            ->where('company', $companyCode)
            ->where('is_active', 1)
            ->where('route_to_date', '>=', $fromDate)
            ->where('route_to_date', '<=', $toDate)
            ->count('id');

        return $response;
    }

}