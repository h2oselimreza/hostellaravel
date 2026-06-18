<?php

use App\Models\MetaData\District;
use App\Models\MetaData\Division;
use App\Models\MetaData\Upozilla;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

function get_common_table_name_str($elementCodeStr, $type)
{
    return DB::table('common_table')
        ->where('type', $type)
        ->whereIn('element_code', explode(',', $elementCodeStr))
        ->pluck('element')
        ->implode(' , ');
}

if (!function_exists('get_block_name_str')) {

    function get_block_name_str($blockCodeStr)
    {
        if (!$blockCodeStr) {
            return "";
        }

        return DB::table('blocks')
            ->whereIn('block_code', explode(',', $blockCodeStr))
            ->pluck('block_name')
            ->implode(' , ');
    }

}

if (!function_exists('get_road_name_str')) {

    function get_road_name_str($roadCodeStr)
    {
        if (!$roadCodeStr) {
            return "";
        }

        return DB::table('roads')
            ->whereIn('road_code', explode(',', $roadCodeStr))
            ->pluck('road_name')
            ->implode(' , ');
    }

}

function reference_no(){
    return uniqid("ap", false);
}


if (!function_exists('get_module_group')) {
    function get_module_group($breadcrumbModuleUrl)
    {
        $row = DB::table('modules')
            ->where('module_url', $breadcrumbModuleUrl)
            ->first();

        return $row ? $row->module_group : "";
    }
}

if (!function_exists('get_modules')) {
    function get_modules($userGroup)
    {
        $row = DB::table('user_group')
            ->where('id', $userGroup)
            ->first();

        return $row ? $row->modules : "";
    }
}

if (!function_exists('get_distinct_rows')) {
    function get_distinct_rows($mList)
    {
        return DB::table('modules')
            ->distinct()
            ->select('modules.module_group', 'module_group.module_group_order')
            ->join('module_group', 'module_group.module_group_code', '=', 'modules.module_group')
            ->whereIn('modules.id', $mList)
            ->orderBy('module_group.module_group_order', 'ASC')
            ->get()
            ->toArray();
    }
}

if (!function_exists('get_module_group_name')) {
    function get_module_group_name($moduleGroupId)
    {
        $row = DB::table('module_group')
            ->where('module_group_code', $moduleGroupId)
            ->first();

        return $row ? $row->module_group_name : "";
    }
}

if (!function_exists('get_row_modules')) {
    function get_row_modules($moduleId, $moduleGroupId)
    {
        return DB::table('modules')
            ->where('id', $moduleId)
            ->where('module_group', $moduleGroupId)
            ->get()
            ->toArray();
    }
}


function getDateTimeFormat($datetime = null)
{
    if (!empty($datetime)) {
        return Carbon::parse($datetime)->format('F j, Y h:i A');
    }

    return '';
}

function get_date_format1($date = null)
{
    return $date ? Carbon::parse($date)->format('F j, Y') : '';
}

function get_time_format($time = null)
{
    return $time ? Carbon::parse($time)->format('h:i A') : null;
}

function get_date_time_format($datetime = null)
{
    return $datetime ? Carbon::parse($datetime)->format('F j, Y h:i A') : '';
}

function getVehicleAssignTypeName($assignType)
{
    return match ($assignType) {
        config('constants.ASSIGN_VACANT')  => 'Vacant',
        config('constants.ASSIGN_ENROUTE')  => 'En Route',
        config('constants.ASSIGN_PERSON')  => 'Assigned',
        default        => '',
    };
}

function get_appointment_status($status = 0, $flag = 'admin')
{
    $statuses = [
        config('constants.APPOINTMENT_PENDING')      => 'Pending',
        config('constants.APPOINTMENT_PROCCESSING')  => 'Processing',
        config('constants.APPOINTMENT_ACCEPT')       => 'Accepted',
        config('constants.APPOINTMENT_REJECT')       => 'Rejected',
        config('constants.APPOINTMENT_COMPLETE')     => 'Completed',
        config('constants.APPOINTMENT_START')        => 'Start',
        config('constants.APPOINTMENT_CASH_COLLECT') => 'Paid',
        config('constants.APPOINTMENT_ALL')          => 'All',
    ];

    return $statuses[$status] ?? '';
}


