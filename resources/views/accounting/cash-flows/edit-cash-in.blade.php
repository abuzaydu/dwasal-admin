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
          	    <form class="form-validate row g-3" method="POST" action="{{route('cash-ins.update', encrypt($cashin->id))}}">
        			@csrf
            		{{ method_field('PATCH') }}
                    <div class="col-md-3">
                        <label class="form-label">Activity Category</label>
                        <select class="form-select form-select-sm mb-1" name="category" required>
                            @if($cashin->category == 'Investing Activities')
                            <option>Investing Activities</option>
                            <option>Financing Activities</option>
                            @else
                            <option>Financing Activities</option>
                            <option>Investing Activities</option>
                            @endif
                        </select>
                    </div>
	            	<div class="col-md-3">
	              		<label class="form-label">{{trans('navmenu.source')}}</label>
                        <input type="text" name="source" value="{{$cashin->source}}" class="form-control form-control-sm mb-1">
	              	</div>
              		<div class="col-md-3">
                		<label class="control-label">{{trans('navmenu.account')}} <span style="color: red; font-weight: bold;">*</span></label>
                  		<select class="form-select form-select-sm mb-1" name="account_id" required style="width: 100%;"> 
                            @foreach($accounts as $acc)
                            @if($cashin->account_id == $acc->id)
                            <option value="{{$acc->id}}" selected>{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                            @else
                            <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                            @endif
                            @endforeach
                  		</select>
              		</div>
              		<div class="col-md-3">
                		<label class="form-label">{{trans('amount')}}</label>
                  		<input type="number" min="0" step="any" name="amount" class="form-control form-control-sm mb-1" value="{{$cashin->amount}}">
              		</div>
              		<div class="col-md-3">
                		<label class="form-label">{{trans('navmenu.date')}}</label>
                		<div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="in_date" id="in_date" value="{{$cashin->in_date}}" placeholder="Choose date" class="form-control form-control-sm mb-1">
                		</div>
              		</div>
	           	 	<!-- /.col -->
              		<div class="col-md-3">
	               		<button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                 		<a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
              		</div>
	        	</form>
          	</div>
          	<!-- /.row -->
        </div>
    </div>
   <!-- /.box -->
@endsection

<link rel="stylesheet" href="../../css/DatePickerX.css">

<script src="../../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="in_date"]'),
                $max = document.querySelector('[name="indate"]');


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