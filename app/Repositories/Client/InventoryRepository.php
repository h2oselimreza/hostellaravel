<?php

namespace App\Repositories\Client;

use App\Models\Admin\MasterData\ServiceVariant;
use App\Models\Client\HomeServiceAppDetail;
use App\Models\Client\HomeServiceAppSummaryGen;
use App\Models\CorporateCompany;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryRepository
{
    public function getStockSummary($arr)
    {
        $query = DB::table('stock_summary');

        if (!empty($arr['summaryId'])) {
            $query->where(
                'stock_summary_id',
                $arr['summaryId']
            );
        }

        $query->where('company', $arr['company']);
        $query->where('stock_type', $arr['stockType']);
        $query->where('is_active', 1);

        $query->orderBy('stock_date', 'DESC');

        return $query->get();
    }

    public function addStockIn(
        $stockSummaryArr,
        $stockDetailInsertArr,
        $stockUpdateQuery,
        $stockInsertArr
    ) {

        DB::beginTransaction();

        try {

            DB::table('stock_summary')
                ->insert($stockSummaryArr);

            DB::table('stock_details')
                ->insert($stockDetailInsertArr);


            if (!empty($stockUpdateQuery)) {

                DB::statement($stockUpdateQuery);
            }

            if (!empty($stockInsertArr)) {

                DB::table('stock')
                    ->insert($stockInsertArr);
            }

            DB::commit();

            return 1;

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Add Stock In Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 0;
        }
    }

    public function getCompanyVariant($arr)
    {
        $variantCode = [];

        $results = DB::table('product_variants')
            ->select('variant_code')
            ->where('company', $arr['company'])
            ->where('variant_type', $arr['variantType'])
            ->where('is_active', 1)
            ->get();

        foreach ($results as $result) {

            $variantCode[] = $result->variant_code;
        }

        return $variantCode;
    }

    public function getCompanyStock($arr)
    {
        $query = DB::table('stock')
            ->where('company', $arr['company']);

        if (!empty($arr['variantArr'])) {

            $query->whereIn(
                'variant',
                $arr['variantArr']
            );
        }

        return $query
            ->get();
    }

    public function removeStockSummary(
        $stockSummaryId,
        $companyCode,
        $stockSummaryArr,
        $stockDetailInsertArr,
        $stockUpdateQuery
    ) {

        DB::beginTransaction();

        try {

            DB::table('stock_summary')
                ->where('company', $companyCode)
                ->where('stock_summary_id', $stockSummaryId)
                ->update($stockSummaryArr);

            DB::table('stock_details')
                ->insert($stockDetailInsertArr);

            if (!empty($stockUpdateQuery)) {
                DB::statement($stockUpdateQuery);
            }

            DB::commit();

            return 1;

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Remove Stock Summary Repo Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return 0;
        }
    }

    // public function getCalculatedStockDetail($arr)
    // {
    //     return DB::table('stock_details')
    //     ->select(
    //         DB::raw('MIN(stock_details.id) as stock_details_auto_id'),
    //         'stock_details.stock_detail_id',
    //         'stock_details.variant',
    //         DB::raw('MAX(stock_details.remarks) as remarks'),
    //         DB::raw('SUM(stock_details.credit_quantity) as credit_quantity'),
    //         DB::raw('SUM(stock_details.debit_quantity) as debit_quantity'),
    //         'product_variants.variant_name',
    //         'product_variants.unit_name'
    //     )
    //     ->join(
    //         'product_variants',
    //         'product_variants.variant_code',
    //         '=',
    //         'stock_details.variant'
    //     )
    //     ->where('stock_details.company', $arr['company'])
    //     ->where('stock_details.stock_summary_id', $arr['summaryId'])

    //     // ✅ added condition safely
    //     ->when(!empty($arr['transactionType']), function ($query) use ($arr) {
    //         $query->where('stock_details.trasaction_type', $arr['transactionType']);
    //     })

    //     ->groupBy(
    //         'stock_details.variant',
    //         'stock_details.stock_detail_id',
    //         'product_variants.variant_name',
    //         'product_variants.unit_name'
    //     )
    //     ->get()
    //     ->toArray();
    // }

    public function getCalculatedStockDetail(array $arr)
    {
        try {

            $query = DB::table('stock_details')
                ->select(
                    DB::raw('MAX(stock_details.id) as stock_details_auto_id'),
                    DB::raw('MAX(stock_details.stock_detail_id) as stock_detail_id'),
                    'stock_details.variant',
                    DB::raw('MAX(stock_details.remarks) as remarks'),

                    DB::raw(
                        'SUM(stock_details.credit_quantity) as credit_quantity'
                    ),

                    DB::raw(
                        'SUM(stock_details.debit_quantity) as debit_quantity'
                    ),

                    DB::raw(
                        'MAX(product_variants.variant_name) as variant_name'
                    ),

                    DB::raw(
                        'MAX(product_variants.unit_name) as unit_name'
                    )
                )
                ->join(
                    'product_variants',
                    'product_variants.variant_code',
                    '=',
                    'stock_details.variant'
                )
                ->where(
                    'stock_details.company',
                    $arr['company']
                )
                ->where(
                    'stock_details.stock_summary_id',
                    $arr['summaryId']
                );

            if (!empty($arr['transactionType'])) {

                $query->where(
                    'stock_details.trasaction_type',
                    $arr['transactionType']
                );
            }

            return $query
                ->groupBy('stock_details.variant')
                ->get();

        } catch (\Throwable $e) {

            Log::error('Get Calculated Stock Detail Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return collect([]);
        }
    }

    public function changeStockStatus($variantArr, $statusData)
    {
        if (!empty($variantArr)) {

            DB::table('stock')
                ->whereIn('variant', $variantArr)
                ->update($statusData);
        }
    }

    public function getDeleteStockDetailsNew($variantDeleteStr, $company, $summaryId)
    {
        $variantArr = [];

        // STEP 1: Get distinct variants from stock_details by IDs
        $results = DB::table('stock_details')
            ->select('variant')
            ->whereIn('id', explode(',', $variantDeleteStr))
            ->groupBy('variant')
            ->get();

        foreach ($results as $result) {
            $variantArr[] = $result->variant;
        }

        // STEP 2: If variants exist, get aggregated stock details
        if (!empty($variantArr)) {

            $data = DB::table('stock_details')
                ->select(
                    DB::raw('SUM(credit_quantity) as credit_quantity'),
                    DB::raw('SUM(debit_quantity) as debit_quantity'),
                    'variant'
                )
                ->where('stock_summary_id', $summaryId)
                ->where('company', $company)
                ->whereIn('variant', $variantArr)
                ->groupBy('variant')
                ->get();

            return $data->toArray();
        }

        return [];
    }

    public function editStockIn(
        $stockSummaryArr,
        $stockDetailInsertArr,
        $stockSummaryId,
        $company,
        $tempTableInsertArr,
        $variantArr
    ) {
        $stockInsertArr = [];
        // dd(        $stockSummaryArr,
        // $stockDetailInsertArr,
        // $stockSummaryId,
        // $company,
        // $tempTableInsertArr,
        // $variantArr,'rrrr');
        //dd($tempTableInsertArr,'ppp');
        try {

            if (!empty($tempTableInsertArr)) {

                $stockTempTable = 'stock_temp' . reference_no();

                // CREATE TEMP TABLE
                DB::statement("
                    CREATE TEMPORARY TABLE IF NOT EXISTS `$stockTempTable` (
                        id int(11) NOT NULL AUTO_INCREMENT,
                        company varchar(50) NOT NULL,
                        variant_temp varchar(50) NOT NULL,
                        debit_quantity_temp decimal(10,2) NOT NULL DEFAULT '0.00',
                        credit_quantity_temp decimal(10,2) NOT NULL DEFAULT '0.00',
                        PRIMARY KEY (id)
                    )
                ");

                // INSERT TEMP DATA
                DB::table($stockTempTable)->insert($tempTableInsertArr);
                //dd('jdjdj');
                // JOIN QUERY
                $results = DB::table($stockTempTable)
                ->selectRaw("
                    SUM({$stockTempTable}.debit_quantity_temp) as debit_quantity_temp,

                    SUM({$stockTempTable}.credit_quantity_temp) as credit_quantity_temp,

                    {$stockTempTable}.variant_temp,

                    MAX(stock.quantity) as stock_quantity,

                    MAX(stock.id) as stock_auto_id,

                    MAX(stock.status) as status
                ")
                ->leftJoin(
                    'stock',
                    DB::raw('stock.variant COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw($stockTempTable . '.variant_temp COLLATE utf8mb4_unicode_ci')
                )
                ->groupBy($stockTempTable . '.variant_temp')
                ->get();
                //dd($results);
                $createUpdateUser = Auth::user()->user_id;
                $dateTime = now();

                // MARK PROCESSING
                $statusData = [
                    'status' => 2,
                    'updated_by' => $createUpdateUser,
                    'updated_dt_tm' => $dateTime
                ];

                $this->changeStockStatus($variantArr, $statusData);

                $stockIdArr = [];
                $quantityStrArr = [];
                $updatedByStrArr = [];
                $updatedDtTmStrArr = [];
                $stockUpdateQuery = '';

                foreach ($results as $result) {

                    if ($result->status == 2) {
                        $statusData['status'] = 1;
                        $this->changeStockStatus($variantArr, $statusData);
                        return 4;
                    }

                    if ($result->stock_quantity) {

                        $stockIdArr[] = $result->stock_auto_id;
                        $variantQuantity = 0;

                        if ($result->credit_quantity_temp >= $result->debit_quantity_temp) {

                            $variantQuantity =
                                $result->credit_quantity_temp - $result->debit_quantity_temp;

                            $quantityStrArr[] =
                                "WHEN `id` = {$result->stock_auto_id} THEN `quantity` + {$variantQuantity}";

                        } else {

                            $variantQuantity =
                                $result->debit_quantity_temp - $result->credit_quantity_temp;

                            $quantityStrArr[] =
                                "WHEN `id` = {$result->stock_auto_id} THEN `quantity` - {$variantQuantity}";

                            if ($result->stock_quantity < $variantQuantity) {

                                $statusData['status'] = 1;
                                $this->changeStockStatus($variantArr, $statusData);

                                return 3;
                            }
                        }

                        $updatedByStrArr[] =
                            "WHEN `id` = {$result->stock_auto_id} THEN '{$createUpdateUser}'";

                        $updatedDtTmStrArr[] =
                            "WHEN `id` = {$result->stock_auto_id} THEN '{$dateTime}'";

                    } else {

                        $stockInsertArr[] = [
                            'company' => $company,
                            'variant' => $result->variant_temp,
                            'quantity' => $result->credit_quantity_temp,
                            'status' => 1,
                            'created_by' => $createUpdateUser,
                            'created_dt_tm' => $dateTime,
                            'updated_by' => $createUpdateUser,
                            'updated_dt_tm' => $dateTime,
                        ];
                    }
                }

                if (!empty($stockIdArr)) {

                    $stockUpdateQuery =
                        "UPDATE `stock` SET `quantity` = CASE "
                        . implode(' ', $quantityStrArr)
                        . " ELSE `quantity` END, "
                        . "`updated_by` = CASE "
                        . implode(' ', $updatedByStrArr)
                        . " ELSE `updated_by` END, "
                        . "`updated_dt_tm` = CASE "
                        . implode(' ', $updatedDtTmStrArr)
                        . " ELSE `updated_dt_tm` END "
                        . "WHERE `id` IN(" . implode(',', $stockIdArr) . ")";
                }
            }

            // UPDATE SUMMARY
            DB::table('stock_summary')
                ->where('company', $company)
                ->where('stock_summary_id', $stockSummaryId)
                ->update($stockSummaryArr);

            // INSERT DETAILS
            if (!empty($stockDetailInsertArr)) {
                DB::table('stock_details')->insert($stockDetailInsertArr);
            }

            // EXECUTE STOCK UPDATE
            if ($stockUpdateQuery) {
                DB::statement($stockUpdateQuery);
            }

            // INSERT NEW STOCK
            if (!empty($stockInsertArr)) {
                DB::table('stock')->insert($stockInsertArr);
            }

            // FINAL STATUS RESET
            $statusData = [
                'status' => 1
            ];

            $this->changeStockStatus($variantArr, $statusData);

            return 1;

        } catch (\Throwable $e) {

            Log::error('editStockIn Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return 0;
        }
    }

    public function getStockVaiant(array $arr)
    {
        return DB::table('stock')
            ->select(
                'stock.quantity',
                'product_variants.*',
                'products.product_name',
                'product_categories.parent_category_str',
                'product_categories.category_name'
            )
            ->join(
                'product_variants',
                'product_variants.variant_code',
                '=',
                'stock.variant'
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
            ->where('stock.company', $arr['company'])
            ->where('product_variants.is_active', 1)
            ->where('products.is_active', 1)
            ->where('product_categories.is_active', 1)
            ->where('product_variants.variant_type', $arr['variantType'])
            ->orderBy('product_variants.product', 'ASC')
            ->get();
    }

    public function addStockOut(
        array $stockSummaryArr,
        array $stockDetailsArr,
        array $tempTableInsetArr,
        $companyCode
    ) {
        DB::beginTransaction();

        try {

            $query = $this->getStockDebitQuery(
                $tempTableInsetArr,
                $companyCode
            );

            if ($query['isSuccess'] == 2) {

                DB::rollBack();

                return 3;
            }

            $stockUpdateQuery = $query['query'];

            DB::table('stock_summary')->insert($stockSummaryArr);

            DB::table('stock_details')->insert($stockDetailsArr);

            DB::statement($stockUpdateQuery);

            DB::commit();

            return 1;

        } catch (\Exception $e) {

            DB::rollBack();

            return 2;
        }
    }

    public function checkStockQuantity(array $tempTableInsetArr, $company)
    {
        try {

            $stockTempTable = 'stock_temp' . reference_no();

            $tempTableSqlStr = "
                CREATE TEMPORARY TABLE IF NOT EXISTS `$stockTempTable` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `company` VARCHAR(50)
                        CHARACTER SET utf8mb4
                        COLLATE utf8mb4_unicode_ci NOT NULL,

                    `variant_temp` VARCHAR(50)
                        CHARACTER SET utf8mb4
                        COLLATE utf8mb4_unicode_ci NOT NULL,

                    `quantity_temp` DECIMAL(10,2) NOT NULL DEFAULT '0.00',

                    PRIMARY KEY (`id`)
                )
            ";

            DB::statement($tempTableSqlStr);

            DB::table($stockTempTable)->insert($tempTableInsetArr);

            $results = DB::table($stockTempTable)
            ->select(
                DB::raw('SUM(' . $stockTempTable . '.quantity_temp) as total_quantity'),
                DB::raw($stockTempTable . '.variant_temp as variant_code'),
                DB::raw('MAX(stock.quantity) as quantity_db')
            )
            ->join(
                'stock',
                DB::raw('stock.variant COLLATE utf8mb4_unicode_ci'),
                '=',
                DB::raw($stockTempTable . '.variant_temp COLLATE utf8mb4_unicode_ci')
            )
            ->where('stock.company', $company)
            ->groupBy($stockTempTable . '.variant_temp')
            ->get();

            foreach ($results as $result) {

                if ($result->total_quantity > $result->quantity_db) {

                    return 2;
                }
            }

            return 1;

        } catch (\Throwable $e) {

            Log::error('Check Stock Quantity Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return 2;
        }
    }


    public function getStockDebitQuery(array $tempTableInsetArr, $company)
    {
        try {

            $queryStr = "";

            $stockTempTable = 'stock_temp' . reference_no();

            /*
            |--------------------------------------------------------------------------
            | Create Temporary Table
            |--------------------------------------------------------------------------
            */

            $tempTableSqlStr = "
                CREATE TEMPORARY TABLE IF NOT EXISTS `$stockTempTable` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,

                    `company` VARCHAR(50)
                        CHARACTER SET utf8mb4
                        COLLATE utf8mb4_unicode_ci NOT NULL,

                    `variant_temp` VARCHAR(50)
                        CHARACTER SET utf8mb4
                        COLLATE utf8mb4_unicode_ci NOT NULL,

                    `quantity_temp` DECIMAL(10,2) NOT NULL DEFAULT '0.00',

                    PRIMARY KEY (`id`)
                )
            ";

            DB::statement($tempTableSqlStr);

            /*
            |--------------------------------------------------------------------------
            | Insert Batch Data
            |--------------------------------------------------------------------------
            */

            DB::table($stockTempTable)->insert($tempTableInsetArr);

            /*
            |--------------------------------------------------------------------------
            | Prepare Arrays
            |--------------------------------------------------------------------------
            */

            $stockIdArr = [];
            $quantityStrArr = [];
            $updatedByStrArr = [];
            $updatedDtTmStrArr = [];

            $dateTime = date('Y-m-d H:i:s');

            $createUpdateUser = session('user_id');

            /*
            |--------------------------------------------------------------------------
            | Get Stock Information
            |--------------------------------------------------------------------------
            */

            $results = DB::table($stockTempTable)
                ->select(
                    DB::raw(
                        'SUM(' . $stockTempTable . '.quantity_temp) as total_quantity'
                    ),

                    DB::raw(
                        $stockTempTable . '.variant_temp as variant_code'
                    ),

                    DB::raw(
                        'MAX(stock.quantity) as quantity_db'
                    ),

                    DB::raw(
                        'MAX(stock.id) as stock_id'
                    )
                )
                ->join(
                    'stock',
                    DB::raw(
                        'stock.variant COLLATE utf8mb4_unicode_ci'
                    ),
                    '=',
                    DB::raw(
                        $stockTempTable . '.variant_temp COLLATE utf8mb4_unicode_ci'
                    )
                )
                ->where('stock.company', $company)
                ->groupBy($stockTempTable . '.variant_temp')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Generate Update Query
            |--------------------------------------------------------------------------
            */

            foreach ($results as $result) {

                if ($result->total_quantity > $result->quantity_db) {

                    return [
                        'isSuccess' => 2
                    ];
                }

                $stockIdArr[] = $result->stock_id;

                $quantityStrArr[] =
                    "WHEN `id` = " . $result->stock_id .
                    " THEN `quantity`-" . $result->total_quantity;

                $updatedByStrArr[] =
                    "WHEN `id` = " . $result->stock_id .
                    " THEN '" . $createUpdateUser . "'";

                $updatedDtTmStrArr[] =
                    "WHEN `id` = " . $result->stock_id .
                    " THEN '" . $dateTime . "'";
            }

            /*
            |--------------------------------------------------------------------------
            | Final Update Query
            |--------------------------------------------------------------------------
            */

            if ($stockIdArr) {

                $queryStr =
                    "UPDATE `stock`
                    SET
                        `quantity` = CASE " .
                            implode(' ', $quantityStrArr) .
                            " ELSE `quantity` END,

                        `updated_by` = CASE " .
                            implode(' ', $updatedByStrArr) .
                            " ELSE `updated_by` END,

                        `updated_dt_tm` = CASE " .
                            implode(' ', $updatedDtTmStrArr) .
                            " ELSE `updated_dt_tm` END

                    WHERE `id` IN(" .
                        implode(',', $stockIdArr) .
                    ")";
            }

            return [
                'isSuccess' => 1,
                'query'     => $queryStr
            ];

        } catch (\Throwable $e) {

            Log::error('Get Stock Debit Query Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return [
                'isSuccess' => 2
            ];
        }
    }

    public function getStockOutVehicle(array $arr)
    {
        try {

            return DB::table('stock_details')
                ->select(
                    'stock_details.vehicle',
                    'vehicles.registration_no'
                )
                ->join(
                    'vehicles',
                    'vehicles.vehicle_id',
                    '=',
                    'stock_details.vehicle'
                )
                ->where(
                    'stock_details.stock_summary_id',
                    $arr['summaryId']
                )
                ->where(
                    'stock_details.company',
                    $arr['company']
                )
                ->distinct()
                ->get();

        } catch (\Throwable $e) {

            Log::error('Get Stock Out Vehicle Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return collect([]);
        }
    }

    public function getCalculatedStockOutDetail(array $arr)
    {
        try {

            return DB::table('stock_details')
                ->select(
                    DB::raw('ANY_VALUE(stock_details.id) as stock_details_auto_id'),
                    'stock_details.vehicle',
                    DB::raw('ANY_VALUE(stock_details.stock_detail_id) as stock_detail_id'),
                    'stock_details.variant',
                    DB::raw('ANY_VALUE(stock_details.remarks) as remarks'),

                    DB::raw('SUM(stock_details.credit_quantity) as credit_quantity'),
                    DB::raw('SUM(stock_details.debit_quantity) as debit_quantity'),

                    DB::raw('ANY_VALUE(product_variants.variant_name) as variant_name'),
                    DB::raw('ANY_VALUE(product_variants.unit_name) as unit_name')
                )
                ->join(
                    'product_variants',
                    'product_variants.variant_code',
                    '=',
                    'stock_details.variant'
                )
                ->where('stock_details.company', $arr['company'])
                ->where('stock_details.stock_summary_id', $arr['summaryId'])
                ->groupBy('stock_details.vehicle')
                ->groupBy('stock_details.variant')
                ->get();

        } catch (\Throwable $e) {

            Log::error('Get Calculated Stock Out Detail Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return collect([]);
        }
    }

    public function getDeleteStockOutDetails($variantDeleteStr, $company, $summaryId)
    {
        $variantArr = [];
        $vehicleArr = [];

        //Get variants + vehicles from selected IDs
        //dd(explode(',', $variantDeleteStr));
        $results = DB::table('stock_details')
            ->select('variant', 'vehicle')
            ->whereIn('id', explode(',', $variantDeleteStr))
            ->groupBy('variant')
            ->groupBy('vehicle')
            ->get();

        foreach ($results as $result) {
            $variantArr[] = $result->variant;
            $vehicleArr[] = $result->vehicle;
        }

        //If valid data exists, calculate stock sums

        if (!empty($variantArr) && !empty($vehicleArr)) {

            return DB::table('stock_details')
                ->select(
                    DB::raw('SUM(credit_quantity) as credit_quantity'),
                    DB::raw('SUM(debit_quantity) as debit_quantity'),
                    'variant',
                    'vehicle'
                )
                ->where('stock_summary_id', $summaryId)
                ->where('company', $company)
                ->whereIn('vehicle', $vehicleArr)
                ->whereIn('variant', $variantArr)
                ->groupBy('vehicle')
                ->groupBy('variant')
                ->get()
                ->toArray();
        }

        return [];
    }

    public function editStockOut(
        $stockSummaryArr,
        $stockDetailInsertArr,
        $stockSummaryId,
        $company,
        $tempTableInsertArr,
        $variantArr
    ) {
        try {
        //     dd($stockSummaryArr,
        // $stockDetailInsertArr,
        // $stockSummaryId,
        // $company,
        // $tempTableInsertArr,
        // $variantArr);
            $stockUpdateQuery = "";

            if (!empty($tempTableInsertArr)) {

                $stockTempTable = 'stock_temp' . reference_no();

                // Create Temp Table

                $tempTableSqlStr = "
                    CREATE TEMPORARY TABLE IF NOT EXISTS `$stockTempTable` (
                        `id` INT(11) NOT NULL AUTO_INCREMENT,
                        `company` VARCHAR(50) NOT NULL,
                        `variant_temp` VARCHAR(50) NOT NULL,
                        `debit_quantity_temp` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
                        `credit_quantity_temp` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
                        PRIMARY KEY (`id`)
                    )
                ";

                DB::statement($tempTableSqlStr);

                DB::table($stockTempTable)->insert($tempTableInsertArr);
                // Aggregate Temp Data

                $results = DB::table($stockTempTable)
                ->selectRaw("
                    SUM({$stockTempTable}.debit_quantity_temp) as debit_quantity_temp,

                    SUM({$stockTempTable}.credit_quantity_temp) as credit_quantity_temp,

                    {$stockTempTable}.variant_temp,

                    MAX(stock.quantity) as stock_quantity,

                    MAX(stock.id) as stock_auto_id,

                    MAX(stock.status) as status
                ")
                ->leftJoin(
                    'stock',
                    DB::raw('stock.variant COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw($stockTempTable . '.variant_temp COLLATE utf8mb4_unicode_ci')
                )
                ->groupBy($stockTempTable . '.variant_temp')
                ->get();
                $createUpdateUser = Auth::user()->user_id;
                $dateTime = Carbon::now();

                // Set Processing Status

                $statusData = [
                    'status' => 2,
                    'updated_by' => Auth::user()->user_id,
                    'updated_dt_tm' => Carbon::now()
                ];
                
                $this->changeStockStatus($variantArr, $statusData);

                $stockIdArr = [];
                $quantityStrArr = [];
                $updatedByStrArr = [];
                $updatedDtTmStrArr = [];

                foreach ($results as $result) {

                    if ($result->status == 2) {

                        $statusData['status'] = 1;
                        $this->changeStockStatus($variantArr, $statusData);

                        return 4;
                    }

                    $stockIdArr[] = $result->stock_auto_id;

                    if ($result->credit_quantity_temp >= $result->debit_quantity_temp) {

                        $variantQuantity =
                            $result->credit_quantity_temp
                            - $result->debit_quantity_temp;

                        $quantityStrArr[] =
                            "WHEN `id` = {$result->stock_auto_id} THEN `quantity` + {$variantQuantity}";

                    } else {

                        $variantQuantity =
                            $result->debit_quantity_temp
                            - $result->credit_quantity_temp;

                        $quantityStrArr[] =
                            "WHEN `id` = {$result->stock_auto_id} THEN `quantity` - {$variantQuantity}";

                        if ($result->stock_quantity < $variantQuantity) {

                            $statusData['status'] = 1;
                            $this->changeStockStatus($variantArr, $statusData);

                            return 3;
                        }
                    }

                    $updatedByStrArr[] =
                        "WHEN `id` = {$result->stock_auto_id} THEN '{$createUpdateUser}'";

                    $updatedDtTmStrArr[] =
                        "WHEN `id` = {$result->stock_auto_id} THEN '{$dateTime}'";
                }

                if (!empty($stockIdArr)) {

                    $stockUpdateQuery = "
                        UPDATE `stock`
                        SET
                            `quantity` = CASE " . implode(' ', $quantityStrArr) . " ELSE `quantity` END,
                            `updated_by` = CASE " . implode(' ', $updatedByStrArr) . " ELSE `updated_by` END,
                            `updated_dt_tm` = CASE " . implode(' ', $updatedDtTmStrArr) . " ELSE `updated_dt_tm` END
                        WHERE `id` IN (" . implode(',', $stockIdArr) . ")
                    ";
                }
            }

            // Update Summary

            DB::table('stock_summary')
                ->where('company', $company)
                ->where('stock_summary_id', $stockSummaryId)
                ->update($stockSummaryArr);

            // Insert Stock Details
            DB::table('stock_details')->insert($stockDetailInsertArr);


            // Execute Stock Update Query

            if (!empty($stockUpdateQuery)) {
                DB::statement($stockUpdateQuery);
            }

            // Final Status Reset

            $statusData = [
                'status' => 1
            ];

            $this->changeStockStatus($variantArr, $statusData);

            return 1;

        } catch (\Throwable $e) {

            Log::error('Edit Stock Out Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return 2;
        }
    }


    function getDeleteStockDetails($variantDeleteStr, $company, $summaryId)
    {
        return DB::table('stock_details')
            ->where('stock_summary_id', $summaryId)
            ->where('company', $company)
            ->whereIn('id', explode(',', $variantDeleteStr))
            ->get()
            ->map(function ($row) {
                return (array) $row;
            })
            ->toArray();
    }

    function checkStockQuantityEdit($tempTableInsertArr)
    {
        if (!empty($tempTableInsertArr)) {

            $stockTempTable = 'stock_temp' . reference_no();

            // Create Temporary Table

            $tempTableSqlStr = "
                CREATE TEMPORARY TABLE IF NOT EXISTS `$stockTempTable` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `company` VARCHAR(50) NOT NULL,
                    `variant_temp` VARCHAR(50) NOT NULL,
                    `debit_quantity_temp` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
                    `credit_quantity_temp` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
                    PRIMARY KEY (`id`)
                )
            ";

            DB::statement($tempTableSqlStr);


            // Insert Batch Data
            DB::table($stockTempTable)->insert($tempTableInsertArr);


            // Aggregation Query (same logic as CI)
            $results = DB::table($stockTempTable)
                ->selectRaw("
                    SUM({$stockTempTable}.debit_quantity_temp) as debit_quantity_temp,

                    SUM({$stockTempTable}.credit_quantity_temp) as credit_quantity_temp,

                    {$stockTempTable}.variant_temp,

                    MAX(stock.quantity) as stock_quantity,

                    MAX(stock.id) as stock_auto_id,

                    MAX(stock.status) as status
                ")
                ->leftJoin(
                    'stock',
                    DB::raw('stock.variant COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw($stockTempTable . '.variant_temp COLLATE utf8mb4_unicode_ci')
                )
                ->groupBy($stockTempTable . '.variant_temp')
                ->get();
        
            // Business Logic Check (UNCHANGED)

            foreach ($results as $result) {

                $variantQuantity = 0;

                if ($result->debit_quantity_temp > $result->credit_quantity_temp) {

                    $variantQuantity =
                        $result->debit_quantity_temp -
                        $result->credit_quantity_temp;

                    if ($result->stock_quantity < $variantQuantity) {
                        return 2;
                    }
                }
            }
        }

        return 1;
    }

    public function getStockDetails(array $arr)
    {
        try {

            $query = DB::table('stock_details')
                ->where('company', $arr['company'])
                ->where(
                    'stock_summary_id',
                    $arr['stockSummaryId']
                );

            if (!empty($arr['transactionType'])) {

                $query->where(
                    'trasaction_type',
                    $arr['transactionType']
                );
            }

            return $query->get();

        } catch (\Throwable $e) {

            Log::error('Get Stock Details Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return collect();
        }
    }
}