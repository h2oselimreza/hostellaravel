<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Facades\DB;

class WorkshopServiceRepository
{
    public function getWorkshopService(
        array $arr,
        int $isActiveFlag = 1
    ) {
        $query = DB::table('service_variants')
            ->select(
                'service_variants.*',
                'services.service_name',
                'service_categories.category_name',
                'service_categories.parent_category_str'
            )
            ->join(
                'services',
                'services.service_code',
                '=',
                'service_variants.service'
            )
            ->join(
                'service_categories',
                'service_categories.category_code',
                '=',
                'services.service_category'
            );

        if ($isActiveFlag == 1) {

            $query->where('service_variants.is_active', 1);

        } elseif ($isActiveFlag == 2) {

            $query->where('service_variants.is_active', 0);
        }

        $query->where('services.is_active', 1);

        $query->where('service_categories.is_active', 1);

        $query->where(
            'service_variants.variant_type',
            $arr['variantType']
        );

        return $query
            ->orderBy('service_variants.service', 'ASC')
            ->get();
    }   
}