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
    <div class="row clearfix">
        <div class=" col-md-8 mx-auto"> 
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" action="{{route('delivery-notes.store')}}">
                        @csrf
                        <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                        <div class="form-group col-md-4">
                            <label class="form-label">DELIVERY NOTE TO:</label>
                            <input type="text" name="customer" class="form-control form-control-sm mb-1" value="{{$sale->name}}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Issued By</label>
                            <input type="text" name="issued_by" value="{{Auth::user()->first_name}} {{Auth::user()->last_name}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Receiver</label>
                            <input type="text" name="received_by" class="form-control form-control-sm mb-1" placeholder="Enter Receiver's name (optional)">
                        </div>
                        <div class="form-group col-md-12">
                            <label class="form-label">Comments: <span style="color: red;">*</span></label>
                            <textarea class="form-control form-control-sm mb-1" rows="1" name="comments" placeholder="Enter Comments here" required>Received the above mentioned goods in good order and condition</textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <button class="btn btn-primary btn-sm pull-right" style="margin-left: 5px;">Create</button>
                            <a href="#" onclick="history.back()" class="btn btn-warning btn-sm pull-right">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection