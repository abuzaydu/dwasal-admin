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
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class=" col-md-9 mx-auto"> 
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" action="{{route('credit-notes.update', $creditnote->id)}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="form-group col-md-4">
                            <label>CREDIT NOTE TO:</label>
                            <input type="text" name="customer" class="form-control form-control-sm mb-1" value="{{$sale->name}}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Invoice No.</label>
                            <input type="text" name="invoice_no" class="form-control form-control-sm mb-1" value="{{ sprintf('%04d', $sale->invoice_no)}}" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Invoice Date :</label>
                            <input type="text" name="date" class="form-control form-control-sm mb-1" value="{{date('d, M Y', strtotime($sale->time_created))}}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Reason for Credit Note <span style="color: red;">*</span></label>
                            <textarea class="form-control form-control-sm mb-1" rows="1" name="reason" placeholder="Enter reason for issueing this Credit Note">{{$creditnote->reason}}</textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <label>{{trans('navmenu.amount')}}  <span style="color: red;">*</span></label>
                            <input type="number" name="amount" class="form-control form-control-sm mb-1" required value="{{$creditnote->amount}}" placeholder="Please enter Amount credited">
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-primary btn-sm pull-right" style="margin-left: 5px;">Create</button>
                            <a href="{{url('cancel-credit-note/'.encrypt($creditnote->id))}}" class="btn btn-warning btn-sm pull-right">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection