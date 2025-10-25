@extends('layouts.inv')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="card radius-10">
            <div class="card-body">
                <form class="form-validate row g-3" method="POST" action="{{route('purchases.update' , encrypt($purchase->id))}}">
                    @csrf
                    @method('PUT')
                    <div class="col-md-3">
                        <label class="form-label">{{trans('navmenu.supplier')}}</label>
                        <select name="supplier_id" class="form-select form-select-sm mb-1">
                            @foreach($suppliers as $supplier)
                            @if($supplier->id == $purchase->supplier_id)
                            <option value="{{$supplier->id}}" selected>{{$supplier->name}}</option>
                            @else
                            <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="form-label">Production Date</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="purchase_date" id="purchase_date" value="{{$purchase->time_created}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                        </div>
                    </div>
                    @if($shop->business_type_id != 1)
                    <div class="col-md-3" id="order_no">
                        <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                        <input type="text" class="form-control form-control-sm mb-1" id="ord_no" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" value="{{$purchase->order_no}}" />
                    </div>
                    <div class="col-md-3" id="delivery_note_no">
                        <label for="total" class="form-label">{{trans('navmenu.delivery_note_no')}}</label>
                        <input type="text" class="form-control form-control-sm mb-1" id="dn_no" placeholder="{{trans('navmenu.hnt_delivery_note_no')}}" name="delivery_note_no" value="{{$purchase->delivery_note_no}}" />
                    </div>
                    <div class="col-md-3" id="invoice_no">
                        <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                        <input type="text"  class="form-control form-control-sm mb-1" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" value="{{$purchase->invoice_no}}" />
                    </div> 
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.comments')}}</label>
                        <textarea name="comments" rows="1" class="form-control form-control-sm mb-1">@if($purchase->comments != 'null'){{$purchase->comments}}@endif</textarea>
                    </div>
                    @endif
                    <div class="col-md-12 pt-1">
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
            var $min = document.querySelector('[name="purchase_date"]');
        
            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>