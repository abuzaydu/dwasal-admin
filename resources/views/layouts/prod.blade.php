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
    <title>{{ config('app.name', 'DWASAL') }} : {{$page}}</title>
    <!-- Application Vendor CSS URL -->
    <link rel="stylesheet" href="{{ asset('side/assets/vendor/notifications/css/lobibox.min.css') }}" />

    <link rel="stylesheet" href="{{ asset('side/assets/vendor/toastr/toastr.min.css') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('side/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('side/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap">
    <link rel="stylesheet" href="{{ asset('side/assets/vendor/parsleyjs/css/parsley.css') }}">
    <link rel="stylesheet" href="{{ asset('side/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('side/assets/vendor/font-awesome/css/font-awesome.min.css') }}">

    <!-- Daterange Picker -->
    <link rel="stylesheet" href="{{ asset('side/assets/vendor/bootstrap-daterangepicker/daterangepicker.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @yield('page-styles')
    <!-- project css file  -->
    <link rel="stylesheet" href="{{ asset('side/assets/css/lucid-style.css') }}">
    <link rel="stylesheet" href="{{ asset('side/assets/css/custom.css') }}">
</head>
<?php
$headercolor = Session::get('headercolor');
$sidebarcolor = Session::get('sidebarcolor');
$company = App\Models\Company::find(Session::get('company_id'));
$shop = App\Models\Shop::find(Session::get('shop_id'));
$settings = App\Models\Setting::where('shop_id', $shop->id)->first();
?>

