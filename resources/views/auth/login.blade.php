@extends('layouts.auth')

@section('content')

    <div class="auth-box">
        <div class="top">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Petopesa">
        </div>
        <div class="card">
            <div class="header">
                <p class="lead">Login to your account</p>
            </div>
            <div class="body">
                <form id="contactForm" novalidate="novalidate" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="control-group">
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-envelope"></i>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" id="signin-email" placeholder="Email or Mobile Number" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group" id="show_hide_password">
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-key"></i>
                            <input id="password-field" type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="signin-password" placeholder="Password" required autocomplete="current-password">
                        </div>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group text-left">
                        <input type="checkbox" onclick="myFunction()"> Show Password
                    </div>
                    <div class="control-group mt-4">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Login') }}</button>
                        @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<script>
    function myFunction() {
      var x = document.getElementById("password-field");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
</script>