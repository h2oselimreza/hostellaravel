<?php

return [

    'months' => [
        1  => 'January',
        2  => 'February',
        3  => 'March',
        4  => 'April',
        5  => 'May',
        6  => 'June',
        7  => 'July',
        8  => 'August',
        9  => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ],

    // ================= CORE =================
    'COMPANY_NAME' => 'VROOM',
    'SERVER_ACCESS_TOKEN' => '13523456',

    'P_ADMIN' => 'admin',
    'CLIENT'  => 'client',

    'USER_TYPE_ADMIN_EMP' => 'admin_employee',
    'USER_TYPE_CORP_EMP'   => 'corporate_employee',
    'USER_TYPE_INDV_EMP'   => 'indv_employee',

    'MODULE_GROUP_CODE' => 'M-GRP-',
    'P_EMPLOYEE_CODE'   => 'AE',
    'PACKAGE_CODE'      => 'PCK-',

    // ================= SMS =================
    // 'SMS_USERNAME' => env('APP_ENV') == 'production' ? 'vroom24x7' : (env('APP_ENV') == 'development' ? 'vroom24x7' : ''),
    // 'SMS_PASSWORD' => env('APP_ENV') == 'production' ? 'Vroom#531' : (env('APP_ENV') == 'development' ? 'Vroom#531' : ''),
    // 'SMS_URL'      => 'http://api.infobip.com/api/v3/sendsms/json',
    // 'SMS_SENDER_ID'=> 'Vroom24x7',
    // 'SMS_SENDER_ID1'=> 'Vroom24X7',

    // ================= PERMISSION =================
    'EDUCATION_QUA_PERMISSION' => '1',
    'WORKING_EXP_PERMISSION'    => '1',

    // ================= SERVICE =================
    'SERVICE_CATG_CODE' => 'SRCATG-',
    'SERVICE_CODE'      => 'SRVC-',
    'VARIANT_CODE'      => 'SRVCVR-',

    'COST_CTG_CODE'     => 'CSTCATG-',
    'COST_HEAD_CODE'    => 'CSTHD-',

    'WORKSHOP_CODE'     => 'WRKSHP-',

    // ================= USER / COMPANY =================
    'CUST_EMP_CODE' => 'FE',
    'INDV_EMP_CODE' => 'IE',
    'CORPORATE_CUST' => 'corp_customer',
    'INDIVIDUAL_CUST'=> 'indv_customer',

    'COMPANY_CODE'     => 'FC',
    'INDV_COMPANY_CODE'=> 'IC',

    // ================= VEHICLE =================
    'VEHICLE_CODE' => 'VHCL-',
    'PLACE_CODE'   => 'PLC-',

    // ================= MODULE =================
    'APPOINTMENT_SER' => 'APPOINMENT',
    'HOME_SER'        => 'HOME',

    // ================= FILE =================
    'OTHER_IMAGE'      => 'other_image',

    'REGISTRATION_FILE'=> 'registration',
    'FITNESS_FILE'     => 'fitness',
    'TAXTOKEN_FILE'    => 'taxtoken',
    'INSURANCE_FILE'   => 'insurance',
    'ROUTE_FILE'       => 'route',

    // ================= REQUEST =================
    'REQ_TYPE_SERVICE' => 'service',
    'REQ_TYPE_PRODUCT' => 'product',

    'REQ_DRAFT_STATUS'     => 2,
    'REQ_PENDING_STATUS'   => 3,
    'REQ_PROCCESSING_STATUS'=> 4,
    'REQ_REJECT_STATUS'    => 5,
    'REQ_QUOT_SUB_STATUS'  => 6,
    'REQ_QUOT_APPV_CUS_STATUS'=> 7,
    'REQ_QUOT_APPR_VRM_STATUS'=> 8,
    'REQ_FULL_DONE_STATUS'  => 1,
    'REQ_QUOT_ALL_STATUS'   => 19,

    // ================= QUOTATION =================
    'QUO_DRAFT_STATUS'  => 9,
    'QUO_SEND_STATUS'   => 10,
    'QUO_APPROV_STATUS' => 11,

    'QUOTATION_REQ' => 'REQ-',
    'QUOTATION_NO'  => 'QUO-',

    'APPOINTMENT_NO' => 'APP',
    'HOMESERVICE_NO' => 'HMAP',

    // ================= EXPENSE =================
    'EXPENSE_NO' => 'EXP-',
    'INDIVIDUAL_EXP' => '999',

    'EXP_TYPE_GENERAL' => 'general',
    'EXP_TYPE_VEHICLE' => 'vehicle',

    // ================= APPOINTMENT =================
    'APPOINTMENT_PENDING'     => '2',
    'APPOINTMENT_PROCCESSING' => '3',
    'APPOINTMENT_ACCEPT'      => '4',
    'APPOINTMENT_REJECT'      => '5',
    'APPOINTMENT_COMPLETE'    => '6',
    'APPOINTMENT_START'       => '7',
    'APPOINTMENT_CASH_COLLECT'=> '8',
    'APPOINTMENT_ALL'         => '19',

    // ================= REMINDER =================
    'VEHICLE_REMINDER_TYPE_ARR' => serialize([
        'custom'   => 'Custom',
        'fitness'  => 'Fitness',
        'taxtoken' => 'Tax Token',
        'insurance'=> 'Insurance'
    ]),

    'REMINDER_FOR_ARR' => serialize([
        'custom'  => 'Custom',
        'vehicle' => 'Vehicle'
    ]),

    'SHOW_REMINDER_TIME' => '10:00:00',
    'SHOOT_DIFF_TM'      => 2,

    // ================= JOB =================
    'JOB_CREATE_UPDTAE_BY'   => '999',
    'JOB_CREATE_UPDTAE_TYPE' => 'job',

    // ================= BLOCK =================
    'USERGROUP_BLOCKLIST' => ['1'],
    'MODULE_BLOCKLIST'    => ['1','2','32'],

    // ================= INVENTORY =================
    'PURCHASE'        => 'purchase',
    'PRODUCT_CATEGORY'=> 'PCATG-',
    'STOCK_ID'        => 'STK-',
    'PRODUCT'         => 'PRD-',
    'VARIANT'         => 'PVAR-',

    'DEBIT'  => 'Dr',
    'CREDIT' => 'Cr',

    // ================= POOL =================
    'ASSIGN_VACANT'  => 'vacant',
    'ASSIGN_PERSON'  => 'person',
    'ASSIGN_ENROUTE' => 'enroute',

    'TRIP_STATUS_VECHILE_SET' => '1',
    'TRIP_STATUS_START' => '2',
    'TRIP_STATUS_END'   => '3',

    'VROOM_ADDRESS' => "KA/67-68, Norda, East Baridhara<br>Dhaka 1212, Bangladesh<br>+88 09678187666<br>info@vroom.com.bd",

    'NXT_RNW_WITHIN_DAY' => 30,

    'PACKAGE_VEHICLE_COUNT' => 'vehicle',
    'PACKAGE_USER_COUNT'    => 'user',

    'OTP_IDLE_TIME' => 50,

    'INDV_DEFAULT_GRP' => '3',
    'INDV_CARD_EXPIY_GRP' => '4',
    'INDV_PACKAGE' => 'PCK-00005',
    'INDV_CARD_EXPIY_PACKAGE' => 'PCK-00006',

    'CARD_NON_ASSIGNED' => '2',
    'CARD_ASSIGNED'     => '3',

    'CARD_ACTIVE'    => '3',
    'CARD_INACTIVE'  => '4',
    'CARD_NOT_ACTIVATE' => '5',

    // ================= BOOKING =================
    'DEFAULT_PROCESS_BY' => 'fms',

    'BOOKING_REQ_PENDING_STATUS'   => '3',
    'BOOKING_REQ_PROCESSING_STATUS'=> '4',
    'BOOKING_REQ_APPROVE_STATUS'   => '5',
    'BOOKING_REQ_REJECT_STATUS'    => '6',
    'BOOKING_REQ_FORWARD_STATUS'   => '7',
    'BOOKING_REQ_FORWARD_PENDING_STATUS' => '8',

    'BOOKING_CODE' => 'BK',
    'VENDOR_CODE'  => 'VNDR-',

    // ================= LICENSE =================
    'LICENSE_KEY' => env('APP_ENV') == 'production'
        ? 'V4-45D6-2D61-D82S-9128-635Q'
        : '123456',

    'MAIL_HOME_SERVICE'   => 'office@vroom.com.bd',
    'MOBILE_HOME_SERVICE' => '8801784426243',

    // ================= MISC =================
    'PLACE_TYPE' => 'fuel',
    'HOME_SERVICE_MODULE' => 'home_ser_mod',

    // ================= CALL CENTER =================
    'CALL_REASON_CODE' => 'CRS',
    'CUSTOMER_FEEDBACK_CODE' => 'CFB',

    // ================= CRM =================
    'NO_NEXT_CALL'   => '0',
    'HAVE_NEXT_CALL' => '1',
    'DONE_NEXT_CALL' => '2',
    'HOLD_NEXT_CALL' => '3',

    'CALL_UNLOCK_MINUITE' => 15,
    'LEAD_CODE' => 'LEAD',

    // ================= MONTH =================
    'ALL_MONTHS' => serialize([
        1=>'January',2=>'February',3=>'March',4=>'April',
        5=>'May',6=>'June',7=>'July',8=>'August',
        9=>'September',10=>'October',11=>'November',12=>'December'
    ]),

    'REPORT_FOOTER_CREDIT' => '©'.date('Y').' <b>Vroom Services Limited</b> | Developed By <b>ArrowLink™ Soft</b>',
    'COPY_RIGHT_Y' => '© '.date('Y'),

    'WEB_SHOW_MORE_DATA_COUNT' => 20,
    'SHOW_MORE_ITEM' => 21,

    // ================= NOTIFICATION =================
    'NOTIFICATION_CREATE_BOOKING_REQUEST_INDV' => 'createBookingRequestIndv',
    'NOTIFICATION_UPDATE_BOOKING_REQUEST_INDV' => 'updateBookingRequestIndv',
    'NOTIFICATION_PROCESSING_BOOKING_REQUEST'  => 'processingBookingRequest',
    'NOTIFICATION_APPROVE_BOOKING_REQUEST'     => 'approveBookingRequest',
    'NOTIFICATION_ASSIGN_VEHICLE'              => 'assignVehicle',
    'NOTIFICATION_CHANGE_VEHICLE'              => 'changeVehicle',
    'NOTIFICATION_FORWARD_BOOKING_REQUEST'     => 'forwardBookingRequest',
    'NOTIFICATION_UNAPPROVED_REJECT_BOOKING_REQUEST' => 'unapprovedRejectBookingRequest',
    'NOTIFICATION_APPROVED_REJECT_BOOKING_REQUEST' => 'approvedRejectBookingRequest',


    'ATTACHMENT_FILE'  => 'attachment',
    'BUILDING_CODE' => 'BLD',
    
];