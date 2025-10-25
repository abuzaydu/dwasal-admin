@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('an-sales') }}">Invoices</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('pro-invoices') }}">Pro Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" action="{{ url('update-invoice-item')}}">
                        @csrf
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <input type="hidden" name="invoice_id" value="{{$item->pro_invoice_id}}">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.product_name')}}</label>
                                <select class="form-select form-select-sm mb-1" name="product_id" required>
                                    <option value="{{$product->id}}">{{$product->slug}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                <input type="number" step="any" name="quantity" class="form-control form-control-sm mb-1" value="{{$item->quantity}}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                                <input type="number" step="any" name="retail_price" class="form-control form-control-sm mb-1" value="{{$item->cost_per_unit}}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{trans('navmenu.total')}}</label>
                            <input type="number" name="" value="{{$item->amount}}" class="form-control form-control-sm mb-1" readonly>
                        </div>
                        @if($settings->discount_by_percent)
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.discount')}} (%)</label>
                                <input type="number" step="any" name="disc_percent" class="form-control form-control-sm mb-1" value="{{$item->disc_percent}}">
                            </div>
                        </div>
                        <input type="hidden" name="total_discount" value="{{$item->total_discount}}">
                        @else
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.total')}} {{trans('navmenu.discount')}}</label>
                                <input type="number" step="any" name="total_discount" class="form-control form-control-sm mb-1" value="{{$item->total_discount}}">
                            </div>
                        </div>
                        <input type="hidden" name="disc_percent" value="{{$item->disc_percent}}">
                        @endif
                        <div class="col-md-3">
                            <label class="form-label">Add VAT</label>
                            <select class="form-select form-select-sm mb-1" name="with_vat">
                                @if($item->with_vat == 'yes')
                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                <option value="no">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="no">{{trans('navmenu.no')}}</option>
                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
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
    <!--end row-->
@endsection