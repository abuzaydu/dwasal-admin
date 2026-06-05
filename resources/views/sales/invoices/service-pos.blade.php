@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/servpos.js') }}"></script>
<script>
        function validateform(form) {
            
            return true;
        }

        function confirmCancel() {
            Swal.fire({
              title: "{{trans('navmenu.are_you_sure')}}",
              text: "{{trans('navmenu.no_revert')}}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{trans('navmenu.cancel_it')}}",
              cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
              if (result.value) {
                // window.location.href="{{url('cancel-sale')}}";
                document.getElementById('delete-form').submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }


        function submitTemp(index) {
            document.getElementById('ptemp-form-'+index).submit();
        }
        
        function submitTempData() {
            var customer_id = document.getElementById('cust-id').value;
            var sale_type = document.getElementById('sale_type').value;
            if (customer_id == '') {
                $('#ermsg').append('<div class="alert alert-danger hideit alertSuc">Please select a Customer</div >');
                setTimeout(function() {
                    $('.hideit').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 1300);
            }else if(sale_type == ''){
                $('#ermsg').append('<div class="alert alert-danger hideit alertSuc">Please select Type of Sales</div >');
                setTimeout(function() {
                    $('.hideit').fadeOut('slow', function() {
                        $(this).remove();
                    });
                }, 1300);
            }else{
                document.getElementById('ptemp-form-on').submit();
            }
        }
        
        function submitTempResetData() {
            document.getElementById('ptemp-form-reset').submit();
        }

        function weg(elem) {
          var x = document.getElementById("sale_date_field");
          if(elem.value !== "auto") {
            x.style.display = "block";
          } else {
            x.style.display = "none";
            $("#sale_date").val('');
          }
        }


        function wegDam(elem) {
          var x = document.getElementById("dam_date_field");
          if(elem.value !== "auto") {
            x.style.display = "block";
          } else {
            x.style.display = "none";
            $("#dam_date").val('');
          }
        }

        function wegSaleType(elem) {
            var stype = "<?php echo $shop->subscription_type_id; ?>";
            var isfill = "<?php echo $settings->is_filling_station; ?>";
            var pm = document.getElementById('paymode');
            var iv = document.getElementById('invoice_no');
            var vehc = document.getElementById('vehcleno');
            var d = document.getElementById('duedate');
            var b = document.getElementById('payable');
            var bk = document.getElementById('bankdetail');
            var mo = document.getElementById('mobaccount');
            var ca = document.getElementById('cashaccount');
            if (elem.value === "credit") {
                pm.style.display = "block";
                ca.style.display = 'none';
                d.style.display = "block";
                if (isfill ==1) {
                    vehc.style.display = "block";
                }
                bk.style.display = "none";
                mo.style.display = "none";
            }else if (elem.value === 'cash') {
                pm.style.display = "block";
                ca.style.display = 'block';
                d.style.display = "none";
                vehc.style.display = "none";
            }else{
                ca.style.display = 'none';
                pm.style.display = "none";
                d.style.display = "none";
                vehc.style.display = "none";
            }
        }

        function detailUpdate(elem) {
            var b = document.getElementById('bank-name');
            var m = document.getElementById('mobaccount');
            var ca = document.getElementById('cashaccount');
            var dpm = document.getElementById('deposit_mode');
            var slip = document.getElementById('slip');
            if (elem.value === 'Bank') {
                b.style.display = 'block';
                m.style.display = 'none';
                ca.style.display = 'none';
                dpm.style.display = "block";
                slip.style.display = 'block'
            }else if (elem.value === 'Mobile Money') {
                ca.style.display = 'none';
                b.style.display = 'none';
                m.style.display = 'block';
                dpm.style.display = 'none';
                slip.style.display = 'none';
            }else{
                ca.style.display = 'block';
                b.style.display = 'none';
                m.style.display = 'none';
                dpm.style.display = 'none';
                slip.style.display = 'none';
            }
        }

        function discountMode(elem) {
          var x = document.getElementById("total_discount_field");
          var y = document.getElementById('total_discount_value');
          var df = document.getElementById('discount_field');
          var dv = document.getElementById('discount_value');
          if(elem.value === "total") {
            x.style.display = "block";
            y.style.display = "none";
            dv.style.display = "block";
            df.style.display = "none";
          } else if (elem.value === "single") {
            x.style.display = "none";
            y.style.display = "block";
            df.style.display = "block";
            dv.style.display = "none";
          }
        }

        function rateMode(val){
            var localRate = document.getElementById('local-rate');
            var foreignRate = document.getElementById('foreign-rate');

            if (val == 'default') {
                foreignRate.style.display = 'none';
                localRate.style.display = 'block';
            }else{
                localRate.style.display = 'none';
                foreignRate.style.display = 'block';
            }
        }
    </script> 
@section('content')
    <!--breadcrumb-->
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
                <div class="d-lg-flex align-items-center mb-1 gap-1">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#customerModal"><i class="fa fa-user-plus"></i>{{trans('navmenu.new_customer')}}</button>
                    @if($settings->is_agent)
                    <a href="{{url('ocamounts')}}" class="btn btn-primary pull-right" style="margin-left: 5px;"><i class="fa fa-file-o"></i>{{trans('navmenu.new_oc_amount')}}</a>
                    @endif
                    <div class="btn-group mt-1" role="group">
                        <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingtemps->count()}}</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Pending Bills/Invoices <i class="fa fa-caret-down"></i></button>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                            @foreach($pendingtemps as $key => $temp) 
                            <form class="row g-3" method="POST" action="{{'pt-pos'}}" id="ptemp-form-{{$key}}">
                                @csrf
                                <input type="hidden" name="id" value="{{$temp->id}}">
                                <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">{{$temp->name}} (<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                            </form>  
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-lg-12 col-md-12 col-sm-12">
                <marquee><small>{{trans('navmenu.expire_notify')}}@if($status > 7) <b style="color: green;">{{date('d M, Y H:m:s:A', strtotime($payment->expire_date))}} ({{$status}} {{trans('navmenu.days')}})</b> @else <b style="color: red;">{{date('d M, Y H:m:s:A', strtotime($payment->expire_date))}} ({{$status}} {{trans('navmenu.days')}})</b>@endif </small></marquee>
            </div> -->
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="saleTempId('<?php echo $saletemp->id; ?>')">
        <?php 
            $customer = App\Models\Customer::where('id', $saletemp->customer_id)->select('id', 'name')->first();
            $custid = '';
            $custname = '';
            if (!is_null($customer)) {
                $custid = $customer->id;
                $custname = $customer->name;
            } 
        ?>
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-3 border rounded print_invoice">
                        @if(is_null($saletemp->customer_id) || is_null($saletemp->sale_type))
                        <form class="form" method="POST" action="{{ url('pos-temp') }}" id="ptemp-form-on">
                            @csrf
                            <input type="hidden" name="sale_temp_id" placeholder="" value=" {{$saletemp->id}}" class="form-control form-control-sm mb-1">
                            <input type="hidden" name="customer_id" id="cust-id" value="{{$custid}}" class="form-control form-control-sm mb-1">
                            <div class="row g-1">
                                <div class="col-sm-12" id="ermsg"></div>
                                <div class="col-sm-4">
                                    <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                    <input id="search_customer_key" placeholder="Search customer" value="{{$custname}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                    <ul id="searchResult2"></ul>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.saledate')}}</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="sale_date" id="sale_date" ng-model="saletemp.sale_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.sales_type')}} <span style="color: red;">*</span></label>
                                    <select name="sale_type" id="sale_type" onchange="wegSaleType(this)" class="form-select form-select-sm mb-1" ng-model="saletemp.sale_type" ng-change="updateSaleTempInfo(saletemp)" required>
                                        <option value="">---{{trans('navmenu.select')}}---</option>
                                        <option value="cash">{{trans('navmenu.cash_sales')}}</option>
                                        <option value="credit">{{trans('navmenu.credit_sales')}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-2" id="paymode" style="display: none;">
                                    <label for="payment_type" class="form-label">{{trans('navmenu.pay_method')}}</label>
                                    <select class="form-select form-select-sm mb-1" name="pay_type" ng-model="saletemp.pay_type" ng-change="updateSaleTempInfo(saletemp)" onchange="detailUpdate(this)" required>
                                        <option value="Cash" selected>{{trans('navmenu.cash')}}</option>
                                        <option value="Bank">{{trans('navmenu.bank')}}</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                        <option value="Multiple">Multiple Pay</option>
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <a href="javascript:;" onclick="submitTempData()" class="btn btn-primary btn-sm float-end">Next</a>
                                </div>
                            </div>
                        </form>
                        @else
                        <form class="form" id="pos-form" name="saleform" method="POST" action="{{ route('pos.store') }}" ng-if="saletemp">
                            @csrf
                            <input type="hidden" name="sale_temp_id" placeholder="" value=" {{$saletemp->id}}" class="form-control form-control-sm mb-1">
                            <input type="hidden" id="use_pre_payment" name="use_pre_payment" value="0">
                            
                            <div class="row g-1 mb-3">
                                <div class="col-sm-4">
                                    <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                    <select name="customer_id" id="customer_id" required class="form-select form-select-sm mb-1">
                                        @if(!is_null($customer))
                                        <option value="{{$customer->id}}" selected>{{$customer->name}}</option>
                                        @endif
                                    </select>
                                </div>                                
                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.saledate')}}</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="sale_date" id="sale_date" ng-model="saletemp.sale_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.sales_type')}} <span style="color: red;">*</span></label>
                                    <select name="sale_type" id="sale_type" class="form-select form-select-sm mb-1" required>
                                        @if($saletemp->sale_type == 'cash')
                                        <option value="cash">{{trans('navmenu.cash_sales')}}</option>
                                        @else
                                        <option value="credit">{{trans('navmenu.credit_sales')}}</option>
                                        @endif
                                    </select>
                                </div>
                                @if($saletemp->sale_type == 'credit')
                                <div class="col-sm-2" id="duedate">
                                    <label for="total" class="form-label">{{trans('navmenu.due_date')}}</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="due_date" value="{{$due_date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                @endif
                                <div class="col-sm-2">
                                    <label for="total" class="form-label">LPO No.</label>
                                    <input type="text" class="form-control form-control-sm mb-1" id="lpo_no" placeholder="Enter LPO Number" name="lpo_no"/>
                                </div>
                                @if($settings->retail_with_wholesale)
                                <div class="col-sm-3">
                                    <label for="invoice" class="control-label">{{trans('navmenu.sold_in')}}:</label>
                                    <select name="sale_mode" id="sale_mode" ng-model="sale_mode" ng-change="updateSaleMode(sale_mode)" class="form-select form-select-sm mb-1" ng-options="mode for mode in salemodes">
                                        
                                    </select>
                                </div>
                                @endif
                                <div class="col-sm-2" id="paymode">
                                    <label for="payment_type" class="form-label">{{trans('navmenu.pay_method')}}</label>
                                    <select class="form-select form-select-sm mb-1" name="pay_type" required>
                                        @if($saletemp->pay_type == 'Cash')
                                        <option value="Cash" selected>{{trans('navmenu.cash')}}</option>
                                        @elseif($saletemp->pay_type == 'Bank')
                                        <option value="Bank">{{trans('navmenu.bank')}}</option>
                                        @elseif($saletemp->pay_type == 'Cheque')
                                        <option value="Cheque">Cheque</option>
                                        @elseif($saletemp->pay_type == 'Mobile Money')
                                        <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                        @else
                                        <option value="Multiple">Multiple Pay</option>
                                        @endif
                                    </select>
                                </div>
                                @if(Auth::user()->can('create-sale-payment'))
                                @if($saletemp->pay_type == 'Cash')
                                <div class="col-sm-2" id="cashaccount">
                                    <label class="form-label">Cash Account </label>
                                    <select class="form-select form-select-sm mb-1" name="cash_acc_id">                  
                                        @foreach($accounts->where('type', 'Cash') as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                @elseif($saletemp->pay_type == 'Bank')
                                <div class="col-sm-2" id="deposit_mode">
                                    <label class="form-label">Deposit Mode</label>
                                    <select name="deposit_mode" class="form-select form-select-sm mb-1">
                                        <option>Direct Deposit</option>
                                        <option>Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="col-sm-4" id="bank-name">
                                    <label class="form-label">Bank Account </label>
                                    <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                        @foreach($accounts->where('type', 'Bank') as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}} @endif @if(!is_null($acc->currency)) - {{$acc->currency}}@endif @if(!is_null($acc->bank_name)) -{{$acc->bank_name}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2" id="slip">
                                    <label class="form-label">Reference Number</label>
                                    <input id="name" type="text" name="slip_no" placeholder="Enter Slip number" class="form-control form-control-sm mb-1">
                                </div>
                                @elseif($saletemp->pay_type == 'Cheque')
                                <div class="col-sm-4" id="bank-name">
                                    <label class="form-label">Bank Account </label>
                                    <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                        @foreach($accounts->where('type', 'Bank') as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}} @endif @if(!is_null($acc->currency)) - {{$acc->currency}}@endif @if(!is_null($acc->bank_name)) -{{$acc->bank_name}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Expire Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-2" id="slip">
                                    <label class="form-label">Cheque Number</label>
                                    <input id="name" type="text" name="slip_no" placeholder="Enter Slip number" class="form-control form-control-sm mb-1">
                                </div>
                                @elseif($saletemp->pay_type == 'Mobile Money')
                                <div class="col-sm-3" id="mobaccount">
                                    <label class="form-label">Mobile Money Account </label>
                                    <select class="form-select form-select-sm mb-1" name="mob_acc_id">
                                        @foreach($accounts->where('type', 'Mobile Money') as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                @if($settings->allow_multi_currency)
                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.currency')}}</label>
                                    <select name="currency" id="currency" class="form-select form-select-sm mb-1" ng-model="saletemp.currency" ng-change="updateSaleTempInfo(saletemp)" ng-options="curr.code as curr.code for curr in currencies" required>
                                    </select>
                                </div>
                                <div class="col-sm-2" ng-if="saletemp.currency != saletemp.defcurr">
                                    <label class="form-label">Ex Rate Mode</label>
                                    <select name="ex_rate_mode"  class="form-select form-select-sm mb-1" ng-model="saletemp.ex_rate_mode">
                                        <option value="Locale">1 @{{saletemp.defcurr}} Equals ? @{{saletemp.currency}}</option>
                                        <option value="Foreign" selected>1 @{{saletemp.currency}} Equals ? @{{saletemp.defcurr}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-2" ng-if="saletemp.currency != saletemp.defcurr && saletemp.ex_rate_mode == 'Locale'">
                                    <label class="form-label">Rate in @{{saletemp.currency}}</label>
                                    <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-1" string-to-number ng-model="saletemp.foreign_ex_rate" ng-blur="updateSaleTempInfo(saletemp)">
                                </div>
                                <div class="col-sm-2" ng-if="saletemp.currency != saletemp.defcurr && saletemp.ex_rate_mode == 'Foreign'">
                                    <label class="form-label">Rate in @{{saletemp.defcurr}}</label>
                                    <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-1" string-to-number ng-model="saletemp.local_ex_rate" ng-blur="updateSaleTempInfo(saletemp)">
                                </div>
                                @endif
                                @endif
                                @if(Auth::user()->can('offer-discount'))
                                <div class="col-sm-2">
                                    <label for="invoice" class="form-label">{{trans('navmenu.discount_by')}}:</label>
                                    <select name="disc_set" id="disc_set" onchange="discountMode(this)" class="form-select form-select-sm mb-1">
                                        <option value="single">{{trans('navmenu.each_product')}}</option>
                                        <option value="total">{{trans('navmenu.total_sale')}}</option>
                                    </select>
                                </div>
                                @endif
                                <div class="col-sm-12">
                                    <a href="#" onclick="submitTempResetData()" class="btn btn-warning btn-sm float-end">Reset</a>
                                </div>
                            </div>
                            <div class="row g-1">
                                <hr>
                                <div class="col-sm-6">
                                    <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                    <div class="input-group mb-0">
                                        <input type="text" class="form-control form-control-sm mb-1" id="search_serv_key" placeholder="{{trans('navmenu.search_service')}}" autocomplete="off" aria-label="Search Service" aria-describedby="button-addon2">
                                        <a href="javascript:;" class="btn btn-outline-danger btn-sm mb-1 empty-serv-search" id="button-addon2"><i class='fa fa-close'></i></a>
                                    </div>
                                    <ul id="searchServiceResult" class="list-group"></ul>
                                </div>
                                <div class="col-md-12" style="display: block; overflow: auto;">
                                    <table id="discount_field" class="items mt-0" style="width: 100%;">
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.service')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            @if(Auth::user()->can('offer-discount'))
                                            <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                            @endif
                                            @if($settings->is_vat_registered)
                                            <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                            @endif
                                            <th>&nbsp;</th>
                                        </tr>
                                        <tr ng-repeat="newservsaletemp in servsaletempitems" id="temps">
                                            <td style="text-align: center;">@{{$index + 1}}</td>
                                            <td>@{{newservsaletemp.name}}</td>
                                            <td style="text-align: center;"><input type="number" style="text-align:center; width: 80px; height: 20px; border: 1px solid #e0e0e0;" autocomplete="off" name="no_of_repeatition" ng-blur="updateSaleTemp(newservsaletemp)" ng-model="newservsaletemp.no_of_repeatition" min="1" step="1" value="@{{newservsaletemp.no_of_repeatition}}"></td>
                                            <td style="text-align: center;">
                                            @if($settings->enable_cpos)
                                                <input type="number" style="text-align:center; width: 100px; height: 20px; border: 1px solid #e0e0e0;" name="price" ng-blur="updateSaleTemp(newservsaletemp)" ng-model="newservsaletemp.price" value="@{{newservsaletemp.price}}">
                                            @else
                                                @{{newservsaletemp.price | number:2}}
                                            @endif
                                            </td>
                                            <td style="text-align: center;">@{{(newservsaletemp.price * newservsaletemp.no_of_repeatition) | number:2}}</td>
                                            @if(Auth::user()->can('offer-discount'))
                                            <td style="text-align: center;"><input type="number" min="0" step="any" style="text-align: center; width: 100px; height: 20px; border: 1px solid #e0e0e0;" name="total_discount" ng-blur="updateSaleTemp(newservsaletemp)" string-to-number ng-model="newservsaletemp.total_discount"></td>
                                            @endif
                                            @if($settings->is_vat_registered)
                                            <td style="text-align: center;"><select ng-model="newservsaletemp.with_vat" name="sold_in" ng-change="updateSaleTemp(newservsaletemp)" style="border: 1px solid #e0e0e0;">
                                                <option value="no">{{trans('navmenu.no')}}</option>
                                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                            </select></td>
                                            <td style="text-align: center;">@{{newservsaletemp.vat_amount | number:2}}</td>
                                            @endif
                                            
                                            <td style="text-align: center;"><a href="#" ng-click="removeSaleTemp(newservsaletemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </table>

                                    <table id="discount_value" class="items mt-0" style="width: 100%; display: none;">
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.service')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            @if(Auth::user()->can('offer-discount'))
                                            <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                            @endif
                                            @if($settings->is_vat_registered)
                                            <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                            @endif
                                            <th>&nbsp;</th>
                                        </tr>
                                        <tr ng-repeat="newservsaletemp in servsaletempitems" id="temps">
                                            <td style="text-align: center;">@{{$index + 1}}</td>
                                            <td>@{{newservsaletemp.name}}</td>
                                            <td style="text-align: center;"><input type="number" style="text-align:center; width: 80px; height: 20px; border: 1px solid #e0e0e0;" autocomplete="off" name="no_of_repeatition" ng-blur="updateSaleTemp(newservsaletemp)" string-to-number ng-model="newservsaletemp.no_of_repeatition" min="1" step="1" value="@{{newservsaletemp.no_of_repeatition}}"></td>
                                            <td style="text-align: center;">
                                            @if($settings->enable_cpos)
                                                <input type="number" style="text-align:center; width: 100px; height: 20px; border: 1px solid #e0e0e0;" name="price" ng-blur="updateSaleTemp(newservsaletemp)" string-to-number ng-model="newservsaletemp.price" value="@{{newservsaletemp.price}}">
                                            @else
                                                @{{newservsaletemp.price | number:2}}
                                            @endif
                                            </td>
                                            <td style="text-align: center;">@{{ newservsaletemp.total | number:2}}</td>
                                            @if(Auth::user()->can('offer-discount'))
                                            <td style="text-align: center;">@{{newservsaletemp.total_discount | number:2}}</td>
                                            @endif
                                            @if($settings->is_vat_registered)
                                            <td style="text-align: center;"><select ng-model="newservsaletemp.with_vat" name="sold_in" ng-change="updateSaleTemp(newservsaletemp)" style="border: 1px solid #e0e0e0;">
                                                <option value="no">{{trans('navmenu.no')}}</option>
                                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                            </select></td>
                                            <td>@{{newservsaletemp.vat_amount | number:2}}</td>
                                            @endif
                                            <td style="text-align: center;"><a href="#" ng-click="removeSaleTemp(newservsaletemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row g-2 pt-3">
                                <div class="col-xl-6">
                                    <div class="row">
                                        <input type="hidden" id="no_items" name="no_items" value="@{{servsaletempitems.length}}" class="form-control form-control-sm mb-3">
                                        <div id="d-msg"></div>
                                        @if($settings->is_service_per_device)
                                        <div class="col-sm-6">
                                            <label class="form-label">Device/Property <span style="color: red;">*</span></label>
                                            <select name="device_id" id="device-id" class="form-select form-select-sm mb-1">
                                                <option value="">{{trans('navmenu.select_device')}}</option>
                                                @foreach($devices as $device)
                                                <option value="{{$device->id}}">{{$device->device_number}} - {{$device->device_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @if($settings->is_rental_service)
                                        <div class="col-sm-6">
                                            <label class="control-label">Rent End Date</label>
                                            <div class="inner-addon left-addon"> 
                                                <i class="myaddon fa fa-calendar"></i>
                                                <input type="text" name="rent_end_date" id="rent_end_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                            </div>
                                        </div>
                                        @endif
                                        @if($settings->enable_trip_logs)
                                        <div class="col-sm-12">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">TRIP</th>
                                                        <th colspan="2">MILEAGE</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>From <span style="color: red;">*</span></td>
                                                        <td>
                                                            <input type="text" name="from" class="form-control form-control-sm mb-1" placeholder="Trip From">
                                                        </td>
                                                        <td>Out <span style="color: red;">*</span></td>
                                                        <td>
                                                            <input type="number" step="any" name="mileage_out" class="form-control form-control-sm mb-1" placeholder="Mileage Out">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>To <span style="color: red;">*</span></td>
                                                        <td>
                                                            <input type="text" name="to" class="form-control form-control-sm mb-1" placeholder="Trip To">
                                                        </td>
                                                        <td>In <span style="color: red;">*</span></td>
                                                        <td>
                                                            <input type="number" min="0" step="any" name="mileage_in" class="form-control form-control-sm mb-1" placeholder="Mileage In">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                        @endif
                                        <div class="col-sm-12">
                                            <label for="employee" class="form-label">{{trans('navmenu.comments')}}</label>
                                            <input type="text" class="form-control form-control-sm mb-1" name="comments" id="comments">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <table class="table " style="width: 100%;">
                                        <tr>
                                            <th>{{trans('navmenu.subtotal')}}</th>
                                            <th style="text-align: right;"><b>@{{sum(servsaletempitems) | number:2}}</b></th>
                                        </tr>
                                        @if(Auth::user()->can('offer-discount'))
                                        <tr id="total_discount_field" style="display: none;">
                                            <th>{{trans('navmenu.total_discount')}}</th>
                                            <th><input type="number" style="text-align:center; width: 80px;" name="sale_discount" id="serv_discount" value="@{{sumDiscount(servsaletempitems)}}"></th>
                                        </tr>
                                        <tr id="total_discount_value">
                                            <th>{{trans('navmenu.discount')}}</th>
                                            <th style="text-align: right;"><b>@{{sumDiscount(servsaletempitems) | number:2}}</b></th>
                                        </tr>
                                        @endif
                                        @if($settings->is_vat_registered)
                                        <tr>
                                            <th>{{trans('navmenu.vat')}}</th>
                                            <th style="text-align: right;"><b>@{{sumVAT(servsaletempitems) | number:2}}</b></th>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th style="text-align: right;"><b>@{{(sum(servsaletempitems)-sumDiscount(servsaletempitems)+sumVAT(servsaletempitems)) | number:2}}</b></th>
                                        </tr>  
                                    </table>

                                    <div class="row">
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <input type="checkbox" id="print_receipt" name="print_receipt">
                                            <label for="print_receipt">Print</label>
                                        </div>
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <button type="submit" id="btn-submit-inv" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
                                        </div>
                                        
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif
                        <form id="delete-form" method="POST" action="{{ route('pos.destroy', encrypt($saletemp->id))}}" style="display: inline;">
                            @csrf
                            @method("DELETE")
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <form class="row g-3" method="POST" action="{{ url('reset-pos-temp') }}" id="ptemp-form-reset">
            @csrf
            <input type="hidden" name="id" value="{{$saletemp->id}}">
        </form>
    </div>
    <!--end row-->

    <!-- Modal -->
    <div class="modal animated zoomIn" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.new_customer')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{url('new-customer')}}">
                        @csrf
                        <div class="row g-1">
                            <div class="col-sm-6">
                                  <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
                                  <input type="text" name="name" required placeholder="{{ trans('navmenu.hnt_customer_name') }}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" placeholder="Please enter contact person" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">{{trans('navmenu.phone_number')}}</label>
                                  <input type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                  <label for="register-email" class="form-label">{{trans('navmenu.email_address')}}</label>
                                  <input id="register-email" type="text" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                                <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label for="address" class="form-label">Category</label>
                                <select name="customer_category_id" class="form-select form-select-sm mb-1">
                                    <option>--Select--</option>
                                    @foreach($categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->cat_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">{{trans('navmenu.tin')}}</label>
                                  <input type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-1"  data-inputmask='"mask": "999-999-999"' data-mask>
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">{{trans('navmenu.vrn')}}</label>
                                  <input type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                                <select class="form-select form-select-sm mb-1" name="cust_id_type">
                                    @foreach($custids as $cid)
                                    @if($cid['id'] == 6)
                                    <option value="{{$cid['id']}}" selected>{{$cid['name']}}</option>
                                    @else
                                    <option value="{{$cid['id']}}">{{$cid['name']}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{trans('navmenu.id_number')}}</label>
                                <input type="text" name="custid" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                            </div>         
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#search_serv_key').on('keyup',function (e) {
                // e.preventDefault();
                var query = $('#search_serv_key').val();
                $.ajax({
                    url:"{{ url('search-service') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchServiceResult").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#searchServiceResult").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'>"+name+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                        }

                        // binding click event to li
                        $("#searchServiceResult li").bind("click",function(){
                            addServSaleTemp(this);
                        });

                    }
                })
            });

            $('.empty-serv-search').on('click', function(){
                $("#search_serv_key").val('');
                $("#searchServiceResult").empty();
            });

            $('#serv_discount').on('blur', function(){
                var discount = $('#serv_discount').val();
                angular.element(document.getElementById('mycontroller')).scope().updateSaleTempDiscount(discount);
            });
            

            $('#search_customer_key').on('keyup',function () {
                var query = $(this).val();
                $.ajax({
                    url:"{{ url('search-customer') }}",
                    type:'GET',
                    data:{'search_customer_key':query},
                    success:function (response) {
                        var len = response.length;
                        $("#searchResult2").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#searchResult2").append("<li value='"+id+"'>"+name+"</li>");
                        }

                        // binding click event to li
                        $("#searchResult2 li").bind("click",function(){
                            setSelectedCustomer(this);
                        });
                    }
                })
            });

            $('#btn-refresh').on('click', function() {
                angular.element(document.getElementById('mycontroller')).scope().getData();
                $('#finish-inv').show();
            });

            $('#btn-submit-inv').on('click', function(e) {
                e.preventDefault();                
                var isPerDevice = "<?php echo $settings->is_service_per_device; ?>";
                var device = $('#device-id').val();
                var isRental = "<?php echo $settings->is_rental_service; ?>";
                var redate = $('#rent_end_date').val();
                var utransactions = <?php echo $utransactions; ?>;
                var items = document.getElementById('no_items').value;
                if (items == 0) {
                    // alert('Please select at least one item to continue.');
                    Swal.fire(
                      'Nothing To Submit!',
                      'Please select at least one item to continue.',
                      'info'
                    )
                    return false;
                }else if(isPerDevice && device == '') {
                    $('#d-msg').append('<div class="alert alert-danger hideit alertSuc">Please select Device/Property</div >');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1300);
                }else if (isRental  && redate == '') {
                    $('#d-msg').append('<div class="alert alert-danger hideit alertSuc">Please select Rent End Date</div >');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1300);
                }else if(utransactions > 0) {
                    Swal.fire({
                      title: "The customer has a balance in previous Payments",
                      text: "Would you like to pay this Invoice for that payment?",
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonColor: '#3085d6',
                      cancelButtonColor: '#d33',
                      confirmButtonText: "Yes, Use It",
                      cancelButtonText: "{{trans('navmenu.no')}}"
                    }).then((result) => {
                      if (result.value) {
                        document.getElementById('use_pre_payment').value = 1;
                        document.getElementById('pos-form').submit();
                        $(this).prop('disabled', true);
                        $(this).val("Please wait...");
                      }else{
                        document.getElementById('use_pre_payment').value = 0;
                        document.getElementById('pos-form').submit();
                        $(this).prop('disabled', true);
                        $(this).val("Please wait...");
                      }
                    })
                    return false;
                }else{
                    $('#pos-form').submit();
                    $(this).prop('disabled', true);
                    $(this).val("Please wait...");
                }
            })
        });


        function setSelectedCustomer(element) {
            var value = $(element).text();
            var custId = $(element).val();
            $('#cust-id').val(custId); 
            $("#search_customer_key").val(value);
            $("#searchResult2").empty();
        }

        function addServSaleTemp(element) {
            var value = $(element).text();
            var serv_id = $(element).val();
            angular.element(document.getElementById('mycontroller')).scope().addSaleTemp(serv_id);
            setTimeout(function(){
                $("#search_serv_key").val('');
                $("#searchServiceResult").empty();
            })
        }
    </script>   


<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="sale_date"]');

            var mind = "<?php echo $settings->sp_mindays; ?>";
            var d = new Date();
            d.setDate(d.getDate() - mind);
            $min.DatePickerX.init({
                mondayFirst: true,
                minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            var iscred = "<?php echo $saletemp->sale_type; ?>";
            if (iscred == 'credit') {
                var $max = document.querySelector('[name="due_date"]');
                $max.DatePickerX.init({
                    mondayFirst: true,
                    format     : 'yyyy-mm-dd',
                    minDate    : new Date(),
                    // maxDate    : new Date()
                });
            }

            var paytype = "<?php echo $saletemp->pay_type; ?>";
            if(paytype == 'Cheque'){
                var $exp = document.querySelector('[name="expire_date"]');
                $exp.DatePickerX.init({
                    mondayFirst: true,
                    format     : 'yyyy-mm-dd',
                    minDate    : new Date(),
                    // maxDate    : new Date()
                });
            }

            var isrental = "<?php echo $settings->is_rental_service; ?>";
            if (isrental) {
                $red.DatePickerX.init({
                    mondayFirst: true,
                    format     : 'yyyy-mm-dd',
                    // minDate    : new Date(),
                });
            }
        });
    </script>