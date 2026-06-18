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

        <!-- DataTables Bootstrap 5 -->
        <link href="https://cdn.datatables.net/v/dt/dt-2.3.7/datatables.min.css" rel="stylesheet" integrity="sha384-wCnlGUpaekN+Mtc+qIoipdqIqe2dvC7hWyzVg8wajZ1sxKnVTbnyBd7pyx7JT0Su" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('assets/css/buttons.bootstrap5.min.css') }}">

        <!-- jQuery UI (latest) -->
        <link href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('assets/jquery-ui/css/jquery-ui.css') }}">

        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="{{ asset('assets/sweetalert2/css/sweetalert2.min.css') }}">

        <!-- Bootstrap Datepicker -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">

        <!-- Ckeditor -->
        <!-- <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/47.5.0/ckeditor5.css"> -->
         <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">

        <!-- Your custom styles -->
        <link rel="stylesheet" href="{{ asset('assets/select_bo/css/theme.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/select_bo/css/myStyle.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <!-- Vite -->
        @vite(['resources/css/app.css'])


        <script>
            function hideSideBar() {
                if ($('#sideNavBar').css('display') != 'none') {
                    $('#sideNavBar').hide();
                    $('#contentDiv').css('margin-left', '0px');
                } else if ($('#sideNavBar').css('display') == 'none') {
                    $('#contentDiv').css('margin-left', '240px');
                    $('#sideNavBar').show();
                }
            }

            // tinyMCE.init({
            //     mode: "specific_textareas",
            //     editor_selector: "tinyMcTextArea"
            // });
        </script>

        <!---exsisting code-->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
    </head>
    <body class="theme-blue">
        <form id="delete-form" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        <div class="min-h-screen">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">

                    <!-- Mobile toggle -->
                    <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Brand -->
                    <a class="navbar-brand d-lg-none" href="#">{{ env('COMPANY_NAME', 'Default Company') }}</a>

                    <!-- Navbar content -->
                    <div class="navbar-collapse" id="mainNavbar">

                        <!-- Sidebar toggle -->
                        <div id="sideNavigation" class="d-flex align-items-center me-auto">
                            <i class="fa-solid fa-bars fs-5 me-2" onclick="hideSideBar()" role="button"></i>
                            <a class="navbar-brand mb-0" href="#">{{ env('COMPANY_NAME', 'Default Company') }}</a>
                        </div>

                        <!-- Right menu -->
                        <ul class="navbar-nav ms-auto">

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle"
                                href="#"
                                role="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">

                                    <i class="fa-solid fa-user me-1"></i>
                                    {{ auth()->user()->name ?? 'User' }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <a class="dropdown-item" target="_blank" href="#">
                                            Change Password
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>

                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button class="dropdown-item" type="submit">
                                                Logout
                                            </button>
                                        </form>
                                    </li>

                                </ul>
                            </li>

                        </ul>
                    </div>
                </div>
            </nav>

            @include('layouts.navigation')
            <div class="content" id="contentDiv">
                <div id="divTop">
                    <div>
                        @yield('content')
                        <div id="overlay" style="display: none">
                            <div class="spinner"></div> 
                        </div>
                    </div>
                    <footer class="mt-5 py-3 border-top">
                        <div class="container">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                                <!-- Left -->
                                <p class="mb-2 mb-md-0">
                                    © 2026 
                                    <a href="#" target="_blank" class="text-decoration-none fw-semibold">
                                        ArrowLink™ Soft
                                    </a>
                                </p>

                                <!-- Right -->
                                <p class="mb-0">
                                    Developed by 
                                    <a href="#" target="_blank" class="text-decoration-none fw-semibold">
                                        ArrowLink™ Soft
                                    </a>
                                </p>

                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
        <!-- jQuery 3.7 -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- Bootstrap 5 Bundle -->
            <script src="{{ asset('assets/css/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>

        <!-- DataTables -->
        <!-- <script src="{{ asset('assets/dataTables/js/dataTables.min.js') }}"></script> -->
        <!-- <script src="{{ asset('assets/dataTables/js/dataTables.bootstrap5.min.js') }}"></script> -->
         <script src="https://cdn.datatables.net/v/dt/dt-2.3.7/datatables.min.js" integrity="sha384-aQ8I1X2x8U0AR8D7C4Ah0OvZlwMslQdN5YDAQBA56jXrrhcECijs/i7H+5DDrlV1" crossorigin="anonymous"></script>

        <!-- DataTables Buttons -->
        <!-- <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script> -->
        <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

        <!-- Export deps -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

        <!-- jQuery UI -->
        <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
        <script src="https://code.jquery.com/ui/1.14.2/jquery-ui.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Bootstrap Datepicker -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.10.0/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
        <!-- TinyMCE (latest) -->
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

        <!-- Your scripts -->
        <script src="{{ asset('assets/select_bo/js/myScript.js') }}"></script>

        <!-- <script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js"></script> -->
        <!-- <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script> -->
        <!-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> -->
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.9/tinymce.min.js"></script> -->
         <!-- Quill JS -->
        <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>

        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        <script src="{{ asset('assets/select_client/js/header.js') }}"></script>

        <!-- Vite -->
        @vite(['resources/js/app.js'])
        {{-- PAGE-SPECIFIC SCRIPTS --}}
        
        @stack('scripts')
        <script>

            function showLoader() {
                $('#overlay').show();
            }
            function hideLoader() {
                $('#overlay').hide();
            }

            $(document).ready(function(){
                $('.dateInput').datepicker({
                    format: 'yyyy-mm-dd',  // format compatible with Laravel date column
                    autoclose: true,       // close picker after selecting a date
                    todayHighlight: true,  // highlight today
                    clearBtn: true,        // optional clear button
                    orientation: 'bottom'  // show below the input
                });
            });
            $('.timepicker').timepicker({
                // showMeridian: true,
                // defaultTime: false,
                // explicitMode: true   // ✅ IMPORTANT FIX
                defaultTime: false,
                disableFocus: true
            });
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

            function hideErrorDiv() {
                $("#errorDiv").attr('class', 'alert alert-danger hidden');
            }
            function getReuiredFiledErrorMsg() {
                return "<strong><li>Fields are requried</li></strong>";
            }
        </script>
    </body>
</html>
