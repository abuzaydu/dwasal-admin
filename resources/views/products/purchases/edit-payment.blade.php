@extends('layouts.inv')

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="card radius-10">
            <div class="card-body">
                <form class="form-validate row g-1" method="POST" action="{{route('purchase-payments.update' , encrypt($payment->id))}}">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                         <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="pay_date" id="pay_date" placeholder="Choose date payment" class="form-control form-control-sm mb-1" required value="{{$payment->pay_date}}" aria-describedby="cal_addon">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.amount_paid')}}</label>
                        <input id="name" type="number" name="amount" required placeholder="Please enter Amount Paid" class="form-control form-control-sm mb-1" value="{{$payment->amount}}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{trans('navmenu.account')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="pay_mode" required>
                            <option value="{{$payment->account}}">@if($payment->account == 'Cash')
                                @if(app()->getLocale() == 'en')
                                    {{$payment->account}}
                                    @else
                                    {{trans('navmenu.cash')}}
                                    @endif
                                @elseif($payment->account == 'Mobile Money')
                                    @if(app()->getLocale() == 'en')
                                    {{$payment->account}}
                                    @else
                                    {{trans('navmenu.mobilemoney')}}
                                    @endif
                                @elseif($payment->account == 'Bank')
                                    @if(app()->getLocale() == 'en')
                                    {{$payment->account}}
                                    @else
                                    {{trans('navmenu.bank')}}
                                    @endif      
                                @endif
                            </option>
                            <option value="Cash">{{trans('navmenu.cash')}}</option>
                            <option value="Bank">{{trans('navmenu.bank')}}</option>
                            <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">

<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>