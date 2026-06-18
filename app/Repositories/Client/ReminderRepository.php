<?php

namespace App\Repositories\Client;

use App\Models\Client\Vehicle;
use App\Models\Client\VehicleAssignDetail;
use App\Models\Client\VehicleBookingSummary;
use App\Models\Company;
use App\Models\CustomerEmployee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReminderRepository
{
    public function getDefaultReminderTo($companyCode, $reminderFor)
    {
        return DB::table('default_reminders')
            ->where('company', $companyCode)
            ->where('reminder_for', $reminderFor)
            ->get();
    }

    public function addReminder($arr)
    {
        DB::table('reminder')->insert($arr);
        return 1;
    }

    public function getReminderList($companyCode)
    {
        return DB::table('reminder')
            ->where('company', $companyCode)
            ->orderBy('created_dt_tm', 'DESC')
            ->get();
    }

    public function removeReminder($reminderNo, $companyCode)
    {
        DB::table('reminder')
            ->where('reminder_no', $reminderNo)
            ->where('company', $companyCode)
            ->delete();

        return 1;
    }

    public function getDefaultReminder($companyCode)
    {
        return DB::table('default_reminders')
            ->where('company', $companyCode)
            ->get();
    }

    /**
     * Set default reminder recipients/numbers for all matching reminders.
     *
     * @param array $arr
     * @return void
     */
    public function setDefaultReminderTo(array $arr): void
    {
        /*
        |---
        | GET ALL REMINDER NOS EXCEPT CURRENT ID
        |-----------------
        */
        $reminderNoArr = DB::table('default_reminders')
            ->whereNotIn('id', (array) $arr['id'])
            ->where('company', $arr['companyCode'])
            ->where('reminder_for', $arr['reminderFor'])
            ->where('reminder_channel_type', $arr['channelType'])
            ->pluck('reminder_no')
            ->toArray();

        // Add current reminder no
        $reminderNoArr[] = $arr['reminderNo'];

        // Remove empty values & duplicates (future safe)
        $reminderNoArr = array_unique(array_filter($reminderNoArr));

        $reminderNoStr = implode(',', $reminderNoArr);

        /*
        |-----
        | GET REMINDER IDS
        |-----------
        */
        $query = DB::table('reminder')
            ->select('id')
            ->where('reminder_for', $arr['reminderFor'])
            ->where('company', $arr['companyCode']);

        if ($arr['channelType'] === 'mobileNo') {
            $query->where('default_mobile_flag', 1);
        } elseif ($arr['channelType'] === 'email') {
            $query->where('default_email_flag', 1);
        }

        $results = $query->get();

        /*
        |----
        | PREPARE UPDATE ARRAY
        |-------
        */
        $updateArr = [];

        foreach ($results as $result) {

            $reminderArr = [
                'id' => $result->id,
            ];

            if ($arr['channelType'] === 'mobileNo') {
                $reminderArr['default_mobile_no'] = $reminderNoStr;
            } elseif ($arr['channelType'] === 'email') {
                $reminderArr['default_email'] = $reminderNoStr;
            }

            $updateArr[] = $reminderArr;
        }

        /*
        |-----
        | UPDATE BATCH
        |---------
        */
        if (!empty($updateArr)) {

            foreach ($updateArr as $updateData) {

                $updateFields = [];

                if (isset($updateData['default_mobile_no'])) {
                    $updateFields['default_mobile_no'] = $updateData['default_mobile_no'];
                }

                if (isset($updateData['default_email'])) {
                    $updateFields['default_email'] = $updateData['default_email'];
                }

                DB::table('reminder')
                    ->where('id', $updateData['id'])
                    ->update($updateFields);
            }
        }
    }

    public function removeDefaultReminder( int $defaultReminderId ): bool{
        try {

            $defaultReminder = DB::table('default_reminders')
                ->where('id', $defaultReminderId)
                ->first();

            if (!$defaultReminder) {
                return false;
            }

            /*
            |----
            | PREPARE ARRAY
            |--------------
            */
            $arr = [
                'companyCode' => Auth::user()->customerEmployee->company,
                'reminderFor' => $defaultReminder->reminder_for,
                'channelType' => $defaultReminder->reminder_channel_type,
                'reminderNo'  => $defaultReminder->reminder_no,
            ];

            /*
            |--------------------------------------------------------------------------
            | GET REMAINING DEFAULT REMINDER NOS
            |--------------------------------------------------------------------------
            */
            $reminderNoArr = DB::table('default_reminders')
                ->where('id', '!=', $defaultReminderId)
                ->where('company', $arr['companyCode'])
                ->where('reminder_for', $arr['reminderFor'])
                ->where(
                    'reminder_channel_type',
                    $arr['channelType']
                )
                ->pluck('reminder_no')
                ->toArray();

            /*
            |--------------------------------------------------------------------------
            | CREATE COMMA SEPARATED STRING
            |--------------------------------------------------------------------------
            */
            $reminderNoStr = implode(',', $reminderNoArr);

            /*
            |--------------------------------------------------------------------------
            | GET REMINDER IDS
            |--------------------------------------------------------------------------
            */
            $query = DB::table('reminder')

                ->select('id')

                ->where('reminder_for', $arr['reminderFor'])

                ->where('company', $arr['companyCode']);

            if ($arr['channelType'] === 'mobileNo') {

                $query->where('default_mobile_flag', 1);

            } elseif ($arr['channelType'] === 'email') {

                $query->where('default_email_flag', 1);
            }

            $results = $query->get();

            /*
            |--------------------------------------------------------------------------
            | YOU MUST KEEP AT LEAST ONE DEFAULT REMINDER
            |--------------------------------------------------------------------------
            */
            if (
                $results->count() > 0 &&
                $reminderNoStr === ''
            ) {

                return false;
            }

            /*
            |--------------------------------------------------------------------------
            | PREPARE UPDATE ARRAY
            |--------------------------------------------------------------------------
            */
            $updateArr = [];

            foreach ($results as $result) {

                $reminderArr = [

                    'id' => $result->id,
                    'updated_by' => Auth::user()->user_id,
                    'updated_dt_tm' => Carbon::now(),
                ];

                if ($arr['channelType'] === 'mobileNo') {

                    $reminderArr['default_mobile_no'] =
                        $reminderNoStr;

                } elseif ($arr['channelType'] === 'email') {

                    $reminderArr['default_email'] =
                        $reminderNoStr;
                }

                $updateArr[] = $reminderArr;
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE REMINDER TABLE
            |--------------------------------------------------------------------------
            */
            if (!empty($updateArr)) {

                foreach ($updateArr as $updateData) {

                    $updateFields = [

                        'updated_by' =>
                            $updateData['updated_by'],

                        'updated_dt_tm' =>
                            $updateData['updated_dt_tm'],
                    ];

                    if (
                        isset($updateData['default_mobile_no'])
                    ) {

                        $updateFields['default_mobile_no'] =
                            $updateData['default_mobile_no'];
                    }

                    if (
                        isset($updateData['default_email'])
                    ) {

                        $updateFields['default_email'] =
                            $updateData['default_email'];
                    }

                    DB::table('reminder')

                        ->where('id', $updateData['id'])

                        ->update($updateFields);
                }
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('Remove Default Reminder Error', [
                'default_reminder_id' => $defaultReminderId,
                'company_code' => Auth::user()->customerEmployee->company,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return false;
        }
    }
}