function get_create_update_by_name($userId, $userType = null)
{
    if (empty($userId)) {
        return '';
    }

    $userType = $userType ?? config('constants.USER_TYPE_CORP_EMP');

    $corpTypes = [
        config('constants.USER_TYPE_CORP_EMP'),
        config('constants.USER_TYPE_INDV_EMP'),
        config('constants.CLIENT'),
    ];

    $adminTypes = [
        config('constants.USER_TYPE_ADMIN_EMP'),
        config('constants.P_ADMIN'),
    ];

    if (in_array($userType, $corpTypes)) {
        return DB::table('customer_employee')
            ->where('employee_id', $userId)
            ->value('employee_name') ?? '';
    }

    if (in_array($userType, $adminTypes)) {
        return DB::table('employee')
            ->where('employee_id', $userId)
            ->value('employee_name') ?? '';
    }

    return '';
}

if (!function_exists('get_division_name')) {
    function get_division_name($divisionId = null)
    {
        if (!$divisionId) {
            return "";
        }

        $division = Division::find($divisionId);

        if ($division) {
            return $division->division_en_name . ' (' . $division->division_bn_name . ')';
        }

        return "";
    }
}

if (!function_exists('get_district_name')) {
    function get_district_name($districtId = null)
    {
        if (!$districtId) {
            return "";
        }

        $district = District::find($districtId);

        return $district
            ? $district->district_en_name . ' (' . $district->district_bn_name . ')'
            : "";
    }
}

if (!function_exists('get_uplozilla_name')) {
    function get_uplozilla_name($upazilaId = null)
    {
        if (!$upazilaId) {
            return "";
        }

        $upazila = Upozilla::find($upazilaId);

        return $upazila
            ? $upazila->upozilla_en_name . ' (' . $upazila->upozilla_bn_name . ')'
            : "";
    }
}


if (!function_exists('get_workshop_name')) {

    function get_workshop_name($workshopCode)
    {
        $workshop = DB::table('workshops')
            ->where('workshop_code', $workshopCode)
            ->first();

        return $workshop->title ?? "";
    }
}

if (!function_exists('get_account_type_name')) {
    function get_account_type_name($accType): string
    {
        return match ($accType) {
            config('constants.CORPORATE_CUST')   => 'Corporate',
            config('constants.INDIVIDUAL_CUST')  => 'Individual',
            'all'            => 'All',
            default          => '',
        };
    }
}

if (!function_exists('get_company_name')) {
    function get_company_name($companyCode): string
    {
        if (!$companyCode) {
            return '';
        }

        $company = DB::table('corporate_companies')
            ->where('company_code', $companyCode)
            ->value('title');

        return $company ?? '';
    }
}


if (!function_exists('get_card_type_by_card')) {

    function get_card_type_by_card($cardCode, $cardFlag = 'package_code')
    {
        $cardTypeName = "";

        if ($cardFlag == 'card_number') {
            $cardCode = substr($cardCode, 7, 1);
        }

        if ($cardCode == '1') {
            $cardTypeName = 'Silver Membership';
        } else if ($cardCode == '2') {
            $cardTypeName = 'Gold Membership';
        } else if ($cardCode == '3') {
            $cardTypeName = 'Platinum Membership';
        }

        return $cardTypeName;
    }
}

if (!function_exists('check_mobile_no')) {
    function check_mobile_no($mobileNo = null)
    {
        if (strlen($mobileNo) == 13) {
            if (preg_match('/^8801[3-9][0-9]{8}$/', $mobileNo)) {
                return 1;
            }
        }

        return 0;
    }
}

