<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /*
    |--------------------------------------------------------------------------
    | SEND SMS
    |--------------------------------------------------------------------------
    */
    public function sendMessage(
        array $responseDbData,
        ?string $corporateCompanyCode = null
    ): mixed {

        try {

            /*
            |--------------------------------------------------------------------------
            | GET SMS CONFIGURATION
            |--------------------------------------------------------------------------
            */
            $smsConfiguration =
                get_sms_configuration($corporateCompanyCode);

            /*
            |--------------------------------------------------------------------------
            | AUTHENTICATION
            |--------------------------------------------------------------------------
            */
            $auth = [
                'username' => $smsConfiguration['username'],
                'password' => $smsConfiguration['password'],
            ];

            /*
            |--------------------------------------------------------------------------
            | REQUEST DATA
            |--------------------------------------------------------------------------
            */
            $data = [
                'authentication' => $auth,
                'messages'       => $responseDbData,
            ];

            /*
            |--------------------------------------------------------------------------
            | API BODY
            |--------------------------------------------------------------------------
            */
            $postData =
                'JSON=' . urlencode(json_encode($data));

            /*
            |--------------------------------------------------------------------------
            | SEND REQUEST
            |--------------------------------------------------------------------------
            */
            $response = Http::withOptions([
                    'verify' => false,
                ])

                ->withHeaders([
                    'Content-Type' =>
                        'application/x-www-form-urlencoded',
                ])

                ->asForm()

                ->post(
                    $smsConfiguration['url'],
                    [
                        'JSON' => json_encode($data),
                    ]
                );

            /*
            |--------------------------------------------------------------------------
            | LOG SUCCESS RESPONSE
            |--------------------------------------------------------------------------
            */
            Log::info('SMS API Response', [
                'company_code' => $corporateCompanyCode,
                'response'     => $response->body(),
            ]);

            return $response->body();

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | LOG ERROR
            |--------------------------------------------------------------------------
            */
            Log::error('SMS Sending Failed', [

                'message' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile(),

                'company_code' => $corporateCompanyCode,
            ]);

            return false;
        }
    }
}