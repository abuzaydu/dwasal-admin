@extends('layouts.prod')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="col-md-10 mx-auto">
        <div class="card radius-10">
            <div class="card-body">
                <form class="form-validate row g-3" method="POST" action="{{route('rm-purchase-payments.update' , encrypt($payment->id))}}">
                    @csrf
                    @method('PUT')
                    <div class="col-sm-4 pt-2">
                        <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                        <div class="input-group date">
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text"name="pay_date" id="pay_date" placeholder="Choose date payment" class="form-control form-control-sm mb-3" required value="{{$payment->pay_date}}" >
                            </div>
                        </div>
                    </div>    
                    <div class="col-sm-4 pt-2">
                        <label class="form-label">{{trans('navmenu.amount_paid')}}</label>
                        <input id="name" type="number" name="amount" required placeholder="Please enter Amount Paid" class="form-control form-control-sm mb-3" value="{{$payment->amount}}">
                    </div>
                    <div class="col-sm-4 pt-2">
                        <label class="form-label">{{trans('navmenu.account')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb3" name="account_id" required>
                            @foreach($accounts as $acc)
                            <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4 pt-2">
                    <div class="form-group">
                       <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                     <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                   </div>
                  </div>
                </form>
              </div>
              <!-- /.row -->
            </div>
        </div>
    </div>
      <!-- /.box -->
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    :  new Date(),
            });


        });
    </script>