if (!function_exists('get_parent_category_str')) {

    function get_parent_category_str($arr = [])
    {
        if (!empty($arr)) {

            $parentCategoryCodeStr = $arr['parentCategoryCodeStr'] ?? '';
            $categories = $arr['categoryArr'] ?? [];

            $parentCatgCodeArr = explode(' / ', $parentCategoryCodeStr);

            $responseArr = [];

            for ($i = 0; $i < count($parentCatgCodeArr); $i++) {

                foreach ($categories as $category) {

                    $categoryCode = is_array($category)
                        ? ($category['category_code'] ?? null)
                        : ($category->category_code ?? null);

                    $categoryName = is_array($category)
                        ? ($category['category_name'] ?? null)
                        : ($category->category_name ?? null);

                    if ($categoryCode == $parentCatgCodeArr[$i]) {
                        $responseArr[] = $categoryName;
                    }
                }
            }

            if (!empty($responseArr)) {
                return implode(' / ', $responseArr);
            }

            return "";
        }

        return "";
    }
}


if (!function_exists('getNextReminderShowDtTm')) {

    function getNextReminderShowDtTm($arr, $currentDtTm)
    {
        $nextReminderOnDtTm = "";

        $currentDtTm = strtotime($currentDtTm);

        $reminderOnDtTm = strtotime(
            $arr['reminder_on_dt_tm'] . config('constants.SHOW_REMINDER_TIME')
        );

        $beforeReminderCount = $arr['before_reminder_count'];

        if (
            $currentDtTm >= $reminderOnDtTm &&
            $arr['repeat_every'] == '0'
        ) {
            return false;
        }

        for ($i = $beforeReminderCount; $i >= 0; $i--) {

            $nextReminderDtTm = date(
                'Y-m-d H:i:s',
                strtotime(
                    '-' . getCalculatedDateStr(
                        $i,
                        $arr['before_reminder_type']
                    ),
                    $reminderOnDtTm
                )
            );

            if ($currentDtTm < strtotime($nextReminderDtTm)) {
                return $nextReminderDtTm;
            }
        }

        //---------- reminder on date is less than current time ----------------//

        while (1) {

            $nextReminderOnDtTm = date(
                'Y-m-d H:i:s',
                strtotime(
                    '+' . getCalculatedDateStr(
                        $arr['repeat_every'],
                        $arr['repeat_type']
                    ),
                    $reminderOnDtTm
                )
            );

            if ($currentDtTm < strtotime($nextReminderOnDtTm)) {
                break;
            }

            $reminderOnDtTm = strtotime($nextReminderOnDtTm);
        }

        $nextReminderOnDtTm = strtotime($nextReminderOnDtTm);

        for ($i = $beforeReminderCount; $i >= 0; $i--) {

            $nextReminderDtTm = date(
                'Y-m-d H:i:s',
                strtotime(
                    '-' . getCalculatedDateStr(
                        $i,
                        $arr['before_reminder_type']
                    ),
                    $nextReminderOnDtTm
                )
            );

            if ($currentDtTm < strtotime($nextReminderDtTm)) {
                return $nextReminderDtTm;
            }
        }

        return false;
    }
}


if (!function_exists('getCalculatedDateStr')) {

    function getCalculatedDateStr($count, $type)
    {
        $str = "";

        if ($type == 'day') {

            $str = $count . ' day';

        } elseif ($type == 'week') {

            $str = ($count * 7) . ' day';

        }

        if ($type == 'month') {

            $str = $count . ' months';

        }

        if ($type == 'year') {

            $str = $count . ' year';
        }

        return $str;
    }

    if (!function_exists('shorten_string')) {

        function shorten_string($string, $limit)
        {
            $stringCount = strlen($string);

            if ($stringCount > $limit) {
                $string = substr($string, 0, $limit) . "...";
            }

            return $string;
        }
    }
}


