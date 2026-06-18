<?php

namespace App\Repositories\Client;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingReqRepository
{

    public function getAllBookingReqList(array $reqArr = []): array
    {
        return DB::table('vehicle_booking_summary')
            ->select(
                'vehicle_booking_summary.*',
                'vehicle_booking_summary.status as summary_status',
                'vehicles.registration_no',
                'customer_employee_first_processing.employee_name as first_processing_by_name',
                'customer_employee_forward_to.employee_name as forward_to_name'
            )
            ->leftJoin(
                'customer_employee as customer_employee_first_processing',
                'customer_employee_first_processing.employee_id',
                '=',
                'vehicle_booking_summary.first_processing_by'
            )

            ->leftJoin(
                'customer_employee as customer_employee_forward_to',
                'customer_employee_forward_to.employee_id',
                '=',
                'vehicle_booking_summary.forward_to'
            )

            ->leftJoin(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'vehicle_booking_summary.vehicle'
            )
            ->where(
                'vehicle_booking_summary.company',
                $reqArr['companyCode']
            )
            ->orderBy(
                'vehicle_booking_summary.created_dt_tm',
                'DESC'
            )
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();
    }

    public function getForwardedBookingReqList(array $reqArr = [])
    {
        return DB::table('vehicle_booking_details')

            ->select(
                'vehicle_booking_details.*',
                'vehicle_booking_summary.*',
                'vehicle_booking_summary.status as summary_status',
                'vehicle_booking_details.status as details_status',
                'employee.employee_name as forwarded_from_name',
                'forward_to_tb.employee_name as forward_to_name'
            )

            ->leftJoin(
                'vehicle_booking_summary',
                'vehicle_booking_details.booking_no',
                '=',
                'vehicle_booking_summary.booking_no'
            )

            ->leftJoin(
                'employee',
                'employee.employee_id',
                '=',
                'vehicle_booking_details.application_from'
            )

            ->leftJoin(
                'employee as forward_to_tb',
                'forward_to_tb.employee_id',
                '=',
                'vehicle_booking_details.forward_to'
            )
            ->where(
                'vehicle_booking_details.application_to',
                $reqArr['currentUser']
            )

            ->where(
                'vehicle_booking_details.application_from_type',
                config('constants.USER_TYPE_CORP_EMP')
            )
            ->get();
    }

    public function getCalendarInfo(string $companyCode, string $bookingYear)
    {
        return DB::table('vehicle_booking_cal_view')
            ->selectRaw("
                booking_no,
                company,
                title,
                color,
                start,
                `end` as end,
                CONCAT(
                    ?,
                    booking_no,
                    '&companyCode=',
                    company
                ) as url
            ", [
                url('client/pool/booking-req/booking-detail?bookingNo') . '='
            ])

            ->where('company', $companyCode)

