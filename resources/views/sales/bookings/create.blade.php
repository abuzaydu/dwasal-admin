@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('bookings') }}">Bookings</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" id="mycontroller" ng-controller="SearchItemCtrl">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-3 border rounded">
                        <form class="form" id="pos-form"  method="POST" action="{{ route('bookings.store') }}">
                            @csrf
                            <div class="row g-1 mb-1">
                                <div class="col-sm-12" id="ermsg"></div>
                                <div class="col-sm-4">
                                    <label for="address" class="form-label">Customer Category</label>
                                    <select name="customer_category_id" class="form-select form-select-sm mb-1">
                                        <option>--None--</option>
                                        @foreach($categories as $cat)
                                        <option value="{{$cat->id}}">{{$cat->cat_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
                                    <input type="text" name="name" required placeholder="{{ trans('navmenu.hnt_customer_name') }}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.phone_number')}}</label>
                                    <input type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-4">
                                    <label for="register-email" class="form-label">{{trans('navmenu.email_address')}}</label>
                                    <input id="register-email" type="text" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-4">
                                    <label for="address" class="form-label">Address</label>
                                    <input id="address" type="text" name="physical_address" placeholder="Enter customer address" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.tin')}}</label>
                                    <input type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-1"  data-inputmask='"mask": "999-999-999"' data-mask>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.vrn')}}</label>
                                    <input type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                                    <select class="form-select form-select-sm mb-1" name="cust_id_type">
                                        @foreach($custids as $cid)
                                        @if($cid['id'] == 6)
                                        <option value="{{$cid['id']}}" selected>{{$cid['name']}}</option>
                                        @else
                                        <option value="{{$cid['id']}}">{{$cid['name']}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.id_number')}}</label>
                                    <input type="text" name="custid" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Check In Date <span style="color: red; font: bold;">*</span></label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="check_in_date" id="check_in_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Check Out Date <span style="color: red; font: bold;">*</span></label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="check_out_date" id="check_out_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-3" id="agent">
                                    <label class="form-label">Agent (Optional)</label>
                                    <select class="form-select form-select-sm mb-1" name="booking_agent_id" id="agent_id">
                                        <option value="">--Select Agent--</option>
                                        @foreach($bagents as $agent)
                                        <option value="{{$agent->id}}">{{$agent->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12" style="margin-top: 5px;">
                                    <button type="submit" id="btn-next" class="btn btn-primary btn-sm">Next</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="check_in_date"]');
            var $max = document.querySelector('[name="check_out_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd'
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min
            });
        });
    </script>