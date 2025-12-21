<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ng-app="smartpos">

<head>
    <title>{{ config('app.name', 'DWASAL') }} : Welcome</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="description" content="The All in One Accounting Software">
    <meta name="author" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <!-- VENDOR CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/color_skins.css') }}">}}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
</head>
<body class="theme-orange">

    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30"><img src="{{ asset('assets/img/loader-5.gif') }}" width="48" height="48" alt="DWASAL"></div>
            <p>Please wait...</p>        
        </div>
    </div>
    <!-- Overlay For Sidebars -->

    <div id="wrapper">
        
        <nav class="navbar navbar-fixed-top">
            <div class="container">
                <div class="navbar-brand">
                    <a href="{{ url('/home')}}"><img src="{{ asset('assets/img/logo-2.png') }}" alt="DWASAL" class="img-responsive logo" width="100"></a>
                </div>
                
                <div class="navbar-right">
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

        <div id="main-content">
            <div class="container">
                <div class="block-header pt-0">
                    <div class="row">
                        <div class="col-lg-6 col-md-8 col-sm-12">
                            <h2><a href="javascript:void(0);" class="btn btn-xs btn-link btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> Welcome!</h2>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('user-profile')}}"><i class="icon-user"></i></a></li>                            
                                <li class="breadcrumb-item">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</li>
                                <li class="breadcrumb-item active">{{Auth::user()->roles[0]['display_name']}}</li>
                            </ul>
                        </div>            
                        <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                            <form action="{{ url('switch-shop') }}" method="POST">
                                @csrf
                                <select name="shop_id" id="auto_submit" onchange='if(this.value != 0) { this.form.submit(); }' class="form-select form-select-sm mb-1">
                                    @foreach (Auth::user()->shops()->get() as $key => $myshop)
                                    @if ($myshop->id == Session::get('shop_id'))
                                    <option value="{{ $myshop->id }}" selected>S/N-{{sprintf('%03d', $myshop->id)}}-{{ $myshop->name }}</option>
                                    @else
                                    <option value="{{ $myshop->id }}">S/N-{{sprintf('%03d', $myshop->id)}}-{{ $myshop->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row clealfix">
                    <div class="col-md-3">
                        <a href="{{ url('sales-dash') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-calculator"></i></h2>
                                    <h6 class="text-primary">Sales Management</h6>
                                    <small>Quotations, Sales Orders, Proforma Invoices, Invoices, Delivery Nots, Customer Accounts, Sales Returns</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('inventory-dash') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-list-alt"></i></h2>
                                    <h6 class="text-primary">Products & Services </h6>
                                    <small>Product List, Purchase Orders, Stock Transfers Orders (STO), Supplier Accounts, Stock Corrections, Services & Devices</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('sand-prod-dash') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-building-o"></i></h2>
                                    <h6 class="text-primary">Washed Sand Production</h6>
                                    <small>Raw Material Sourcings, Production Records, Quality Tests, Washing Plants, Washing Equipments</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('prod-dash') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-industry"></i></h2>
                                    <h6 class="text-primary">Other Production</h6>
                                    <small>Raw Materials, Packaging Materials, Manufacturing Overheads Costs, Labour Costs, Production Records, etc.</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('accounting-dash') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-money"></i></h2>
                                    <h6 class="text-primary"> Accounting & Finance</h6>
                                    <small>Payment Accounts, Petty Cash Requests, Expenses, Cash Flows, Chart Of Accounts, Assets & Depreciations</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('hr-dash') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-group"></i></h2>
                                    <h6 class="text-primary"> HR & Payroll</h6>
                                    <small>Employees, Departments, Attendances, Leave Requests, Events, Employee Loans, Payrolls & Payroll Deductions</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('reports') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-bar-chart-o"></i></h2>
                                    <h6 class="text-primary"> Reporting</h6>
                                    <small>Sales Summary Reports, Inventory Reports, Expense Reports, Management Reports, Finacial Statements, etc.</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('vehicles-dash') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-truck"></i></h2>
                                    <h6 class="text-primary">Vehicle Management </h6>
                                    <small>Vehicles, Legal Documents, Insurance, Maintenance, Refueling,  Trip Logs, Mileage Logs, Cost Logs</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('visitors-dash') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-group"></i></h2>
                                    <h6 class="text-primary">Visitors Management </h6>
                                    <small>Visitors Logs & Analysis </small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('settings') }}" target="_blank">
                            <div class="card">
                                <div class="body text-center">
                                    <h2><i class="fa fa-cog"></i></h2>
                                    <h6 class="text-primary"> Account Settings</h6>
                                    <small>General Settings, User Profile, Companies, Branches, Users & Roles, Delivery Settings, Invoice Settings, Recyclebin</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Javascript -->
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>    
    <script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script>

    <script src="{{ asset('assets/vendor/bootstrap-treeview/bootstrap-treeview.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jstree/jstree.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/toastr.js') }}"></script>

    <script src="{{ asset('assets/bundles/mainscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/pages/treeview/jstree.js') }}"></script>
    <script src="{{ asset('assets/js/pages/treeview/bootstrap-treeview.js') }}"></script>
</body>
</html>
