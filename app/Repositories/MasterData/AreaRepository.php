<?php

namespace App\Repositories\MasterData;

use App\Models\MetaData\District;
use App\Models\MetaData\Division;
use App\Models\MetaData\Upozilla;
use Illuminate\Support\Facades\DB;

class AreaRepository
{
    public function getAreaCounts()
    {
        return [
            'divisionCount' => Division::where('is_active', 1)->count(),
            'districtCount' => District::where('is_active', 1)->count(),
            'upozillaCount' => Upozilla::where('is_active', 1)->count(),
        ];
    }
   // ✅ Get Divisions
    public function getDivision($arr = [])
    {
        return Division::where('is_active', 1)
            ->get();
    }

    // ✅ Get Districts with Division
    public function getDistrict($division_id)
    {
        return District::where(['is_active'=>1, 'division' => $division_id])->get();
    }

    public function getUpozilla($district_id)
    {
        return Upozilla::where(['is_active'=>1, 'district' => $district_id])->get();
    }
}