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
            <div class="card-body">
              	<form class="form-validate row g-3" method="POST" action="{{route('cash-flows.update', encrypt($cashout->id))}}">
                    @csrf
                    {{ method_field('PATCH') }}
                    <div class="col-md-3">
                        <label class="form-label">Activity Category</label>
                        <select class="form-select form-select-sm mb-1" name="category" required>
                            @if($cashout->category == 'Investing Activities')
                            <option>Investing Activities</option>
                            <option>Financing Activities</option>
                            <option>Other</option>
                            @elseif($cashout->category == 'Financing Activities')
                            <option>Financing Activities</option>
                            <option>Investing Activities</option>
                            <option>Other</option>
                            @else
                            <option>Other</option>
                            <option>Investing Activities</option>
                            <option>Financing Activities</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{trans('navmenu.reason')}}</label>
                        <input type="text" class="form-control form-control-sm mb-1" name="reason" value="{{$cashout->reason}}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{trans('navmenu.account')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="account_id" required style="width: 100%;">
                            @foreach($accounts as $acc)
                            @if($cashout->account_id == $acc->id)
                            <option value="{{$acc->id}}" selected>{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                            @else
                            <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{trans('amount')}}</label>
                        <input type="text" name="amount" class="form-control form-control-sm mb-1" value="{{$cashout->amount}}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{trans('navmenu.date')}}</label>
                        <div class="input-group date">
                            <div class="input-group-text">
                            <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" name="out_date" id="out_date" value="{{$cashout->out_date}}" placeholder="Choose date" class="form-control form-control-sm mb-1">
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                    </div>
                </form>
            </div>
        </div>
       <!-- /.box -->
   </div>
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="out_date"]'),
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