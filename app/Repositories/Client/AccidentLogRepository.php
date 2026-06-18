<?php

namespace App\Repositories\Client;

use Illuminate\Support\Facades\DB;

class AccidentLogRepository
{

    public function getAccidentalLog($companyCode, $id = null)
    {
        $query = DB::table('accidental_log')
            ->select(
                'accidental_log.*',
                'vehicles.vehicle_id',
                'vehicles.registration_no',
                'customer_employee.employee_name',
                'customer_employee.designation'
            )
            ->join('vehicles', 'vehicles.vehicle_id', '=', 'accidental_log.vehicle')
            ->join('customer_employee', 'customer_employee.employee_id', '=', 'accidental_log.driver')
            ->where('accidental_log.company', $companyCode);

        return $id
            ? $query->where('accidental_log.id', $id)->first()
            : $query->get();
    }

}