<?php

namespace App\Repositories\MasterData;

use App\Models\CommonTable;
use App\Models\MetaData\District;
use App\Models\MetaData\Division;
use App\Models\MetaData\Upozilla;
use Illuminate\Support\Facades\DB;

class VehicleRepository
{
    public function getVehicleTypeCounts()
    {
        return [
            'vehicleTypeCount' => CommonTable::where(['type'=>'vehicle_type','is_active'=>1])->count(),
            'vehicleClassCount' => CommonTable::where(['type'=>'vehicle_class','is_active'=>1])->count(),
            'vehicleBrandCount' => CommonTable::where(['type'=>'vehicle_brand','is_active'=>1])->count(),
            'vehicleBrandModelCount' => CommonTable::where(['type'=>'v_brand_model','is_active'=>1])->count(),
            'vehicleColorCount' => CommonTable::where(['type'=>'vehicle_color','is_active'=>1])->count(),
            'vehicleConditionCount' => CommonTable::where(['type'=>'vehicle_cndtn','is_active'=>1])->count(),
            'vehicleGroupCount' => CommonTable::where(['type'=>'vehicle_group','is_active'=>1])->count(),
        ];
    }


    public function getVehicleType($id = null)
    {
        $query = CommonTable::where(['type' => 'vehicle_type', 'is_active' => 1]);

        if ($id) {
            return $query->where('id', $id)->first();
        }

        return $query->get();
    }

    public function getVehicleClass($id = null){
        $query = CommonTable::where(['type' => 'vehicle_class', 'is_active' => 1]);

        if ($id) {
            return $query->where('id', $id)->first();
        }

        return $query->get();
    }

    public function getVehicleBrand($id = null){
        $query = CommonTable::where(['type' => 'vehicle_brand', 'is_active' => 1]);

        if ($id) {
            return $query->where('id', $id)->first();
        }

        return $query->get();
    }

    public function getVehicleBrandModel($id = null)
    {
        $query = CommonTable::from('common_table as models')
            ->select('models.*', 'brands.element as brand_name')
            ->leftJoin('common_table as brands', function($join) {
                $join->on('models.depend_on_element', '=', 'brands.element_code')
                    ->where('brands.type', '=', 'vehicle_brand');
            })
            ->where('models.type', 'v_brand_model')
            ->where('models.is_active', 1);

        if ($id) {
            return $query->where('models.id', $id)->first();
        }

        return $query->orderBy('models.element_order', 'asc')
                    ->orderBy('models.element', 'asc')
                    ->get();
    }

    public function getVehicleColor($id = null){
        $query = CommonTable::where(['type' => 'vehicle_color', 'is_active' => 1]);

        if ($id) {
            return $query->where('id', $id)->first();
        }

        return $query->get();
    }

    public function getVehicleCondition($id = null){
        $query = CommonTable::where(['type' => 'vehicle_cndtn', 'is_active' => 1]);

        if ($id) {
            return $query->where('id', $id)->first();
        }

        return $query->get();
    }
     public function getVehicleGroup($id = null){
        $query = CommonTable::from('common_table as models')
            ->select('models.*', 'company.title as company_name')
            ->leftJoin('corporate_companies as company', function($join) {
                $join->on('models.company', '=', 'company.company_code');
            })
            ->where('models.type', 'vehicle_group')
            ->where('models.is_active', 1);

        if ($id) {
            return $query->where('models.id', $id)->first();
        }

        return $query->orderBy('models.element_order', 'asc')
                    ->orderBy('models.element', 'asc')
                    ->get();
    }

}