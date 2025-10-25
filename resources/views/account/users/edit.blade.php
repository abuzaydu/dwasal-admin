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
        <div class="col-md-12 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="row g-3 form-validate" method="POST" action="{{ route('user-profile.update', encrypt($user->id)) }}" enctype="multipart/form-data">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-md-3">
                            <label for="register-username" class="form-label">{{ trans('navmenu.first_name') }}</label>
                            <input id="register-username" type="text" name="first_name" value="{{$user->first_name}}" required placeholder="{{ trans('navmenu.hnt_name') }}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-3">
                            <label for="register-username" class="form-label">{{ trans('navmenu.last_name') }}</label>
                            <input id="register-username" type="text" name="last_name" value="{{$user->last_name}}" required placeholder="{{ trans('navmenu.hnt_name') }}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-4">
                            <label for="register-phone" class="form-label">{{ trans('navmenu.mobile') }}</label>
                            <input type="tel" class="form-control form-control-sm mb-1" name="phone" placeholder="Eg. 0789XXXXXX" value="{{ $user->phone }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="register-email" class="form-label">{{trans('navmenu.email_address')}} </label>
                            <input id="register-email" type="email" name="email" data-msg="Please enter a valid email address" class="form-control form-control-sm mb-1" value="{{$user->email}}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Default Page <span style="color: red;">*</span></label>
                            <select name="default_page" class="form-select form-select-sm mb-1">
                                <option value="">---Select Page---</option>
                                @foreach ($defaultpages as $key => $page)
                                @if($user->default_page == $key)
                                <option value="{{$key}}" selected>{{ $page }}</option>
                                @else
                                <option value="{{$key}}">{{ $page }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">User Photo</label>
                            <input type="file" id="exampleInputFile" name="photo">
                            <p class="help-block">Please upload your Photo here.</p>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection