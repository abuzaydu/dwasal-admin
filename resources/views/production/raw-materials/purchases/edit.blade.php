@extends('layouts.prod')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 ms-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form-validate row g-1" method="POST" action="{{route('rm-purchases.update', encrypt($purchase->id))}}">
                        @csrf
                        @method('PUT')
                        <div class="col-md-3" >
                            <label class="form-label">{{trans('navmenu.purchase_date')}}</label>
                            <div class="input-group date"> 
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="date" id="purchase_date" class="form-control form-control-sm mb-3 " value="{{$purchase->date}}" >
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.supplier')}}</label>
                            <select name="supplier_id" class="form-control form-select-sm mb-3">
                                @if(!is_null(App\Models\Supplier::find($purchase->supplier_id)))
                                <option value="{{$purchase->supplier_id}}">{{App\Models\Supplier::find($purchase->supplier_id)->name}}</option>
                                @else
                                <option value="">{{trans('navmenu.unknown')}}</option>
                                @endif
                                @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="total" class="form-label">{{trans('navmenu.delivery_note_no')}}</label>
                            <input type="text" class="form-control form-control-sm mb-3" id="dn_no" placeholder="{{trans('navmenu.hnt_delivery_note_no')}}" name="delivery_note_no" value="{{$purchase->delivery_note_no}}" />
                        </div> 
                        <div class="col-md-3">
                            <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                            <input type="text"  class="form-control form-control-sm mb-3" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" value="{{$purchase->invoice_no}}" />
                        </div> 
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea rows="1" name="comments" class="form-control form-control-sm mb-3">@if($purchase->comments != 'null'){{$purchase->comments}}@endif</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.amount_paid')}}</label>
                            <input type="text" name="amount_paid" class="form-control form-control-sm mb-3" value="{{$purchase->amount_paid}}" readonly>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
    
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[id="purchase_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>