<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="smartpos">

<head>
    <title>{{ config('app.name', 'DWASAL') }} : {{$page}}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="description" content="The All in One Accounting Software">
    <meta name="author" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('assets/img/icon.png') }}" type="image/png">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/notifications/css/lobibox.min.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @yield('page-styles')
    <!-- <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}"> -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.css') }}"> -->

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
    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/color_skins.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>

<body class="theme-orange">

    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30"><img src="{{ asset('assets/images/loader-5.gif') }}" width="48" height="48" alt="The 1Biz"></div>
            <p>Please wait...</p>        
        </div>
    </div>
    <!-- Overlay For Sidebars -->

    <div id="wrapper">

        <nav class="navbar navbar-fixed-top">
            <div class="container">
                <div class="navbar-brand">
                    <a href="{{ url('/') }}">DWASAL</a>
                    <!-- <a href="{{ url('/')}}"><img src="{{ asset('assets/images/logo.png') }}" alt="DWASAL" class="img-responsive logo" style="width: 150px; height: 50px;"></a>                 -->
                </div>
                
                <div class="navbar-right">
                    <form id="navbar-search" class="navbar-form search-form" action="{{ url('switch-shop') }}" method="POST" style="padding-top: 5px;">
                        @csrf
                        <select name="shop_id" id="auto_submit" onchange='if(this.value != 0) { this.form.submit(); }' class="form-select form-select-sm mb-1 select-bar-box">
                            @foreach (Auth::user()->shops()->get() as $key => $myshop)
                            @if ($myshop->id == Session::get('shop_id'))
                            <option value="{{ $myshop->id }}" selected>S/N-{{sprintf('%03d', $myshop->id)}}-{{ $myshop->name }}</option>
                            @else
                            <option value="{{ $myshop->id }}">S/N-{{sprintf('%03d', $myshop->id)}}-{{ $myshop->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </form>               

                    <div id="navbar-menu">
                        <ul class="nav navbar-nav">  
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle icon-menu" data-toggle="dropdown"><i class="icon-settings"></i></a>
                                <ul class="dropdown-menu user-menu menu-icon animated bounceIn">
                                    <li class="menu-heading">PROFILE SETTINGS</li>
                                    <li><a href="{{ url('user-profile') }}"><i class="icon-user"></i> <span>My Account</span></a></li>
                                    <li><a href="javascript:;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="icon-login"></i> <span>Logout</span></a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="icon-menu d-none d-sm-block rightbar_btn"><i class="icon-equalizer"></i></a>
                            </li>
                            <li><a href="javascript:;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="icon-menu d-none d-sm-none d-md-none d-lg-block"><i class="icon-login"></i></a>
                            </li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </div>
                </div>

                <div class="navbar-btn">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-expanded="false">
                        <i class="lnr lnr-menu fa fa-bars"></i>
                    </button>
                </div>
            </div>
        </nav>

        <div id="rightbar" class="rightbar">
            <div class="sidebar-scroll">            
                <!-- Nav tabs -->
                <ul class="nav nav-tabs-new">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#setting"><i class="icon-settings"></i></a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#question"><i class="icon-question"></i></a></li>                
                </ul>
                    
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane animated fadeIn active" id="setting">
                        <h6>Choose Skin</h6>
                        <ul class="choose-skin list-unstyled">
                            <li data-theme="purple">
                                <div class="purple"></div>
                                <span>Purple</span>
                            </li>                   
                            <li data-theme="blue">
                                <div class="blue"></div>
                                <span>Blue</span>
                            </li>
                            <li data-theme="cyan">
                                <div class="cyan"></div>
                                <span>Cyan</span>
                            </li>
                            <li data-theme="green">
                                <div class="green"></div>
                                <span>Green</span>
                            </li>
                            <li data-theme="orange" class="active">
                                <div class="orange"></div>
                                <span>Orange</span>
                            </li>
                            <li data-theme="blush">
                                <div class="blush"></div>
                                <span>Blush</span>
                            </li>
                        </ul>
                        <hr>
                    </div>
                    <div class="tab-pane animated fadeIn" id="question">
                        <form>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" ><i class="icon-magnifier"></i></span>
                                </div>
                                <input type="text" class="form-control" placeholder="Search...">
                            </div>
                        </form>
                        <ul class="list-unstyled question">
                            <li class="menu-heading">HOW-TO</li>
                            <li><a href="javascript:void(0);">How to Add New User</a></li>
                            <li><a href="javascript:void(0);">Boost Your Sales</a></li>
                            <li class="menu-heading">ACCOUNT</li>
                            <li><a href="javascript:void(0);">Add New Business</a></li>
                            <li><a href="javascript:void(0);">Change Password?</a></li>
                            <li><a href="javascript:void(0);">Privacy &amp; Policy</a></li>
                            <li class="menu-heading">BILLING</li>
                            <li><a href="javascript:void(0);">Payment info</a></li>                       
                            <li class="menu-button m-t-30">
                                <a href="javascript:void(0);" class="btn btn-primary"><i class="icon-question"></i> Need Help?</a>
                            </li>
                        </ul>
                    </div>                
                </div>          
            </div>
        </div>

        <div class="main_menu">
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <div class="navbar-collapse align-items-center collapse" id="navbar">
                        <ul class="navbar-nav">
                            <li class="nav-item {{ request()->is('prod-dash') ? 'active' : '' }}">
                                <a href="{{ url('sand-prod-dash') }}" class="nav-link"><i class="icon-home"></i> <span> Dashboard</span></a>
                            </li>
                            <li class="nav-item dropdown {{ request()->is('sand-productions') || request()->is('sand-productions') ? 'active' : '' }}">
                                <a href="javascript:void(0)" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="icon-list"></i>Washed Sand Productions<span></span></a>
                                <ul class="dropdown-menu animated bounceIn">
                                    <li><a href="{{ route('sand-productions.create') }}">New Production Run</a></li>        
                                    <li><a href="{{ url('sand-productions') }}">Production Records</a></li>
                                    <li><a href="{{ url('quality-tests') }}">Quality Tests</a></li>
                                    <li><a href="{{ url('rm-sourcings') }}">Raw Material Sourcings</a></li>
                                    <li><a href="{{ url('raw-material-sources') }}">Raw Material Sources</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown {{ request()->is('washing-plants') || request()->is('washing-equipments') ? 'active' : '' }}">
                                <a href="javascript:void(0)" class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="icon-grid"></i> Washing Plants & Equipments<span></span></a>
                                <ul class="dropdown-menu animated bounceIn">
                                    <li><a href="{{ url('washing-plants') }}">Washing Plants</a></li>
                                    <li><a href="{{ url('washing-equipments') }}">Washing Equipments</a></li>
                                </ul>
                            </li>
                            <li class="nav-item {{ request()->is('storage-locations') ? 'active' : '' }}">
                                <a href="{{ url('storage-locations') }}" class="nav-link"><i class="fa fa-map-marker"></i> <span> Storage Locations</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>

        <div id="main-content">
            <div class="container">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Javascript -->
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script>
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
    <script src="{{ asset('assets/bundles/mainscripts.bundle.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/index.js') }}"></script> -->
    <script src="{{ asset('assets/vendor/notifications/js/notification-custom-script.js') }}"></script>

    @if ($message = Session::get('success'))
        <script>
            $(document).ready(function() {
                success_noti("{{ $message }}");
            });
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            $(document).ready(function() {
                console.log("{{ $message }}");
                error_noti("{{ $message }}");
            });
        </script>
    @endif

    @if ($message = Session::get('info'))
        <script>
            $(document).ready(function() {
                info_noti("{{ $message }}");
            });
        </script>
    @endif

    @if ($message = Session::get('warning'))
        <script>
            $(document).ready(function() {
                warning_noti("{{ $message }}");
            });
        </script>
    @endif

        <!-- page script -->
   <?php
        
        $dur = '';
        $is_post = '';
        $startdate = '';
        $enddate = '';

        if ($page == 'Home' || $page == 'Raw Material Sourcings' || $page == 'Production Runs') {
            $is_post = $is_post_query;
            $startdate = $start_date;
            $enddate = $end_date;
            $dur = $duration;
        }
        
    ?>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
    <script>
        $(document).ready(function() {
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;

            var duration = "<?php echo $dur; ?>";

            //Date range as a button
            var start = moment();
            var end = moment();
            var reportrangequery = false;
            var startstring = "<?php echo $startdate; ?>";
            var endstring = "<?php echo $enddate; ?>";
            start = moment(startstring, 'YYYY-MM-DD');
            end = moment(endstring, 'YYYY-MM-DD');
            
            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                if (reportrangequery) {
                    $('#start_input').val(start.format('YYYY-MM-DD'));
                    $('#end_input').val(end.format('YYYY-MM-DD'));
                    $('.dashform').submit();
                }
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year')
                        .endOf('year')
                    ]
                }
            }, cb)
            cb(start, end);

            $('#reportrange').on('click.daterangepicker', function() {
                // $('.dashform').submit();
                reportrangequery = true;
            });

            $('[data-mask]').inputmask();

            $('#my-select').select2({
                dropdownParent: $('#itemModal')
            });

            $('#serv-select').select2({
                dropdownParent: $('#servitemModal')
            });

            $('.select2').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $(this).parent(),
                });
            });
            
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
    @yield('page-scripts')
</body>
</html>
