<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class PrayerService
{
    public function getPrayerTime()
    {
        $crrDate = Carbon::now()->format('Y-m-d');

        $row = DB::table('web_prayer_time')->first();

        if (!$row) {
            return null;
        }

        $prayerArr = [
            'fajr'       => $row->fajr,
            'zuhor'      => $row->zuhor,
            'asor'       => $row->asor,
            'maghrib'    => $row->maghrib,
            'isha'       => $row->isha,
            'jumma'      => $row->jumma,
            'sunrise'    => $row->sunrise,
            'sunset'     => $row->sunset,
            'prayer_date'=> $crrDate,
        ];

        if ($row->prayer_date == $crrDate) {
            return $prayerArr;
        }

        // API call
        $date = Carbon::now()->format('d-m-Y');

        $response = Http::get("https://api.aladhan.com/v1/timingsByCity/$date", [
            'city' => 'Dhaka',
            'country' => 'Bangladesh',
            'method' => 2
        ]);

        $data = $response->json();

        if ($data && $data['code'] == 200) {

            $timings = $data['data']['timings'];

            $prayerArr['fajr']    = Carbon::parse($timings['Fajr'])->format('h:i A');
            $prayerArr['zuhor']   = Carbon::parse($timings['Dhuhr'])->format('h:i A');
            $prayerArr['asor']    = Carbon::parse($timings['Asr'])->format('h:i A');
            $prayerArr['maghrib'] = Carbon::parse($timings['Maghrib'])->format('h:i A');
            $prayerArr['isha']    = Carbon::parse($timings['Isha'])->format('h:i A');
            $prayerArr['sunrise'] = Carbon::parse($timings['Sunrise'])->format('h:i A');
            $prayerArr['sunset']  = Carbon::parse($timings['Sunset'])->format('h:i A');
        }

        DB::table('web_prayer_time')->update([
            'fajr' => $prayerArr['fajr'],
            'zuhor' => $prayerArr['zuhor'],
            'asor' => $prayerArr['asor'],
            'maghrib' => $prayerArr['maghrib'],
            'isha' => $prayerArr['isha'],
            'sunrise' => $prayerArr['sunrise'],
            'sunset' => $prayerArr['sunset'],
            'prayer_date' => $crrDate,
            'updated_by' => 'system',
            'updated_dt_tm' => Carbon::now()->toDateTimeString(),
        ]);

        return $prayerArr;
    }
}