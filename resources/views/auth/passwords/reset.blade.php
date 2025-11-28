@extends('layouts.auth')

@section('content')
    
    <div class="auth-box">
        <div class="top">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Lucid">
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
                <form method="POST" action="{{ url('reset-pass')}}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="col-md-12">
                        <label for="email" class="col-form-label text-md-end">{{ __('Mobile Number') }}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-envelope"></i>
                            <input id="email" type="tel" class="form-control form-control-sm mb-1 @error('phone') is-invalid @enderror" name="phone" value="{{ $user->phone }}" required autofocus>
                        </div>
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="password" class="col-form-label text-md-end">{{ __('Password') }}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-key"></i>
                            <input id="password" type="password" class="form-control form-control-sm mb-1 @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        </div>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="password-confirm" class="col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-key"></i>
                            <input id="password-confirm" type="password" class="form-control form-control-sm mb-1" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                {{ __('Reset Password') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
