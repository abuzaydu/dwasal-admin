@extends('layouts.prod')

@section('content')

      <!-- SELECT2 EXAMPLE -->
      <div class="card radius-10">
        <div class="card-body">
          <div class="row">

            <form class="form-validate" method="POST" action="{{route('rm-purchase-payments.update' , encrypt($payment->id))}}">
                @csrf
                @method('PUT')
                <div class="col-sm-4 pt-2">
                    <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                    <div class="input-group">
                        <div id="cal_addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="pay_date" id="pay_date" placeholder="Choose date payment" class="form-control" required value="{{$payment->pay_date}}" aria-describedby="cal_addon">
                     </div>
                <div class="col-sm-4 pt-2">
                    <label class="form-label">{{trans('navmenu.amount_paid')}}</label>
                    <input id="name" type="number" name="amount" required placeholder="Please enter Amount Paid" class="form-control" value="{{$payment->amount}}">
                </div>

                <div class="col-sm-4 pt-2">
                   <label class="form-label">{{trans('navmenu.account')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="account" required>
                                <option value="{{$payment->account}}">@if($payment->account == 'Cash')
                                    @if(app()->getLocale() == 'en')
                                    {{$payment->account}}
                                    @else
                                    {{trans('navmenu.cash')}}
                                    @endif
                                @elseif($payment->account == 'Mobile Money')
                                    @if(app()->getLocale() == 'en')
                                    {{$payment->account}}
                                    @else
                                    {{trans('navmenu.mobilemoney')}}
                                    @endif
                                @elseif($payment->account == 'Bank')
                                    @if(app()->getLocale() == 'en')
                                    {{$payment->account}}
                                    @else
                                    {{trans('navmenu.bank')}}
                                    @endif      
                                @endif</option>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                </div>

                <!-- /.col -->
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
      <!-- /.box -->
@endsection

<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
           var $min = document.querySelector('[name="pay_date"]'),
                $max = document.querySelector('[name="end_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : $max
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min,
                // maxDate    : new Date()
            });

        });
    </script>