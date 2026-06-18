<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Support\Facades\DB;

class TokenService
{
    /**
     * Generate and return the next token number by code
     *
     * @param string|null $code
     * @return string
     */
    public function getTokenByCode(string $code): string
    {
        if (!$code) {
            throw new \InvalidArgumentException("Code is required");
        }

        return DB::transaction(function () use ($code) {

            // Lock the row for update to prevent race conditions
            $token = DB::table('token')->where('code', $code)->lockForUpdate()->first();

            if (!$token) {
                // If token doesn't exist, create it starting at 1
                DB::table('token')->insert([
                    'code' => $code,
                    'tokenNo' => 1
                ]);
                $tokenNo = 1;
            } else {
                // Increment tokenNo by 1
                $tokenNo = $token->tokenNo + 1;
                DB::table('token')->where('code', $code)->update([
                    'tokenNo' => $tokenNo
                ]);
            }

            // Return tokenNo with leading zeros
            return str_pad($tokenNo, 5, '0', STR_PAD_LEFT);
        });
    }
}
