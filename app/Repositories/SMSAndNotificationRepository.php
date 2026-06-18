<?php

namespace App\Repositories;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SMSAndNotificationRepository
{
    public function getDataForMess(
        mixed $idArr,
        string $messType,
        mixed $customMsgBody = null,
        mixed $checkBulkStdFlag = null,
        mixed $mobileNo = null,
        mixed $corporateCompany = null
    ): array {
        $returnData = [];

        if ($messType === 'forgetPassword') {

            $username = $idArr;
            $dbResponseArray = $this->getUserForgetMsg($username);

            $returnData = $this->getFormatedMessArraySingle( $dbResponseArray, $messType, $customMsgBody );

        } elseif ($messType === 'newUserCreate') {

            $username = $idArr;
            $dbResponseArray = $this->getUserCreateMsg($username);

            $msgArr = [
                'username' => $username,
                'password' => $customMsgBody,
            ];

            $returnData = $this->getFormatedMessArraySingle( $dbResponseArray, $messType, $msgArr );

        } elseif ($messType === 'registration') {

            $dbResponseArray = [
                [
                    'mob1' => $mobileNo,
                ],
            ];

            $returnData = $this->getFormatedMessArraySingle($dbResponseArray,$messType,$customMsgBody);

        } elseif ($messType === 'homeServicePending') {

            $dbResponseArray = [
                [
                    'mob1' => $mobileNo,
                ],
            ];

            $returnData = $this->getFormatedMessArraySingle($dbResponseArray,$messType,$customMsgBody);

        } elseif (
            $messType === config('constants.NOTIFICATION_CREATE_BOOKING_REQUEST_INDV')
            || $messType === config('constants.NOTIFICATION_UPDATE_BOOKING_REQUEST_INDV')
            || $messType === config('constants.NOTIFICATION_PROCESSING_BOOKING_REQUEST')
            || $messType === config('constants.NOTIFICATION_APPROVE_BOOKING_REQUEST')
            || $messType === config('constants.NOTIFICATION_UNAPPROVED_REJECT_BOOKING_REQUEST')
            || $messType === config('constants.NOTIFICATION_ASSIGN_VEHICLE') . '_DRIVER'
            || $messType === config('constants.NOTIFICATION_ASSIGN_VEHICLE') . '_INDV'
            || $messType === config('constants.NOTIFICATION_CHANGE_VEHICLE') . '_OLD_DRIVER'
            || $messType === config('constants.NOTIFICATION_CHANGE_VEHICLE') . '_INDV'
            || $messType === config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST') . '_INDV'
            || $messType === config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST') . '_FORWARD'
            || $messType === config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST') . '_INDV'
            || $messType === config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST') . '_DRIVER'
        ) {

            $dbResponseArray = $mobileNo;

            $returnData = $this->getFormatedMessArraySingle( $dbResponseArray, $messType, $customMsgBody, $corporateCompany);
        }

        return $returnData;
    }

    private function getUserCreateMsg(string $username): array
    {
        return DB::table('users')
            ->selectRaw('contact_no as mob1')
            ->where('username', $username)
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();
    }

    private function getUserForgetMsg(string $username): array
    {
        return DB::table('users')
            ->select('contact_no as mob1')
            ->where('username', $username)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }

    public function getSentSms(): array
    {
        return DB::table('sent_sms')
            ->orderBy('created_dt_tm', 'DESC')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }

    public function getFormatedMessArraySingle(
        array $responsedbdata,
        string $messType,
        mixed $customMsgBody,
        mixed $corporateCompany = null
    ): array {
        $msg = [];

        $smsConfiguration = get_sms_configuration($corporateCompany);

        $rowcount = 0;
        $messcount = 0;

        foreach ($responsedbdata as $row) {

            $mno = [];

            if (!empty($row['mob1'])) {
                $mno[0]['gsm'] = $row['mob1'];
            }

            if (count($mno) > 0) {

                $msg[$rowcount]['sender'] = $smsConfiguration['senderId'];

                $msgText = $this->getMessString( $row, $messType, $customMsgBody );

                $msg[$rowcount]['text'] = $msgText;
                $msg[$rowcount]['type'] = 'longSMS';

                $messcount += count_sms_part($msgText);

                $msg[$rowcount]['recipients'] = $mno;

                $rowcount++;
            }
        }

        return [
            'msgCount' => $messcount,
            'message'  => $msg,
        ];
    }

