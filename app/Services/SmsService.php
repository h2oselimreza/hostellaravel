<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsService
{
    /**
     * Send SMS via Configured Provider
     * * @param array $responsedbdata
     * @param string|null $corporateCompanyCode
     * @return string|null
     */
    public function sendMessage(array $responsedbdata, ?string $corporateCompanyCode = null): ?string
    {
        try {
            // 1. Fetch SMS configuration using your config files/helpers
            // (Ideally mapped via config('sms.corporate') or database config)
            $smsConfiguration = $this->getSmsConfiguration($corporateCompanyCode);

            // 2. Build payload structure matching your exact provider format
            $payload = [
                'authentication' => [
                    'username' => $smsConfiguration['username'] ?? '',
                    'password' => $smsConfiguration['password'] ?? '',
                ],
                'messages' => $responsedbdata
            ];
            //dd($payload);
            // 3. Make the API request using Laravel's native HTTP wrapper
            // Mimicking the original "JSON=" URL-encoded payload format
            $response = Http::asForm()
                ->withoutVerifying() // Equivalent to CURLOPT_SSL_VERIFYPEER => false
                ->post($smsConfiguration['url'], [
                    'JSON' => json_encode($payload)
                ]);

            Log::channel('sms')->info('SMS Send Body:', [
                'body' => $response->getBody()->getContents(),
            ]);
            //dd($response->getBody()->getContents());
            // 4. Return response string if successful
            if ($response->successful()) {
                Log::channel('sms')->info('SMS sent successfully');
                return $response->body();
            }

            Log::channel('sms')->error('SMS Send Error:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return null;

        } catch (Throwable $e) {
            Log::channel('sms')->error('SMS Service Exception: ' . $e->getMessage(), [
                'exception' => $e,
                'corporateCode' => $corporateCompanyCode,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return null;
        }
    }

    /**
     * Mock helper method to match your original configuration pull.
     * Adapt this to grab values from config/services.php or your database.
     */
    protected function getSmsConfiguration(?string $corporateCompanyCode): array
    {
        // Example fallback leveraging Laravel config() helper
        return [
            'username' => config('services.sms.username'),
            'password' => config('services.sms.password'),
            'url'      => config('services.sms.url'),
            'senderId' => config('constants.sms.sms_sender_id')
        ];
    }
}