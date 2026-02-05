@extends('layouts.prof')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>           
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row g-1">
        <div class="col-lg-9 col-md-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-uppercase">Recycled Records</h6>
                    <hr>
                    <ul class="list-group list-group-custom list-group-flush">
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <i class="fa fa-file-o"></i>
                                </div>
                                <div class="col-sm-10">
                                    <h6 class="mb-1"><a target="_blank" href="{{ url('recycle-sales') }}">{{ trans('navmenu.invoices') }}</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                                <div class="col-sm-10" style="vertical-align: middle;">
                                     <h6 class="mb-1"><a class="link" href="{{ url('recycle-purchases') }}"> {{ trans('navmenu.purchases') }}</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <i class="fa fa-credit-card"></i>
                                </div>
                                <div class="col-sm-10">
                                    <h6 class="mb-1"><a href="{{ url('recycle-expenses') }}"> {{ trans('navmenu.expenses') }}</a></h6>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-2">
                                    <i class="fa fa-trash" style=" color: red;"></i>
                                </div>
                                <div class="col-sm-10" style="vertical-align: middle;">
                                     <h6 class="mb-1"><a href="{{ url('clear-all-shop-data') }}" style=" color: red;"> Clear All Shop Data</a></h6>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection