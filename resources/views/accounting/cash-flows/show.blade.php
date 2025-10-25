@extends('layouts.acc')

@section('content')
    
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-5 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.date')}}</b> <a class="pull-right">{{$cashout->out_date}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.amount')}}</b> <a class="pull-right">{{$cashout->amount}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.paid')}}</b> <a class="pull-right">{{$cashout->amount_paid}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.reason')}}</b> <a class="pull-right">{{$cashout->reason}}</a>
                        </li>
                        @if($cashout->is_borrowed === 1)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.is_borrowed')}}</b> <a class="pull-right">{{trans('navmenu.yes')}}</a>
                        </li>
                        @else
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.is_borrowed')}}</b> <a class="pull-right">{{trans('navmenu.no')}}</a>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.status')}}</b> <a class="pull-right">{{$cashout->status}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.customer')}}</b> <a class="pull-right">{{$customer}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection