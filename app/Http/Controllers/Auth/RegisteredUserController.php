<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SentSMS;
use App\Models\User;
use App\Repositories\OtpRepository;
use App\Repositories\RegisterRepository;
use App\Repositories\SMSAndNotificationRepository;
use App\Services\Client\GenerateMonthlyToken;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $request->name,
            'user_id' => 1,
            'user_type_code' => 'admin',
            'created_by' => 1,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    public function doRegistration(
        Request $request, 
        RegisterRepository $registrationRepository,
        OtpRepository $otpRepository,
        SmsService $smsService,
        SMSAndNotificationRepository $sMSAndNotificationRepository
        )
    {
        // Verify reCAPTCHA
        // $response = $request->input('g-recaptcha-response');

        // $verify = Http::asForm()->post(
        //     'https://www.google.com/recaptcha/api/siteverify',
        //     [
        //         'secret'   => '6LcjbtsUAAAAAHR7jwClyu5MLXdCbhBvtNXxHRSp',
        //         'response' => $response,
        //     ]
        // );

        // $captchaSuccess = $verify->json();

        // if (!($captchaSuccess['success'] ?? false)) {
        //     return redirect()->route('individual.registration');
        // }


        $response = $request->input('g-recaptcha-response');

        if (empty($response)) {
            if(session()->has('otp_mobile')){
                return redirect()
                ->route('new-register')
                ->withInput()
                ->with('error', 'Please complete the captcha.');
            }else{
                return redirect('/')
                ->withInput()
                ->with('error', 'Please complete the captcha.');
            }
        }

        $verify = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret'   => env('RECAPTCHA_SECRET_KEY'),
                'response' => $response,
                'remoteip' => $request->ip(),
            ]
        );

        $captchaSuccess = $verify->json();

        if (!($captchaSuccess['success'] ?? false)) {
            return redirect('/')
                ->withInput()
                ->with('error', 'Captcha verification failed.');
        }

        if(session('otp_mobile') && session('otp_fullName')){
            $mobile   = session('otp_mobile');
            $email    = session('otp_email');
            $fullName = session('otp_fullName');
            $password = session('otp_password');
            session()->forget([
                'otp_mobile',
                'otp_email',
                'otp_fullName'
            ]);
        }else{
            $mobile   = $request->mobile;
            $email    = $request->email;
            $fullName = $request->fullName;
            $password = $request->password;
        }

        if (!$mobile || !$fullName || !$password) {
            return redirect('/');
        }

        $mobileNo = '88' . $mobile;
        $duplicateFlag = $registrationRepository->checkDuplicateUser($mobileNo);

        if ($duplicateFlag != 1) {
            return redirect('/')
                ->with('error', 'This number is already registered.');        
        }

        $dateTime = now();

        $flagResponseArr = $otpRepository->checkOtpGenerate($mobileNo);

        if ($flagResponseArr['generateFlag'] == 1) {

            $otp = random_int(100000, 999999);

            // SMS Send
            $smsArr = [
                'msgBody'    => 'Use ' . $otp . ' as your mobile verification code for Vroom',
                'msgType'    => 'registration',
                'mobileNo'   => $mobileNo,
                'userTpe'    => 'indv_reg',
                'userId'     => 'indv_reg',
                'logSmsBody' => 'mobileNo: '.$mobile.'|msgBody: Use '.$otp.' as your mobile verification code for Vroom',
            ];

            $this->smsSend($smsArr, $smsService, $sMSAndNotificationRepository);

            $otpData = [
                'mobile_no'       => $mobileNo,
                'user_id'         => null,
                'user_type_code'  => null,
                'encrypted_otp'   => md5($otp),
                'otp_type'        => 'indv_reg',
                'otp_status'      => 1,
                'created_by'      => $mobileNo,
                'created_dt_tm'   => $dateTime,
                'updated_by'      => $mobileNo,
                'updated_dt_tm'   => $dateTime,
            ];

            $otpRepository->insertOtp($otpData);

            $otpExpiredTime = Carbon::parse($dateTime)->addSeconds(config('constants.OTP_IDLE_TIME'));

        } elseif ($flagResponseArr['generateFlag'] == 2) {

            $otpExpiredTime = Carbon::parse($flagResponseArr['otpCreatedDtTm'])->addSeconds(config('constants.OTP_IDLE_TIME'));
        }

        //Session::flush();

        return view('login.indvRegOtpFormView', [
            'mobile'         => $mobile,
            'email'          => $email,
            'fullName'       => $fullName,
            'password'       => $password,
            'otpExpiredTime' => $otpExpiredTime,
            'msg'            => "A verification code has been sent to this number {$mobile}",
            'msgFlag'        => 'success',
            'slidingImageFlag' => 0,
        ]);
    }

    public function getCurrentDtTm()
    {
        return now()->format('Y-m-d H:i:s');
    }


    public function checkVerificationCode(Request $request, OtpRepository $otpRepository)
    {
        $mobile = '88' . trim($request->input('mobile'));
        $verificationCode = trim($request->input('verificationCode'));

        // 2 means not delete
        $response = $otpRepository->checkOtp(
            $mobile,
            $verificationCode,
            2
        );

        return response((string) $response);
    }

    public function createNewRegistration(
        Request $request, 
        OtpRepository $otpRepository,
        GenerateMonthlyToken $generateMonthlyToken,
        RegisterRepository $registrationRepository
        )
    {
        $verificationCode = trim($request->verificationCode);
        $fullName = trim($request->input('fullName'));
        $email = $request->filled('email') ?? null;

        $mobile = '88' . trim($request->input('mobile'));
        $password = trim($request->input('password'));
        // 1 means delete otp
        $otpResponse = $otpRepository->checkOtp(
            $mobile,
            $verificationCode,
            1
        );

        if ($otpResponse == 1) {

            $dateTime = now()->format('Y-m-d H:i:s');

            $userId = config('constants.INDV_EMP_CODE') . $generateMonthlyToken->get_month_token(config('constants.INDV_EMP_CODE'));

            // users table
            $usersData = [
                'user_id'             => $userId,
                'username'            => $mobile,
                'password'            => \Illuminate\Support\Facades\Hash::make($password),
                'password_reset_code' => null,
                'user_group'          => config('constants.INDV_DEFAULT_GRP'),
                'email'               => $email,
                'user_type_code'      => config('constants.USER_TYPE_INDV_EMP'),
                'panel_type'          => config('constants.CLIENT'),
                'full_name'           => $fullName,
                'contact_no'          => $mobile,
                'is_reset'            => 0,
                'created_by'          => $userId,
                'created_type'        => config('constants.USER_TYPE_INDV_EMP'),
                'created_dt_tm'       => $dateTime,
                'updated_by'          => $userId,
                'updated_type'        => config('constants.USER_TYPE_INDV_EMP'),
                'updated_dt_tm'       => $dateTime,
                'is_active'           => 1,
            ];

            // corporate_companies table
            $companyCode = config('constants.INDV_COMPANY_CODE') . $generateMonthlyToken->get_month_token(config('constants.INDV_COMPANY_CODE'));

            $companyArr = [
                'company_code'            => $companyCode,
                'package'                 => config('constants.INDV_PACKAGE'),
                'company_type'            => config('constants.INDIVIDUAL_CUST'),
                'title'                   => $fullName,
                'company_mobile'          => $mobile,
                'primary_contact_mobile'  => $mobile,
                'primary_contact_person'  => $fullName,
                'status'                  => 1,
                'created_by'              => $userId,
                'created_dt_tm'           => $dateTime,
                'updated_by'              => $userId,
                'updated_dt_tm'           => $dateTime,
            ];

            // customer_employee table
            $customerEmp = [
                'company'         => $companyCode,
                'employee_id'     => $userId,
                'employee_name'   => $fullName,
                'primary_mobile'  => $mobile,
                'customer_type'   => config('constants.INDIVIDUAL_CUST'),
                'emp_type'        => 'system_manager',
                'created_by'      => $userId,
                'created_type'    => config('constants.USER_TYPE_INDV_EMP'),
                'created_dt_tm'   => $dateTime,
                'updated_by'      => $userId,
                'updated_type'    => config('constants.USER_TYPE_INDV_EMP'),
                'updated_dt_tm'   => $dateTime,
                'system_user'     => 1,
            ];

            DB::beginTransaction();

            try {

                $response = $registrationRepository->createNewRegistration(
                    $usersData,
                    $customerEmp,
                    $companyArr
                );

                if ($response == 1) {

                    DB::commit();

                    // return view(
                    //     'website.registration.indvRegSuccessView',
                    //     [
                    //         'msg' => 'Thanks for being registered with Vroom Services Limited, We are glad to having you as our member',
                    //         'msgFlag' => 'success',
                    //         'slidingImageFlag' => 0,
                    //     ]
                    // );
                    return redirect()
                    ->route('login')
                    ->with('success', 'Thanks for being registered with Vroom Services Limited, We are glad to having you as our member.');
                }

                DB::rollBack();

                // return redirect()
                //     ->route('login')
                //     ->with('success', 'Your account has been created successfully. Please log in to continue.');

            } catch (\Exception $e) {

                DB::rollBack();

                throw $e;
            }

        } elseif ($otpResponse == 2) {

            session([
                'otp_mobile' => $request->mobile,
                'otp_email' => $request->email,
                'otp_fullName' => $request->fullName,
                'otp_password' => $request->password,
            ]);
            // otp does not match
            return redirect()
                ->route('new-register')
                ->with('error', 'OTP is not match.');

        } elseif ($otpResponse == 3) {
            session([
                'otp_mobile' => $request->mobile,
                'otp_email' => $request->email,
                'otp_fullName' => $request->fullName,
                'otp_password' => $request->password,
            ]);
            // otp expired
            return redirect()
                ->route('new-register')
                ->with('error', 'OTP Expired.');

        } else {
            return redirect('/');
        }
    }

    public function checkDuplicateUser(Request $request, RegisterRepository $registrationRepository)
    {
        $password = $request->input('password');

        if (passwordValidation($password) === 'invalid') {
            return response('3', 200);
        }

        $mobile = trim($request->input('mobile'));

        $result = $registrationRepository->checkDuplicateUser('88' . $mobile);

        return response((string) $result, 200);
    }

    private function smsSend($smsArr, $smsService, $sMSAndNotificationRepository){

        $customMsgBody = $smsArr['msgBody'];
        $msgType = $smsArr['msgType'];
        $responsedbdata = $sMSAndNotificationRepository->getDataForMess(NULL, $msgType, $customMsgBody, NULL, $smsArr['mobileNo'], NULL);
        if ($responsedbdata['msgCount'] > 0) {

            $smsService->sendMessage($responsedbdata['message']);

            $arr = array();
            $refNo = reference_no();
            $arr['reference_number'] = $refNo;
            $arr['sms_template'] = $msgType;
            $arr['sms_count'] = $responsedbdata['msgCount'];
            $arr['custom_sms'] = $customMsgBody;
            $arr['channel_type'] = 'mobileNo';
            $arr['module_type'] = 'registration';
            $arr['mobile_no'] = $smsArr['mobileNo'];
            $arr['mail_address'] = NULL;
            $arr['created_by'] = $smsArr['userId'];
            $arr['created_type'] = $smsArr['userTpe'];
            $arr['created_dt_tm'] = date('Y-m-d H:i:s');
            $arr['updated_by'] = $smsArr['userId'];
            $arr['updated_type'] = $smsArr['userTpe'];
            $arr['updated_dt_tm'] = date('Y-m-d H:i:s');
            SentSMS::create($arr);
        }
        // $log_data['reference_number'] = $refNo;
        // $log_data['message'] = "Forget Password for " . $smsArr['username'];
    }

}
