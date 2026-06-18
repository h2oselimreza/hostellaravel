<?php

namespace App\Repositories\Client;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MemberShipCardRepository
{
    public function getCompanyCard(array $arr): array
    {
        return DB::table('membership_card')
            ->where('company', $arr['companyCode'])
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }

    public function addCard(
        string $cardNumber,
        string $companyCode,
        string $createUpdateUser,
        string $contengencyDtTm
    ) {
        $card = DB::table('membership_card')
            ->where('card_number', $cardNumber)
            ->first();
            
        if (!$card) {
            return 2; // no card against this number
        }

        if ($card->company == $companyCode) {
            return 4; // already taken this card
        }

        if ($card->status == config('constants.CARD_ASSIGNED')) {
            return 3; // already assigned by other company
        }

        $packageCode = $card->package_code;
        $cardId = $card->card_id;
        $validityMonth = $card->validity_month;

        $dateTime = now()->format('Y-m-d H:i:s');

        $membershipCardArr = [
            'is_active'       => config('constants.CARD_NOT_ACTIVATE'),
            'activation_dt_tm'=> null,
            'valid_dt_tm'     => null,
        ];

        $company = DB::table('corporate_companies')
            ->where('company_code', $companyCode)
            ->first();

        if (!$company || $company->updated_dt_tm != $contengencyDtTm) {
            return 7; // contengency check
        }

        if ($company->membership_card == '') {
            $membershipCardArr['is_active'] = config('constants.CARD_ACTIVE');
            $membershipCardArr['activation_dt_tm'] = $dateTime;
            $membershipCardArr['valid_dt_tm'] = date(
                'Y-m-d H:i:s',
                strtotime('+' . $validityMonth . ' months', strtotime($dateTime))
            );
        }

        //-------- membership card --------//
        $membershipCardArr['company'] = $companyCode;
        $membershipCardArr['status'] = config('constants.CARD_ASSIGNED');
        $membershipCardArr['updated_by'] = $createUpdateUser;
        $membershipCardArr['updated_dt_tm'] = $dateTime;
        $membershipCardArr['updated_type'] = Auth::user()->user_type_code;

        DB::table('membership_card')
            ->where('card_id', $cardId)
            ->update($membershipCardArr);

        if ($membershipCardArr['is_active'] == config('constants.CARD_NOT_ACTIVATE')) {
            return 5; // only card add
        }

        //-------- corporate company --------//
        $companyUpdateArr = [
            'package'         => get_package_by_card($packageCode),
            'membership_card' => $cardNumber,
            'created_by'      => $createUpdateUser,
            'created_dt_tm'   => $dateTime,
            'updated_by'      => $createUpdateUser,
            'updated_dt_tm'   => $dateTime,
        ];

        DB::table('corporate_companies')
            ->where('company_code', $companyCode)
            ->update($companyUpdateArr);

        //-------- users --------//
        $userArr = [
            'user_group'   => get_user_group_by_card($packageCode),
            'updated_by'   => $createUpdateUser,
            'updated_dt_tm'=> $dateTime,
            'updated_type' => Auth::user()->user_type_code,
        ];

        DB::table('users')
            ->where('user_id', $createUpdateUser)
            ->update($userArr);

        //-------- session update --------//
        Session::put('user_group', $userArr['user_group']);

        //-------- apps user delete --------//
        DB::table('apps_user_login')
            ->where('user_id', $createUpdateUser)
            ->delete();

        return 1; // first time card add and active
    }

