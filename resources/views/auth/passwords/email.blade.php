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
                @if(Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif
                @if(Session::has('warning'))
                    <div class="alert alert-warning">{{ Session::get('warning') }}</div>
                @endif

                <form class="row g-3 needs-validation" method="POST" action="{{ url('password-phone') }}">
                    @csrf
                    <div class="col-md-12">
                        <label class="form-labe">Please enter your E-Mail or Mobile number</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-envelope"></i>
                            <input type="text" name="phone" class="form-control form-control-sm mb-3 @error('email') is-invalid @enderror" id="inputEmailAddress" placeholder="{{trans('navmenu.email_mobile')}}" value="{{old('phone')}}" required>
                        </div>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="invalid-feedback">Please provide your email address or Phone number.</div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-12">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Send Password Reset Code') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
