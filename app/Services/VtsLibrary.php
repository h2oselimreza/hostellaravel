<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VtsLibrary
{
    /**
     * Get current vehicle location from EasyTrax
     */
    public function getCurrentLocationETracks(
        $vehicle,
        $appkey
    ) {

        try {

            $url =
                'http://track.easytrax.com.bd/api/api.php'
                . '?api=user'
                . '&ver=1.0'
                . '&key=' . $appkey
                . '&cmd=OBJECT_GET_LOCATIONS,' . $vehicle;

            $response = Http::withoutVerifying()
                ->asForm()
                ->post($url);

            return $response->body();

        } catch (\Throwable $e) {

            Log::error('EasyTrax Location API Error', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get address from latitude & longitude
     */
    public function getAddressETracks(
        $latitude,
        $longitude,
        $appkey
    ) {

        try {

            $url =
                'http://track.easytrax.com.bd/api/api.php'
                . '?api=user'
                . '&ver=1.0'
                . '&key=' . $appkey
                . '&cmd=GET_ADDRESS,' . $latitude . ',' . $longitude;

            $response = Http::withoutVerifying()
                ->asForm()
                ->post($url);

            return $response->body();

        } catch (\Throwable $e) {

            Log::error('EasyTrax Address API Error', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}