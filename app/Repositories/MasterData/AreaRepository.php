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
    public function getDistrict($arr = [])
    {
        return District::with('division')
        ->where('is_active', 1)
        ->whereHas('division', function ($q) {
            $q->where('is_active', 1);
        })
        ->get();
    }

    // ✅ Get Upozillas with District + Division
    // public function getUpozilla($arr = [])
    // {
    //     return Upozilla::with([
    //             'district:id,district_en_name,district_bn_name,division',
    //             'district.division:id,division_en_name,division_bn_name'
    //         ])
    //         ->where('is_active', 1)
    //         ->whereHas('districtInfo', function ($q) {
    //             $q->where('is_active', 1)
    //               ->whereHas('divisionInfo', function ($q2) {
    //                   $q2->where('is_active', 1);
    //               });
    //         })
    //         ->get()
    //         ->map(function ($item) {
    //             return [
    //                 'division_en_name' => $item->district->division->division_en_name,
    //                 'division_bn_name' => $item->district->division->division_bn_name,
    //                 'district_en_name' => $item->district->district_en_name,
    //                 'district_bn_name' => $item->district->district_bn_name,
    //                 'id' => $item->id,
    //                 'district' => $item->district,
    //                 'upozilla_en_name' => $item->upozilla_en_name,
    //                 'upozilla_bn_name' => $item->upozilla_bn_name,
    //                 'is_active' => $item->is_active,
    //                 'created_by' => $item->created_by,
    //                 'created_dt_tm' => $item->created_dt_tm,
    //                 'updated_by' => $item->updated_by,
    //                 'updated_dt_tm' => $item->updated_dt_tm,
    //             ];
    //         })
    //         ->toArray();
    // }

    public function getUpozilla($arr = [])
    {
        $upozillas = DB::table('upazilas as u')
            ->join('districts as d', 'd.id', '=', 'u.district')
            ->join('divisions as div', 'div.id', '=', 'd.division')
            ->select(
                'u.id',
                'u.upozilla_en_name',
                'u.upozilla_bn_name',
                'u.is_active',
                'u.created_by',
                'u.created_dt_tm',
                'u.updated_by',
                'u.updated_dt_tm',
                'd.id as district_id',
                'd.district_en_name',
                'd.district_bn_name',
                'div.id as division_id',
                'div.division_en_name',
                'div.division_bn_name'
            )
            ->where('u.is_active', 1)
            ->where('d.is_active', 1)
            ->where('div.is_active', 1)
            ->get();

        return $upozillas;
    }
}