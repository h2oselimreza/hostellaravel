<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <!-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
        <!-- Bootstrap 5.3 -->
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
        <!-- Font Awesome 6 -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
        <!---exsisting code-->
        <style>
            .menu_header{
                background-color: #F79522;
            }
            .auth_button{
                margin:0px;
                list-style: none;
                float: right;
                margin-top: 12px;
            }
            .auth_button li{
                display: inline;
                padding-right: 30px;
            }
            .auth_button li a{
                text-decoration: none;
                color: #fff;
                font-weight: bold;
            }
            .logo img{
                width: 85%;
            }
            .banner{
                position: relative;
                overflow: hidden;
            }
            .banner::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5); /* adjust darkness here */
                z-index: 1;
            }
            .banner h1{
                position: absolute;
                top: 71px;
                left: 0;
                width: 100%;
                text-align: center;
                color: #fff;
                font-weight: bold;
                z-index: 999;
            }
            .registration_box{
                border: 1px solid #ddd;
                padding: 15px;
                -webkit-box-shadow: 1px 1px 5px -1px rgba(0,0,0,0.75);
                -moz-box-shadow: 1px 1px 5px -1px rgba(0,0,0,0.75);
                box-shadow: 1px 1px 5px -1px rgba(0,0,0,0.75);
            }
            .account_box{
                text-align: center;
            }
            .account_box img{
                width: 15%;
            }
            .account_box h4{
                font-size: 26px;
                margin-top: 8px;
                margin-bottom: 3px;
            }
            .account_box p{
                font-size: 15px;
                line-height: 22px;
                margin-bottom: 12px;
            }
            .account_box button{
                background: #F79522;
                border: 0px;
                color: #fff;
                padding: 4px 12px;
                font-size: 16px;
                border-radius: 4px;
            }
            .account_box button i{
                margin-left: 5px;
            }
            .registration_form{
                border: 1px solid #ddd;
                padding: 15px;
                -webkit-box-shadow: 1px 1px 5px -1px rgba(0,0,0,0.75);
                -moz-box-shadow: 1px 1px 5px -1px rgba(0,0,0,0.75);
                box-shadow: 1px 1px 5px -1px rgba(0,0,0,0.75);
            }
            .registration_form h4{
                text-align: center;
                margin-bottom: 5px;
                margin-bottom: 35px;
            }
            .form_item label{
                margin: 5px 0px 5px 0px;
            }
            .registration_button button{
                background: #F79522;
                border: 0px;
                color: #fff;
                padding: 4px 12px;
                font-size: 16px;
                border-radius: 4px;
                margin-top: 15px;
            }
            .custom-text-danger{
                color: red;
            }
            .save_button{
                background: #F79522;
                border: 0px;
                color: #fff;
                padding: 4px 12px;
                font-size: 16px;
                border-radius: 4px;
                margin-top: 12px;
            }
            .hidden{
                display: none;
            }
        </style>
    </head>
    <body>
        <div id="divTop"></div>
        <div class="menu_header">
            <div class="container py-1">
                <div class="row">
                    <div class="col-md-3">
                        <div class="logo">
                            <img src="{{ asset('assets/website/images/vroomCOMlogo.png') }}" alt="vroomCOMlogo">
                        </div>
                    </div>
                    <div class="col-md-9">
                        <ul class="auth_button">
                            <li><a href="/">Registration</a></li>
                            <li><a href="{{ route('login') }}">Login</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="account_box registration_box">
                        <img src="{{ asset('assets/website/images/boss.svg') }}" alt="boss">
                        <h4>Individual Account</h4>
                        <p>This account is best suited for you, if you have just one or may be a few vehicles.
                            It’s the ideal choice for individuals who want to manage vehicles online. </p>
                        <button>Registration <i class="fa fa-arrow-right"></i></button>
                    </div>
                    <div class="account_box registration_box mt-4">
                        <img src="{{ asset('assets/website/images/boss.svg') }}" alt="boss">
                        <h4>Fleet Management Account</h4>
                        <p>This type of account is best suited for you, if you have multiple vehicles. It’s the ideal choice for the fleet
                             manager and management of companies that intend to vehicle management online.  </p>
                        <button>Request For Demo <i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
                <div class="col-md-6">
                    <section class="other-page-content">
                        <div id="setOtpDiv" class="registration-box">
                            <div class="row">
                                <div class="text-center">
                                    <h3 style="font-weight:450">Individual Registration</h3>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12" >
                                    <div id="errorDiv" class="alert alert-danger hidden">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    </div>
                                </div>
                                <!-- Success Message -->
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Success!</strong> {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <!-- Error Message -->
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Error!</strong> {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                <form id="registrationForm" action="{{ route('registration.createNewRegistration') }}" method="POST">
                                    @csrf
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label class="form-label"> Verification Code </label>
                                            <span id="verificationCodeReq-Error" class="error hidden">Verification Code is required</span>
                                            <input type="text" class="form-control" name="verificationCode" id="verificationCode" autocomplete="new-password">
                                            <span> <small>Verification Code will be expired after <b><span id="otpExpireTime"></span></b> minutes</small></span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="fullName" value="<?php echo $fullName ?>">
                                    <input type="hidden" name="mobile" id="mobile" value="<?php echo $mobile ?>">
                                    <input type="hidden" name="email" id="email" value="<?php echo $email ?>">
                                    <input type="hidden" name="password" value="<?php echo $password ?>">
                                </form>

                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <button type="button" onclick="submitForm()" class="save_button">Submit</button>
                                </div>
                            </div>
                        </div>
                        <div id="resendOtpForm" style="display: none" class="registration-box">
                            <div class="row">
                                <div class="text-center">
                                    <h3 style="font-weight:450">Individual Registration</h3>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="alert alert-danger" style="">
                                        <strong>Your Verification Code has already expired</strong>
                                    </div>
                                </div>
                                <form  id="resendOtpSendForm" action="{{ route('new-register') }}" method="POST">
                                    @csrf
                                    <div class="col-md-12">
                                        <script src='https://www.google.com/recaptcha/api.js'></script>
                                        <div class="captcha_wrapper">
                                            <div id="recaptchaDiv" class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="fullName" value="<?php echo $fullName ?>">
                                    <input type="hidden" name="mobile" id="mobileResend" value="<?php echo $mobile ?>">
                                    <input type="hidden" name="email" id="emailResend" value="<?php echo $email ?>">
                                    <input type="hidden" name="password" value="<?php echo $password ?>">
                                    <input type="hidden" name="resendFlag" value="1">
                                </form>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <br>
                                    <button type="button" class="save_button" onclick="resendOtp()">Resend Code</button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            var otpExpiredDtTm = new Date('<?php echo $otpExpiredTime ?>');
            var currentDtTm = "";
            $.ajax({
                url: '{{route("registration.getCurrentDtTm")}}',
                success: function (result) {
                    currentDtTm = new Date(result);
                    calculateTime();
                }
            });
            var timeExpireOtp = "";

            function calculateTime() {
                var seconds = Math.floor((otpExpiredDtTm - (currentDtTm)) / 1000);
                var minutes = Math.floor(seconds / 60);
                var hours = Math.floor(minutes / 60);
                var days = Math.floor(hours / 24);

                hours = hours - (days * 24);
                minutes = minutes - (days * 24 * 60) - (hours * 60);
                seconds = seconds - (days * 24 * 60 * 60) - (hours * 60 * 60) - (minutes * 60);
                timeExpireOtp = '00:' + minutes + ':' + seconds;
                setTimeout(showExpireTime, 1000);
            }

            function showExpireTime() {
                var myTime = timeExpireOtp;
                var ss = myTime.split(":");
                var dt = new Date();
                dt.setHours(ss[0]);
                dt.setMinutes(ss[1]);
                dt.setSeconds(ss[2]);
                var dt2 = new Date(dt.valueOf() - 1000);
                var ts = dt2.toTimeString().split(" ")[0];
                var arr = ts.split(':');
                timeExpireOtp = ts;

                $('#otpExpireTime').text(arr[1] + ':' + arr[2]);
                if ((arr[1] + ':' + arr[2]) === '00:00') {
                    $('#setOtpDiv').remove();
                    $('#resendOtpForm').show();
                } else {
                    setTimeout(showExpireTime, 1000);
                }
            }



            function submitForm() {
                var errorMsg = "";
                var fieldsArr = ["verificationCode|verificationCodeReq-Error"];

                var inputFiledJsonData = getInputData(fieldsArr);

                if (!inputFiledJsonData) {
                    errorMsg += getReuiredFiledErrorMsg();
                    showErrorMsg(errorMsg);
                    return false;
                } else {
                    hideErrorDiv();
                }

                showLoader();

                $.ajax({
                    type: "POST",
                    url: "{{ route('registration.checkVerificationCode') }}",

                    data: {
                        _token: "{{ csrf_token() }}",
                        mobile: $.trim($('#mobile').val()),
                        verificationCode: $.trim($('#verificationCode').val())
                    },

                    success: function(result) {

                        hideLoader();

                        if (result == '1') {
                            $('#registrationForm').submit();

                        } else if (result == '2') {
                            sweetAlert('You have entered a wrong Verification code...!');
                            return false;

                        } else if (result == '3') {
                            sweetAlert('Your verification code has been expired...!');
                            return false;
                        }
                    },

                    error: function(xhr) {

                        hideLoader();

                        console.log(xhr.responseText);

                        sweetAlert('Something went wrong. Please try again.');
                    }
                });
            }

            function resendOtp() {
                var $captcha = $('#recaptchaDiv'),
                        response = grecaptcha.getResponse();
                if (response.length === 0) {
                    sweetAlert('Please complete the reCAPTCHA.');
                    return false;
                }
                $('#resendOtpSendForm').submit();
            }


            function redirectFunc(school, flag) {
                if (flag === '1') {
                    window.location.href = "/" + school;
                } else if (flag === '2') {
                    window.location.href = "/Registration/registrationShow/" + school;
                }
            }

            function getInputData(fieldsArr) {
                var jsonVariable = {};
                var inputArr = new Array();
                var requiredFlag = 1;
                for (var i = 0; i < fieldsArr.length; i++) {
                    var inputTextValue = "";
                    inputArr = fieldsArr[i].split("|");

                    inputTextValue = $.trim($("#" + inputArr[0]).val());
                    if (inputArr.length === 2) {
                        if (inputTextValue === "") {
                            requiredFlag = 0;
                            $("#" + inputArr[1]).attr("class", "error");
                        } else {
                            $("#" + inputArr[1]).attr("class", "hidden error");
                        }
                    }
                    jsonVariable[inputArr[0]] = inputTextValue;
                }
                if (requiredFlag === 0) {
                    return false;
                }
                return jsonVariable;
            }

            function getReuiredFiledErrorMsg() {
                return "<strong><li>Fields are requried</li></strong>";
            }

            function hideErrorDiv() {
                $("#errorDiv").attr("class", "alert alert-danger hidden");
            }
            function showErrorMsg(errorMsg = "") {
                if (errorMsg !== "") {
                    $("#errorDiv").attr("class", "alert alert-danger");
                    document.getElementById("errorDiv").innerHTML = errorMsg;
                    var etop = $("#divTop").offset().top;
                    $("html, body").animate(
                        {
                            scrollTop: etop,
                        },
                        1000,
                    );
                }
            }
            function showLoader() {
                $("#loader").show();
            }

            function hideLoader() {
                $("#loader").hide();
            }
        </script>
    </body>
</html>
