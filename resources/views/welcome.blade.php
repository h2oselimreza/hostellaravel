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
        <div class="banner">
            <img src="{{ asset('assets/website/images/banner.jpg') }}" alt="banner">
            <h1>Individual Registration</h1>
        </div>
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6">
                    <div class="account_box registration_box">
                        <img src="{{ asset('assets/website/images/boss.svg') }}" alt="boss">
                        <h4>Individual Account</h4>
                        <p>This account is best suited for you, if you have just one or may be a few vehicles.
                            It’s the ideal choice for individuals who want to manage vehicles online. </p>
                        <a href="/"><button>Registration <i class="fa fa-arrow-right"></i></button></a>
                    </div>
                    <div class="account_box registration_box mt-4">
                        <img src="{{ asset('assets/website/images/organization.svg') }}" alt="boss">
                        <h4>Fleet Management Account</h4>
                        <p>This type of account is best suited for you, if you have multiple vehicles. It’s the ideal choice for the fleet
                             manager and management of companies that intend to vehicle management online.  </p>
                        <button>Request For Demo <i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="errorDiv" class="alert alert-danger hidden">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    </div>
                    <div class="registration_form">
                        <h4>Individual Registration</h4>
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
                        <form id="registrationForm" action="{{ route('new-register') }}" method="post">
                            @csrf
                            <div class="form_item">
                                <div class="row">
                                    <div class="col-md-12">    
                                        <div class="form-group">
                                            <label>  Full Name </label><small class="custom-text-danger"> *</small>
                                            <input type="text" class="form-control" name="fullName" id="fullName">
                                        </div>
                                    </div>
                                    <div class="col-md-12">    
                                        <div class="form-group">
                                            <label> Mobile No <small><i>(eg. 017XXXXXXX)</i></small></label><small class="custom-text-danger"> *</small>
                                            <input type="text" class="form-control" name="mobile" id="mobile" onchange="checkMobileNumberWithout88(this.value, this.id)">
                                        </div>
                                    </div>

                                    <div class="col-md-12"> 
                                        <div class="form-group">
                                            <label>  Email </label>
                                            <input type="text" class="form-control" name="email" id="Email" onchange="checkEmail(this.value, this.id)">
                                        </div>
                                    </div>
                                    <div class="col-md-12"> 
                                        <div class="form-group">
                                            <label> Password <small><i>(Min 8 digit. One letter and one number)</i></small></label><small class="custom-text-danger"> *</small>
                                            <input type="password" class="form-control" name="password" id="password" autocomplete="new-password">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>  Retype Password </label><small class="custom-text-danger"> *</small>
                                            <input type="password" class="form-control" name="repassword" id="repassword" autocomplete="new-password">
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <input type="checkbox" id="termConditionCheck" name="termConditionCheck"> Please confirm that you agree to our <a target="_blank" href="http://localhost/old_project2/Home/termsCondition">Terms &amp; Conditions</a>
                                        <br>
                                        <small class="custom-text-danger" style="display:none" id="termConditionCheckReq-Error"> Please checked this, if you are agree</small>
                                        <br>
                                    </div>

                                    <div class="col-md-12">
                                        <script src='https://www.google.com/recaptcha/api.js'></script>
                                        <div class="captcha_wrapper">
                                            <div id="recaptchaDiv" class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 registration_button">    
                                        <button type="button" class="btn" onclick="submitForm()">Registration</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            function submitForm() {
                var errorMsg = "";
                var fieldsArr = new Array("fullName|fullNameReq-Error", "mobile|mobileNoReq-Error", "password|passwordReq-Error", "repassword|rePasswordReq-Error");  // filed id, error div id
                var inputFiledJsonData = getInputData(fieldsArr);
                if (!inputFiledJsonData) {
                    errorMsg += getReuiredFiledErrorMsg();
                    showErrorMsg(errorMsg);
                    return false;  // required filed check
                } else {
                    hideErrorDiv();
                }

                if (!$("#termConditionCheck").is(':checked')) {
                    $('#termConditionCheckReq-Error').show();
                    return false;
                } else {
                    $('#termConditionCheckReq-Error').hide();
                }

                if ($.trim($('#password').val()) !== $.trim($('#repassword').val())) {
                    sweetAlert('Password and Re-type Passord doesnot match...!');
                    return false;
                }

                var $captcha = $('#recaptchaDiv'),
                        response = grecaptcha.getResponse();

                if (response.length === 0) {
                    sweetAlert('Recaptcha not select...!');
                    return false;
                }

                showLoader();
        //        return false;
                $.ajax({
                    type: 'POST',
                    url: '{{ route("registration.checkDuplicateUser") }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mobile: $.trim($('#mobile').val()),
                        password: $.trim($('#password').val())
                    },
                    success: function (result) {
                        hideLoader();

                        if (result == '1') {
                            $('#registrationForm').submit();
                        } else if (result == '2') {
                            sweetAlert('This mobile number is already registered!');
                        } else if (result == '3') {
                            sweetAlert('Your password pattern is invalid. Please enter a valid password!');
                        }
                    },
                    error: function (xhr) {
                        hideLoader();
                        console.log(xhr.responseText);
                    }
                });

            }

            function getInputData(fieldsArr) {
                var jsonVariable = {};
                var inputArr = new Array();
                var requiredFlag = 1;
                for (var i = 0; i < fieldsArr.length; i++) {
                    var inputTextValue = "";
                    inputArr = fieldsArr[i].split('|');

                    inputTextValue = $.trim($('#' + inputArr[0]).val());  // inputArr[0] text filed id
                    if (inputArr.length === 2) {  // 2 means this filed is required
                        if (inputTextValue === "") {
                            requiredFlag = 0;
                            $("#" + inputArr[1]).attr('class', 'custom-text-danger');  // inputArr[1] is required field's error div id
                        } else {
                            $("#" + inputArr[1]).attr('class', 'hidden custom-text-danger');
                        }
                    }
                    jsonVariable[inputArr[0]] = inputTextValue;
                }
                if (requiredFlag === 0) {
                    return false;  // required field validation return false
                }
                return jsonVariable;
            }

            function getReuiredFiledErrorMsg() {
                return "<strong><li>Fields are requried</li></strong>";
            }

            function hideErrorDiv() {
                $("#errorDiv").attr("class", "alert alert-danger hidden");
            }

            function showErrorMsg(errorMsg = ""){
                if (errorMsg !== "") {
                    $("#errorDiv").attr('class', 'alert alert-danger');
                    document.getElementById('errorDiv').innerHTML = errorMsg;
                    var etop = $('#divTop').offset().top;
                    $('html, body').animate({
                        scrollTop: etop
                    }, 1000);
                }
            }

            function showLoader() {
                $("#loader").show();
            }

            function hideLoader() {
                $("#loader").hide();
            }

            function checkMobileNumberWithout88(mobileNumber, fieldId) {

                if (mobileNumber.length === 11) {

                    var re = /^01[3-9][0-9]{8}$/;

                    if (!re.test(mobileNumber)) {
                        sweetAlert("Please enter valid mobile number...! eg. 017XXXXXXXX");
                        document.getElementById(fieldId).value = '';
                        document.getElementById(fieldId).select();
                    }

                } else {
                    sweetAlert("Please enter valid mobile number...! eg. 017XXXXXXXX");
                    document.getElementById(fieldId).value = '';
                    document.getElementById(fieldId).select();
                }
            }
        </script>
    </body>
</html>
