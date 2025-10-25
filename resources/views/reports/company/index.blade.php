@extends('layouts.gen')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>               
                    <li class="breadcrumb-item">General Reports</li>             
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row row-cols-xxl-2 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 row-cols-sm-2 row-cols-1 g-2 mb-4 row-deck">
        <div class="col"> 
            <div class="card text-center">
                <div class="card-body py-4">
                    <figure>
                        <img class="invoice-logo" src="{{asset('assets/img/is.png')}}" alt="" width="50">
                    </figure>
                </div>
                <div class="card-footer border-bottom border-top py-3">
                    <h5 class="mb-1"><a class="link" href="{{ url('company-income-stmt') }}"> Income Statement</a></h5>
                    <span class="color-400"></span>
                </div>
            </div>
        </div>
        <div class="col"> 
            <div class="card text-center">
                <div class="card-body py-4">
                    <figure>
                        <img class="invoice-logo" src="{{asset('assets/img/cf.png')}}" alt="" width="50">
                    </figure>
                </div>
                <div class="card-footer border-bottom border-top py-3">
                    <h5 class="mb-1"><a href="{{ url('company-cf-stmt') }}"> Cash Flow Statement</a></h5>
                    <span class="color-400"></span>
                </div>
            </div>
        </div>
        <div class="col"> 
            <div class="card text-center">
                <div class="card-body py-4">
                    <figure>
                        <img class="invoice-logo" src="{{asset('assets/img/bs.png')}}" alt="" width="50">
                    </figure>
                </div>
                <div class="card-footer border-bottom border-top py-3">
                    <h5 class="mb-1"><a href="{{ url('balance-sheet') }}"> Balance Sheet</a></h5>
                    <span class="color-400"></span>
                </div>
            </div>
        </div>
        <div class="col"> 
            <div class="card text-center">
                <div class="card-body py-4">
                    <figure>
                        <img class="invoice-logo" src="{{asset('assets/img/gl.png')}}" alt="" width="50">
                    </figure>
                </div>
                <div class="card-footer border-bottom border-top py-3">
                    <h5 class="mb-1"><a href="{{ url('balance-sheet') }}"> General Ledger</a></h5>
                    <span class="color-400"></span>
                </div>
            </div>
        </div>
    </div> <!-- .row end -->
@endsection