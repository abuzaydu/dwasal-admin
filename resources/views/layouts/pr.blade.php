<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="smartpos">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon"> <!-- Favicon-->
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DWASAL') }} : {{$page}}</title>
    <!-- Application Vendor CSS URL -->
    <link rel="stylesheet" href="{{ asset('hr/assets/vendor/parsleyjs/css/parsley.css') }}">
    <link rel="stylesheet" href="{{ asset('hr/assets/vendor/notifications/css/lobibox.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('hr/assets/cssbundle/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hr/assets/cssbundle/bootstrapdatepicker.min.css') }}">
    <!-- Daterange Picker -->
    <link rel="stylesheet" href="{{ asset('hr/assets/vendor/bootstrap-daterangepicker/daterangepicker.css') }}">
    <!-- project css file  -->
    <link rel="stylesheet" href="{{ asset('hr/assets/css/lucid-style.css') }}">
    <link rel="stylesheet" href="{{ asset('hr/assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('hr/assets/cssbundle/tuicalendar.min.css')}}">
    <link rel="stylesheet" href="{{ asset('side/assets/css/custom.css') }}">
</head>

<?php
$headercolor = Session::get('headercolor');
$sidebarcolor = Session::get('sidebarcolor');
$company = App\Models\Company::find(Session::get('company_id'));
$shop = App\Models\Shop::find(Session::get('shop_id'));
$settings = null;
if (!is_null($shop)) {
    $settings = App\Models\Setting::where('shop_id', $shop->id)->first();
}
?>

<body id="layout" data-lucid="theme-orange">
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
                    <a href="{{ url('/home')}}"><img src="{{ asset('assets/img/logo-2.png') }}" alt="DWASAL" class="img-responsive logo" width="100"></a>
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
                <div class="user-account p-3 mb-0">
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
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- nav tab: menu list -->
                <ul class="nav nav-tabs text-center mb-2" role="tablist">
                    <li class="nav-item flex-fill"><a class="nav-link active" data-bs-toggle="tab" href="#payroll_menu" role="tab">PR</a></li>
                    <li class="nav-item flex-fill"><a class="nav-link"  href="{{ url('hr-dash') }}">HR</a></li>
                    <li class="nav-item flex-fill"><a class="nav-link" data-bs-toggle="tab" href="#setting_menu" role="tab"><i class="fa fa-cog"></i></a></li>
                </ul>
                <!-- nav tab: content -->
                <div class="tab-content px-0">
                    <div class="tab-pane fade show active" id="payroll_menu" role="tabpanel">
                        <nav class="sidebar-nav">
                            <ul class="metismenu list-unstyled">
                                <li class="{{ request()->is('payroll-dash') ? 'active' : '' }}"><a href="{{ url('payroll-dash') }}"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
                                <li class="{{ request()->is('payrolls/create') ? 'active' : '' }}"><a href="{{ route('payrolls.create')}}"><i class="fa fa-plus-square-o"></i><span>New Payroll</span></a></li>
                                <li class="{{ request()->is('payrolls') ? 'active' : '' }}"><a href="{{ url('payrolls') }}"><i class="fa fa-list-alt"></i><span>Payrolls List</span></a></li>
                                <li class="{{ request()->is('payroll-reports') ? 'active' : '' }}"><a href="{{ url('payroll-reports') }}"><i class="fa fa-bar-chart-o"></i><span>Payroll Reports</span></a></li>
                                <li class="{{ request()->is('payroll-settings') ? 'active' : '' }}"><a href="{{ url('payroll-settings') }}"><i class="fa fa-cogs"></i><span>Payroll Settings</span></a></li>
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
                            <h6>General Settings</h6>
                            <ul class="setting-list list-unstyled">
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">Default checkbox</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault1">
                                        <label class="form-check-label" for="flexCheckDefault1">Email Redirect</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2" checked>
                                        <label class="form-check-label" for="flexCheckDefault2">Notifications</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault3">
                                        <label class="form-check-label" for="flexCheckDefault3">Auto Updates</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault4">
                                        <label class="form-check-label" for="flexCheckDefault4">Offline</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault5">
                                        <label class="form-check-label" for="flexCheckDefault5">Location Permission</label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>  
            </div>      
        </div>
        <div id="main-content">
            <div class="container-fluid">
                <div class="block-header pt-3 py-lg-2 py-1 mb-0" id="div-only-mobile">
                    <div class="row g-1">
                        <div class="col-md-12 align-items-center">
                            <form action="{{ url('switch-role') }}" method="POST" class="">
                                @csrf
                                <select class="form-select form-select-sm mb-1" name="role_id" onchange="this.form.submit()">
                                    @foreach(Auth::user()->roles as $role)
                                        <option value="{{$role->id}}" style="text-transform: capitalize;">{{$role->display_name}}</option>
                                    @endforeach
                                </select>
                            </form> 
                        </div>
                    </div>
                </div>
                @yield('content')
            </div>
        </div>
    </div>
    <!-- Jquery Page Js -->
    <!-- Jquery Core Js -->
    <script src="{{ asset('hr/assets/js/plugins.js') }}"></script>
    <!-- Project Js -->
    <script src="{{ asset('hr/assets/js/theme.js') }}"></script>
    <script src="{{ asset('hr/assets/vendor/parsleyjs/js/parsley.min.js') }}"></script>
    <!-- Jquery Page Js -->  
    <script src="{{ asset('hr/assets/js/bundle/dataTables.bundle.js') }}"></script>
    <script src="{{ asset('hr/assets/js/bundle/owlcarousel.bundle.js') }}"></script>
    <script src="{{ asset('hr/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('hr/assets/js/bundle/sweetalert2.bundle.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chance/1.1.8/chance.min.js"></script>
    <!-- event calender -->
    <script src="{{asset('hr/assets/js/bundle/tui-calendar.bundle.js')}}"></script>
    <!-- InputMask -->
    <script src="{{ asset('hr/assets/vendor/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('hr/assets/vendor/input-mask/jquery.inputmask.date.extensions.js') }}"></script>    
    <script src="{{ asset('hr/assets/vendor/input-mask/jquery.inputmask.extensions.js') }}"></script>

    <!--notification js -->
    <script src="{{ asset('hr/assets/vendor/notifications/js/lobibox.min.js') }}"></script>
    <script src="{{ asset('hr/assets/vendor/notifications/js/notifications.min.js') }}"></script>
    <script src="{{ asset('hr/assets/vendor/notifications/js/notification-custom-script.js') }}"></script>
    
    <!-- date-range-picker -->
    <!-- <script src="{{asset('hr/assets/plugins/moment/min/moment.min.js')}}"></script> -->
    <script src="{{ asset('hr/assets/vendor/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

    @yield('page-scripts')

    @if ($message = Session::get('success'))
    <script>
        $(document).ready( function () {
            round_success_noti("{{$message}}");
        });
    </script>
    @endif
    @if ($message = Session::get('error'))
    <script>
        $(document).ready( function () {
            console.log("{{$message}}");
            round_error_noti("{{$message}}");
        });
    </script>
    @endif
    
    @if($message = Session::get('info'))
    <script>
        $(document).ready( function () {
            round_info_noti("{{$message}}");
        });
    </script>
    @endif
    
    @if ($message = Session::get('warning')) 
    <script>
        $(document).ready( function () {
            round_warning_noti("{{$message}}");
        });
    </script>
    @endif
    
    <?php   
        $is_post = '';
        $startdate = '';
        $enddate = '';

        if ($page == 'Payrolls' || $page == 'Payrolls Summary Report') {
            $is_post = $is_post_query;
            $startdate = $start_date;
            $enddate = $end_date;
        }
    ?>
    <script>

        $(function() {
            // initialize after multiselect
            $('#basic-form').parsley();
        });

        $(document).ready(function() {
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
            });

            $(".due-date").datepicker({
                todayHighlight: true,
                autoclose: true,
            });


            $('#psettings').dataTable({
                scrollX: true,
            });

            $('#total-payrolls').dataTable({
                scrollX: true,
            });
            
            $('#payrolls').dataTable({
                scrollX: true,
            });
            
             $('[data-mask]').inputmask();

            //Date range as a button
            var start = moment();
            var end = moment();
            var reportrangequery = false;
            var is_postq = "<?php echo $is_post; ?>";
                
            if (is_postq) {
                var startstring = "<?php echo $startdate; ?>";
                var endstring = "<?php echo $enddate; ?>";
                start = moment(startstring, 'YYYY-MM-DD');
                end = moment(endstring, 'YYYY-MM-DD');
                // alert('Date is '+start+' - '+end);
            }
                
            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                if (reportrangequery) {
                    $('#start_input').val(start.format('YYYY-MM-DD'));
                    $('#end_input').val(end.format('YYYY-MM-DD'));
                    $('.report-form').submit();
                }
            }
                
            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges   : {
                    'Today'       : [moment(), moment()],
                    'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                    'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'This Year'   : [moment().startOf('year'), moment().endOf('year')],
                    'Last Year'   : [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                }
            }, cb)
            cb(start, end);
                
            $('#reportrange').on('click.daterangepicker', function(){
                // $('.dashform').submit();
                reportrangequery = true;
            });
        });
    </script>

</body>
</html>
