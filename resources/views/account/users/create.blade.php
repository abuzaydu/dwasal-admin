@extends('layouts.prof')

@section('page-styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">My Account</li>               
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-8">
            <div class="box box-success">
                <div class="card radius-6 p-4">
                    <form class="form-validate row g-3 my-form" method="POST" action="{{ route('user-profile.store') }}">
                        @csrf
                        <div class="col-md-4">
                            <label for="register-username" class="form-label">{{ trans('navmenu.first_name') }}</label>
                            <input id="register-username" type="text" name="first_name" required placeholder="{{ trans('navmenu.hnt_first_name') }}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-4">
                            <label for="register-username" class="form-label">{{ trans('navmenu.last_name') }}</label>
                            <input id="register-username" type="text" name="last_name" required placeholder="{{ trans('navmenu.hnt_last_name') }}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-4">
                            <label for="register-phone" class="form-label">{{ trans('navmenu.mobile') }}</label>
                            <input type="tel" class="form-control form-control-sm mb-1" id="inputPhoneNumber" name="phone" placeholder="Eg. 0789XXXXXX" value="{{old('phone')}}" required>
                        </div>
                            <input type="hidden" name="phone_country" id="countryCode">
                            <input type="hidden" name="dial_code" id="dialCode">
                            <input type="hidden" name="country" id="country">
                        
                        <div class="col-md-4">
                            <label for="register-email" class="form-label">{{ trans('navmenu.email_address') }}</label>
                            <input id="register-email" type="email" name="email" placeholder="{{ trans('navmenu.hnt_email_address') }}" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ trans('navmenu.role') }} <span style="color: red;">*</span></label>
                            <select name="role" class="form-select form-select-sm mb-1">
                                <option value="">---Select Role---</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Default Page <span style="color: red;">*</span></label>
                            <select name="default_page" class="form-select form-select-sm mb-1">
                                <option value="">---Select Page---</option>
                                @foreach ($defaultpages as $key => $p)
                                <option value="{{$key}}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="register-password" class="form-label">{{ trans('navmenu.password') }} </label>
                            <input id="password" type="password" name="password" required placeholder="{{ trans('navmenu.hnt_password') }}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" id="btn-submit" class="btn btn btn-success btn-sm">{{ trans('navmenu.btn_save') }}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{ trans('navmenu.btn_cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card radius-6 p-4">
                <h5>Add from Employee List</h5>
                <div class="p-4 border rounded">
                    <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ url('add-user-from-employee') }}">
                        @csrf
                        <div class="col-md-12" id="single-opt">
                            <label class="form-label">Employee <span style="color: red;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="employee_id">
                                <option value="">---Select Employee--</option>
                                @foreach($employees as $emp)
                                <option value="{{$emp->id}}">{{$emp->fname}} {{$emp->lname}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ trans('navmenu.role') }} <span style="color: red;">*</span></label>
                            <select name="role" class="form-select form-select-sm mb-1">
                                <option value="">---Select Role---</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Default Page <span style="color: red;">*</span></label>
                            <select name="default_page" class="form-select form-select-sm mb-1">
                                <option value="">---Select Page---</option>
                                @foreach ($defaultpages as $key => $p)
                                <option value="{{$key}}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary btn-sm px-4 radius-30" type="submit">Add User</button>
                            <a onclick="showHideForm('hide')" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>
    <script type="text/javascript">
        
            var code = "+255"; // Assigning value from model.
            $('#inputPhoneNumber').val(code);
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
            var btnFinish = $('#btn-submit').on('click', function() {
                'use strict'

                var iso2 = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").iso2;
                var dialCode = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").dialCode;
                var phoneNumber = $('#inputPhoneNumber').val();
                var name = $("#inputPhoneNumber").intlTelInput("getSelectedCountryData").name;
                // alert('Country Code : ' + code + '\nPhone Number : ' + phoneNumber + '\nCountry Name : ' + name);
                var cc = $('#countryCode').val(iso2.toUpperCase());
                var dc = $('#dialCode').val(dialCode);
                var country = $('#country').val(name);
            });
    </script>
@endsection