if (!function_exists('get_company_info')) {

    function get_company_info($companyCode): array
    {
        $companies = DB::table('corporate_companies')
            ->where('company_code', $companyCode)
            ->get()
            ->toArray();

        return $companies ?: [];
    }
}

if (!function_exists('numberConvertToWords')) {

    function numberConvertToWords($num = null)
    {
        if (is_numeric($num)) {

            $num = str_replace([' ', ','], '', trim($num));

            if (!$num) {
                return false;
            }

            $num = (int) $num;

            $words = [];

            $list1 = [
                '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen',
                'Eighteen', 'Nineteen'
            ];

            $list2 = [
                '', 'Ten', 'Twenty', 'Thirty', 'Forty', 'Fifty',
                'Sixty', 'Seventy', 'Eighty', 'Ninety', 'Hundred'
            ];

            $list3 = [
                '', 'Thousand', 'million', 'billion', 'trillion', 'quadrillion',
                'quintillion', 'sextillion', 'septillion', 'Octillion', 'Nonillion',
                'Decillion', 'Undecillion', 'Duodecillion', 'Tredecillion',
                'Quattuordecillion', 'Quindecillion', 'Sexdecillion',
                'Septendecillion', 'octodecillion', 'Novemdecillion', 'Vigintillion'
            ];

            $num_length = strlen($num);
            $levels = (int)(($num_length + 2) / 3);
            $max_length = $levels * 3;

            $num = substr('00' . $num, -$max_length);
            $num_levels = str_split($num, 3);

            for ($i = 0; $i < count($num_levels); $i++) {

                $levels--;

                $hundreds = (int)($num_levels[$i] / 100);
                $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred ' : '');

                $tens = (int)($num_levels[$i] % 100);
                $singles = '';

                if ($tens < 20) {
                    $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                } else {
                    $tens = (int)($tens / 10);
                    $tens = ' ' . $list2[$tens] . ' ';
                    $singles = (int)($num_levels[$i] % 10);
                    $singles = ' ' . $list1[$singles] . ' ';
                }

                $words[] =
                    $hundreds .
                    $tens .
                    $singles .
                    (($levels && (int)($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
            }

            $commas = count($words);

            if ($commas > 1) {
                $commas = $commas - 1;
            }

            return implode(' ', $words);
        }

        return "";
    }
}

if (!function_exists('validateDate')) {

    function validateDate($date = null): int
    {
        if ($date === null) {
            return 1;
        }
        try {
            $format = 'Y-m-d';

            $parsed = Carbon::createFromFormat($format, $date);

            if ($parsed && $parsed->format($format) === $date) {
                return 1;
            }

        } catch (\Exception $e) {
            // invalid date will fall here
        }

        exit();
    }
}

if (!function_exists('get_vehicle_assign_type_name')) {

    function get_vehicle_assign_type_name($assignType): string
    {
        // new pool

        $assignTypeName = '';

        if ($assignType == config('constants.ASSIGN_VACANT')) {

            $assignTypeName = 'Vacant';

        } elseif ($assignType == config('constants.ASSIGN_ENROUTE')) {

            $assignTypeName = 'En Route';

        } elseif ($assignType == config('constants.ASSIGN_PERSON')) {

            $assignTypeName = 'Assigned';
        }

        return $assignTypeName;
    }
}

if (!function_exists('get_quotation_req_status')) {

    function get_quotation_req_status($status = 0, $flag = 'admin')
    {
        if ($status == config('constants.REQ_DRAFT_STATUS')) {

            return "<span style='color:#9c9b98'><b>Draft</b></span>";

        } elseif ($status == config('constants.REQ_PENDING_STATUS')) {

            return "<span style='color:#FFC300'><b>Pending</b></span>";

        } elseif ($status == config('constants.REQ_PROCCESSING_STATUS')) {

            return "<span style='color:#ff8302'><b>Processing</b></span>";

        } elseif ($status == config('constants.REQ_REJECT_STATUS')) {

            return "<span style='color:red'><b>Rejected</b></span>";

        } elseif ($status == config('constants.REQ_QUOT_SUB_STATUS')) {

            return "<span style='color:#b9752e'><b>Submited Quotation</b></span>";

        } elseif ($status == config('constants.REQ_QUOT_APPV_CUS_STATUS')) {


            return "<span style='color:green'><b>Approved Quotation</b></span>";

        } elseif ($status == config('constants.REQ_QUOT_APPR_VRM_STATUS')) {

            return "Start Service By Vroom";

        } elseif ($status == config('constants.REQ_FULL_DONE_STATUS')) {

            return "Service Complete";

        } elseif ($status == config('constants.REQ_QUOT_ALL_STATUS')) {

            return "All";

        } elseif ($status == config('constants.QUO_DRAFT_STATUS')) {

            return "<span style='color:#9c9b98'><b>Draft</b></span>";

        } elseif ($status == config('constants.QUO_SEND_STATUS')) {

            return "<span style='color:#ff8302'><b>Sent</b></span>";

        } elseif ($status == config('constants.QUO_APPROV_STATUS')) {

            return "<span style='color:green'><b>Approved</b></span>";

        } else {

            return "";
        }
    }
}


if (!function_exists('get_status_name_booking_req')) {

    function get_status_name_booking_req($statusCode, $flag = null)
    {
        $status = '';

        if ($statusCode == config('constants.BOOKING_REQ_PENDING_STATUS')) {
            $status = 'Pending';

        } elseif ($statusCode == config('constants.BOOKING_REQ_PROCESSING_STATUS')) {
            $status = 'Processing';

        } elseif ($statusCode == config('constants.BOOKING_REQ_APPROVE_STATUS')) {
            $status = 'Approved';

        } elseif ($statusCode == config('constants.BOOKING_REQ_REJECT_STATUS')) {
            $status = 'Rejected';

        } elseif ($statusCode == config('constants.BOOKING_REQ_FORWARD_STATUS')) {

            if ($flag == config('constants.USER_TYPE_INDV_EMP')) {
                $status = 'Processing';
            } else {
                $status = 'Forwarded';
            }

        } elseif ($statusCode == config('constants.BOOKING_REQ_FORWARD_PENDING_STATUS')) {

            if ($flag == config('constants.USER_TYPE_INDV_EMP')) {
                $status = 'Processing';
            } else {
                $status = 'Forwarded & Pending';
            }
        }

        return $status;
    }
}

if (!function_exists('get_trip_status_name_booking_req')) {

    function get_trip_status_name_booking_req(
        $statusCode
    ): string {

        $status = 'N/A';

        if ($statusCode == config('constants.TRIP_STATUS_VECHILE_SET')) {

            $status = 'Vehicle Set';

        } elseif ($statusCode == config('constants.TRIP_STATUS_START')) {

            $status = 'Trip Started';

        } elseif ($statusCode == config('constants.TRIP_STATUS_END')) {

            $status = 'Trip End';
        }

        return $status;
    }
}


if (!function_exists('get_sub_modules')) {

    function get_sub_modules(
        string $moduleUrl
    ): array {

        /*
        |--------------------------------------------------------------------------
        | GET MODULE
        |--------------------------------------------------------------------------
        */
        $module = DB::table('modules')

            ->where('module_url', $moduleUrl)

            ->first();

        if (!$module) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | GET USER GROUP
        |--------------------------------------------------------------------------
        */
        $userGroupId = Auth::user()->user_group;
  
        $userGroup = DB::table('user_group')

            ->where('id', $userGroupId)

            ->first();

        if (
            !$userGroup
            ||
            empty($userGroup->sub_modules)
        ) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | SUB MODULE IDS
        |--------------------------------------------------------------------------
        */
        $subModuleIds = array_filter(
            explode(',', $userGroup->sub_modules)
        );

        if (empty($subModuleIds)) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | GET SUB MODULES
        |--------------------------------------------------------------------------
        */
        $results = DB::table('sub_modules')

            ->where('module', $module->id)

            ->whereIn('id', $subModuleIds)

            ->pluck('sub_module_code')

            ->toArray();

        return $results;
    }
}

if (!function_exists('get_modules')) {

    function get_modules(
        int|string $userGroup
    ): ?string {

        $row = DB::table('user_group')

            ->where('id', $userGroup)

            ->first();

        return $row?->modules;
    }
}

if (!function_exists('check_trip_end')) {
    function check_trip_end(string $bookingNo): bool
    {
        $assignDetail = DB::table('vehicle_assign_details')
            ->where('booking_no', $bookingNo)
            ->where('assign_type', config('constants.ASSIGN_ENROUTE'))
            ->first();

        if (!$assignDetail) {
            return false;
        }

        $startDtTm = Carbon::parse($assignDetail->assign_dt_tm)
            ->format('Y-m-d H:i:s');

        $endDtTm = Carbon::now()
            ->format('Y-m-d H:i:s');

        return $startDtTm <= $endDtTm;
    }
}

if (!function_exists('passwordValidation')) {
    function passwordValidation(string $password): string
    {
        if (
            !preg_match('/[A-Za-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            strlen($password) < 8
        ) {
            return 'invalid';
        }

        return 'valid';
    }
}

if (!function_exists('count_sms_part')) {

    function count_sms_part(?string $content = null): int
    {
        $content = trim(preg_replace('/\s\s+/', ' ', $content ?? ''));

        $charset7bit = [
            '@', '£', '$', '¥', 'è', 'é', 'ù', 'ì', 'ò', 'Ç', "\n", 'Ø', 'ø',
            "\r", 'Å', 'å', 'Δ', '_', 'Φ', 'Γ', 'Λ', 'Ω', 'Π', 'Ψ', 'Σ', 'Θ',
            'Ξ', 'Æ', 'æ', 'ß', 'É', ' ', '!', '"', '#', '¤', '%', '&', "'",
            '(', ')', '*', '+', ',', '-', '.', '/', '0', '1', '2', '3', '4',
            '5', '6', '7', '8', '9', ':', ';', '<', '=', '>', '?', '¡', 'A',
            'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ä',
            'Ö', 'Ñ', 'Ü', '§', '¿', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
            'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u',
            'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ñ', 'ü', 'à'
        ];

        $charset7bitext = [
            "\f", '^', '{', '}', '\\', '[', '~', ']', '|', '€'
        ];

        // Equivalent to CI str_split_unicode()
        $charsArr = preg_split('//u', $content, -1, PREG_SPLIT_NO_EMPTY);

        $coding = '7bit';
        $parts = 1;
        $charsUsed = 0;

        foreach ($charsArr as $char) {

            if (in_array($char, $charset7bit, true)) {
                $charsUsed += 1;
            } elseif (in_array($char, $charset7bitext, true)) {
                $charsUsed += 2;
            } else {
                $coding = '16bit';
                $charsUsed = count($charsArr);
                break;
            }
        }

        if ($coding === '7bit') {
            if ($charsUsed > 160) {
                $parts = (int) ceil($charsUsed / 153);
            }
        } else {
            if ($charsUsed > 70) {
                $parts = (int) ceil($charsUsed / 67);
            }
        }

        return $parts;
    }

    if (!function_exists('get_sms_configuration')) {

        function get_sms_configuration(?string $companyCode = ''): array
        {
            $configuration = [
                'url'      => config('sms.url'),
                'senderId' => config('sms.sender_id'),
                'username' => config('sms.username'),
                'password' => config('sms.password'),
            ];

            $smsSettings = DB::table('company_settings')
                ->where('company', $companyCode)
                ->where('setting_type', 'sms_settings')
                ->first();

            if (!$smsSettings) {
                return $configuration;
            }

            $smsConfiguration = json_decode($smsSettings->description);

            return [
                'url'      => $smsConfiguration->url ?? '',
                'senderId' => $smsConfiguration->senderId ?? '',
                'username' => $smsConfiguration->username ?? '',
                'password' => $smsConfiguration->password ?? '',
            ];
        }
    }

    if (!function_exists('get_package_by_card')) {

        function get_package_by_card($packageCode)
        {
            $package = null;

            if ($packageCode == '1') {
                $package = 'PCK-00001';
            } elseif ($packageCode == '2') {
                $package = 'PCK-00002';
            } elseif ($packageCode == '3') {
                $package = 'PCK-00003';
            }

            return $package;
        }
    }

    if (!function_exists('get_user_group_by_card')) {

        function get_user_group_by_card($packageCode)
        {
            $userGroup = 0;

            if ($packageCode == '1') {
                $userGroup = '5';
            } elseif ($packageCode == '2') {
                $userGroup = '6';
            } elseif ($packageCode == '3') {
                $userGroup = '7';
            }

            return $userGroup;
        }
    }


    if (!function_exists('captha_check')) {

        function captha_check($response)
        {
            $verify = Http::asForm()->post(
                'https://www.google.com/recaptcha/api/siteverify',
                [
                    'secret'   => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $response,
                ]
            );

            $captchaSuccess = $verify->json();

            if (
                !isset($captchaSuccess['success']) ||
                $captchaSuccess['success'] == false
            ) {
                return 0;
            }

            return 1;
        }
    }

    if (!function_exists('get_card_status_name')) {

        function get_card_status_name($validDtTm = null, $activeFlag)
        {
            $activeFlagTitle = "";

            if ($activeFlag == config('constants.CARD_ACTIVE')) {
                $activeFlagTitle = "Active";
            } elseif ($activeFlag == config('constants.CARD_INACTIVE')) {
                $activeFlagTitle = "Inactive";
            } elseif ($activeFlag == config('constants.CARD_NOT_ACTIVATE')) {
                $activeFlagTitle = "Not Activate";
            }

            $expiredTitle = "";
            $expireFlag = 0;

            if ($validDtTm) {

                $todayDate = new DateTime(date('Y-m-d H:i:s'));
                $validDt    = new DateTime($validDtTm);

                if ($todayDate > $validDt) {
                    $expiredTitle = "<br><span class='text-danger'><b>Expired</b></span>";
                    $expireFlag = 1;
                }
            }

            return [
                'title'      => $activeFlagTitle . $expiredTitle,
                'expireFlag' => $expireFlag
            ];
        }
    }

    if (!function_exists('package_change_check')) {

        function package_change_check($company, $package)
        {
            // ---------- package details ----------
            $packageRow = DB::table('package')
                ->where('package_code', $package)
                ->first();

            $packageDetailsObj = json_decode($packageRow->package_details);

            $packageVehicleCount = $packageDetailsObj->vehicle->count;
            $packageUserCount    = $packageDetailsObj->user->count;

            // ---------- vehicle count ----------
            $companyVehicle = DB::table('vehicles')
                ->where('company', $company)
                ->where('is_active', 1)
                ->count('id');

            // ---------- user count ----------
            $companyUser = DB::table('customer_employee')
                ->where('company', $company)
                ->where('system_user', 1)
                ->count('id');

            // ---------- validation ----------
            if ($packageVehicleCount < $companyVehicle) {
                return [
                    'success' => 0,
                    'packageCheckType' => 'vehicle'
                ];
            }

            if ($packageUserCount < $companyUser) {
                return [
                    'success' => 0,
                    'packageCheckType' => 'user'
                ];
            }

            return [
                'success' => 1
            ];
        }
    }
}