    private function getMessString(
        array $arraydata,
        string $messType,
        mixed $customMsgBody
    ): string {
        $mesg = '';

        if ($messType === 'forgetPassword') {

            $mesg = $customMsgBody;

        } elseif ($messType === 'newUserCreate') {

            if (app()->environment('production')) {

                $mesg = 'Welcome to Vroom FMS. Login URL is https://vroom24x7.com/Login . Your username is '
                    . $customMsgBody['username']
                    . ' and password is '
                    . $customMsgBody['password'];

            } elseif (app()->environment('production')) {

                // Preserved exactly from original logic
                $mesg = 'Welcome to Vroom FMS. Login URL is https://vroom24x7.com/demo/Login . Your username is '
                    . $customMsgBody['username']
                    . ' and password is '
                    . $customMsgBody['password'];

            } else {

                $mesg = 'Welcome to Vroom FMS. Login URL is https://vroom24x7.com/demo/Login . Your username is '
                    . $customMsgBody['username']
                    . ' and password is '
                    . $customMsgBody['password'];
            }

        } elseif ($messType === 'jobSms') {

            $mesg = $arraydata['message_body'];

        } elseif ($messType === 'registration') {

            $mesg = $customMsgBody;

        } elseif ($messType === 'homeServicePending') {

            $mesg = $customMsgBody;

        } elseif ($messType === config('constants.NOTIFICATION_CREATE_BOOKING_REQUEST_INDV')) {

            $mesg = $customMsgBody['person_name']
                . ' has requested for a vehicle from '
                . $customMsgBody['from_dt_tm']
                . ' to '
                . $customMsgBody['to_dt_tm']
                . '. His/her booking number is '
                . $customMsgBody['booking_no']
                . '.';

        } elseif ($messType === config('constants.NOTIFICATION_UPDATE_BOOKING_REQUEST_INDV')) {

            $mesg = $customMsgBody['person_name']
                . ' has changed the information of the vehicle booking request. His/her booking number is '
                . $customMsgBody['booking_no']
                . '.';

        } elseif ($messType === config('constants.NOTIFICATION_PROCESSING_BOOKING_REQUEST')) {

            $mesg = 'Your vehicle booking request is now in the processing state. The booking number is '
                . $customMsgBody['booking_no']
                . '.';

        } elseif ($messType === config('constants.NOTIFICATION_APPROVE_BOOKING_REQUEST')) {

            $mesg = 'Your vehicle booking request is approved. The booking number is '
                . $customMsgBody['booking_no']
                . '.';

        } elseif ($messType === config('constants.NOTIFICATION_UNAPPROVED_REJECT_BOOKING_REQUEST')) {

            $mesg = 'Your vehicle booking request is rejected. The booking number is '
                . $customMsgBody['booking_no']
                . '.';

        } elseif ($messType === config('constants.NOTIFICATION_ASSIGN_VEHICLE') . '_INDV') {

            $driverStr = '';

            if ($customMsgBody['driver_name']) {
                $driverStr = 'Driver: '
                    . $customMsgBody['driver_name']
                    . ' ('
                    . $customMsgBody['driver_mobile_no']
                    . ')';
            }

            $mesg = 'A vehicle has been assigned'
                . "\n"
                . 'Vehicle: '
                . $customMsgBody['registration_no']
                . "\n"
                . $driverStr
                . "\n"
                . 'Date: '
                . $customMsgBody['from_dt_tm']
                . "\n"
                . 'B.N.: '
                . $customMsgBody['booking_no'];

        } elseif ($messType === config('constants.NOTIFICATION_ASSIGN_VEHICLE') . '_DRIVER') {

            $mesg = 'A trip has been assigned'
                . "\n"
                . 'Vehicle: '
                . $customMsgBody['registration_no']
                . "\n"
                . 'T.W.: '
                . $customMsgBody['booking_person_name']
                . ' ('
                . $customMsgBody['booking_person_mobile_no']
                . ')'
                . "\n"
                . 'Date: '
                . $customMsgBody['from_dt_tm']
                . "\n"
                . 'B.N.: '
                . $customMsgBody['booking_no'];

        } elseif ($messType === config('constants.NOTIFICATION_CHANGE_VEHICLE') . '_INDV') {

            $driverStr = '';

            if ($customMsgBody['new_driver_name']) {
                $driverStr = 'Driver: '
                    . $customMsgBody['new_driver_name']
                    . ' ('
                    . $customMsgBody['new_driver_mobile_no']
                    . ')';
            }

            $mesg = 'The assigned vehicle has been changed and new info'
                . "\n"
                . 'Vehicle: '
                . $customMsgBody['new_registration_no']
                . "\n"
                . $driverStr
                . "\n"
                . 'Date: '
                . $customMsgBody['from_dt_tm']
                . "\n"
                . 'B.N.: '
                . $customMsgBody['booking_no'];

        } elseif ($messType === config('constants.NOTIFICATION_CHANGE_VEHICLE') . '_OLD_DRIVER') {

            $mesg = 'The trip has been canceled'
                . "\n"
                . 'Vehicle: '
                . $customMsgBody['old_registration_no']
                . "\n"
                . 'T.W.: '
                . $customMsgBody['booking_person_name']
                . '('
                . $customMsgBody['booking_person_mobile_no']
                . ')'
                . "\n"
                . 'Date: '
                . $customMsgBody['from_dt_tm']
                . "\n"
                . 'B.N.: '
                . $customMsgBody['booking_no'];

        } elseif ($messType === config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST') . '_INDV') {

            $mesg = 'Your vehicle booking request is forwarded to '
                . $customMsgBody['to_employee_name']
                . '. The booking number is '
                . $customMsgBody['booking_no']
                . '.';

        } elseif ($messType === config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST') . '_FORWARD') {

            $mesg = $customMsgBody['from_employee_name']
                . ' has forwarded a vehicle booking request of '
                . $customMsgBody['person_name']
                . ' to you. The booking number is '
                . $customMsgBody['booking_no']
                . '.';

        } elseif ($messType === config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST') . '_INDV') {

            $mesg = 'The approved vehicle booking request has been rejected'
                . "\n"
                . 'Date: '
                . $customMsgBody['from_dt_tm']
                . "\n"
                . 'B.N.: '
                . $customMsgBody['booking_no'];

        } elseif ($messType === config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST') . '_DRIVER') {

            $mesg = 'The trip has been canceled'
                . "\n"
                . 'Vehicle: '
                . $customMsgBody['registration_no']
                . "\n"
                . 'T.W.: '
                . $customMsgBody['booking_person_name']
                . ' ('
                . $customMsgBody['booking_person_mobile_no']
                . ')'
                . "\n"
                . 'Date: '
                . $customMsgBody['from_dt_tm']
                . "\n"
                . 'B.N.: '
                . $customMsgBody['booking_no'];
        }

        return $mesg;
    }

    public function inertSentSmsData(array $arr): void
    {
        DB::table('sent_sms')->insert($arr);
    }

}