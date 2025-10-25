<!doctype html>
<html lang="en">

<head>
<title>DWASAL | Login</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="description" content="">
<meta name="author" content="">

<link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">
<!-- VENDOR CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
<!-- MAIN CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/color_skins.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css') }}">
</head>

<body class="theme-orange">
    <!-- WRAPPER -->
    <div id="wrapper">
        <div class="vertical-align-wrap flex-container">
            @yield('content')
        </div>
    </div>
    <!-- END WRAPPER -->
     <!-- Javascript -->
    <script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/toastr.js') }}"></script>

     <!-- InputMask -->
    <script src="{{ asset('assets/vendor/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('assets/vendor/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('assets/vendor/input-mask/jquery.inputmask.extensions.js') }}"></script>
    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>
    <script>
        $(function(){

            $('[data-mask]').inputmask();
            
            var code = "+255"; // Assigning value from model.
            // $('#inputPhoneNumber').val(code);
            $('#inputPhoneNumber').intlTelInput({
                autoHideDialCode: true,
                autoPlaceholder: "ON",
                dropdownContainer: document.body,
                formatOnDisplay: true,
                hiddenInput: "full_number",
                initialCountry: "auto",
                nationalMode: true,
                placeholderNumberType: "MOBILE",
                preferredCountries: ['TZ'],
                separateDialCode: true
            });
            // Toolbar extra buttons
            var btnFinish = $('#submitButton').on('click', function() {
                'use strict'

                var iso2 = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").iso2;
                var dialCode = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").dialCode;
                var cc = $('#countryCode').val(iso2.toUpperCase());
                var dc = $('#dialCode').val(dialCode);
            });
        });
    </script>
</body>
</html>
