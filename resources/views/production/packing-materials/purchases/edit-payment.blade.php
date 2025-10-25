@extends('layouts.prod')
@section('content')
  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">{{$page}}</div>
      <div class="ps-3">
          <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0 p-0">
                  <li class="breadcrumb-item"><a href="javascript:;"><i class="fa fa-home-alt"></i></a>
                  </li>
                  <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
              </ol>
          </nav>
      </div>
    <div class="ms-auto">
        
      </div>
  </div>
  <!-- breadcrub -->
    <div class="col-md-9 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>

      <!-- SELECT2 EXAMPLE -->
        <div class="card radius-10">
            <div class="card-body">
              <div class="row">
                <form class="form-validate" method="POST" action="{{route('pm-purchase-payments.update' , encrypt($payment->id))}}">
                    @csrf
                    @method('PUT')
                    <div class="row ">
                        <div class="col-sm-4">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="input-group date">
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text"name="pay_date" id="pay_date" placeholder="Choose date payment" class="form-control form-control-sm mb-3" required value="{{$payment->pay_date}}" >
                                </div>
                            </div>
                        </div>    
                        <div class="col-sm-4 ">
                            <label class="form-label">{{trans('navmenu.amount_paid')}}</label>
                            <input id="name" type="number" name="amount" required placeholder="Please enter Amount Paid" class="form-control form-control-sm mb-3" value="{{$payment->amount}}">
                        </div>

                        <div class="col-sm-4 ">
                            <label class="form-label">{{trans('navmenu.account')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-control form-control-sm mb-3" name="account" required>
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
                    </div>
        
                    <!-- /.col -->
                  <div class="row">
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