    public function doCardActivate($cardId, $company, $createUpdateUser, $contengencyDtTm)
    {
        $card = DB::table('membership_card')
            ->where('card_id', $cardId)
            ->where('company', $company)
            ->first();

        if (!$card) {
            return 3; // card number is not assigned against this company
        }

        $isActive = $card->is_active;
        $validityMonth = $card->validity_month;

        if ($isActive != config('constants.CARD_NOT_ACTIVATE')) {
            return 4; // card number is activate once
        }

        //------ check current activated company ----------//
        $companyRow = DB::table('corporate_companies')
            ->where('company_code', $company)
            ->first();

        if ($companyRow->updated_dt_tm != $contengencyDtTm) {
            return 6; // contingency check
        }

        if ($companyRow->membership_card == "") {
            return 5; // there is no current activated card
        }

        $dateTime = Carbon::now()->format('Y-m-d H:i:s');

        $membershipCardArr = [
            'is_active'        => config('constants.CARD_INACTIVE'),
            'activation_dt_tm' => $dateTime,
            'valid_dt_tm'      => Carbon::parse($dateTime)
                                    ->addMonths($validityMonth)
                                    ->format('Y-m-d H:i:s'),
            'updated_by'       => $createUpdateUser,
            'updated_dt_tm'    => $dateTime,
            'updated_type'     => Auth::user()->user_type_code,
        ];

        DB::table('membership_card')
            ->where('card_id', $cardId)
            ->update($membershipCardArr);

        return 1;
    }

    public function doCardActive($cardId, $company, $createUpdateUser, $contengencyDtTm, $currentCard)
    {
        $card = DB::table('membership_card')
            ->where('card_id', $cardId)
            ->where('company', $company)
            ->first();

        if (!$card) {
            return 3; // not assigned against company
        }

        $isActive = $card->is_active;
        $packageShortCode = $card->package_code;
        $cardNumber = $card->card_number;
        $validDtTm = $card->valid_dt_tm;

        // ----- expiry check -----
        $todayDate = Carbon::now();
        $validDt = Carbon::parse($validDtTm);

        if ($todayDate->gt($validDt)) {
            return 8; // expired
        }

        if ($isActive != config('constants.CARD_INACTIVE')) {
            return 4; // not in inactive status
        }

        $dateTime = Carbon::now()->format('Y-m-d H:i:s');

        $package = get_package_by_card($packageShortCode);
        $userGroup = get_user_group_by_card($packageShortCode);

        $packageCheck = package_change_check($company, $package);

        if ($packageCheck['success'] == 0) {
            if ($packageCheck['packageCheckType'] == 'vehicle') {
                return 5;
            } elseif ($packageCheck['packageCheckType'] == 'user') {
                return 6;
            }
        }

        // ----- corporate check -----
        $companyRow = DB::table('corporate_companies')
            ->where('company_code', $company)
            ->first();

        if ($companyRow->updated_dt_tm != $contengencyDtTm) {
            return 9;
        }

        if ($companyRow->membership_card == "") {
            return 7;
        }

        if ($companyRow->membership_card != $currentCard) {
            return 10;
        }

        // =========================
        // Inactivate current active card
        // =========================
        DB::table('membership_card')
            ->where('company', $company)
            ->where('is_active', config('constants.CARD_ACTIVE'))
            ->update([
                'is_active'      => config('constants.CARD_INACTIVE'),
                'updated_by'     => $createUpdateUser,
                'updated_dt_tm'  => $dateTime,
                'updated_type'   => Auth::user()->user_type_code,
            ]);

        // =========================
        // Activate selected card
        // =========================
        DB::table('membership_card')
            ->where('card_id', $cardId)
            ->update([
                'is_active'      => config('constants.CARD_ACTIVE'),
                'updated_by'     => $createUpdateUser,
                'updated_dt_tm'  => $dateTime,
                'updated_type'   => Auth::user()->user_type_code,
            ]);

        // =========================
        // Update company
        // =========================
        DB::table('corporate_companies')
            ->where('company_code', $company)
            ->update([
                'package'           => $package,
                'membership_card'   => $cardNumber,
                'created_by'        => $createUpdateUser,
                'created_dt_tm'     => $dateTime,
                'updated_by'        => $createUpdateUser,
                'updated_dt_tm'     => $dateTime,
            ]);

        // =========================
        // Update user
        // =========================
        DB::table('users')
            ->where('user_id', $createUpdateUser)
            ->update([
                'user_group'     => $userGroup,
                'updated_by'     => $createUpdateUser,
                'updated_dt_tm'  => $dateTime,
                'updated_type'   => Auth::user()->user_type_code,
            ]);

        // =========================
        // Update session
        // =========================
        session(['user_group' => $userGroup]);

        // =========================
        // Delete app session
        // =========================
        DB::table('apps_user_login')
            ->where('user_id', $createUpdateUser)
            ->delete();

        return 1;
    }
}