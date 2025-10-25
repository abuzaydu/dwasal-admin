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
        <div class=" col-md-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" action="{{route('delivery-notes.update', encrypt($dnote->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                        <div class="form-group col-md-4">
                            <label>DELIVERY NOTE TO:</label>
                            <input type="text" name="customer" class="form-control form-control-sm mb-1" value="{{$sale->name}}" readonly>
                        </div>
                        <div class="form-group col-md-8">
                            <label>Comments: <span style="color: red;">*</span></label>
                            <textarea class="form-control form-control-sm mb-1" rows="1" name="comments" placeholder="Enter Comments here" required>{{$dnote->comments}}</textarea>
                        </div>
                        <div class="form-group col-md-4">
                            <button class="btn btn-primary btn-sm pull-right" style="margin-left: 5px;">Update</button>
                            <a href="#" onclick="history.back()" class="btn btn-warning btn-sm pull-right">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection