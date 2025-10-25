@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row row-cols-1 row-cols-md-1 row-cols-lg-1 row-cols-xl-1">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{route('customers.update', encrypt($customer->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-sm-3">
                            <label class="form-label">@if($settings->is_cm_business) Rider Name @else {{trans('navmenu.customer_name')}} @endif<span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_customer_name')}}" value="{{$customer->name}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" value="{{$customer->contact_person}}" placeholder="Please enter contact person" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">                                
                            <label for="inputEmailAddress" class="form-label">{{trans('navmenu.mobile')}} <span style="color:red">*</span></label>
                            <input type="tel" class="form-control form-control-sm mb-1" name="phone" value="{{$customer->phone}}" placeholder="Eg. 0789XXXXXX" value="{{old('phone')}}">
                            <input type="hidden" name="phone_country" id="countryCode" value="{{$customer->country_code}}">
                            <input type="hidden" name="dial_code" id="dialCode">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.email_address')}}</label>
                            <input id="email" type="email" name="email" value="{{$customer->email}}" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                            <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" value="{{$customer->physical_address}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label for="address" class="form-label">Category</label>
                            <select name="customer_category_id" class="form-select form-select-sm mb-1">
                                <option>--Select--</option>
                                @foreach($categories as $cat)
                                <option value="{{$cat->id}}">{{$cat->cat_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(Auth::user()->can('activate-customer'))
                        <div class="col-sm-2">
                            <label class="form-label">Is Active</label>
                            <select class="form-select form-select-sm mb-1" name="is_active">
                                @if($customer->is_active)
                                <option value="1">YES</option>
                                <option value="0">NO</option>
                                @else
                                <option value="0">NO</option>
                                <option value="1">YES</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Due Days Limit  for Invoice</label>
                            <input type="number" name="default_due_days" value="{{$customer->default_due_days}}" placeholder="Enter Due Days Limit for Invoice" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-3">
                            <label for="address" class="form-label">Limit of Due Amount</label>
                            <input type="number" min="0" step="any" name="due_amount_limit" placeholder="Enter Limit of Due Amount" value="{{$customer->due_amount_limit}}" class="form-control form-control-sm mb-1">
                        </div>
                        @endif
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.tin')}}</label>
                            <input id="tin" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" value="{{$customer->tin}}" class="form-control form-control-sm mb-1"  data-inputmask='"mask": "999-999-999"' data-mask>
                        </div>
                        <div class="col-sm-3">                                
                            <label class="form-label">{{trans('navmenu.vrn')}}</label>
                            <input id="vrn" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" value="{{$customer->vrn}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                            <select class="form-select" name="cust_id_type">
                                @foreach($custids as $cid)
                                @if($cid['id'] == $customer->cust_id_type)
                                <option value="{{$cid['id']}}" selected>{{$cid['name']}}</option>
                                @else
                                <option value="{{$cid['id']}}">{{$cid['name']}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.id_number')}}</label>
                            <input type="text" name="custid" value="{{$customer->custid}}" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-1">
                        </div>
                                
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="{{ url('customers') }}" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection