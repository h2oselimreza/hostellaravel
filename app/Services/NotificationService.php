<?php

namespace App\Services;

use App\Models\Client\VehicleBookingSummary as ClientVehicleBookingSummary;
use App\Models\CompanyNotificationPermission;
use App\Models\CompanySetting;
use App\Models\CustomerEmployee;
use App\Models\SentSMS;
use App\Models\VehicleBookingSummary;
use App\Repositories\SMSAndNotificationRepository;
use App\Services\SmsService;
use App\Services\MailSendService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected SmsService $smsService;
    //protected MailSendService $mailSendService;
    protected SMSAndNotificationRepository $smsAndNotificationRepository;
    protected MailSendService $mailSendService;

    public function __construct(
        SmsService $smsService,
        SMSAndNotificationRepository $smsAndNotificationRepository,
        MailSendService $mailSendService
        //MailSendService $mailSendService
    ) {
        $this->smsService      = $smsService;
        $this->smsAndNotificationRepository = $smsAndNotificationRepository;
        $this->mailSendService = $mailSendService;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK NOTIFICATION PERMISSION
    |--------------------------------------------------------------------------
    */
    public function checkNotificationPermission(
        string $notificationType,
        ?string $corporateCompanyCode
    ): bool {

        $companyNotificationPermissions = $this->getCompanyNotificationPermissions( $corporateCompanyCode );

        $permittedNotifications = $this->convertPermissionDBRowToArray( $companyNotificationPermissions );

        return in_array( $notificationType, $permittedNotifications
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE AND SEND NOTIFICATION
    |--------------------------------------------------------------------------
    */
    public function generateAndSendNotification(
        array $notification
    ): bool {
        Log::channel('notification')->info('Notification Data'. json_encode($notification));
        if (
            !$this->checkNotificationPermission(
                $notification['type'],
                $notification['corporate_company'] ?? null
            )
        ) {
            return false;
        }

        $corporateCompanyCode = "";
        $contactAddress       = [];

        /*
        |--------------------------------------------------------------------------
        | CREATE / UPDATE BOOKING REQUEST
        |--------------------------------------------------------------------------
        */
        //dd($notification['corporate_company']);
        if (
            $notification['type'] == config('constants.NOTIFICATION_CREATE_BOOKING_REQUEST_INDV')
            ||
            $notification['type'] == config('constants.NOTIFICATION_UPDATE_BOOKING_REQUEST_INDV')
        ) {

            $contactAddress = CustomerEmployee::query()

                ->select([
                    'primary_mobile as mob1',
                    'email',
                ])

                ->where(
                    'company',
                    $notification['corporate_company']
                )

                ->where('is_active', 1)

                ->where('emp_type', 'system_manager')

                ->get()

                ->toArray();

            Log::channel('notification')->info('Notification email and mobile number'. json_encode($contactAddress));
            $corporateCompanyCode = $notification['corporate_company'] ?? '';
        }

        /*
        |--------------------------------------------------------------------------
        | PROCESSING / APPROVE / REJECT
        |--------------------------------------------------------------------------
        */
        elseif (
            $notification['type'] == config('constants.NOTIFICATION_PROCESSING_BOOKING_REQUEST')
            ||
            $notification['type'] == config('constants.NOTIFICATION_APPROVE_BOOKING_REQUEST')
            ||
            $notification['type'] == config('constants.NOTIFICATION_UNAPPROVED_REJECT_BOOKING_REQUEST')
        ) {

            $contactAddress = CustomerEmployee::query()

                ->select([
                    'primary_mobile as mob1',
                    'email',
                ])

                ->where(
                    'employee_id',
                    $notification['person_employee_id']
                )

                ->get()

                ->toArray();
            Log::channel('notification')->info('Booking processing/approve/Reject email and mobile number'. json_encode($contactAddress));
            $corporateCompanyCode = $notification['corporate_company'] ?? '';
        }

        /*
        |--------------------------------------------------------------------------
        | ASSIGN VEHICLE
        |--------------------------------------------------------------------------
        */
        elseif (
            $notification['type'] == config('constants.NOTIFICATION_ASSIGN_VEHICLE')
        ) {

            $vehicleBooking =
                $this->vehicleAssignNotificationDetails(
                    $notification['booking_no']
                );

            if (!$vehicleBooking) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | INDIVIDUAL USER
            |--------------------------------------------------------------------------
            */
            $notification['type'] = config('constants.NOTIFICATION_ASSIGN_VEHICLE') . '_INDV';

            $notification['registration_no'] = $vehicleBooking->registration_no;

            $notification['from_dt_tm'] = get_date_time_format( $vehicleBooking->from_dt_tm_confirmed );

            $notification['booking_person_name'] = $vehicleBooking->booking_person_name;

            $notification['driver_name'] = $vehicleBooking->driver_name;

            $notification['driver_mobile_no'] = $vehicleBooking->driver_mobile_no;

            $contactAddress[] = [
                'mob1' => $vehicleBooking->booking_person_mobile_no,
                'email' => $vehicleBooking->booking_person_email,
            ];

            $corporateCompanyCode = $notification['corporate_company'] ?? '';

            $this->smsNotification($notification,$contactAddress,$corporateCompanyCode);

            $this->emailNotification($notification,$contactAddress);

            /*
            |--------------------------------------------------------------------------
            | DRIVER
            |--------------------------------------------------------------------------
            */
            $contactAddress = [];

            $notification['type'] = config('constants.NOTIFICATION_ASSIGN_VEHICLE') . '_DRIVER';

            $notification['driver_name'] = $vehicleBooking->driver_name;

            $notification['booking_person_mobile_no'] = $vehicleBooking->booking_person_mobile_no;

            $contactAddress[] = [
                'mob1' => $vehicleBooking->driver_mobile_no,
                'email' => $vehicleBooking->driver_email,
            ];

            $this->smsNotification($notification,$contactAddress,$corporateCompanyCode);

            $this->emailNotification($notification,$contactAddress);

            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | CHANGE VEHICLE
        |--------------------------------------------------------------------------
        */
        elseif (
            $notification['type'] == config('constants.NOTIFICATION_CHANGE_VEHICLE')
        ) {

            $vehicleBooking = $this->vehicleAssignNotificationDetails($notification['booking_no']);

            if (!$vehicleBooking) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | NEW DRIVER
            |--------------------------------------------------------------------------
            */
            $contactAddress = [];

            $notification['type'] = config('constants.NOTIFICATION_ASSIGN_VEHICLE') . '_DRIVER';

            $notification['registration_no'] = $vehicleBooking->registration_no;

            $notification['driver_name'] = $vehicleBooking->driver_name;

            $notification['from_dt_tm'] = get_date_time_format( $vehicleBooking->from_dt_tm_confirmed );

            $notification['booking_person_name'] = $vehicleBooking->booking_person_name;

            $notification['booking_person_mobile_no'] = $vehicleBooking->booking_person_mobile_no;

            $contactAddress[] = [
                'mob1' => $vehicleBooking->driver_mobile_no,
                'email' => $vehicleBooking->driver_email,
            ];

            $corporateCompanyCode = $notification['corporate_company'] ?? '';

            $this->smsNotification( $notification, $contactAddress, $corporateCompanyCode );

            $this->emailNotification( $notification, $contactAddress );

            /*
            |--------------------------------------------------------------------------
            | INDIVIDUAL USER
            |--------------------------------------------------------------------------
            */
            $contactAddress = [];

            $notification['type'] = config('constants.NOTIFICATION_CHANGE_VEHICLE') . '_INDV';
            $notification['new_registration_no'] =  $vehicleBooking->registration_no;
            $notification['old_registration_no'] = $notification['oldVehicleBooking']->registration_no;
            $notification['booking_person_name'] = $vehicleBooking->booking_person_name;
            $notification['new_driver_name'] = $vehicleBooking->driver_name;
            $notification['new_driver_mobile_no'] = $vehicleBooking->driver_mobile_no;

            $contactAddress[] = [
                'mob1' =>
                    $vehicleBooking->booking_person_mobile_no,

                'email' =>
                    $vehicleBooking->booking_person_email,
            ];

            $this->smsNotification( $notification, $contactAddress, $corporateCompanyCode );

            $this->emailNotification( $notification, $contactAddress );

            /*
            |--------------------------------------------------------------------------
            | OLD DRIVER
            |--------------------------------------------------------------------------
            */
            $contactAddress = [];

            $notification['type'] = config('constants.NOTIFICATION_CHANGE_VEHICLE'). '_OLD_DRIVER';

            $notification['from_dt_tm'] = get_date_time_format(
                    $notification['oldVehicleBooking']->from_dt_tm_confirmed
                );

            $notification['driver_name'] = $notification['oldVehicleBooking']->driver_name;

            $contactAddress[] = [
                'mob1' => $notification['oldVehicleBooking']->driver_mobile_no,

                'email' => $notification['oldVehicleBooking']->driver_email,
            ];

            $this->smsNotification($notification,$contactAddress,$corporateCompanyCode);

            $this->emailNotification($notification,$contactAddress);

            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | FORWARD BOOKING REQUEST
        |--------------------------------------------------------------------------
        */
        elseif (
            $notification['type'] == config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST')
        ) {

            $corporateCompanyCode = $notification['corporate_company'] ?? '';

            $applicationFrom = CustomerEmployee::query()

                ->where(
                    'employee_id',
                    $notification['application_from']
                )

                ->first();

            $applicationTo = CustomerEmployee::query()

                ->where(
                    'employee_id',
                    $notification['application_to']
                )

                ->first();

            $notification['from_employee_name'] = $applicationFrom?->employee_name;

            $notification['to_employee_name'] = $applicationTo?->employee_name;

            $notification['to_employee_mobile_no'] = $applicationTo?->primary_mobile;

            $notification['to_employee_email'] = $applicationTo?->email;

            /*
            |--------------------------------------------------------------------------
            | INDIVIDUAL
            |--------------------------------------------------------------------------
            */
            $contactAddress = [];

            $notification['type'] = config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST'). '_INDV';

            $contactAddress[] = [
                'mob1' => $notification['person_mobile_no'],
                'email' => $notification['person_email'],
            ];

            $this->smsNotification( $notification, $contactAddress, $corporateCompanyCode);

            $this->emailNotification( $notification, $contactAddress );

            /*
            |--------------------------------------------------------------------------
            | FORWARDED EMPLOYEE
            |--------------------------------------------------------------------------
            */
            $contactAddress = [];

            $notification['type'] = config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST'). '_FORWARD';

            $contactAddress[] = [
                'mob1' => $notification['to_employee_mobile_no'],
                'email' => $notification['to_employee_email'],
            ];

            $this->smsNotification( $notification, $contactAddress,  $corporateCompanyCode);

            $this->emailNotification($notification,$contactAddress);

            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | APPROVED REJECT BOOKING REQUEST
        |--------------------------------------------------------------------------
        */
        elseif (
            $notification['type'] == config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST')
        ) {

            $vehicleBooking = $this->vehicleAssignNotificationDetails($notification['booking_no']);

            $corporateCompanyCode = $notification['corporate_company'] ?? '';

            if (!$vehicleBooking) {
                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | INDIVIDUAL USER
            |--------------------------------------------------------------------------
            */
            $notification['type'] = config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST'). '_INDV';

            $notification['from_dt_tm'] = get_date_time_format( $vehicleBooking->from_dt_tm_confirmed );

            $notification['booking_person_name'] = $vehicleBooking->booking_person_name;

            $contactAddress[] = [
                'mob1' => $vehicleBooking->booking_person_mobile_no,

                'email' => $vehicleBooking->booking_person_email,
            ];

            $this->smsNotification( $notification, $contactAddress, $corporateCompanyCode );

            $this->emailNotification( $notification, $contactAddress );

            /*
            |--------------------------------------------------------------------------
            | DRIVER
            |--------------------------------------------------------------------------
            */
            if (
                $vehicleBooking->vehicle && $vehicleBooking->driver_name
            ) {

                $contactAddress = [];

                $notification['type'] = config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST'). '_DRIVER';

                $notification['driver_name'] = $vehicleBooking->driver_name;

                $notification['booking_person_mobile_no'] = $vehicleBooking->booking_person_mobile_no;

                $notification['registration_no'] = $vehicleBooking->registration_no;

                $contactAddress[] = [
                    'mob1' => $vehicleBooking->driver_mobile_no,
                    'email' => $vehicleBooking->driver_email,
                ];

                $this->smsNotification( $notification, $contactAddress, $corporateCompanyCode );

                $this->emailNotification( $notification, $contactAddress );
            }

            return true;
        }

        else {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | SEND SMS & EMAIL
        |--------------------------------------------------------------------------
        */
        Log::channel('notification')->info('Notification Final Data'. json_encode($contactAddress));
        //dd($notification, $contactAddress, $corporateCompanyCode);
        $this->smsNotification( $notification, $contactAddress, $corporateCompanyCode );

        $this->emailNotification( $notification, $contactAddress );

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | SMS NOTIFICATION
    |--------------------------------------------------------------------------
    */
    private function smsNotification(
        array $notification,
        array $mobileNo,
        ?string $corporateCompanyCode
    ): bool {
        // Log::channel('daily')->info('dddddddddd',debug_backtrace(json_decode(0,3)));
        $idArr        = "";
        $customMessage = $notification;

        $responseDbData =
            $this->smsAndNotificationRepository->getDataForMess(
                $idArr,
                $notification['type'],
                $customMessage,
                null,
                $mobileNo,
                $corporateCompanyCode
            );

        //dd($responseDbData,debug_backtrace(json_decode(0,3)));
        if (($responseDbData['msgCount'] ?? 0) == 0) {
            return false;
        }
        
        Log::channel('sms')->info('SMS Data'. json_encode($responseDbData));

        if (
            app()->environment([ 'production', 'development'])
        ) {
            //dd($responseDbData, $corporateCompanyCode);
            $this->smsService->sendMessage( $responseDbData['message'], $corporateCompanyCode );
        }

        $this->insertSentSmsData( $responseDbData, $notification );

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT SENT SMS DATA
    |--------------------------------------------------------------------------
    */
    private function insertSentSmsData(
        array $responseDbData,
        array $notification
    ): void {

        foreach ($responseDbData['message'] as $message) {

            $refNo = reference_no();

            $smsArr = [

                'reference_number' => $refNo,
                'sms_template' => $notification['type'],
                'sms_count' => count_sms_part($message['text']),
                'custom_sms' => $message['text'],
                'channel_type' => 'mobileNo',
                'module_type' => $notification['type'],
                'mobile_no' => $message['recipients'][0]['gsm'],
                'created_by' => Auth::user()->user_id,
                'created_type' => "",
                'created_dt_tm' => now(),
                'updated_by' => Auth::user()->user_id,
                'updated_type' => "",
                'updated_dt_tm' => now(),
            ];

            SentSMS::create($smsArr);

            Log::channel('sms')->info(
                'sent_sms',
                [
                    'reference_number' => $refNo,
                    'message'          => $smsArr['custom_sms'],
                ]
            );
        }
    }

    private function emailNotification(array $notification, array $emails): bool
    {
        $subject = '';
        $body = '';

        if ($notification['type'] == config('constants.NOTIFICATION_CREATE_BOOKING_REQUEST_INDV')) {

            $subject = "New vehicle booking request";
            $body = "Dear Concern, <br>{$notification['person_name']} has requested for a vehicle from {$notification['from_dt_tm']} to {$notification['to_dt_tm']}. His/her booking number is {$notification['booking_no']}.";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_UPDATE_BOOKING_REQUEST_INDV')) {

            $subject = "Change the information of vehicle booking request";
            $body = "Dear Concern, <br>{$notification['person_name']} has changed the information of vehicle booking request. His/her booking number is {$notification['booking_no']}.";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_PROCESSING_BOOKING_REQUEST')) {

            $subject = "Processing vehicle booking request";
            $body = "Dear {$notification['person_name']}, <br>Your vehicle booking request is now in processing state. The booking number is {$notification['booking_no']}.";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_APPROVE_BOOKING_REQUEST')) {

            $subject = "Approve vehicle booking request";
            $body = "Dear {$notification['person_name']}, <br>Your vehicle booking request is approved. The booking number is {$notification['booking_no']}.";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_UNAPPROVED_REJECT_BOOKING_REQUEST')) {

            $subject = "Reject vehicle booking request";
            $body = "Dear {$notification['person_name']}, <br>Your vehicle booking request is rejected. The booking number is {$notification['booking_no']}.";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_ASSIGN_VEHICLE') . "_INDV") {

            $subject = "Assigned a vehicle according to booking request";

            $driverStr = '';

            if (!empty($notification['driver_name'])) {
                $driverStr = "<br>Driver: {$notification['driver_name']} ({$notification['driver_mobile_no']})";
            }

            $body = "Dear {$notification['booking_person_name']},
                    <br>A vehicle has been assigned.
                    <br>Vehicle: {$notification['registration_no']}
                    {$driverStr}
                    <br>Date: {$notification['from_dt_tm']}
                    <br>B.N.: {$notification['booking_no']}";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_ASSIGN_VEHICLE') . "_DRIVER") {

            $subject = "Assigning to a new trip";

            $body = "Dear {$notification['driver_name']},
                    <br>A trip has been assigned.
                    <br>Vehicle: {$notification['registration_no']}
                    <br>T.W.: {$notification['booking_person_name']} ({$notification['booking_person_mobile_no']})
                    <br>Date: {$notification['from_dt_tm']}
                    <br>B.N.: {$notification['booking_no']}";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_CHANGE_VEHICLE') . "_INDV") {

            $subject = "Changed assigned vehicle";

            $driverStr = '';

            if (!empty($notification['new_driver_name'])) {
                $driverStr = "<br>Driver: {$notification['new_driver_name']} ({$notification['new_driver_mobile_no']})";
            }

            $body = "Dear {$notification['booking_person_name']},
                    <br>The assigned vehicle has been changed.
                    <br>New info:
                    <br>Vehicle: {$notification['new_registration_no']}
                    {$driverStr}
                    <br>Date: {$notification['from_dt_tm']}
                    <br>B.N.: {$notification['booking_no']}";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_CHANGE_VEHICLE') . "_OLD_DRIVER") {

            $subject = "Cancelled your trip";

            $body = "Dear {$notification['driver_name']},
                    <br>The trip has been canceled.
                    <br>Vehicle: {$notification['old_registration_no']}
                    <br>T.W.: {$notification['booking_person_name']} ({$notification['booking_person_mobile_no']})
                    <br>Date: {$notification['from_dt_tm']}
                    <br>B.N.: {$notification['booking_no']}";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST') . "_INDV") {

            $subject = "Forwarded vehicle booking request";

            $body = "Dear {$notification['person_name']},
                    <br>Your vehicle booking request is forwarded to {$notification['to_employee_name']}.
                    The booking number is {$notification['booking_no']}.";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_FORWARD_BOOKING_REQUEST') . "_FORWARD") {

            $subject = "Forwarded vehicle booking request";

            $body = "Dear {$notification['to_employee_name']},
                    <br>{$notification['from_employee_name']} has forwarded a vehicle booking request of {$notification['person_name']} to you.
                    The booking number is {$notification['booking_no']}.";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST') . "_INDV") {

            $subject = "Reject vehicle booking request";

            $body = "Dear {$notification['booking_person_name']},
                    <br>The approved vehicle booking request has been rejected.
                    <br>Date: {$notification['from_dt_tm']}
                    <br>B.N.: {$notification['booking_no']}";

        } elseif ($notification['type'] == config('constants.NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST') . "_DRIVER") {

            $subject = "Canceled your trip";

            $body = "Dear {$notification['driver_name']},
                    <br>The trip has been cancelled.
                    <br>Vehicle: {$notification['registration_no']}
                    <br>T.W.: {$notification['booking_person_name']} ({$notification['booking_person_mobile_no']})
                    <br>Date: {$notification['from_dt_tm']}
                    <br>B.N.: {$notification['booking_no']}";

        } else {
            return false;
        }

        $toEmails = collect($emails)
            ->pluck('email')
            ->filter()
            ->toArray();

        if (empty($toEmails)) {
            return false;
        }

        $mailArr = [
            'toMailMultiple' => $toEmails,
            'mailHeading'    => $subject,
            'mailBody'       => $body,
            'customerMail'   => '',
        ];
        
        Log::channel('mail')->info('Mail Data'. json_encode($mailArr));

        if (app()->environment(['production', 'development'])) {
            $this->mailSendService->mailSend(
                $mailArr
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | VEHICLE ASSIGN NOTIFICATION DETAILS
    |--------------------------------------------------------------------------
    */
    private function vehicleAssignNotificationDetails(
        string $bookingNo
    ) {

        //return VehicleBookingSummary::query()
        return ClientVehicleBookingSummary::query()
            ->select([
                'vehicle_booking_summary.*',

                'vehicles.vehicle_id',
                'vehicles.driver_id',
                'vehicles.registration_no',

                'driver_tb.employee_name as driver_name',
                'driver_tb.email as driver_email',
                'driver_tb.primary_mobile as driver_mobile_no',

                'booking_person_tb.employee_id',
                'booking_person_tb.employee_name as booking_person_name',
                'booking_person_tb.primary_mobile as booking_person_mobile_no',
                'booking_person_tb.email as booking_person_email',
            ])

            ->leftJoin(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'vehicle_booking_summary.vehicle'
            )

            ->leftJoin(
                'customer_employee as driver_tb',
                'driver_tb.employee_id',
                '=',
                'vehicles.driver_id'
            )

            ->leftJoin(
                'customer_employee as booking_person_tb',
                'booking_person_tb.employee_id',
                '=',
                'vehicle_booking_summary.person_emp_id'
            )

            ->where(
                'vehicle_booking_summary.booking_no',
                $bookingNo
            )

            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | GET NOTIFICATION PERMISSION MASTER DATA
    |--------------------------------------------------------------------------
    */
    public function getNotificationPermissionMasterData()
    {
        return CompanySetting::query()

            ->where(
                'setting_type',
                'notification_permission'
            )

            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE PERMISSION
    |--------------------------------------------------------------------------
    */
    public function savePermission(
        array $insertArr,
        string $companyCode
    ): void {

        DB::transaction(function () use (
            $insertArr,
            $companyCode
        ) {

            CompanyNotificationPermission::query()

                ->where('company', $companyCode)

                ->delete();

            if (!empty($insertArr)) {

                CompanyNotificationPermission::insert(
                    $insertArr
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | GET COMPANY NOTIFICATION PERMISSIONS
    |--------------------------------------------------------------------------
    */
    public function getCompanyNotificationPermissions(
        ?string $companyCode
    ) {

        return CompanyNotificationPermission::query()

            ->where('company', $companyCode)

            ->get()

            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | CONVERT DB ROW TO ARRAY
    |--------------------------------------------------------------------------
    */
    public function convertPermissionDBRowToArray(
        array $companyNotificationPermissions
    ): array {

        $arr = [];

        foreach (
            $companyNotificationPermissions
            as $companyNotificationPermission
        ) {

            $arr[] =
                $companyNotificationPermission[
                    'notification_code'
                ];
        }

        return $arr;
    }

    public function getFcmToken(): array
    {
        return DB::table('device_fcm')
            ->select('fcm_token')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }
}