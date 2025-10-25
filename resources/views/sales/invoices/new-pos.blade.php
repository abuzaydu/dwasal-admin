@extends('layouts.app')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('js/pos-new.js') }}"></script>
    <script>
        function validateform(form) {
            var items = document.saleform.no_items.value;
            if (items == 0) {
                // alert('Please select at least one item to continue.');
                Swal.fire(
                  'Nothing To Submit!',
                  'Please select at least one item to continue.',
                  'info'
                )
                return false;
            }

            var utransactions = <?php echo $utransactions; ?>;
            if (utransactions > 0) {
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
                  }else{
                    document.getElementById('use_pre_payment').value = 0;
                    document.getElementById('pos-form').submit();
                  }
                })
                return false;
            }

            form.myButton.disabled = true;
            form.myButton.value = "Please wait...";
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

        function wegInvoiceType(elem) {
          var x = document.getElementById("ref-customer");
          if(elem.value == 'Yes') {
            x.style.display = "block";
          } else {
            x.value = '';
            x.style.display = "none";
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
            var canaddpay = "<?php Auth::user()->can('create-sale-payment'); ?>"
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
                d.style.display = "block";
                if (isfill == 1) {
                    vehc.style.display = "block";
                }
                if (canaddpay) {
                    bk.style.display = "none";
                    mo.style.display = "none";
                    ca.style.display = "none"
                }
            }else if (elem.value === 'cash') {
                pm.style.display = "block";
                if (canaddpay) {
                    ca.style.display = 'block';
                }
                d.style.display = "none";
            }else{
                pm.style.display = "none";
                if (canaddpay) {
                    ca.style.display = 'none'
                }
                d.style.display = "none";
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

        function detailUpdate(elem) {
            var b = document.getElementById('bank-name');
            var m = document.getElementById('mobaccount');
            var ca = document.getElementById('cashaccount');

            var dpm = document.getElementById('deposit_mode');
            var chq = document.getElementById('cheque');
            var slip = document.getElementById('slip');
            var expire = document.getElementById('expire');
            if (elem.value === 'Bank' || elem.value === 'Cheque') {
                b.style.display = 'block';
                m.style.display = 'none';
                ca.style.display = 'none';
                if (elem.value === 'Bank') {
                    dpm.style.display = "block";
                    slip.style.display = 'block'
                    chq.style.display = 'none';
                    expire.style.display = "none";
                }else{
                    dpm.style.display = 'none';
                    slip.style.display = "none";
                    chq.style.display = "block";
                    expire.style.display = "block";
                }
            }else if (elem.value === 'Mobile Money') {
                ca.style.display = 'none';
                b.style.display = 'none';
                m.style.display = 'block';
                dpm.style.display = 'none';
                chq.style.display = 'none';
                slip.style.display = 'none';
                expire.style.display = 'none';
            }else{
                ca.style.display = 'block';
                b.style.display = 'none';
                m.style.display = 'none';
                dpm.style.display = 'none';
                chq.style.display = 'none';
                slip.style.display = 'none';
                expire.style.display = 'none';
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
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-1">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
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
        @if(!$saletemp->is_from_so)
        <div class="col-xl-12 mx-auto">
        @else
        <div class="col-xl-10 mx-auto">
        @endif    
            <!-- <h6 class="mb-0 text-uppercase">{{trans('navmenu.sale_items')}}</h6>
            <hr> -->
            <div class="card">
                <div class="card-body">
                    <div class="row mb-1">
                        <div class="col-sm-6 d-lg-flex align-items-center mb-1 gap-1">
                            @if(!$saletemp->is_from_so && is_null($saletemp->customer_id))
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#customerModal"><i class="bx bx-user-plus"></i>{{trans('navmenu.new_customer')}}</button>
                            @if($settings->is_agent)
                            <a href="{{url('ocamounts')}}" class="btn btn-primary pull-right" style="margin-left: 5px;"><i class="fa fa-file-o"></i>{{trans('navmenu.new_oc_amount')}}</a>
                            @endif
                            @endif
                        </div>
                        <div class="btn-group col-sm-6" role="group">
                            <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingtemps->count()}}</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Pending Bills/Invoices <i class="bx bx-caret-down"></i></button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                                @foreach($pendingtemps as $key => $temp) 
                                <form class="row g-3" method="POST" action="{{ url('pt-pos') }}" id="ptemp-form-{{$key}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$temp->id}}">
                                    <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">{{$temp->name}} (<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                                </form>  
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if(!is_null($saletemp->customer_id) && !is_null($saletemp->sale_type))
                    <div class="row mb-1">
                        <form id="search-form" action="#">
                            <div class="col-sm-8">
                                <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                <div class="input-group mb-0">
                                    <input type="text" class="form-control" id="search_key" placeholder="{{trans('navmenu.search_product')}}" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2"><i class='bx bx-search'></i> Search</button>
                                    <button class="btn btn-outline-danger empty-search" type="submit" id="button-addon2"><i class='bx bx-x'></i> Clear</button>
                                </div>
                                <ul id="searchResult3" class="list-group"></ul>
                            </div>
                        </form>
                    </div>
                    @endif
                    @if ($message = Session::get('error'))
                    <div class="row mb-1">
                        <div class="alert alert-danger alert-block">
                          <button type="button" class="close" data-dismiss="alert">×</button> 
                          <strong>{{ $message }}</strong>
                        </div>
                    </div>
                    @endif
                    <div class="p-3 border rounded" style="overflow: auto; min-height: 350px;">
                        @if(is_null($saletemp->customer_id) || is_null($saletemp->sale_type))
                        <form class="row g-1" method="POST" action="{{ url('pos-temp') }}" id="ptemp-form-on">
                            @csrf
                            <input type="hidden" name="sale_temp_id" value="{{$saletemp->id}}">
                            <input type="hidden" name="customer_id" id="cust-id" value="{{$custid}}" class="form-control form-control-sm mb-1">
                            <div class="row g-1">
                                <div class="col-sm-12" id="ermsg"></div>
                                <div class="col-sm-4">
                                    <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                    <input id="search_customer_key" placeholder="Search customer" value="{{$custname}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                    <ul id="searchResult2"></ul>
                                </div>
                                <div class="col-sm-2">
                                    <label for="invoice" class="form-label">{{trans('navmenu.saledate')}}</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon bx bx-calendar"></i>
                                        <input type="text" name="sale_date" value="{{$saletemp->sale_date}}" id="sale_date" ng-model="saletemp.sale_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
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
                                <div class="col-sm-2" id="duedate" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.due_date')}} <span style="color: red;">*</span></label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon bx bx-calendar"></i>
                                        <input type="text" name="due_date" ng-model="saletemp.due_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-2" id="paymode">
                                    <label for="payment_type" class="form-label">{{trans('navmenu.pay_method')}}</label>
                                    <select class="form-select form-select-sm mb-1" name="pay_type" ng-model="saletemp.pay_type" ng-change="updateSaleTempInfo(saletemp)" onchange="detailUpdate(this)" required>
                                        <option value="Cash" selected>{{trans('navmenu.cash')}}</option>
                                        <option value="Bank">{{trans('navmenu.bank')}}</option>
                                        <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                    </select>
                                </div>
                                @if(Auth::user()->can('create-sale-payment'))
                                <div class="col-sm-2" id="cashaccount">
                                    <label class="form-label">Cash Account </label>
                                    <select class="form-select form-select-sm mb-1" name="cash_acc_id">                  
                                        @foreach($accounts->where('type', 'Cash') as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2" id="deposit_mode" style="display: none;">
                                    <label class="form-label">Deposit Mode</label>
                                    <select name="deposit_mode" class="form-select form-select-sm mb-1">
                                        <option>Direct Deposit</option>
                                        <option>Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="col-sm-2" id="bank-name" style="display: none;">
                                    <label class="form-label">Bank Account </label>
                                    <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                        @foreach($accounts->where('type', 'Bank') as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2" id="cheque" style="display: none;">
                                    <label class="form-label">Cheque Number</label>
                                    <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-2" id="expire" style="display: none;">
                                    <label class="form-label">Expire Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon bx bx-calendar"></i>
                                        <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-2" id="slip" style="display: none;">
                                    <label class="form-label">Reference Number</label>
                                    <input id="name" type="text" name="slip_no" placeholder="Enter Slip number" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3" id="mobaccount" style="display: none;">
                                    <label class="form-label">Mobile Money Account </label>
                                    <select class="form-select form-select-sm mb-1" name="mob_acc_id">
                                        @foreach($accounts->where('type', 'Mobile Money') as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($settings->allow_multi_currency)
                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.currency')}}</label>
                                    <select name="currency" id="currency" class="form-select form-select-sm mb-1" ng-model="saletemp.currency" ng-change="updateSaleTempInfo(saletemp)" ng-options="curr.code as curr.code for curr in currencies" required>
                                    </select>
                                </div>
                                <div class="col-sm-2" ng-if="saletemp.currency != saletemp.defcurr">
                                    <label class="form-label">Exchange Rate Mode</label>
                                    <select name="ex_rate_mode"  class="form-select form-select-sm mb-1" ng-model="saletemp.ex_rate_mode">
                                        <option value="Locale" selected>1 @{{saletemp.defcurr}} Equals ? @{{saletemp.currency}}</option>
                                        <option value="Foreign">1 @{{saletemp.currency}} Equals ? @{{saletemp.defcurr}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3" ng-if="saletemp.currency != saletemp.defcurr && saletemp.ex_rate_mode == 'Locale'">
                                    <label class="form-label">Rate Amount in @{{saletemp.currency}}</label>
                                    <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-1" string-to-number ng-model="saletemp.foreign_ex_rate" ng-blur="updateSaleTempInfo(saletemp)">
                                </div>
                                <div class="col-sm-3" ng-if="saletemp.currency != saletemp.defcurr && saletemp.ex_rate_mode == 'Foreign'">
                                    <label class="form-label">Rate Amount in @{{saletemp.defcurr}}</label>
                                    <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-1" string-to-number ng-model="saletemp.local_ex_rate" ng-blur="updateSaleTempInfo(saletemp)">
                                </div>
                                @endif
                                @endif
                                <div class="col-sm-2">
                                    <label for="invoice" class="form-label">Is Property Invoice</label>
                                    <select name="is_property_invoice" id="property" ng-model="saletemp.is_property_invoice" ng-change="updateSaleTempInfo(saletemp)" onchange="wegInvoiceType(this)" class="form-select form-select-sm mb-1">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                @if($saletemp->is_property_invoice == 'Yes')
                                <div class="col-sm-4" id="ref-customer">
                                    <label class="form-label">Ref. Customer</label>
                                    <input type="text" name="ref_customer" id="ref_customer" ng-model="saletemp.ref_customer" ng-blur="updateSaleTempInfo(saletemp)" placeholder="Enter Ref Customer name" class="form-control form-control-sm mb-1">
                                </div>
                                @else
                                <div class="col-sm-4" id="ref-customer" style="display: none;">
                                    <label class="form-label">Ref. Customer</label>
                                    <input type="text" name="ref_customer" id="ref_customer" ng-model="saletemp.ref_customer" ng-blur="updateSaleTempInfo(saletemp)" placeholder="Enter Ref Customer name" class="form-control form-control-sm mb-1">
                                </div>
                                @endif
                                <div class="col-sm-2 pt-4">
                                    <a href="javascript:;" onclick="submitTempData()" class="btn btn-primary btn-sm">Next </a>
                                </div>
                            </div>
                        </form>
                        @else
                        <form class="form" id="pos-form" name="saleform" method="POST" action="{{ route('pos.store') }}" onsubmit="return validateform(this)" ng-if="saletemp">
                            @csrf
                            <input type="hidden" name="sale_temp_id" placeholder="" value="{{$saletemp->id}}" class="form-control form-control-sm mb-1">
                            <input type="hidden" id="use_pre_payment" name="use_pre_payment" value="0">
                            <div class="row g-1">
                                <div class="col-sm-3">
                                    <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                    <select name="customer_id" required class="form-select form-select-sm mb-1">
                                        @if(!is_null($customer))
                                        <option value="{{$customer->id}}" selected>{{$customer->name}}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label for="invoice" class="form-label">{{trans('navmenu.saledate')}}</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon bx bx-calendar"></i>
                                        <input type="text" name="sale_date" id="sale_date" value="{{$saletemp->sale_date}}" ng-model="saletemp.sale_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.sales_type')}} <span style="color: red;">*</span></label>
                                    <select name="sale_type" id="sale_type" onchange="wegSaleType(this)" class="form-select form-select-sm mb-1" required>
                                        @if($saletemp->sale_type == 'cash')
                                        <option value="cash">{{trans('navmenu.cash_sales')}}</option>
                                        @else
                                        <option value="credit">{{trans('navmenu.credit_sales')}}</option>
                                        @endif
                                    </select>
                                </div>
                                @if($saletemp->sale_type == 'credit')
                                <div class="col-sm-2" id="duedate">
                                    <label for="total" class="form-label">{{trans('navmenu.due_date')}} </label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon bx bx-calendar"></i>
                                        <input type="text" name="due_date" ng-model="saletemp.due_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                @endif
                                <div class="col-sm-2" id="paymode">
                                    <label for="payment_type" class="form-label">{{trans('navmenu.pay_method')}}</label>
                                    <select class="form-select form-select-sm mb-1" name="pay_type" required>
                                        @if($saletemp->pay_type == 'Cash')
                                        <option value="Cash" selected>{{trans('navmenu.cash')}}</option>
                                        @elseif($saletemp->pay_type == 'Bank')
                                        <option value="Bank">{{trans('navmenu.bank')}}</option>
                                        @else
                                        <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                        @endif
                                    </select>
                                </div>

                                @if($saletemp->sale_type == 'cash')
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
                                <div class="col-sm-2" id="bank-name">
                                    <label class="form-label">Bank Account </label>
                                    <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                        @foreach($accounts->where('type', 'Bank') as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2" id="cheque" style="display: none;">
                                    <label class="form-label">Cheque Number</label>
                                    <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-2" id="expire" style="display: none;">
                                    <label class="form-label">Expire Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon bx bx-calendar"></i>
                                        <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-2" id="slip">
                                    <label class="form-label">Reference Number</label>
                                    <input id="name" type="text" name="slip_no" placeholder="Enter Slip number" class="form-control form-control-sm mb-1">
                                </div>
                                @else
                                <div class="col-sm-3" id="mobaccount" style="display: none;">
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
                                    <label class="form-label">Exchange Rate Mode</label>
                                    <select name="ex_rate_mode"  class="form-select form-select-sm mb-1" ng-model="saletemp.ex_rate_mode">
                                        <option value="Locale" selected>1 @{{saletemp.defcurr}} Equals ? @{{saletemp.currency}}</option>
                                        <option value="Foreign">1 @{{saletemp.currency}} Equals ? @{{saletemp.defcurr}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3" ng-if="saletemp.currency != saletemp.defcurr && saletemp.ex_rate_mode == 'Locale'">
                                    <label class="form-label">Rate Amount in @{{saletemp.currency}}</label>
                                    <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-1" string-to-number ng-model="saletemp.foreign_ex_rate" ng-blur="updateSaleTempInfo(saletemp)">
                                </div>
                                <div class="col-sm-3" ng-if="saletemp.currency != saletemp.defcurr && saletemp.ex_rate_mode == 'Foreign'">
                                    <label class="form-label">Rate Amount in @{{saletemp.defcurr}}</label>
                                    <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-1" string-to-number ng-model="saletemp.local_ex_rate" ng-blur="updateSaleTempInfo(saletemp)">
                                </div>
                                @endif
                                @endif
                                @endif
                                <div class="col-sm-2">
                                    <label for="invoice" class="form-label">Is Property Invoice</label>
                                    <select name="is_property_invoice" id="property" ng-model="saletemp.is_property_invoice" ng-change="updateSaleTempInfo(saletemp)" onchange="wegInvoiceType(this)" class="form-select form-select-sm mb-1">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </div>
                                @if($saletemp->is_property_invoice == 'Yes')
                                <div class="col-sm-4" id="ref-customer">
                                    <label class="form-label">Ref. Customer</label>
                                    <input type="text" name="ref_customer" id="ref_customer" ng-model="saletemp.ref_customer" ng-blur="updateSaleTempInfo(saletemp)" placeholder="Enter Ref Customer name" class="form-control form-control-sm mb-1">
                                </div>
                                @else
                                <div class="col-sm-4" id="ref-customer" style="display: none;">
                                    <label class="form-label">Ref. Customer</label>
                                    <input type="text" name="ref_customer" id="ref_customer" ng-model="saletemp.ref_customer" ng-blur="updateSaleTempInfo(saletemp)" placeholder="Enter Ref Customer name" class="form-control form-control-sm mb-1">
                                </div>
                                @endif
                                <div class="col-sm-2 pt-4">
                                    <a href="#" onclick="submitTempResetData()" class="btn btn-warning btn-sm">Reset</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-sm-12">
                                    <table id="discount_field" class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                        <tr>
                                            <th>#</th>
                                            <th style="text-align: center;">{{trans('navmenu.product_code')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.item_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit')}}</th>
                                            @if($settings->retail_with_wholesale)
                                            <th style="text-align: center;">{{trans('navmenu.sold_in')}}</th>
                                            @endif
                                            <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                            @if($settings->allow_unit_discount)
                                            <th style="text-align: center;">{{trans('navmenu.unit_discount')}}</th>
                                            @endif
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            @if($settings->discount_by_percent)
                                            <th style="text-align: center;">{{trans('navmenu.discount')}} (%)</th>
                                            @endif
                                            <!-- <th style="text-align: center;">{{trans('navmenu.discount')}}</th> -->
                                            @if($settings->is_vat_registered)
                                            <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                            <!-- <th style="text-align: center;">{{trans('navmenu.vat')}}</th> -->
                                            @endif
                                            <th>&nbsp;</th>
                                        </tr>
                                        <tr ng-repeat="newsaletemp in saletempitems" id="temps">
                                            <td>@{{$index + 1}}</td>
                                            <td>@{{newsaletemp.product_code}}</td>
                                            <td>@{{newsaletemp.name}}</td>
                                            <td><input type="number" style="text-align:center; width: 80px;" autocomplete="off" name="quantity_sold" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.quantity_sold" min="0" step="any"></td>
                                            <td>
                                                <select ng-model="newsaletemp.product_unit_id" name="product_unit_id" ng-change="updateSaleTemp(newsaletemp)" ng-options="unit.id as unit.unit_name for unit in newsaletemp.units">
                                                    
                                                </select>
                                            </td>
                                            @if($settings->retail_with_wholesale)
                                            <td><select ng-model="newsaletemp.sold_in" name="sold_in" ng-change="updateSaleTemp(newsaletemp)" class="form-select form-select-sm mb-1" style="border: 1px solid #e0e0e0;">
                                                <option value="Retail Price">{{trans('navmenu.retail_price')}}</option>
                                                <option value="Wholesale Price">{{trans('navmenu.wholesaleprice')}}</option>
                                            </select></td>
                                            @endif
                                            <td>
                                            @if($settings->enable_cpos)
                                                <input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="retail_price" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.retail_price">
                                            @else
                                                @{{newsaletemp.retail_price | number:2}}
                                            @endif
                                            </td>
                                            @if($settings->allow_unit_discount)
                                            <td><input type="number" min="0" step="any" style="text-align:center; width: 60px;" name="discount" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.discount"></td>
                                            @endif
                                            <td style="text-align: center;">@{{(newsaletemp.retail_price * newsaletemp.quantity_sold) | number:2}}</td>
                                            @if($settings->discount_by_percent)
                                            <td><input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="disc_percent" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.disc_percent"></td>
                                            <!-- <td style="text-align: center;">@{{newsaletemp.total_discount | number:2}}</td> -->
                                            @else
                                            <!-- <td><input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="total_discount" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.total_discount"></td> -->
                                            @endif
                                            @if($settings->is_vat_registered)
                                            <td><select ng-model="newsaletemp.with_vat" name="with_vat" ng-change="updateSaleTemp(newsaletemp)" style="border: 1px solid #e0e0e0;">
                                                <option value="no">{{trans('navmenu.no')}}</option>
                                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                            </select></td>
                                            <!-- <td>@{{newsaletemp.vat_amount | number:2}}</td> -->
                                            @endif
                                    
                                            <td><a href="#" ng-click="removeSaleTemp(newsaletemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </table>

                                    <table id="discount_value" class="table table-responsive table-striped display nowrap" style="width: 100%; display: none; overflow: scroll; overflow: auto; ">
                                        <tr>
                                            <th>#</th>
                                            <th style="text-align: center;">{{trans('navmenu.product_code')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.item_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit')}}</th>
                                            @if($settings->retail_with_wholesale)
                                            <th style="text-align: center;">{{trans('navmenu.sold_in')}}</th>
                                            @endif
                                            <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                            @if($settings->is_vat_registered)
                                            <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                            <!-- <th style="text-align: center;">{{trans('navmenu.vat')}}</th> -->
                                            @endif
                                            <th>&nbsp;</th>
                                        </tr>
                                        <tr ng-repeat="newsaletemp in saletempitems" id="temps">
                                            <td>@{{$index + 1}}</td>
                                            <td>@{{newsaletemp.product_code}}</td>
                                            <td>@{{newsaletemp.name}}</td>
                                            <td><input type="number" style="text-align:center; width: 80px;" autocomplete="off" name="quantity_sold" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.quantity_sold" min="0" step="any"></td>
                                            <td>
                                                <select ng-model="newsaletemp.product_unit_id" name="product_unit_id" ng-change="updateSaleTemp(newsaletemp)" ng-options="unit.id as unit.unit_name for unit in newsaletemp.units">
                                                    
                                                </select>
                                            </td>
                                            @if($settings->retail_with_wholesale)
                                            <td><select ng-model="newsaletemp.sold_in" name="sold_in" ng-change="updateSaleTemp(newsaletemp)" style="border: 1px solid #e0e0e0;">
                                                <option value="Retail Price">{{trans('navmenu.retail_price')}}</option>
                                                <option value="Wholesale Price">{{trans('navmenu.wholesaleprice')}}</option>
                                            </select></td>
                                            @endif
                                            <td>
                                            @if($settings->enable_cpos)
                                                <input type="number" min="0" step="any" style="text-align:center; height: 20px; width: 240px; border: 1px solid #e0e0e0;" name="retail_price" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.retail_price">
                                            @else
                                                @{{newsaletemp.retail_price | number:2}}
                                            @endif
                                            </td>
                                            <td>@{{(newsaletemp.retail_price * newsaletemp.quantity_sold) | number:2}}</td>
                                            <td>@{{newsaletemp.total_discount | number:2}}</td>
                                            @if($settings->is_vat_registered)
                                            <td><select ng-model="newsaletemp.with_vat" name="sold_in" ng-change="updateSaleTemp(newsaletemp)" style="border: 1px solid #e0e0e0;">
                                                <option value="no">{{trans('navmenu.no')}}</option>
                                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                            </select></td>
                                            <!-- <td>@{{newsaletemp.vat_amount | number:2}}</td> -->
                                            @endif
                                            <td><a href="#" ng-click="removeSaleTemp(newsaletemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-sm-12">
                                    <a href="javascript:;" id="btn-refresh" class="btn btn-outline-primary btn-sm float-end"><i class="bx bx-refresh"></i> Update</a>
                                </div>
                                <hr>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="row">
                                        <input type="hidden" id="no_items" name="no_items" value="@{{saletempitems.length}}" class="form-control form-control-sm mb-1">
                                        <div class="col-sm-12" id="vehcleno" style="display: none;">     
                                            <label for="total" class="form-label">{{trans('navmenu.vehicle_no')}}</label>
                                            <input type="text" class="form-control form-control-sm mb-1" id="vehicle_no" placeholder="{{trans('navmenu.vehicle_no')}}" name="vehicle_no"/>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="employee" class="form-label">{{trans('navmenu.comments')}}</label>
                                            <textarea  class="form-control form-control-sm mb-1" name="comments" ng-model="saletemp.comments" id="comments" ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <table class="table " style="width: 100%;">
                                        <tr>
                                            <th>{{trans('navmenu.subtotal')}}</th>
                                            <th style="text-align: right;"><b>@{{sum(saletempitems) | number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.total_discount')}}</th>
                                            <th id="total_discount_field" style="display: none;"><input type="number" style="text-align:center; width: 100px; height: 20px;" name="sale_discount" id="sale_discount" value="@{{ sumDiscount(saletempitems) }}"></th>
                                            <th id="total_discount_value" style="text-align: right;"><b>@{{sumDiscount(saletempitems) | number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.vat')}}</th>
                                            <th style="text-align: right;"><b>@{{sumVAT(saletempitems)| number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th style="text-align: right;"><b>@{{(sum(saletempitems)-sumDiscount(saletempitems)+sumVAT(saletempitems)) | number:2}}</b></th>
                                        </tr>  
                                        <tr>
                                            <th>{{trans('navmenu.currency')}}</th>
                                            <th style="text-align: right;"><b>@{{saletemp.currency}}</b></th>
                                        </tr> 
                                    </table>

                                    <div class="row">
                                        <!-- <div class="col-sm-4" style="margin-top: 5px;">
                                            <input type="checkbox" id="print_receipt" name="print_receipt">
                                            <label for="print_receipt">Print</label>
                                        </div> -->
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <button type="submit" name="myButton" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
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
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.new_customer')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{url('new-customer')}}">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                              <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
                              <input type="text" name="name" required placeholder="{{ trans('navmenu.hnt_customer_name') }}" class="form-control form-control-sm mb-1">
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
                            <label for="address" class="form-label">{{trans('navmenu.postal_address')}}</label>
                            <input id="address" type="text" name="postal_address" placeholder="{{trans('navmenu.hnt_postal_address')}}" class="form-control form-control-sm mb-1">
                        </div>

                        <div class="col-sm-6">
                            <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                            <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control form-control-sm mb-1">
                        </div>

                        <div class="col-sm-6">
                            <label for="address" class="form-label">{{trans('navmenu.street')}}</label>
                            <input id="address" type="text" name="street" placeholder="{{trans('navmenu.hnt_street')}}" class="form-control form-control-sm mb-1">
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
                            <select class="form-select" name="cust_id_type">
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="damageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">�</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                    {{trans('navmenu.new_depth_measure')}} </h4>
                </div>
            <form class="form-validate" method="POST" action="{{url('damages')}}">
                <div class="modal-body row g-1">
                    @csrf
                    <div class="col-sm-6">
                        <label class="form-label">{{trans('navmenu.product_name')}}</label>
                        <select name="product_id" class="form-select form-select-sm mb-1" required>
                            <option value="">{{trans('navmenu.select_product')}}</option>
                            @if(!is_null($products))
                            @foreach($products as $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{{trans('navmenu.depth_measure')}}<span style="color: red;"> *</span></label>
                        <input id="deph_measure" type="number" step="any" name="deph_measure" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{{trans('navmenu.date')}}</label>
                        <select onchange="wegDam(this)" class="form-select form-select-sm mb-1">
                            <option value="auto">Auto</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    <div class="col-sm-6" id="dam_date_field" style="display: none;">
                        <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                        <div class="inner-addon left-addon"> 
                            <i class="myaddon bx bx-calendar"></i>
                            <input type="text" name="dam_date" id="dam_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#search-form').on('submit', function(e){
                e.preventDefault();
            // })
            // $('#search_key').on('keyup',function () {
                var query = $('#search_key').val();
                if (query.length >= 3) {
                    $.ajax({
                        url:"{{ url('search-product') }}",
                        type:'GET',
                        data:{'search_key':query},
                        success:function (response) {
                            // $('#product_list').html(data);
                            var len = response.length;
                            $("#searchResult3").empty();
                            for( var i = 0; i<len; i++){
                                var id = response[i]['id'];
                                var name = response[i]['product_code']+' - '+response[i]['name'];
                                var qty = +response[i]['in_stock'];
                                if (qty > 0) {
                                    $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-8'>"+name+"</div><div class='col-sm-3'><span style='color: blue;'>("+(qty+0)+")</span></div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='bx bx-redo' aria-hidden='true'></span></span></div></li>");
                                }else{
                                    $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-8'>"+name+"</div><div class='col-sm-3'><span style='color: red;'>("+(qty+0)+")</span></div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='bx bx-redo' aria-hidden='true'></span></span></div></li>");
                                }
                            }

                            // binding click event to li
                            $("#searchResult3 li").bind("click",function(){
                                addSaleTemp(this);
                                // $("#searchResult3").empty();
                            });

                        }
                    })
                }else{
                    $("#searchResult3").empty();
                }
            });

            $('.empty-search').on('click', function(){
                $("#search_key").val('');
                $("#searchResult3").empty();
            });

            $('#sale_discount').on('blur', function(){
                var discount = $('#sale_discount').val();
                angular.element(document.getElementById('mycontroller')).scope().updateSaleTempDiscount(discount);
            })

            // $('#customer_id').on('change', function(){
            //     var selection = $('#customer_id').val();
            //     var customer_id = selection.replace('number:', '');
            //     angular.element(document.getElementById('mycontroller')).scope().updateSaleCustomerID(customer_id);
            // });

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
        });

        function setSelectedCustomer(element) {
            var value = $(element).text();
            var custId = $(element).val();
            $('#cust-id').val(custId); 
            $("#search_customer_key").val(value);
            $("#searchResult2").empty();
        }

        function addSaleTemp(element) {
            var value = $(element).text();
            var productid = $(element).val();
            $.ajax({
                url:"{{ url('fetch-product') }}",
                type:'GET',
                data:{'product_id':productid},
                success:function (response) {
                    var item = response;
                    angular.element(document.getElementById('mycontroller')).scope().addSaleTemp(item);
                    setTimeout(function(){
                        $("#search_key").val('');
                        $("#searchResult3").empty();
                    }, 2000);
                }
            })   
        }
    </script>   

<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="sale_date"]');
            var $max = document.querySelector('[name="due_date"]');
            var $dam = document.querySelector('[name="dam_date"]');

            var mind = "<?php echo $settings->sp_mindays; ?>";
            var d = new Date();
            d.setDate(d.getDate() - mind);
            $min.DatePickerX.init({
                mondayFirst: true,
                minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

            $dam.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

            var csp = "<?php echo Auth::user()->can('create-sale-payment'); ?>";
            if(csp){
                var $exp = document.querySelector('[name="expire_date"]');
                $exp.DatePickerX.init({
                    mondayFirst: true,
                    format     : 'yyyy-mm-dd',
                    minDate    : new Date(),
                    // maxDate    : new Date()
                });
            }
        });
    </script>