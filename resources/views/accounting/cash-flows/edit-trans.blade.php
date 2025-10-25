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

    <div class="row clearfix">
        <div class="card radius-6">
            <!-- /.box-header -->
            <div class="card-body">
                <form class="form-validate row" method="POST" action="{{route('acc-transactions.update', encrypt($acctrans->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.from')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="from" required style="width: 100%;">
                                @foreach($accounts as $acc)
                                @if($acctrans->from_acc_id == $acc->id)
                                <option value="{{$acc->id}}" selected>{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @else
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.to')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="to" required style="width: 100%;">
                                @foreach($accounts as $acc)
                                @if($acctrans->to_acc_id == $acc->id)
                                <option value="{{$acc->id}}" selected>{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @else
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endif
                                @endforeach
                            </select>
                        </div>                    
                        <div class="col-md-3">
                            <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="number" min="0" step="any" name="amount" placeholder="Please enter Amount" class="form-control form-control-sm mb-1" value="{{$acctrans->amount}}">
                        </div>
                        <div class="col-md-3">
                            <label for="register-username" class="label-control">Reason </label>
                            <input id="register-username" type="text" name="reason" required placeholder="Please enter Reason(Optional)" class="form-control form-control-sm mb-1" value="{{$acctrans->reason}}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="date" id="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" value="{{$acctrans->date}}">
                            </div>
                        </div>
                        <div class="col-md-3 pt-4">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
            </div>
        </div>
    </div>
@endsection

<link rel="stylesheet" href="../../css/DatePickerX.css">

<script src="../../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]'),
                $max = document.querySelector('[name="outdate"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

        });
    </script>