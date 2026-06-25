<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateMonthlyToken
{
    // public function generateMonthlyToken($code = null)
    // {
    //     if (!$code) {
    //         return '0001';
    //     }

    //     return DB::transaction(function () use ($code) {

    //         $token = Token::where('code', $code)
    //             ->lockForUpdate()
    //             ->first();
    //         if (!$token) {
    //             Token::create([
    //                 'code'     => $code,
    //                 'dt'       => now()->toDateString(),
    //                 'tokenNo'  => '0001',
    //             ]);

    //             return '0001';
    //         }

    //         $expMonth   = Carbon::parse($token->dt)->format('Y-m');
    //         $todayMonth = now()->format('Y-m');

    //         if ($todayMonth > $expMonth) {
    //             $token->update([
    //                 'dt'      => now()->toDateString(),
    //                 'tokenNo' => '0001',
    //             ]);

    //             return '0001';
    //         }

    //         $nextTokenNo = str_pad(((int) $token->tokenNo) + 1, 4, '0', STR_PAD_LEFT);

    //         $token->update([
    //             'dt'      => now()->toDateString(),
    //             'tokenNo' => $nextTokenNo,
    //         ]);

    //         return $nextTokenNo;
    //     });
    // }


    function get_month_token($code = null)
    {
        if (!$code) {
            return null;
        }

        return DB::transaction(function () use ($code) {

            // 🔒 Lock row to prevent duplicate token
            $token = DB::table('token')
                ->where('code', $code)
                ->lockForUpdate()
                ->first();

            if (!$token) {
                return null;
            }

            $today = Carbon::now();
            $todayMonth = $today->format('Y-m');

            $expMonth = $token->dt
                ? Carbon::parse($token->dt)->format('Y-m')
                : null;

            // ✅ Reset if month changed
            if ($todayMonth > $expMonth) {

                $newTokenNo = '0001';

                DB::table('token')
                    ->where('code', $code)
                    ->update([
                        'dt' => $today->format('Y-m-d'),
                        'tokenNo' => $newTokenNo,
                    ]);

            } else {

                $increment = (int) $token->tokenNo + 1;

                $newTokenNo = str_pad($increment, 4, "0", STR_PAD_LEFT);

                DB::table('token')
                    ->where('code', $code)
                    ->update([
                        'dt' => $today->format('Y-m-d'),
                        'tokenNo' => $newTokenNo,
                    ]);
            }

            return $today->format('ym') . $newTokenNo;
        });
    }
}