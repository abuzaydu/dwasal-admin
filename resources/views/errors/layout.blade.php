<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="keyword" content="">
        <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon"> <!-- Favicon-->
        <title>Maintenance</title>
        <!-- VENDOR CSS -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}">

        <!-- MAIN CSS -->
        <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/color_skins.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
    </head>

    <body class="theme-orange">
        <!-- Overlay For Sidebars -->
        <div id="wrapper">
            <div class="vertical-align-wrap flex-container">
                <div class="d-flex h100vh justify-content-center align-items-center">
                    <div class="text-center pt-5">
                        <article>
                            <h1>@yield('title')</h1>
                            <h1>@yield('code')</h1>
                            <div>
                                @yield('message')
                            </div>
                        </article>
                        <div class="margin-top-30">
                            <a href="javascript:history.go(-1)" class="btn btn-secondary"><i class="fa fa-arrow-left"></i>
                            <span>Go Back</span></a>
                            <a href="{{ url('/home') }}" class="btn btn-info"><i class="fa fa-home"></i> <span>Home</span></a>
                            <a class="btn btn-danger" href="javascript:;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class='fa fa-log-out-circle'></i><span>Logout</span></a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Javascript -->
        <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
        <script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script>
        <script src="{{ asset('assets/bundles/mainscripts.bundle.js') }}"></script>
    </body>
</html>