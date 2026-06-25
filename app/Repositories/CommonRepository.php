<?php

namespace App\Repositories;

use App\Models\Client\Product;
use App\Models\Company;
use App\Models\CustomerEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommonRepository
{

    public function getCommonTableElement($commonTableElementArr)
    {
        return DB::table('common_table')
            ->when(isset($commonTableElementArr['type']), function ($q) use ($commonTableElementArr) {
                $q->where('type', $commonTableElementArr['type']);
            })
            ->when(isset($commonTableElementArr['depend_on_element']), function ($q) use ($commonTableElementArr) {
                $q->where('depend_on_element', $commonTableElementArr['depend_on_element']);
            })
            ->get();
    }

    // public function getCompanyList($isActiveFlag = 1, $companyType = null)
    // {
    //     $query = DB::table('corporate_companies')
    //         ->select(
    //             'corporate_companies.*',
    //             'employee.employee_name as rm_name',
    //             'package.package_name'
    //         )
    //         ->leftJoin('employee', 'employee.employee_id', '=', 'corporate_companies.rm_id')
    //         ->leftJoin('package', 'package.package_code', '=', 'corporate_companies.package');

    //     // ACTIVE FILTER (same logic)
    //     if ($isActiveFlag == 1) {
    //         $query->where('corporate_companies.is_active', 1);
    //     } elseif ($isActiveFlag == 2) {
    //         $query->where('corporate_companies.is_active', 0);
    //     }

    //     // COMPANY TYPE FILTER (same logic)
    //     if ($companyType) {
    //         $query->where('corporate_companies.company_type', $companyType);
    //     }

    //     return $query->get();
    // }
      
    // public function getIndividualAccList($isActiveFlag = 1, $companyType = null)
    // {
    //     $query = DB::table('corporate_companies')
    //         ->select(
    //             'corporate_companies.*',
    //             'employee.employee_name as rm_name',
    //             'package.package_name',
    //             'user_group.group_name as user_group_name'
    //         )
    //         ->leftJoin('employee', 'employee.employee_id', '=', 'corporate_companies.rm_id')
    //         ->leftJoin('package', 'package.package_code', '=', 'corporate_companies.package')
    //         ->join('customer_employee', 'customer_employee.company', '=', 'corporate_companies.company_code')
    //         ->join('users', 'users.user_id', '=', 'customer_employee.employee_id')
    //         ->join('user_group', 'user_group.id', '=', 'users.user_group');

    //     if ($isActiveFlag == 1) {
    //         $query->where('corporate_companies.is_active', 1);
    //     } elseif ($isActiveFlag == 2) {
    //         $query->where('corporate_companies.is_active', 0);
    //     }

    //     $query->where('corporate_companies.company_type', $companyType);

    //     return $query->get();
    // }

    // public function getCallReason($reasonCode = null, $isActiveFlag = 1)
    // {
    //     $query = DB::table('call_reason')
    //         ->select('call_reason.*', 'common_table.element as call_type_name')
    //         ->join('common_table', 'common_table.element_code', '=', 'call_reason.call_type');

    //     // filter by reasonCode
    //     if (!empty($reasonCode)) {
    //         $query->where('reason_code', $reasonCode);
    //     }

    //     // is_active logic (same as CI)
    //     if ($isActiveFlag == 1) {
    //         $query->where('call_reason.is_active', 1);
    //     } elseif ($isActiveFlag == 2) {
    //         $query->where('call_reason.is_active', 0);
    //     }

    //     // ordering
    //     $query->orderBy('call_reason.call_type')
    //         ->orderBy('call_reason.reason_order');

    //     return $query->get();
    // }

    // public function getCostHead($isActiveFlag = 1)
    // {
    //     return DB::table('cost_heads as ch')
    //         ->select('ch.*', 'cc.parent_category_str', 'cc.category_name')
    //         ->join('cost_categories as cc', 'cc.category_code', '=', 'ch.cost_category')

    //         ->where('cc.is_active', 1)

    //         ->when($isActiveFlag == 1, function ($q) {
    //             $q->where('ch.is_active', 1);
    //         })

    //         ->when($isActiveFlag == 2, function ($q) {
    //             $q->where('ch.is_active', 0);
    //         })

    //         ->when( Auth::user()->customerEmployee->customer_type == config('constants.INDIVIDUAL_CUST'), function ($q) {
    //             $q->where('cc.company', config('constants.INDIVIDUAL_EXP'));
    //         })

    //         ->when(Auth::user()->customerEmployee->customer_type  == config('constants.CORPORATE_CUST'), function ($q) {
    //             $q->where('cc.company', Auth::user()->customerEmployee->company );
    //         })

    //         ->orderBy('cc.category_name', 'ASC')

    //         ->get();
    // }

    // public function getCostCategory($isActiveFlag = 1)
    // {
    //     $query = DB::table('cost_categories');

    //     if ($isActiveFlag == 1) {
    //         $query->where('is_active', 1);
    //     } elseif ($isActiveFlag == 2) {
    //         $query->where('is_active', 0);
    //     }

    //     if ( Auth::user()->customerEmployee->customer_type == config('constants.INDIVIDUAL_CUST')) {
    //         $query->where('company', config('constants.INDIVIDUAL_EXP'));
    //     } elseif (Auth::user()->customerEmployee->customer_type == config('constants.CORPORATE_CUST')) {
    //         $query->where('company', Auth::user()->customerEmployee->company);
    //     }

    //     return $query
    //         ->orderBy('parent_category_str', 'ASC')
    //         ->get();
    // }

    // public function getProductCategoryList($arr, $isActiveFlag = 1)
    // {
    //     $query = DB::table('product_categories');

    //     if ($isActiveFlag == 1) {

    //         $query->where('is_active', 1);

    //     } elseif ($isActiveFlag == 2) {

    //         $query->where('is_active', 0);
    //     }

    //     $query->where('category_type', $arr['categoryType']);

    //     $query->where('company', $arr['company']);
    //     $query->orderBy('parent_category_str', 'ASC');

    //     return $query->get();
    // }

    // public function getProduts($arr, $isActiveFlag = 1)
    // {
    //     $query = Product::select('products.*', 'product_categories.parent_category_str')
    //         ->join('product_categories', 'product_categories.category_code', '=', 'products.category')
    //         ->where('products.company', $arr['company'])
    //         ->where('products.product_type', $arr['productType'])
    //         ->where('product_categories.is_active', 1);

    //     if (!empty($arr['productCode'])) {
    //         $query->where('products.product_code', $arr['productCode']);
    //     }

    //     if ($isActiveFlag == 1) {
    //         $query->where('products.is_active', 1);
    //     } elseif ($isActiveFlag == 2) {
    //         $query->where('products.is_active', 0);
    //     }

    //     $query->orderBy('products.product_name', 'ASC');

    //     return $query->get();
    // }

    // public function getProductVariants($arr, $isActiveFlag = 1)
    // {
    //     $query = DB::table('product_variants')
    //         ->select(
    //             'product_variants.*',
    //             'products.product_name',
    //             'product_categories.parent_category_str',
    //             'product_categories.category_name'
    //         )
    //         ->join('products', 'products.product_code', '=', 'product_variants.product')
    //         ->join('product_categories', 'product_categories.category_code', '=', 'products.category')
    //         ->where('product_variants.company', $arr['company']);

    //     // Active filter (same logic)
    //     if ($isActiveFlag == 1) {
    //         $query->where('product_variants.is_active', 1);
    //     } elseif ($isActiveFlag == 2) {
    //         $query->where('product_variants.is_active', 0);
    //     }

    //     $query->where('products.is_active', 1)
    //         ->where('product_categories.is_active', 1)
    //         ->where('product_variants.variant_type', $arr['variantType'])
    //         ->orderBy('product_variants.product', 'ASC');

    //     return $query->get();
    // }

    // public function getWorkshopList($isActiveFlag = 1)
    // {
    //     $query = DB::table('workshops');

    //     if ($isActiveFlag == 1) {
    //         $query->where('is_active', 1);
    //     } elseif ($isActiveFlag == 2) {
    //         $query->where('is_active', 0);
    //     }

    //     return $query->get();
    // }

    public function getItemHead($isActiveFlag = 1)
    {
        $query = DB::table('item_heads')
            ->select(
                'item_heads.*',
                'item_categories.parent_category_str',
                'item_categories.category_name'
            )
            ->join(
                'item_categories',
                'item_categories.category_code',
                '=',
                'item_heads.item_category'
            )
            ->where('item_categories.is_active', 1);

        if ($isActiveFlag == 1) {
            $query->where('item_heads.is_active', 1);
        } elseif ($isActiveFlag == 2) {
            $query->where('item_heads.is_active', 0);
        }

        return $query
            ->orderBy('item_categories.category_name', 'asc')
            ->get()
            ->toArray();
    }

    public function getCostHead($isActiveFlag = 1)
    {
        $query = DB::table('cost_heads')
            ->select(
                'cost_heads.*',
                'cost_categories.parent_category_str',
                'cost_categories.category_name'
            )
            ->join(
                'cost_categories',
                'cost_categories.category_code',
                '=',
                'cost_heads.cost_category'
            )
            ->where('cost_categories.is_active', 1);

        if ($isActiveFlag == 1) {
            $query->where('cost_heads.is_active', 1);
        } elseif ($isActiveFlag == 2) {
            $query->where('cost_heads.is_active', 0);
        }

        return $query
            ->orderBy('cost_categories.category_name', 'asc')
            ->get();
    }

}