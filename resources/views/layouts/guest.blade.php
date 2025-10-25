<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="smartpos">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <link rel="icon" href="{{ asset('assets/img/icon.png') }}" type="image/x-icon"> <!-- Favicon-->
    <title>Smart Mauzo Enterprise | {{$page}}</title>
    <!-- Application Vendor CSS URL -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/notifications/css/lobibox.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/toastr.min.css') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap">
    <link rel="stylesheet" href="{{ asset('assets/vendor/parsleyjs/css/parsley.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}">

    <!-- Daterange Picker -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @yield('page-styles')
    <!-- project css file  -->
    <link rel="stylesheet" href="{{ asset('assets/css/lucid-style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body id="layout" data-lucid="theme-blue">
    <!-- Page Loader -->
    <div class="page-loader-wrapper text-center">
        <div class="loader">
            <img src="{{ asset('assets/img/loader.gif')}}">
            <div class="h5 fw-light mt-3">Please wait</div>
        </div>
    </div>
    <div id="wrapper">
        <!-- top navbar -->
        <nav class="navbar navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-btn">
                    <button type="button" class="btn-toggle-offcanvas"><i class="fa fa-bars"></i></button>
                </div>
                <div class="navbar-brand ps-2">
                    <a href="{{ url('my-default-page') }}">
                        <img src="{{ asset('assets/img/icon.png') }}" width="30"> 
                        <small>Smart Mauzo</small>
                    </a>
                </div>
                <div class="d-flex flex-grow-1 align-items-center">
                    
                </div>
            </div>
        </nav>
        <!-- Sidbar menu -->
        
        <div id="main-content" style="width: 100%;">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Jquery Page Js -->
    <!-- Jquery Core Js -->
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <!-- Project Js -->
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/toastr.js') }}"></script>

     <!-- InputMask -->
    <script src="{{ asset('assets/vendor/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('assets/vendor/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('assets/vendor/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- date-range-picker -->
    <script src="{{ asset('assets/vendor/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!--notification js -->
    <script src="{{ asset('assets/vendor/notifications/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/notifications/js/notifications.min.js') }}"></script>

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.js') }}"></script>
    
    <!-- Jquery Page Js -->
    <script src="{{ asset('assets/vendor/notifications/js/notification-custom-script.js') }}"></script>
</body>

</html>