<body id="layout" data-lucid="theme-orange">
    <!-- Page Loader -->
    <div class="page-loader-wrapper text-center">
        <div class="loader">
            <img src="{{ asset('side/assets/img/loader.gif')}}">
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
                    <a href="{{ url('/home') }}">
                        <img src="{{ asset('assets/img/icon.png') }}" width="30"> 
                        <small>DWASAL</small>
                    </a>
                </div>
                <div class="d-flex flex-grow-1 align-items-center">
                    <div class="flex-grow-1" id="hide-on-mobile">
                        <marquee>{{$company->name}}</marquee>
                    </div>
                    <div class="flex-grow-1">
                        <ul class="nav navbar-nav flex-row justify-content-end align-items-center">
                            <li class="d-none d-sm-block">
                                <form id="navbar-search" class="navbar-form search-form" action="{{ url('switch-shop') }}" method="POST" style="padding-top: 5px;">
                                    @csrf
                                    <select name="shop_id" id="auto_submit" onchange='if(this.value != 0) { this.form.submit(); }' class="form-select form-select-sm mb-1 select-bar-box">
                                        @foreach (Auth::user()->shops()->get() as $key => $myshop)
                                        @if ($myshop->id == Session::get('shop_id'))
                                        <option value="{{ $myshop->id }}" selected>{{ $myshop->name }}</option>
                                        @else
                                        <option value="{{ $myshop->id }}">{{ $myshop->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </form>
                            </li>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                            <li class="dropdown">
                                <a class="dropdown-toggle icon-menu" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-bell"></i>
                                    <span class="notification-dot"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end p-0 shadow notification">
                                    <ul class="list-unstyled feeds_widget">
                                        <?php 
                                            $nottypes = array();
                                            $latest = null;
                                            foreach(Auth::user()->unreadNotifications as $notification) {
                                                $type = '';
                                                $type = str_replace('App\Notification', '', $notification->type);
                                                $type = str_replace("s\\", "New ", $type);
                                                $type = str_replace("tA", "t A", $type);
                                                $type = str_replace("lN", "l N", $type);
                                                array_push($nottypes, $type);
                                                $latest = $notification->created_at;
                                            }

                                            $types = array_count_values($nottypes);
                                        ?>
                                        @foreach($types as $key => $not)
                                        <li class="d-flex">
                                            <div class="feeds-left"><i class="fa fa-thumbs-o-up"></i></div>
                                            <div class="feeds-body flex-grow-1">
                                                <h6 class="mb-1"> {{ $not }} {{ $key }}<small class="float-end text-muted small">{{$latest->diffForHumans()}}</small></h6>
                                                <span class="text-muted"><a href="{{ url('approval-requests') }}"> Click here to Approve</a></span>
                                                <br>
                                            </div>
                                        </li>
                                        @endforeach
                                        <li class="d-flex text-center">
                                            <a href="{{ url('mark-all-as-read')}}" class="text-warning">Mark All As Read</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            @endif
                            <li class="dropdown">
                                <a class="dropdown-toggle icon-menu" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-sliders"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end p-2 shadow">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#switchShop"><i class="icon-shuffle"></i> <span>Switch Shop</span></a></li>
                                    @if(Auth::user()->can('edit-settings'))
                                    <li><a class="dropdown-item" href="{{ url('settings') }}"><i class="icon-note"></i> <span>Settings</span></a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ url('user-profile') }}"><i class="icon-user"></i> <span>My Account</span></a></li>
                                    @if(Auth::user()->can('view-recyclebin'))
                                    <!-- <li><a class="dropdown-item" href="{{ url('recyclebin') }}"><i class="icon-trash"></i> <span>Recyclebin</span></a></li> -->
                                    @endif
                                    @if(Auth::user()->can('view-action-logs'))
                                    <!-- <li><a class="dropdown-item" href="{{ url('action-logs') }}"><i class="icon-list"></i><span>User Action LOgs</span></a></li> -->
                                    @endif
                                    <!-- <li><a class="dropdown-item" href="javascript:void(0);"><i class="icon-bell"></i> <span>Notifications</span></a></li> -->
                                </ul>
                            </li>
                            <li><a href="javascript:;" class="icon-menu" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i></a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Sidbar menu -->
        <div id="left-sidebar" class="sidebar">
            <div id="scroll-content">
                <div class="user-account p-0 mb-0 mt-3">
                    <div class="d-flex mb-3 pb-3 border-bottom align-items-center">
                        <img src="{{ asset('side/assets/img/user.jpg') }}" class="avatar lg rounded me-3" alt="User Profile Picture">
                        <div class="dropdown flex-grow-1">
                            <span class="d-block">Welcome,</span>
                            <a href="#" class="dropdown-toggle user-name" data-bs-toggle="dropdown"><strong>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</strong></a>
                            <ul class="dropdown-menu p-2 shadow-sm">
                                <li><a href="{{ url('user-profile') }}"><i class="fa fa-user me-2"></i>My Profile</a></li>
                                <li><a href="{{ url('settings') }}"><i class="fa fa-cog me-2"></i>Settings</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off me-2"></i>Logout</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- nav tab: menu list -->
                <ul class="nav nav-tabs text-center mb-2" role="tablist">
                    <li class="nav-item flex-fill"><a class="nav-link active" data-bs-toggle="tab" href="#hr_menu" role="tab"><i class="fa fa-bars"></i></a></li>
                    <li class="nav-item flex-fill"><a class="nav-link" data-bs-toggle="tab" href="#setting_menu" role="tab"><i class="fa fa-cog"></i></a></li>
                </ul>
                <!-- nav tab: content -->
                <div class="tab-content px-0">
                    <div class="tab-pane fade show active" id="hr_menu" role="tabpanel">
                        <nav class="sidebar-nav">
                            <ul class="metismenu list-unstyled">
                                @if(Auth::user()->can('view-reports'))
                                <li class="{{ request()->is('prod-dash') ? 'active' : '' }}"><a href="{{ url('prod-dash') }}" class="has-arrow"><i class='fa fa-tachometer'></i><span>Dashboard</span></a></li>
                                @endif
                                @if($settings->is_livestock)
                                @if(Auth::user()->can('view-food-production'))
                                <li class="{{ request()->is('food-productions') ? 'active' : '' }}"><a href="{{ url('food-productions') }}" > <i class="fa fa-indent"></i><span>Food Productions</span></a></li>
                                <li class="{{ request()->is('food-types') ? 'active' : '' }}"><a href="{{url('food-types')}}" > <i class="fa fa-outdent"></i><span>Food Types</span></a></li>
                                @endif
                                @else
                                @if(Auth::user()->can('manage-pricing-calculator'))
                                <li class="{{ request()->is('product-pricings') ? 'active' : '' }}"><a href="{{ url('product-pricings') }}"><i class='fa fa-calculator'></i><span>Pricing Calculators</span></a></li>
                                @endif
                                @if(Auth::user()->can('manage-production'))
                                <li class="{{ request()->is('prod-costs/create') ? 'active' : '' }}"><a href="{{ url('prod-costs/create') }}"><i class='fa fa-gears'></i><span>Production Section</span></a></li>
                                <li class="{{ request()->is('prod-costs') ? 'active' : '' }}"><a href="{{url('prod-costs')}}" > <i class="fa fa-indent"></i><span>{{trans('navmenu.production_records')}}</span></a></li>
                                @endif
                                @if(Auth::user()->can('manage-wips'))
                                <!-- <li class="{{ request()->is('prod-wips') ? 'active' : '' }}"><a href="{{ url('prod-wips') }}"><i class='fa fa-wrench'></i><span>Work In Progress</span></a></li> -->
                                @endif
                                @endif
                                @if(Auth::user()->can('manage-rm'))
                                <li class="{{ request()->is('raw-materials') || request()->is('rm-purchases') || request()->is('rm-suppliers-transaction') || request()->is('rm-payments') || request()->is('rm-uses') ? 'active' : '' }}"><a href="javascript:;" class="has-arrow"><i class='fa fa-list-alt'></i><span>{{trans('navmenu.raw_materials')}}</span></a>
                                    <ul class="list-unstyled">
                                        <li> <a href="{{url('raw-materials')}}">{{trans('navmenu.raw_materials')}}</a></li>
                                        <li><a href="{{url('rm-purchases')}}">{{trans('navmenu.purchase_of_rm')}}</a></li>
                                        <li><a href="{{url('rm-suppliers-transaction')}}">{{trans('navmenu.supplier_accounts')}}</a></li>
                                        <li><a href="{{ url('rm-payments')}}">Raw Material Payments</a></li>
                                        <li><a href="{{url('rm-uses')}}"> {{trans('navmenu.rm_utilized')}}</a></li>
                                    </ul>
                                </li>
                                @endif
                                @if(Auth::user()->can('manage-pm'))
                                <li class="{{ request()->is('packing-materials') || request()->is('pm-purchases') || request()->is('pm-suppliers-transaction') || request()->is('pm-payments') || request()->is('pm-uses') ? 'active' : '' }}"><a class="has-arrow" href="javascript:;"><i class='fa fa-shopping-bag'></i><span>{{trans('navmenu.packing_materials')}}</span></a>
                                    <ul class="list-unstyled">
                                        <li><a href="{{url('packing-materials')}}"> {{trans('navmenu.packing_materials')}}</a></li>
                                        <li><a href="{{url('pm-purchases')}}">{{trans('navmenu.purchase_of_pm')}}</a></li>
                                        <li><a href="{{url('pm-suppliers-transaction')}}"> {{trans('navmenu.supplier_accounts')}}</a></li>
                                        <li><a href="{{ url('pm-payments')}}">Packing Material Payments</a></li>
                                        <li><a href="{{url('pm-uses')}}">{{trans('navmenu.pm_utilized')}}</a></li>
                                    </ul>
                                </li>
                                @endif

                                @if(Auth::user()->can('manage-labour-costs'))
                                <li class="{{ request()->is('prod-labour-costs') || request()->is('plc-payments') || request()->is('plc-items') || request()->is('production-stages') ? 'active' : '' }}"><a class="has-arrow" href="javascript:;"><i class="fa fa-credit-card"></i> <span>Labour Costs</span></a>
                                    <ul class="list-unstyled">
                                        <li><a href="{{url('prod-labour-costs')}}">Labour Costs</a></li>
                                        <li><a href="{{url('plc-payments')}}">Labour Cost  Payments</a></li>
                                        <li><a href="{{url('plc-items')}}">Labour Costs Utilized</a></li>
                                        <li><a href="{{ url('production-stages') }}"> Production Stages</a></li>
                                    </ul>
                                </li>
                                <li class="{{ request()->is('moh-costs') || request()->is('moh-payments') || request()->is('mro-items') ? 'active' : '' }}"><a class="has-arrow" href="javascript:;"><i class="fa fa-credit-card"></i> <span>MOH Costs</span></a>
                                    <ul class="list-unstyled">
                                        <li><a href="{{url('moh-costs')}}">MOH Costs</a></li>
                                        <li><a href="{{url('moh-payments')}}">MOH Cost  Payments</a></li>
                                        <li><a href="{{url('mro-items')}}">MOH Costs Utilized</a></li>
                                    </ul>
                                </li>
                                @endif
                                @if(Auth::user()->can('view-production-reports'))
                                <li class="{{ request()->is('general-report') || request()->is('prod-stock-status-report') || request()->is('rm-purchases-report') || request()->is('rm-uses-report') || request()->is('pm-purchases-report') || request()->is('pm-uses-report') ? 'active' : '' }}"><a class="has-arrow" href="javascript:;"><i class="fa fa-line-chart"></i><span> Reports</span></a>
                                    <ul class="list-unstyled">
                                        <li><a href="{{url('general-report')}}">General Reports</a></li>
                                        <li><a href="{{url('prod-stock-status-report')}}">{{trans('navmenu.stock_status_report')}}</a></li>
                                        <li><a href="{{url('rm-purchases-report')}}">{{trans('navmenu.rm_purchases')}}</a></li>
                                        <li><a href="{{url('rm-uses-report')}}">{{trans('navmenu.rm_uses_report')}}</a></li>
                                        <li><a href="{{url('pm-purchases-report')}}"> {{trans('navmenu.pm_purchases')}} </a></li>
                                        <li><a href="{{url('pm-uses-report')}}">{{trans('navmenu.pm_uses_report')}}</a></li>
                                    </ul>
                                </li>
                                @endif
                                <li class="{{ request()->is('prod-settings') ? 'active' : '' }}"><a href="{{ url('prod-settings') }}"><i class="fa fa-gear"></i><span>{{trans('navmenu.settings')}}</span></a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="tab-pane fade" id="setting_menu" role="tabpanel">
                        <div class="px-3">
                            <h6>Choose Skin</h6>
                            <ul class="choose-skin list-unstyled">
                                <li data-theme="purple" class="mb-2">
                                    <div class="purple"></div><span>Purple</span>
                                </li>
                                <li data-theme="blue" class="mb-2">
                                    <div class="blue"></div><span>Blue</span>
                                </li>
                                <li data-theme="cyan" class="mb-2">
                                    <div class="cyan"></div><span>Cyan</span>
                                </li>
                                <li data-theme="green" class="mb-2">
                                    <div class="green"></div><span>Green</span>
                                </li>
                                <li data-theme="orange" class="active mb-2">
                                    <div class="orange"></div><span>Orange</span>
                                </li>
                                <li data-theme="blush" class="mb-2">
                                    <div class="blush"></div><span>Blush</span>
                                </li>
                            </ul>
                            <hr>
                            <h6>Theme Option</h6>
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-1">
                                    <div class="form-check form-switch theme-switch">
                                        <input class="form-check-input" type="checkbox" id="theme-switch">
                                        <label class="form-check-label" for="theme-switch">Enable Dark Mode!</label>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center mb-1">
                                    <div class="form-check form-switch theme-high-contrast">
                                        <input class="form-check-input" type="checkbox" id="theme-high-contrast">
                                        <label class="form-check-label" for="theme-high-contrast">Enable High Contrast</label>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center mb-1">
                                    <div class="form-check form-switch theme-rtl">
                                        <input class="form-check-input" type="checkbox" id="theme-rtl">
                                        <label class="form-check-label" for="theme-rtl">Enable RTL Mode!</label>
                                    </div>
                                </li>
                            </ul>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="main-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

     <!-- Default Size -->
    <div class="modal animated zoomIn" id="switchShop" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="title" id="defaultModalLabel">Switch Shop</h6>
                </div>
                <form method="POST" action="{{ url('switch-shop')}}">
                    @csrf        
                    <div class="modal-body">
                        <select name="shop_id" id="auto_submit" onchange='if(this.value != 0) { this.form.submit(); }' class="form-select form-select-sm mb-1 select-bar-box">
                            @foreach (Auth::user()->shops()->get() as $key => $myshop)
                            @if ($myshop->id == Session::get('shop_id'))
                            <option value="{{ $myshop->id }}" selected>{{ $myshop->name }}</option>
                            @else
                            <option value="{{ $myshop->id }}">{{ $myshop->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Jquery Page Js -->
    <!-- Jquery Core Js -->
    <script src="{{ asset('side/assets/js/plugins.js') }}"></script>
    <!-- Project Js -->
    <script src="{{ asset('side/assets/js/theme.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/toastr/toastr.js') }}"></script>

     <!-- InputMask -->
    <script src="{{ asset('side/assets/vendor/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- date-range-picker -->
    <script src="{{ asset('side/assets/vendor/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!--notification js -->
    <script src="{{ asset('side/assets/vendor/notifications/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/notifications/js/notifications.min.js') }}"></script>

    <script src="{{ asset('side/assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 
    <script src="{{ asset('side/assets/vendor/sweetalert2/sweetalert2.all.js') }}"></script>
    
    <!-- Jquery Page Js -->
    <script src="{{ asset('side/assets/vendor/notifications/js/notification-custom-script.js') }}"></script>

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
    $cust = '';
    $supp = '';
    $is_post = '';
    $startdate = '';
    $enddate = '';
    $is_pos = false;
    $loadcountries = false;
    $is_school = false;
    $is_filling_station = false;
    if (!is_null($settings)) {
        if ($settings->is_school) {
            $is_school = true;
        }
        if ($settings->is_filling_station) {
            $is_filling_station = true;
        }
    }
    
    if ($page == 'Home' || $page == 'Reports' || $page == 'Work In Progress' || $page == 'Material WIPs' || $page == 'Production Records' || $page == 'Account Statement' || $page == 'Labour Costs' || $page == 'Labour Cost Payments' || $page == 'MOH Costs' || $page == 'MOH Cost Payments') {
        $is_post = $is_post_query;
        $startdate = $start_date;
        $enddate = $end_date;
    }
    
    if ($page == 'Customers' || $page == 'Suppliers' || $page == 'Profile') {
        $loadcountries = true;
    }
    
    if ($page == 'Point of Sale') {
        $is_pos = true;
    }
    
    if ($page == 'Reports') {
        if (app()->getLocale() == 'en') {
            $dur = $duration;
        } else {
            $dur = $duration_sw;
        }
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
            var customer = "<?php echo $cust; ?>";
            var supplier = "<?php echo $supp; ?>";
            var shop_name = "<?php echo $shop->name; ?>";

            $('[data-mask]').inputmask();

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

            $('#saledate').on('change', function(){
                $('.dashform').submit();
            })

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
            
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
    @yield('page-scripts')
</body>

</html>