            ->where('booking_year', $bookingYear)

            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | GET SCHEDULE BOOKING REQUEST SUMMARY
    |--------------------------------------------------------------------------
    */
    public function getScheduleBookingReqSummary(
        string $bookingNo,
        string $companyCode
    ) {

        return DB::table('vehicle_booking_summary')

            ->select(
                'vehicle_booking_summary.*',
                'booking_person_tb.employee_image as booking_person_image'
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
            ->where(
                'vehicle_booking_summary.company',
                $companyCode
            )
            ->get();
    }


    public function getForwardHistoryBooking(string $bookingNo)
    {
        return DB::table('vehicle_booking_details')

            ->select(
                'vehicle_booking_details.*',
                'vehicle_booking_summary.*',

                'vehicle_booking_details.created_dt_tm as details_created_dt_tm',

                'vehicle_booking_details.updated_dt_tm as details_updated_dt_tm',

                'vehicle_booking_summary.status as summary_status',

                'vehicle_booking_details.status as details_status',

                'application_from_tb.employee_name as application_from_name',

                'application_to_tb.employee_name as application_to_name',

                'application_from_tb.employee_image as from_emp_image',

                'application_to_tb.employee_image as to_emp_image',

                'booking_person_tb.employee_image as booking_person_image'
            )

            ->leftJoin(
                'vehicle_booking_summary',
                'vehicle_booking_summary.booking_no',
                '=',
                'vehicle_booking_details.booking_no'
            )

            ->leftJoin(
                'customer_employee as application_from_tb',
                'application_from_tb.employee_id',
                '=',
                'vehicle_booking_details.application_from'
            )

            ->leftJoin(
                'customer_employee as application_to_tb',
                'application_to_tb.employee_id',
                '=',
                'vehicle_booking_details.application_to'
            )

            ->leftJoin(
                'customer_employee as booking_person_tb',
                'booking_person_tb.employee_id',
                '=',
                'vehicle_booking_summary.person_emp_id'
            )

            ->where(
                'vehicle_booking_details.booking_no',
                $bookingNo
            )
            ->orderBy(
                'vehicle_booking_details.id',
                'ASC'
            )
            ->get();
    }

    public function getCorporateCompanies(
        $individualCompanyCode,
        ?string $companyCode = null
    ): Collection {

        $query = DB::table('enrolled_corp_indv')

            ->select([
                'enrolled_corp_indv.*',
                'corporate_companies.title as company_name',
                'corporate_companies.company_code',
                'customer_employee.employee_name',
                'customer_employee.emp_type'
            ])

            ->join(
                'corporate_companies',
                'corporate_companies.company_code',
                '=',
                'enrolled_corp_indv.corp_company'
            )

            ->join(
                'customer_employee',
                'customer_employee.company',
                '=',
                'enrolled_corp_indv.indv_company'
            )
            // ->where(
            //     'customer_employee.emp_type',
            //     'system_manager'
            // )
            ->where(
                'enrolled_corp_indv.indv_company',
                $individualCompanyCode
            );
        if (!empty($companyCode)) {

            $query->where(
                'enrolled_corp_indv.corp_company',
                $companyCode
            );
        }
        
        return $query->get();
    }

    public function getBookingInfo(
        string $userCode,
        ?string $bookingCode = null
    ) {
        //dd($userCode, $bookingCode);
        $query = DB::table('vehicle_booking_summary')

            ->select([
                'vehicle_booking_summary.*',
                'corporate_companies.title as company_name',
                'booking_person_tb.employee_image as booking_person_image',
                'vehicles.registration_no',
                'driver_tb.employee_name as driver_name',
                'driver_tb.primary_mobile as driver_mobile',
            ])
            ->join(
                'corporate_companies',
                'corporate_companies.company_code',
                '=',
                'vehicle_booking_summary.company'
            )
            ->leftJoin(
                'customer_employee as booking_person_tb',
                'booking_person_tb.employee_id',
                '=',
                'vehicle_booking_summary.person_emp_id'
            )
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
            ->where(
                'vehicle_booking_summary.person_emp_id',
                $userCode
            );

        if (!empty($bookingCode)) {

            $query->where(
                'vehicle_booking_summary.booking_no',
                $bookingCode
            );
        }

        $query->orderBy(
            'vehicle_booking_summary.created_dt_tm',
            'desc'
        );

        return $query->get();
    }

    public function newBooking(
        array $insertArr,
        array $insertDetailArr
    ): int {
        DB::table('vehicle_booking_summary')
            ->insert($insertArr);

        DB::table('vehicle_booking_details')
            ->insert($insertDetailArr);

        return 1;
    }

    public function getPoolReportDetails(string $bookingNo)
    {
        return DB::table('vehicle_assign_details')
            ->select(
                'vehicle_assign_details.*',
                'vehicles.registration_no',
                'brand_tb.element as brand_name',
                'brand_model_tb.element as brand_model_name',
                'customer_employee.employee_name as driver_name',
                'customer_employee.primary_mobile as driver_mobile'
            )
            ->join(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'vehicle_assign_details.vehicle'
            )
            ->leftJoin(
                'common_table as brand_tb',
                'brand_tb.element_code',
                '=',
                'vehicles.brand'
            )
            ->leftJoin(
                'common_table as brand_model_tb',
                'brand_model_tb.element_code',
                '=',
                'vehicles.brand_model'
            )
            ->leftJoin(
                'customer_employee',
                'customer_employee.employee_id',
                '=',
                'vehicle_assign_details.driver'
            )
            ->where(
                'vehicle_assign_details.booking_no',
                $bookingNo
            )
            ->orderBy(
                'vehicle_assign_details.vehicle'
            )
            ->orderBy(
                'vehicle_assign_details.assign_dt_tm'
            )
            ->get();
    }

    public function editBooking(
        array $updateArr,
        array $updateDetailArr
    ) {

        /*
        |------
        | UPDATE BOOKING SUMMARY
        |--------------------------------------------------------------------------
        */
        DB::table('vehicle_booking_summary')
            ->where(
                'booking_no',
                $updateArr['booking_no']
            )
            ->update($updateArr);

        /*
        |------
        | UPDATE BOOKING DETAILS
        |--------------------------------------------------------------------------
        */
        DB::table('vehicle_booking_details')
            ->where(
                'booking_no',
                $updateArr['booking_no']
            )
            ->update($updateDetailArr);

        return 1;
    }

    public function removeBooking(
    string $bookingCode
    ) {

        /*
        |-------
        | DELETE BOOKING SUMMARY
        |--------------------------------------------------------------------------
        */
        DB::table('vehicle_booking_summary')
            ->where(
                'booking_no',
                $bookingCode
            )
            ->delete();

        /*
        |-----
        | DELETE BOOKING DETAILS
        |--------------------------------------------------------------------------
        */
        DB::table('vehicle_booking_details')
            ->where(
                'booking_no',
                $bookingCode
            )
            ->delete();

        return 3;
    }

    public function getBookingReqList(
        array $reqArr = []
    ) {

        $query = DB::table('vehicle_booking_summary')

            ->select(
                'vehicle_booking_summary.*',
                'customer_employee.employee_name as forward_to_name',
                'vehicle_booking_details.*',
                'vehicle_booking_summary.status as summary_status',
                'vehicle_booking_details.status as details_status',
                'vehicles.registration_no',
                'vehicle_assign_details.assign_type'
            )
            ->leftJoin(
                'vehicle_booking_details',
                'vehicle_booking_details.booking_no',
                '=',
                'vehicle_booking_summary.booking_no'
            )
            ->leftJoin(
                'customer_employee',
                'customer_employee.employee_id',
                '=',
                'vehicle_booking_details.forward_to'
            )
            ->leftJoin(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'vehicle_booking_summary.vehicle'
            )
            ->leftJoin(
                'vehicle_assign_details',
                'vehicle_assign_details.booking_no',
                '=',
                'vehicle_booking_summary.booking_no'
            )
            /*
            |--------------------------------------------------------------------------
            | WHERE
            |--------------------------------------------------------------------------
            */
            ->where(
                'vehicle_booking_summary.company',
                $reqArr['companyCode']
            );

        /*
        |--------------------------------------------------------------------------
        | BOOKING NO
        |--------------------------------------------------------------------------
        */
        if (
            isset($reqArr['bookingNo'])
            &&
            $reqArr['bookingNo']
        ) {

            $query->where(
                'vehicle_booking_summary.booking_no',
                $reqArr['bookingNo']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | WHERE IN
        |--------------------------------------------------------------------------
        */
        $query->whereIn(
            'vehicle_booking_summary.first_processing_by',
            [
                config('constants.DEFAULT_PROCESS_BY'),
                $reqArr['currentUser']
            ]
        );

        $query->whereIn(
            'vehicle_booking_details.application_to',
            [
                config('constants.DEFAULT_PROCESS_BY'),
                $reqArr['currentUser']
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | APPLICATION TYPE
        |--------------------------------------------------------------------------
        */
        $query->where(
            'vehicle_booking_details.application_from_type',
            config('constants.USER_TYPE_INDV_EMP')
        );

        /*
        |--------------------------------------------------------------------------
        | ORDER BY
        |--------------------------------------------------------------------------
        */
        $query->orderBy(
            'vehicle_booking_summary.created_dt_tm',
            'DESC'
        );
        /*
        |--------------------------------------------------------------------------
        | GET RESULT
        |--------------------------------------------------------------------------
        */
        return $query->get();
    }

    public function getBookingReqDetail(
        array $reqArr = []
    ) {
        return DB::table('vehicle_booking_summary')

            ->select(
                'vehicle_booking_details.*',
                'customer_employee.employee_name as forward_to_name',
                'vehicle_booking_summary.*',
                'vehicle_booking_summary.status as summary_status',
                'vehicle_booking_details.status as details_status',
                'application_from_tb.employee_name',
                'booking_person_tb.employee_image as booking_person_image',
                'booking_person_tb.primary_mobile as person_mobile_no',
                'booking_person_tb.email as person_email',
                'vehicles.registration_no',
                'vehicle_assign_details.assign_type',
                'driver_tb.employee_name as driver_name',
                'driver_tb.primary_mobile as driver_mobile'
            )

            /*
            |--------------------------------------------------------------------------
            | JOIN
            |--------------------------------------------------------------------------
            */
            ->leftJoin(
                'vehicle_booking_details',
                'vehicle_booking_details.booking_no',
                '=',
                'vehicle_booking_summary.booking_no'
            )
            ->leftJoin(
                'customer_employee',
                'customer_employee.employee_id',
                '=',
                'vehicle_booking_details.forward_to'
            )
            ->leftJoin(
                'customer_employee as application_from_tb',
                'application_from_tb.employee_id',
                '=',
                'vehicle_booking_details.application_from'
            )
            ->leftJoin(
                'customer_employee as booking_person_tb',
                'booking_person_tb.employee_id',
                '=',
                'vehicle_booking_summary.person_emp_id'
            )
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
                'vehicle_assign_details',
                'vehicle_assign_details.booking_no',
                '=',
                'vehicle_booking_summary.booking_no'
            )
            /*
            |--------------------------------------------------------------------------
            | WHERE
            |--------------------------------------------------------------------------
            */
            ->where(
                'vehicle_booking_summary.booking_no',
                $reqArr['bookingNo']
            )
            ->where(
                'vehicle_booking_summary.company',
                $reqArr['companyCode']
            )
            ->where(
                'vehicle_booking_details.detail_no',
                $reqArr['detailTableNo']
            )
            ->where(
                'vehicle_booking_details.application_from_type',
                config('constants.USER_TYPE_INDV_EMP')
            )

            /*
            |--------------------------------------------------------------------------
            | WHERE IN
            |--------------------------------------------------------------------------
            */
            ->whereIn(
                'vehicle_booking_summary.first_processing_by',
                [
                    config('constants.DEFAULT_PROCESS_BY'),
                    $reqArr['currentUser']
                ]
            )

            ->whereIn(
                'vehicle_booking_details.application_to',
                [
                    config('constants.DEFAULT_PROCESS_BY'),
                    $reqArr['currentUser']
                ]
            )

            /*
            |--------------------------------------------------------------------------
            | GET RESULT
            |--------------------------------------------------------------------------
            */
            ->get();
    }

    public function getAllBookingReqDetail(
        array $reqArr = []
    ) {

        return DB::table('vehicle_booking_summary')

            ->select(
                'vehicle_booking_summary.*',
                'vehicle_booking_summary.status as summary_status',
                'booking_person_tb.employee_image as booking_person_image',
                'vehicles.registration_no',
                'vehicle_assign_details.assign_type'
            )

            /*
            |--------------------------------------------------------------------------
            | JOIN
            |--------------------------------------------------------------------------
            */
            ->leftJoin(
                'customer_employee as booking_person_tb',
                'booking_person_tb.employee_id',
                '=',
                'vehicle_booking_summary.person_emp_id'
            )
            ->leftJoin(
                'vehicles',
                'vehicles.vehicle_id',
                '=',
                'vehicle_booking_summary.vehicle'
            )
            ->leftJoin(
                'vehicle_assign_details',
                'vehicle_assign_details.booking_no',
                '=',
                'vehicle_booking_summary.booking_no'
            )

            /*
            |--------------------------------------------------------------------------
            | WHERE
            |--------------------------------------------------------------------------
            */
            ->where(
                'vehicle_booking_summary.booking_no',
                $reqArr['bookingNo']
            )
            ->where(
                'vehicle_booking_summary.company',
                $reqArr['companyCode']
            )

            /*
            |--------------------------------------------------------------------------
            | GET RESULT
            |--------------------------------------------------------------------------
            */
            ->get();
    }

    public function getForwardFMEmp(
        array $whereArr = []
    ) {

        return DB::table('customer_employee')

            ->select('*')
            /*
            |--------------------------------------------------------------------------
            | WHERE
            |--------------------------------------------------------------------------
            */
            ->where(
                'company',
                $whereArr['companyCode']
            )

            ->where(
                'system_user',
                1
            )
            /*
            |--------------------------------------------------------------------------
            | WHERE NOT IN
            |--------------------------------------------------------------------------
            */
            ->whereNotIn(
                'employee_id',
                [
                    $whereArr['FMEmpCode']
                ]
            )
            /*
            |--------------------------------------------------------------------------
            | GET RESULT
            |--------------------------------------------------------------------------
            */
            ->get();
    }

    public function changeBookingReqStatus(
        array $updateArr = [],
        array $reqArr = []
    ) {
        $dateTime = Carbon::now()->format('Y-m-d H:i:s');

        $updateArrDet = [];

        /*
        |--------------------------------------------------------------------------
        | VEHICLE BOOKING SUMMARY UPDATE
        |--------------------------------------------------------------------------
        */
        $updateArr['updated_dt_tm']      = $dateTime;
        $updateArr['first_processing_by'] = $reqArr['currentUser'];
        $updateArr['updated_type']       = config('constants.USER_TYPE_CORP_EMP');

        DB::table('vehicle_booking_summary')
            ->where('booking_no', $reqArr['bookingNo'])
            ->update($updateArr);

        /*
        |--------------------------------------------------------------------------
        | VEHICLE BOOKING DETAILS UPDATE
        |--------------------------------------------------------------------------
        */
        $updateArrDet['application_to']      = $reqArr['currentUser'];
        $updateArrDet['application_to_type'] = config('constants.USER_TYPE_CORP_EMP');
        $updateArrDet['updated_dt_tm']       = $dateTime;
        $updateArrDet['updated_type']        = config('constants.USER_TYPE_CORP_EMP');
        $updateArrDet['updated_by']          = $updateArr['updated_by'];

        $updateArrDet['comment_to'] = !empty($updateArr['comment'])
            ? $updateArr['comment']
            : null;

        $updateArrDet['status'] = $updateArr['status'];

        /*
        |--------------------------------------------------------------------------
        | FORWARD DATA RESET
        |--------------------------------------------------------------------------
        | forward_to set when status 8,
        | from 8 -> 5/6 unset forward_to
        |--------------------------------------------------------------------------
        */
        $updateArrDet['forward_to']      = null;
        $updateArrDet['forward_to_type'] = null;

        DB::table('vehicle_booking_details')
            ->where('detail_no', $reqArr['detailTableNo'])
            ->update($updateArrDet);

        /*
        |--------------------------------------------------------------------------
        | HANDLE FORWARD & PENDING
        |--------------------------------------------------------------------------
        */
        $this->delRowForForwardNPending(
            $reqArr['detailTableNo']
        );

        /*
        |--------------------------------------------------------------------------
        | RETURN STATUS
        |--------------------------------------------------------------------------
        */
        if ($updateArr['status'] == '4') {
            return 4;
        } elseif ($updateArr['status'] == '5') {
            return 5;
        } elseif ($updateArr['status'] == '6') {
            return 6;
        }

        return null;
    }

    public function delRowForForwardNPending($refNo): void
    {
        if (empty($refNo)) {
            return;
        }

        DB::table('vehicle_booking_details')
            ->where('ref_no', $refNo)
            ->delete();
    }

    public function rejectBooking(string $bookingCode)
    {
        if (empty($bookingCode)) {
            return 3;
        }

        DB::table('vehicle_booking_summary')
            ->where('booking_no', $bookingCode)
            ->update([
                'updated_dt_tm' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_by'    => Auth::user()->user_id,
                'updated_type'  => '',
                'status'        => config('constants.BOOKING_REQ_REJECT_STATUS'),
            ]);

        return 3;
    }

    public function forwardBookingReqStatus(array $inputArr = [], array $whereArr = []): int
    {
        try {

            $dateTime = now()->format('Y-m-d H:i:s');

            /*
            |--------------------------------------------------------------------------
            | Update Summary Table
            |--------------------------------------------------------------------------
            */
            $updateArrSum = [
                'comment'                => $inputArr['forwardComment'] ?? null,
                'status'                 => $inputArr['status'] ?? null,
                'from_dt_tm_confirmed'   => $inputArr['from_dt_tm_confirmed'] ?? null,
                'to_dt_tm_confirmed'     => $inputArr['to_dt_tm_confirmed'] ?? null,
                'updated_by'             => $inputArr['currentUser'] ?? null,
                'updated_dt_tm'          => $dateTime,
                'updated_type'           => config('constants.USER_TYPE_CORP_EMP'),
                'forward_to'             => $inputArr['forwardEmpCode'] ?? null,
            ];

            DB::table('vehicle_booking_summary')
                ->where('booking_no', $whereArr['bookingNo'])
                ->update($updateArrSum);

            /*
            |--------------------------------------------------------------------------
            | Update Details Table
            |--------------------------------------------------------------------------
            */
            $updateArrDet = [
                'forward_to'      => $inputArr['forwardEmpCode'] ?? null,
                'forward_to_type' => config('constants.USER_TYPE_CORP_EMP'),
                'comment_to'      => $inputArr['forwardComment'] ?? null,
                'status'          => $inputArr['status'] ?? null,
                'updated_by'      => $inputArr['currentUser'] ?? null,
                'updated_dt_tm'   => $dateTime,
                'updated_type'    => config('constants.USER_TYPE_CORP_EMP'),
            ];

            DB::table('vehicle_booking_details')
                ->where('detail_no', $whereArr['detailTableNo'])
                ->update($updateArrDet);

            /*
            |--------------------------------------------------------------------------
            | Handle Forward & Pending
            |--------------------------------------------------------------------------
            */
            $this->delRowForForwardNPending(
                $whereArr['detailTableNo']
            );

            /*
            |--------------------------------------------------------------------------
            | Insert New Detail Record
            |--------------------------------------------------------------------------
            */
            $insertArrDet = [
                'detail_no'             => reference_no(),
                'booking_no'            => $inputArr['bookingNo'],
                'application_from'      => $inputArr['currentUser'],
                'application_from_type' => config('constants.USER_TYPE_CORP_EMP'),
                'application_to'        => $inputArr['forwardEmpCode'],
                'application_to_type'   => config('constants.USER_TYPE_CORP_EMP'),
                'comment_from'          => $inputArr['forwardComment'],
                'status'                => config('constants.BOOKING_REQ_PENDING_STATUS'),
                'ref_no'                => $inputArr['detailTableNo'],
                'created_by'            => $inputArr['currentUser'],
                'created_dt_tm'         => $dateTime,
                'created_type'          => config('constants.USER_TYPE_CORP_EMP'),
                'updated_by'            => $inputArr['currentUser'],
                'updated_dt_tm'         => $dateTime,
                'updated_type'          => config('constants.USER_TYPE_CORP_EMP'),
            ];

            DB::table('vehicle_booking_details')
                ->insert($insertArrDet);

            return 1;

        } catch (\Throwable $e) {

            Log::error('Forward Booking Request Repository Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'input'   => $inputArr,
                'where'   => $whereArr,
            ]);

            throw $e;
        }
    }

    public function getForwardedBookingReqDetail(array $reqArr = []): array
    {
        try {

            return DB::table('vehicle_booking_details')
                ->select(
                    'vehicle_booking_details.*',
                    'employee.employee_name as forward_to_name',
                    'vehicle_booking_summary.*',
                    'vehicle_booking_summary.status as summary_status',
                    'vehicle_booking_details.status as details_status',
                    'application_from_tb.employee_name',
                    'vehicles.registration_no'
                )
                ->leftJoin(
                    'vehicle_booking_summary',
                    'vehicle_booking_summary.booking_no',
                    '=',
                    'vehicle_booking_details.booking_no'
                )
                ->leftJoin(
                    'employee',
                    'employee.employee_id',
                    '=',
                    'vehicle_booking_details.application_from'
                )
                ->leftJoin(
                    'employee as application_from_tb',
                    'application_from_tb.employee_id',
                    '=',
                    'vehicle_booking_details.application_from'
                )
                ->leftJoin(
                    'vehicles',
                    'vehicles.vehicle_id',
                    '=',
                    'vehicle_booking_summary.vehicle'
                )
                ->where(
                    'vehicle_booking_details.detail_no',
                    $reqArr['detailTableNo']
                )
                ->where(
                    'vehicle_booking_details.application_to',
                    $reqArr['currentUser']
                )
                ->where(
                    'vehicle_booking_details.application_from_type',
                    config('constants.USER_TYPE_CORP_EMP')
                )
                ->get()
                ->toArray();

        } catch (\Throwable $e) {

            Log::error('Get Forwarded Booking Request Detail Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'reqArr'  => $reqArr,
            ]);

            return [];
        }
    }

    public function getForwardFMEmpForwardedBooking(
        string $companyCode,
        array $empCode = []
    ): array {
        try {

            $query = DB::table('customer_employee')
                ->select('*')
                ->where('company', $companyCode)
                ->where('system_user', 1);

            // Equivalent of CI where_not_in()
            if (!empty($empCode)) {
                $query->whereNotIn('employee_id', $empCode);
            }

            return $query
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray();

        } catch (\Throwable $e) {

            Log::error('Get Forward FM Employee Forwarded Booking Error', [
                'message'     => $e->getMessage(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
                'companyCode' => $companyCode,
                'empCode'     => $empCode,
            ]);

            return [];
        }
    }

    public function forwardedkBookingReqDetail(array $reqArr = []): array
    {
        try {

            return DB::table('vehicle_booking_summary')
                ->select(
                    'vehicle_booking_details.*',
                    'vehicle_booking_summary.*',
                    'vehicle_booking_summary.status as summary_status',
                    'vehicle_booking_details.status as details_status',
                    'booking_person_tb.primary_mobile as person_mobile_no',
                    'booking_person_tb.email as person_email'
                )
                ->leftJoin(
                    'vehicle_booking_details',
                    'vehicle_booking_summary.booking_no',
                    '=',
                    'vehicle_booking_details.booking_no'
                )
                ->leftJoin(
                    'customer_employee as booking_person_tb',
                    'booking_person_tb.employee_id',
                    '=',
                    'vehicle_booking_summary.person_emp_id'
                )
                ->where(
                    'vehicle_booking_details.detail_no',
                    $reqArr['detailTableNo']
                )
                ->where(
                    'vehicle_booking_details.application_to',
                    $reqArr['currentUser']
                )
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray();

        } catch (\Throwable $e) {

            Log::error('Forwarded Booking Request Detail Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'reqArr'  => $reqArr,
            ]);

            return [];
        }
    }

    public function changeForwardedBookingReqStatus(
        array $updateArr = [],
        array $reqArr = []
    ): int|null {
        try {

            $dateTime = now()->format('Y-m-d H:i:s');

            $updateArrDet = [];
            $updateArrPrvDet = [];

            $updateArr['updated_dt_tm'] = $dateTime;
            $updateArr['updated_type'] = config('constants.USER_TYPE_CORP_EMP');

            /*
            |--------------------------------------------------------------------------
            | Only For Processing (Status = 4)
            | Handle Previous Row Where Status Is 8
            |--------------------------------------------------------------------------
            */
            if ($updateArr['status'] == '4') {

                $updateArrPrvDet['updated_by'] = $updateArr['updated_by'];
                $updateArrPrvDet['updated_dt_tm'] = $updateArr['updated_dt_tm'];
                $updateArrPrvDet['updated_type'] = $updateArr['updated_type'];
                $updateArrPrvDet['status'] = config('constants.BOOKING_REQ_FORWARD_STATUS');

                DB::table('vehicle_booking_details')
                    ->where('detail_no', $reqArr['refNo'])
                    ->update($updateArrPrvDet);
            }

            /*
            |--------------------------------------------------------------------------
            | Update vehicle_booking_summary
            |--------------------------------------------------------------------------
            */
            DB::table('vehicle_booking_summary')
                ->where('booking_no', $reqArr['bookingNo'])
                ->update($updateArr);

            /*
            |--------------------------------------------------------------------------
            | Update vehicle_booking_details
            |--------------------------------------------------------------------------
            */
            $updateArrDet['updated_dt_tm'] = $dateTime;
            $updateArrDet['updated_type'] = config('constants.USER_TYPE_CORP_EMP');
            $updateArrDet['updated_by'] = $updateArr['updated_by'];
            $updateArrDet['comment_to'] = !empty($updateArr['comment'])
                ? $updateArr['comment']
                : null;

            $updateArrDet['status'] = $updateArr['status'];

            /*
            |--------------------------------------------------------------------------
            | Forward_to Set When Status 8
            | From 8 To 5/6 Unset Forward_to
            |--------------------------------------------------------------------------
            */
            $updateArrDet['forward_to'] = null;
            $updateArrDet['forward_to_type'] = null;

            DB::table('vehicle_booking_details')
                ->where('detail_no', $reqArr['detailTableNo'])
                ->update($updateArrDet);

            /*
            |--------------------------------------------------------------------------
            | Handle Forward & Pending
            |--------------------------------------------------------------------------
            */
            $this->delRowForForwardNPending(
                $reqArr['detailTableNo']
            );

            /*
            |--------------------------------------------------------------------------
            | Return Same As CI
            |--------------------------------------------------------------------------
            */
            if ($updateArr['status'] == '4') {
                return 4;
            }

            if ($updateArr['status'] == '5') {
                return 5;
            }

            if ($updateArr['status'] == '6') {
                return 6;
            }

            return null;

        } catch (\Throwable $e) {

            Log::error('Change Forwarded Booking Req Status Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'updateArr' => $updateArr,
                'reqArr'    => $reqArr,
            ]);

            throw $e;
        }
    }

    public function getBookingSummaryOfAssignedVehicle(string $bookingNo)
    {
        return DB::table('vehicle_booking_summary')
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
            ->where('vehicle_booking_summary.booking_no', $bookingNo)
            ->where('vehicle_booking_summary.company', Auth::user()->customerEmployee->company)
            ->first() ?: false;
    }

    public function assignVehicle($assignVehicleId, $bookingNo): int
    {
        return DB::table('vehicle_booking_summary')
            ->where('booking_no', $bookingNo)
            ->where('company', Auth::user()->customerEmployee->company)
            ->update([
                'vehicle'       => $assignVehicleId,
                'trip_status'   => config('constants.TRIP_STATUS_VECHILE_SET'),
                'updated_dt_tm' => Carbon::now(),
                'updated_type'  => config('constants.USER_TYPE_CORP_EMP'),
                'updated_by'    => Auth::user()->user_id,
            ]);
    }

    public function getBookingSummary(string $bookingNo, string $companyCode): array
    {
        return DB::table('vehicle_booking_summary')
            ->where('company', $companyCode)
            ->where('booking_no', $bookingNo)
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();
    }
}