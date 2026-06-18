<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OtpRepository
{

    public function checkOtpGenerate($mobile)
    {
        $otp = DB::table('otp')
            ->where('mobile_no', $mobile)
            ->first();

        // No OTP exists
        if (!$otp) {
            return [
                'generateFlag' => 1,
            ];
        }

        $nowTime = Carbon::now()->timestamp;
        $otpCreationTime = Carbon::parse($otp->created_dt_tm)->timestamp;

        if (($nowTime - $otpCreationTime) <= config('constants.OTP_IDLE_TIME')) {
            return [
                'generateFlag'   => 2,
                'otpCreatedDtTm' => $otp->created_dt_tm,
            ];
        }

        return [
            'generateFlag' => 1,
        ];
    }

    public function insertOtp(array $data)
    {
        $exists = DB::table('otp')
            ->where('mobile_no', $data['mobile_no'])
            ->exists();

        if ($exists) {
            $this->deleteOtp($data['mobile_no']);
        }

        $otpId = DB::table('otp')->insertGetId($data);

        $logData = $data;
        $logData['otp_id'] = $otpId;

        DB::table('otp_log')->insert($logData);
    }

    private function deleteOtp(string $mobileNo)
    {
        DB::table('otp')
            ->where('mobile_no', $mobileNo)
            ->delete();
    }

    public function checkOtp($mobile, $otp, $deleteFlag)
    {
        $otpRecord = Otp::where('mobile_no', $mobile)->first();

        if (!$otpRecord) {
            return 4;
        }

        $encryptedOtp = $otpRecord->encrypted_otp;

        $nowTime = Carbon::now()->timestamp;
        $otpCreationTime = Carbon::parse($otpRecord->created_dt_tm)->timestamp;

        if (($nowTime - $otpCreationTime) <= config('constants.OTP_IDLE_TIME')) {

            if ((string) md5(trim($otp)) === (string) trim($encryptedOtp)) {
                if ($deleteFlag == 1) {
                    $this->deleteOtp($mobile);
                }

                return 1;
            }

            return 2; // OTP does not match
        }

        $this->deleteOtp($mobile);

        return 3; // OTP expired
    }
}