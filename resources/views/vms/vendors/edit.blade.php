@extends('layouts.vms')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-3">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-3 text-right pt-0">
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#vendorModal"><i class="fa fa-user-plus"></i> New Vendor</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <form class="form-validate row g-1" method="POST" action="{{route('vendors.update', encrypt($vendor->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-sm-3">
                              <label class="form-label">Vendor Name <span style="color: red;">*</span></label>
                              <input id="register-username" type="text" name="vendor_name" value="{{$vendor->vendor_name}}" required placeholder="Please enter vendor name" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                              <label class="form-label">Mobile</label>
                              <input id="register-username" type="text" name="phone" value="{{$vendor->phone}}" placeholder="Please enter vendor mobile number" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                              <label class="form-label">Email Address</label>
                              <input id="register-email" type="text" name="email" value="{{$vendor->email}}" placeholder="Please enter vendor email address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                              <label class="form-label">Address</label>
                              <input id="address" type="text" name="address" value="{{$vendor->address}}" placeholder="Please enter vendor address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label for="account" class="form-label">Account Number</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account_number" name="account_number" value="{{$vendor->account_number}}" placeholder="Account Number">
                        </div>
                        <div class="col-sm-3">
                            <label for="account" class="form-label">Account Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="account_name" name="account_name" value="{{$vendor->account_name}}" placeholder="Account Name">
                        </div>
                        <div class="col-sm-3">
                            <label for="swift_code" class="form-label">Swift Code</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="swift_code" name="swift_code" value="{{$vendor->swift_code}}" placeholder="Swift Code">
                        </div>
                        <div class="col-sm-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" value="{{$vendor->bank_name}}" placeholder="Bank Name">
                        </div>
                        <div class="col-sm-3">
                            <label for="bank_name" class="form-label">Branch Name</label>
                            <input type="text" class="form-control form-control-sm mb-1" id="branch_name" name="branch_name" value="{{$vendor->branch_name}}" placeholder="Branch Name">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn btn-success btn-sm">Save Changes</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>      
    </div>

@endsection