@extends('layouts.prof')
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
        <div class="col-md-7 mx-auto">
            <h6 class="mb-0 text-uppercase">Create New Password</h6>
            <hr>
            <div class="card radius-6">
                <div class="card-body">
                    @include('flash-message')
                    <form class="p-4 border rounded" action="{{ url('change-password') }}" method="POST">
                        @csrf
                        <div class="pt-2 row">
                            <input id="curr_password" type="password" name="curr_password" data-rule="required" data-msg="Please enter your Current password" class="form-control form-control-sm mb-1" placeholder="Current Password" required>       
                            <div class="validation" style="color: red;"></div>
                        </div>

                        <div class="pt-2 row">
                            <input id="password" type="password" name="password" data-rule="required" data-msg="Please enter your New password" class="form-control form-control-sm mb-1" placeholder="New Password" required>       
                            <div class="validation" style="color: red;"></div>
                            @if ($errors->has('password'))
                            <span class="help-block" style="color: red;">
                              <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="pt-2 row">
                             <input id="confirm-password" type="password" name="password_confirmation" data-rule="required" data-msg="Please Re-enter your New password" placeholder="Re-Enter New password" class="form-control form-control-sm mb-1" required>
                            <div class="validation" style="color: red;"></div>
                        </div>

                        <div class="pt-2">
                            <div class="col-md-12 pt-3">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ __('Change Password') }}
                                </button>
                              <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection