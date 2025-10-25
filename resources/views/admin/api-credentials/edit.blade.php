@extends('layouts.adm')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Accounts & Users</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-2 rounded">
                         <form class="form row g-1" method="post" action="{{ route('payment-auths.update', encrypt($payauth->id)) }}" validate>
                            @csrf
                            @method('PUT')
                            <div class="col-md-3">
                                <label class="form-label">Business</label>
                                <select name="shop_id" required="required"
                                    class="form-select form-select-sm mb-1 border-primary mb-1">
                                    <option value="">Select Business</option>
                                    @foreach ($shops as $key => $shop)
                                        @if($payauth->shop_id == $shop->id)
                                        <option value="{{ $shop->id }}" selected>{{ $shop->name }} ({{ $shop->mobile }})</option>
                                        @else
                                        <option value="{{ $shop->id }}">{{ $shop->name }} ({{ $shop->mobile }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Merchant MSISDN</label>
                                <input class="form-control form-control-sm mb-1 border-primary" account="text" name="merchant_msisdn" value="{{$payauth->merchant_msisdn}}" placeholder="Enter Merchant msisdn 255XXXXXXXXX" id="userinput8" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Username</label>
                                <input class="form-control form-control-sm mb-1 border-primary" account="text" name="username" value="{{$payauth->username}}" placeholder="Enter Username" id="userinput8" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Password</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="password" value="{{$payauth->passhint}}" placeholder="Enter Password" required>
                            </div>
                            <hr>
                            <div class="col-md-3">
                                <label class="form-label">Account Type <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="type" required>
                                    @if($acc->type == 'Bank')
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                    @else
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_number" value="{{$acc->account_number}}" placeholder="Account Number">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Account Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-1" id="account" name="account_name" value="{{$acc->account_name}}" placeholder="Account Name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bank Name/MNO Channel</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" value="{{$acc->bank_name}}" placeholder="Bank Name">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                <a href="{{ url('admin/payment-auths')}}" class="btn btn-warning btn-sm">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection