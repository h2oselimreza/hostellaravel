<x-guest-layout>
    <style>
        .vroom_logo img{
            width: 46%;
            height: auto;
        }
        .switch_button{
            font-size: 18px;
            cursor: pointer;
        }
        .login_active{
            color: #F79522;
        }
        .arrow_link{
            text-align: center;
            margin-top: 21px;
            color: #555;
            font-size: 15px;
        }
        /* .g-recaptcha {
            transform: scale(1.01);
            -webkit-transform: scale(1.01);
            transform-origin: left top;
            -webkit-transform-origin: left top;
        } */
    </style>
     <div class=" text-center vroom_logo">
        <img src="{{ asset('assets/website/images/vroom_logo.png') }}" alt="Logo" class="mx-auto h-16">
    </div>

    <div class="text-center font-20 mt-3 mb-3">
        <b>
            <span id="corporateHeading" class="corporate switch_button login_active" onclick="loginType('1')">Hostel</span> 
            {{-- <span id="invidualHeading" class="individual switch_button" onclick="loginType('2')">Individual </span> --}}
        </b>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
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
    <form method="POST" action="{{ route('login') }}" onsubmit="return validateCaptcha();">
        @csrf

        <!-- Email Address -->
        <div>
            {{-- <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" /> --}}
            <div id="mobile_no" style="display: none">
                <x-input-label for="Mobile No" :value="__('Mobile No')" />
            </div>
            <div id="username">
                <x-input-label for="username" :value="__('Username or Mobile No')" />
            </div>
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        {{-- <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div> --}}
        <div class="col-md-12 mt-5">
            <script src='https://www.google.com/recaptcha/api.js'></script>
            <div class="captcha_wrapper">
                <div id="recaptchaDiv" class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">

            <div class="flex items-center gap-1">
                {{-- <a href="{{ url('/') }}" class="underline text-sm" style="color:#00BCD4">
                    Registration
                </a>

                <span>|</span> --}}

                <a href="{{ route('password.request') }}" class="underline text-sm" style="color:#00BCD4;">
                    Forgot your password?
                </a>
            </div>

            <x-primary-button style="background-color:#00BCD4;">
                Log in
            </x-primary-button>

        </div>
        <div class="arrow_link">
            <p>Developed By ArrowLink™ Soft</p>
        </div>
    </form>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    function loginType(type) {

        const corporateHeading = document.getElementById('corporateHeading');
        const individualHeading = document.getElementById('invidualHeading');

        const usernameDiv = document.getElementById('username');
        const mobileDiv = document.getElementById('mobile_no');

        if (type == '1') {

            corporateHeading.classList.add('login_active');
            individualHeading.classList.remove('login_active');

            usernameDiv.style.display = 'block';
            mobileDiv.style.display = 'none';

        } else {

            individualHeading.classList.add('login_active');
            corporateHeading.classList.remove('login_active');

            usernameDiv.style.display = 'none';
            mobileDiv.style.display = 'block';
        }
    }
</script>
<script>
    function validateCaptcha() {
        var response = grecaptcha.getResponse();

        if (response.length === 0) {
            sweetAlert('Please complete the reCAPTCHA.');
            return false;
        }

        return true;
    }
</script>
</x-guest-layout>
