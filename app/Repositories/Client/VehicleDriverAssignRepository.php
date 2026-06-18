<?php

namespace App\Repositories\Client;

use App\Models\Company;
use App\Models\CustomerEmployee;
use Illuminate\Support\Facades\DB;

class VehicleDriverAssignRepository
{
    public function assignDriver($vehicle, $driver, $companyCode, $contengencyDtTm)
    {
        return DB::transaction(function () use ($vehicle, $driver, $companyCode, $contengencyDtTm) {

            $vehicleRow = DB::table('vehicles')
                ->select('vehicle_id', 'updated_dt_tm')
                ->where('vehicle_id', $vehicle)
                ->where('company', $companyCode)
                ->where('is_active', 1)
                ->lockForUpdate()
                ->first();

            if (!$vehicleRow) {
                return 2;
            }

            if ((string) $vehicleRow->updated_dt_tm !== (string) $contengencyDtTm) {
                return 3;
            }

            $driverExists = DB::table('customer_employee')
                ->where('employee_id', $driver)
                ->where('company', $companyCode)
                ->where('emp_type', 'driver')
                ->where('is_active', 1)
                ->exists();

            if (!$driverExists) {
                return 4;
            }

            DB::table('vehicles')
                ->where('vehicle_id', $vehicle)
                ->update([
                    'driver_id'     => $driver,
                    'updated_by'    => auth()->id(),
                    'updated_dt_tm' => now(),
                ]);

            return 1;
        });
    }

    public function removeDriver($vehicle, $companyCode, $contengencyDtTm)
    {
        return DB::transaction(function () use ($vehicle, $companyCode, $contengencyDtTm) {

            // Get vehicle with lock (prevents race condition)
            $row = DB::table('vehicles')
                ->select('vehicle_id', 'assign_type', 'updated_dt_tm')
                ->where('vehicle_id', $vehicle)
                ->where('company', $companyCode)
                ->where('is_active', 1)
                ->lockForUpdate()
                ->first();

            // Vehicle not found
            if (!$row) {
                return 2;
            }

            // Concurrency check (same as CI)
            if ((string) $row->updated_dt_tm !== (string) $contengencyDtTm) {
                return 4;
            }

            // If vehicle is enroute → cannot remove
            if ($row->assign_type == config('constants.ASSIGN_ENROUTE')) {
                return 3;
            }

            // Update (remove driver)
            DB::table('vehicles')
                ->where('vehicle_id', $vehicle)
                ->update([
                    'driver_id'     => null,
                    'updated_by'    => auth()->id(),
                    'updated_dt_tm' => now(),
                ]);

            return 1;
        });
    }

}