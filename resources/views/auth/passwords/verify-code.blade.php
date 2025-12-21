@extends('layouts.auth')

@section('content')
    <div class="auth-box">
        <div class="top">
            <a href="{{ url('/')}}"><img src="{{ asset('assets/img/logo.png') }}" alt="Dwasal"></a>
        </div>
        <div class="card">
            <div class="header">
                <p class="lead">{{ __('Verify It Is You') }}</p>
            </div>
            <div class="body">
                @if($success)
                    <div class="alert alert-success">{{ $success }}</div>
                @endif
                @if(Session::has('error'))
                    <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif
                @if(Session::has('warning'))
                    <div class="alert alert-warning">{{ Session::get('warning') }}</div>
                @endif
                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ url('verify-code') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{$user->id}}">
                    <div class="col-md-12">
                        <label class="form-label">Please enter the Reset code here</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-edit-alt"></i>
                            <input type="number" name="code" class="form-control form-control-sm mb-1" placeholder="Enter Verification Code" required>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
