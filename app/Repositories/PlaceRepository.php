<?php

namespace App\Repositories;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PlaceRepository
{
    public function getPlaceList($isActiveFlag = 1)
    {
        $query = DB::table('places');

        if ($isActiveFlag == 1) {
            $query->where('is_active', 1);
        } elseif ($isActiveFlag == 2) {
            $query->where('is_active', 0);
        }

        return $query->get();
    }
}