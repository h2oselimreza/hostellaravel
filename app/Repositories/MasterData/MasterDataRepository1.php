<?php

namespace App\Repositories\MasterData;

use App\Models\Admin\MasterData\CostHead;
use App\Models\Admin\MasterData\Service;
use App\Models\Client\Product;
use App\Models\Client\ProductCategory;
use App\Models\Client\ProductVariant;
use App\Models\CommonTable;
use App\Models\MetaData\District;
use App\Models\MetaData\Division;
use App\Models\MetaData\Upozilla;
use App\Services\Client\GenerateMonthlyToken;
use App\Services\TokenService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MasterDataRepository1
{
    public function getVendorGeneralInfo(array $arr)
    {
        return DB::table('corporate_vendor as cv')
            ->select(
                'cv.*',
                'd.division_en_name',
                'd.division_bn_name',
                'dt.district_en_name',
                'dt.district_bn_name',
                'u.upozilla_en_name',
                'u.upozilla_bn_name'
            )

            ->leftJoin('divisions as d', 'd.id', '=', 'cv.division')
            ->leftJoin('districts as dt', 'dt.id', '=', 'cv.district')
            ->leftJoin('upazilas as u', 'u.id', '=', 'cv.upozilla')

            ->when(isset($arr['bulkFlag']) && $arr['bulkFlag'] == 0, function ($q) use ($arr) {
                $q->where('cv.vendor_code', $arr['vendorCode']);
            })

            ->where('cv.company', $arr['companyCode'])

            ->get();
    }

    public function getVendorList($isActiveFlag = 1, $companyCode)
    {
        $query = DB::table('corporate_vendor');

        if ($isActiveFlag == 1) {
            $query->where('is_active', 1);
        } elseif ($isActiveFlag == 2) {
            $query->where('is_active', 0);
        }
        $query->where('company', $companyCode);

        return $query->get();

    }

    public function deleteFile($fileId)
    {
        $imageIds = explode(',', $fileId);
        $results = DB::table('corporate_vendor_file')
            ->select('file_name')
            ->whereIn('id', $imageIds)
            ->get();

        foreach ($results as $result) {

            $file = public_path('assets/client/files/vendor/' . $result->file_name);

            if (File::exists($file)) {
                File::delete($file);
            }
        }

        DB::table('corporate_vendor_file')
            ->whereIn('id', $imageIds)
            ->delete();

        return 1;
    }

    public function insertExpenseCategory(array $insertArr, $tokenService): int
    {
        $exists = DB::table('cost_categories')
            ->where('company', $insertArr['company'])
            ->where('category_name', $insertArr['category_name'])
            ->exists();

        if ($exists) {
            return 2; // duplicate entry
        }

        $insertArr['category_code'] = config('constants.COST_CTG_CODE') . $tokenService->getTokenByCode(config('constants.COST_CTG_CODE'));

        // Parent category string (unchanged logic)
        $insertArr['parent_category_str'] = $this->getParentCostCategoryStr($insertArr);

        // Insert record safely
        DB::table('cost_categories')->insert($insertArr);

        return 1;
    }

    public function getParentCostCategoryStr(array $arr): string
    {
        if ($arr['parent_category'] == 1) {
            return $arr['category_code'];
        }

        $row = DB::table('cost_categories')
            ->where('company', $arr['company'])
            ->where('category_code', $arr['parent_category'])
            ->first();

        $strArr = [];

        if ($row) {
            $strArr[] = $row->parent_category_str;
        }

        $strArr[] = $arr['category_code'];

        return implode(' / ', $strArr);
    }

    public function editExpenseCategory(array $updateArr, int $categoryId): int
    {
        // Check duplicate entry
        $exists = DB::table('cost_categories')
            ->where('company', $updateArr['company'])
            ->where('category_name', $updateArr['category_name'])
            ->where('id', '!=', $categoryId)
            ->exists();

        if ($exists) {
            return 2; // duplicate entry
        }

        // Generate parent category string
        $updateArr['parent_category_str'] = $this->getParentCostCategoryStr($updateArr);

        // Get existing category info
        $row = DB::table('cost_categories')
            ->where('company', $updateArr['company'])
            ->where('category_code', $updateArr['category_code'])
            ->first();

        // Check parent category changed
        if ($row && $updateArr['parent_category'] != $row->parent_category) {

            $categoryBatchUpdateArr = [];

            $results = DB::table('cost_categories')
                ->where('category_code', '!=', $updateArr['category_code'])
                ->where('parent_category_str', 'LIKE', '%' . $row->parent_category_str . '%')
                ->get();

            foreach ($results as $result) {

                $arr = [];

                $arr['id'] = $result->id;

                $arr['parent_category_str'] =
                    $updateArr['parent_category_str'] .
                    str_replace(
                        $row->parent_category_str,
                        '',
                        $result->parent_category_str
                    );

                $categoryBatchUpdateArr[] = $arr;
            }

            // Batch update
            if (!empty($categoryBatchUpdateArr)) {

                foreach ($categoryBatchUpdateArr as $batchData) {

                    DB::table('cost_categories')
                        ->where('id', $batchData['id'])
                        ->update([
                            'parent_category_str' => $batchData['parent_category_str']
                        ]);
                }
            }
        }

        // Update main category
        DB::table('cost_categories')
            ->where('id', $categoryId)
            ->where('company', $updateArr['company'])
            ->update($updateArr);

        return 1;
    }

    public function removeExpenseCategory(int $categoryId, $company): int
    {
        // Get category information
        $row = DB::table('cost_categories')
            ->where('id', $categoryId)
            ->where('company', $company)
            ->first();

        // Category not found
        if (!$row) {
            return 4;
        }

        $categoryCode = $row->category_code;
        $categoryId   = $row->id;

        // Check child category exists
        $childCategoryExists = DB::table('cost_categories')
            ->where('parent_category', $categoryCode)
            ->where('is_active', 1)
            ->where('company', $company)
            ->exists();

        if ($childCategoryExists) {
            return 2; // this category has child category
        }

        // Check cost head exists
        $costHeadExists = DB::table('cost_heads')
            ->where('cost_category', $categoryCode)
            ->where('is_active', 1)
            ->where('company', $company)
            ->exists();

        if ($costHeadExists) {
            return 3; // this category has services
        }

        // Update category status
        DB::table('cost_categories')
            ->where('id', $categoryId)
            ->where('company', $company)
            ->update([
                'is_active' => 0
            ]);

        return 1;
    }

    public function activeExpenseCategory(int $categoryId, $company): int
    {
        DB::table('cost_categories')
            ->where('id', $categoryId)
            ->where('company', $company)
            ->update([
                'is_active' => 1
            ]);

        return 1;
    }

    public function insertExpenseHead(array $insertArr, $tokenService): int
    {
        // Check duplicate entry
        $exists = DB::table('cost_heads')
            ->where('company', $insertArr['company'])
            ->where('cost_head', $insertArr['cost_head'])
            ->exists();

        if ($exists) {
            return 2; // duplicate entry
        }

        // Generate codes (same logic)
        $insertArr['cost_head_code'] = config('constants.COST_HEAD_CODE') . $tokenService->getTokenByCode(config('constants.COST_HEAD_CODE'));

        $insertArr['cost_head_dis_code'] = $insertArr['cost_head_code'];

        DB::table('cost_heads')->insert($insertArr);

        return 1;
    }

    public function editExpenseHead($updateArr, $costHeadId)
    {
        // 1. Duplicate Check (Logic Preserved)
        $exists = DB::table('cost_heads')
            ->where('id', '!=', $costHeadId)
            ->where('company', $updateArr['company'])
            ->where('cost_head', $updateArr['cost_head'])
            ->exists();

        if ($exists) {
            return 2; // Duplicate entry code
        }

        // 2. Perform Update (Logic Preserved)
        $updated = DB::table('cost_heads')
            ->where('id', $costHeadId)
            ->where('company', $updateArr['company'])
            ->update($updateArr);

        return 1; // Success code
    }

    public function checkExpHead(array $arr): int
    {
        $isUsed = DB::table('expense_detail')
            ->join('expense_summary', 'expense_summary.expense_no', '=', 'expense_detail.expense_no')
            ->where('expense_detail.expense_head', $arr['cost_head_code'])
            ->where('expense_summary.company', $arr['company'])
            ->exists();
        return $isUsed ? 0 : 1;
    }

    public function removeExpenseHead($costHeadId, $company, $status): int
    {
        $value = ($status == 'inactive') ? 0 : 1;
        try {
            // Replicating $this->db->where()->where()->update()
            $updated = CostHead::where('id', $costHeadId)
                ->where('company', $company)
                ->update(['is_active' => $value]);

            return 1;

        } catch (Exception $e) {
            Log::error("Failed to remove Expense Head ID {$costHeadId}: " . $e->getMessage());
            throw $e;
        }
    }

    public function insertCategory($insertArr,$generateMonthlyToken)
    {
        $query = ProductCategory::where('company', $insertArr['company'])
            ->where('category_type', $insertArr['category_type'])
            ->where('category_name', $insertArr['category_name'])
            ->first();

        if ($query) {
            return 2;
        }

        $insertArr['category_code'] = config('constants.PRODUCT_CATEGORY') . $generateMonthlyToken->get_month_token(config('constants.PRODUCT_CATEGORY'));

        $insertArr['parent_category_str'] = $this->getParentCategoryStr($insertArr);

        ProductCategory::create($insertArr);

        return 1;
    }

    private function getParentCategoryStr($arr)
    {
        if ($arr['parent_category'] == 1) {
            return $arr['category_code'];
        }

        $row = ProductCategory::where('category_code', $arr['parent_category'])->first();

        $strArr = [];

        if ($row) {
            $strArr[] = $row->parent_category_str;
        } else {
            $strArr[] = '';
        }

        $strArr[] = $arr['category_code'];

        return implode(' / ', $strArr);
    }

    public function editProductCategory($updateArr, $categoryType)
    {
        $duplicate = ProductCategory::where('category_type', $categoryType)
            ->where('category_name', $updateArr['category_name'])
            ->where('company', $updateArr['company'])
            ->where('category_code', '!=', $updateArr['category_code'])
            ->first();

        if ($duplicate) {
            return 2; // duplicate entry
        }

        $updateArr['parent_category_str'] = $this->getParentCategoryStr($updateArr);

        $currentRow = ProductCategory::where('company', $updateArr['company'])
            ->where('category_code', $updateArr['category_code'])
            ->first();

        if (!$currentRow) {
            return 1; // safety fallback (no change in logic flow)
        }

        // Step 4: If parent changed → update child hierarchy
        if ($updateArr['parent_category'] != $currentRow->parent_category) {

            $results = ProductCategory::where('company', $updateArr['company'])
                ->where('category_code', '!=', $updateArr['category_code'])
                ->where('parent_category_str', 'like', '%' . $currentRow->parent_category_str . '%')
                ->get();

            $categoryBatchUpdateArr = [];

            foreach ($results as $result) {

                $categoryBatchUpdateArr[] = [
                    'id' => $result->id,
                    'parent_category_str' =>
                        $updateArr['parent_category_str'] .
                        str_replace(
                            $currentRow->parent_category_str,
                            '',
                            $result->parent_category_str
                        )
                ];
            }
            if (!empty($categoryBatchUpdateArr)) {

                ProductCategory::upsert(
                    $categoryBatchUpdateArr,
                    ['id'],
                    ['parent_category_str']
                );
            }
        }

        ProductCategory::where('company', $updateArr['company'])
            ->where('category_code', $updateArr['category_code'])
            ->update($updateArr);

        return 1;
    }

    public function removeCategory($categoryId, $company)
    {
        $row = ProductCategory::where('id', $categoryId)
            ->where('company', $company)
            ->first();

        if (!$row) {
            return 1; // safe fallback (no change in original flow)
        }

        $categoryCode = $row->category_code;

        $childExists = ProductCategory::where('parent_category', $categoryCode)
            ->where('company', $company)
            ->where('is_active', 1)
            ->exists();

        if ($childExists) {
            return 2; // this category has child category
        }

        $productExists = DB::table('products')
            ->where('company', $company)
            ->where('category', $categoryCode)
            ->where('is_active', 1)
            ->exists();

        if ($productExists) {
            return 3; // this category has product
        }

        ProductCategory::where('id', $categoryId)
            ->where('company', $company)
            ->update([
                'is_active' => 0
            ]);

        return 1;
    }

    public function activeCategory($categoryId, $company)
    {
        ProductCategory::where('id', $categoryId)
            ->where('company', $company)
            ->update([
                'is_active' => 1
            ]);

        return 1;
    }

    public function insertProduct($insertArr, $insertVariantArr, $generateMonthlyToken)
    {
        $duplicate = Product::where('company', $insertArr['company'])
            ->where('product_type', $insertArr['product_type'])
            ->where('product_name', $insertArr['product_name'])
            ->first();

        if ($duplicate) {
            return 2; // duplicate entry
        }

        // Generate product code
        $insertArr['product_code'] = config('constants.PRODUCT') . $generateMonthlyToken->get_month_token(config('constants.PRODUCT'));

        // Insert product
        Product::create($insertArr);

        // Generate variant code
        $insertVariantArr['variant_code'] = config('constants.VARIANT') . $generateMonthlyToken->get_month_token(config('constants.VARIANT'));

        $insertVariantArr['product'] = $insertArr['product_code'];

        // Insert default variant
        ProductVariant::create($insertVariantArr);

        return 1;
    }

    public function updateProduct($updateArr)
    {
        // Duplicate check
        $duplicate = Product::where('product_code', '!=', $updateArr['product_code'])
            ->where('product_type', $updateArr['product_type'])
            ->where('product_name', $updateArr['product_name'])
            ->where('company', $updateArr['company'])
            ->first();

        if ($duplicate) {
            return 2; // duplicate entry
        }

        // Update product
        Product::where('product_code', $updateArr['product_code'])
            ->update($updateArr);

        return 1;
    }

    public function getProductVariant($arr)
    {
        $query = DB::table('product_variants')
            ->where('variant_type', $arr['variantType'])
            ->where('product', $arr['productCode'])
            ->where('company', $arr['company'])
            ->where('is_active', 1)
            ->get();

        return $query->toArray();
    }

    public function checkVariantContengency($contentVariantId, $contentCheckUpdateDtTm)
    {
        $row = DB::table('product_variants')
            ->where('id', $contentVariantId)
            ->first();

        if ($row && $row->updated_dt_tm == $contentCheckUpdateDtTm) {
            return 1;
        }

        return 2;
    }

    public function checkDupProductVariant(
        array $variantNameArr,
        $product,
        $variantType,
        $company
    ) {

        $productVariants = DB::table('product_variants')
            ->select('variant_name')
            ->where('product', $product)
            ->where('company', $company)
            ->where('variant_type', $variantType)
            ->where('is_active', 0)
            ->get()
            ->toArray();

        for ($i = 0; $i < count($variantNameArr); $i++) {

            foreach ($productVariants as $productVariant) {

                if ($productVariant->variant_name == $variantNameArr[$i]) {
                    return 2;
                }
            }
        }

        return 1;
    }

    public function saveProductVariant($updateArr, $insertArr, $deleteVariantIdStr)
    {
        DB::beginTransaction();

        try {

            $flag = 0;

            /*
            |--------------------------------------------------------------------------
            | UPDATE VARIANTS
            |--------------------------------------------------------------------------
            */

            if (!empty($updateArr)) {

                foreach ($updateArr as $row) {

                    DB::table('product_variants')
                        ->where('id', $row['id'])
                        ->update([
                            'variant_name' => $row['variant_name'],
                            'unit_name' => $row['unit_name'],
                            'model' => $row['model'],
                            'display_code' => $row['display_code'],
                            'details' => $row['details'],
                            'updated_by' => $row['updated_by'],
                            'updated_dt_tm' => $row['updated_dt_tm'],
                        ]);
                }

                $flag = 1;
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT VARIANTS
            |--------------------------------------------------------------------------
            */

            if (!empty($insertArr)) {

                DB::table('product_variants')->insert($insertArr);

                $flag = 1;
            }

            /*
            |--------------------------------------------------------------------------
            | SOFT DELETE VARIANTS
            |--------------------------------------------------------------------------
            */

            if (!empty($deleteVariantIdStr)) {

                $deleteIdArr = array_filter(
                    explode(',', $deleteVariantIdStr)
                );

                DB::table('product_variants')
                    ->whereIn('id', $deleteIdArr)
                    ->update([
                        'is_active' => 0,
                        'updated_by' => Auth::user()->user_id,
                        'updated_dt_tm' => now(),
                    ]);

                $flag = 1;
            }

            DB::commit();

            return $flag ? 1 : 0;

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Save Product Variant Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 0;
        }
    }

    public function activeVariant($variantId, $company)
    {
        DB::table('product_variants')
            ->where('company', $company)
            ->where('id', $variantId)
            ->update([
                'is_active' => 1,
                'updated_dt_tm' => Carbon::now(),
                'updated_by' => Auth::user()->user_id ?? null,
            ]);

        return 1;
    }

    public function addMembershipCardList(array $membershipCardArr): array
    {
        try {

            $membershipCardTempTable = 'membership_card_' . reference_no();

            DB::statement("
                CREATE TEMPORARY TABLE IF NOT EXISTS `$membershipCardTempTable` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `card_id` VARCHAR(30) NOT NULL,
                    `card_number` VARCHAR(20) NOT NULL,
                    `validity_month` INT(11) NOT NULL DEFAULT 0,
                    `package_code` VARCHAR(10) NOT NULL,
                    `created_by` VARCHAR(50) NOT NULL,
                    `created_dt_tm` DATETIME NOT NULL,
                    `created_type` VARCHAR(50) NOT NULL,
                    `updated_by` VARCHAR(50) NOT NULL,
                    `updated_dt_tm` DATETIME NOT NULL,
                    `updated_type` VARCHAR(50) NOT NULL,
                    PRIMARY KEY (`id`)
                )
            ");

            /*
            * Existing membership_card data copy to temp table
            * Same as get_compiled_select() + INSERT INTO temp SELECT ...
            */
            DB::statement("
                INSERT INTO {$membershipCardTempTable}
                (
                    id,
                    card_id,
                    card_number,
                    validity_month,
                    package_code,
                    created_by,
                    created_dt_tm,
                    created_type,
                    updated_by,
                    updated_dt_tm,
                    updated_type
                )
                SELECT
                    id,
                    card_id,
                    card_number,
                    validity_month,
                    package_code,
                    created_by,
                    created_dt_tm,
                    created_type,
                    updated_by,
                    updated_dt_tm,
                    updated_type
                FROM membership_card
            ");

            /*
            * Insert uploaded records into temp table
            */
            DB::table($membershipCardTempTable)->insert($membershipCardArr);

            /*
            * Duplicate check
            */
            $results = DB::table($membershipCardTempTable)
                ->selectRaw('COUNT(id) AS card_count, card_number')
                ->groupBy('card_number')
                ->get();

            foreach ($results as $result) {

                if ((int) $result->card_count !== 1) {

                    return [
                        'result'     => 3,
                        'cardNumber' => $result->card_number
                    ];
                }
            }

            /*
            * No duplicate found
            * Insert into actual table
            */
            DB::table('membership_card')->insert($membershipCardArr);

            return [
                'result' => 1
            ];

        } catch (\Throwable $e) {

            Log::error('Membership card import failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return [
                'result'     => 0,
                'cardNumber' => null
            ];
        }
    }
}