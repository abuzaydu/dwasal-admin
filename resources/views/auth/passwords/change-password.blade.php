@extends('layouts.auth')

@section('content')
    <div class="auth-box">
        <div class="top">
            <a href="{{ url('/')}}"><img src="{{ asset('assets/img/logo.png') }}" alt="Dwasal"></a>
        </div>
        <div class="card">
            <div class="header">
                <p class="lead">{{ __('Reset Password') }}</p>
            </div>
            <div class="body">
                @include('flash-message')
                <form action="{{ url('change-password') }}" method="post" role="form" class="contactForm">
                    @csrf
                    <div class="pt-2 row">
                        <input id="curr_password" type="password" name="curr_password" data-rule="required" data-msg="Please enter your Current password" class="form-control" placeholder="Current Password">       
                        <div class="validation" style="color: red;"></div>
                    </div>

                    <div class="pt-2 row">
                        <input id="password" type="password" name="password" data-rule="required" data-msg="Please enter your New password" class="form-control" placeholder="New Password">       
                        <div class="validation" style="color: red;"></div>
                        @if ($errors->has('password'))
                        <span class="help-block" style="color: red;">
                          <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="pt-2 row">
                        <input id="confirm-password" type="password" name="password_confirmation" data-rule="required" data-msg="Please Re-enter your New password" placeholder="Re-Enter New password" class="form-control">
                        <div class="validation" style="color: red;"></div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Change Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection