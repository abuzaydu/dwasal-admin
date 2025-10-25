@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/pmpurchase.js') }}"></script>
<script> 
    function validateform(form) {
        var items = document.pmitemform.no_items.value;
        if (items == 0) {
            // alert('Please select at least one item to continue.');
            Swal.fire(
              'Nothing To Submit!',
              'Please select at least one item to continue.',
              'info'
            )
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
            window.location.href="{{url('cancel-pmitem')}}";
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }



    function weg(elem) {
      var x = document.getElementById("pmitem_date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#purchase_date").val('');
      }
    }

    function wegPurchaseType(elem) {
        // var c = document.getElementById('paid-field');
        // var ad = document.getElementById('amount_due');
        var acc = document.getElementById('account');
        var du =  document.getElementById('due_date_field');

        var sbscr = "<?php echo $shop->subscription_type_id; ?>";
        if (sbscr == 3 || sbscr == 4) {
            var or = document.getElementById('order_no');
            var dn = document.getElementById('delivery_note_no');
            var inv = document.getElementById('invoice_no');
            if (elem.value === "credit") {
                acc.style.display = "none";
                or.style.display = "block";
                dn.style.display = "block";                    
                inv.style.display = " block";
                // c.style.display = " block";
                // ad.style.display = " block";
                du.style.display = "block";
            }else{
                acc.style.display = "block";
                or.style.display = "none";
                dn.style.display = "none";
                inv.style.display = "none";
                du.style.display = "none";

            }
        }else{
            if (elem.value === "credit") {
                acc.style.display = "none";
                du.style.display = "block";
            }else{
                acc.style.display = "block";
                du.style.display = "none";
            }
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

    <div class="row" ng-controller="SearchItemCtrl" ng-init="pmPurchaseId('<?php echo $pmtemp->id; ?>')">
        <div class="col-xl-4 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_packing_material')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body p-2">
                    <div class="p-2 border rounded"> 
                        <div class="col-sm-12 text-center mb-1">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#packing_materialModal">
                                <i class="fa fa-plus"></i>
                                {{trans('navmenu.new_packing_material')}}
                            </button>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                            <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_packing_material')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-12">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items  | filter: searchKeyword | limitTo:10" ng-click="addStockTemp(item, newpmitemtemp , tempid)">
                                    <div class="col-sm-11">
                                        @{{item.name}}
                                    </div>
                                    <div class="col-sm-1">
                                        <span class="badge bg-success rounded-pill"><span class="fa fa-arrow-right" aria-hidden="true"></span></span>
                                    </div>
                                </li>
                            </ul>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.purchase_items')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body p-2">
                    <div class="d-lg-flex align-items-center mb-1 gap-1">
                        <div class="ms-auto">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="fa fa-user-plus"></i>{{trans('navmenu.new_supplier')}}</button>
                        </div>
                    </div>

                    <div class="p-4 border rounded print_invoice">
                        <form class="row g-3"  name="pmitemform" method="POST" action="{{route('pm-purchases.store')}}" onsubmit="return validateform(this)">
                            @csrf

                            <input type="hidden" name="pm_purchase_temp_id" placeholder="" value="{{$pmtemp->id}}" class="form-control form-control-sm mb-1">

                            <div class="col-sm-3">
                                <label for="suppler_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                <select name="supplier_id" id="supplier_id"  class="form-select form-select-sm mb-1" ng-model="pmtemp.supplier_id" ng-change="updatePmTempInfo(pmtemp)" ng-options="supplier.id as supplier.name for supplier in suppliers" required>
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                            
                            <div class="col-sm-3">
                                <label for="date_set" class="form-label">{{trans('navmenu.purchase_date')}}</label>
                                <select name="date_set" id="date_set" ng-model="pmtemp.date_set" ng-change="updatePmTempInfo(pmtemp)" onchange="weg(this)" class="form-select form-select-sm mb-1">
                                    <option value="auto">Auto</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>

                            <div class="col-sm-3" id="pmitem_date_field"    style="display:none;">
                                
                                <label class="form-label">{{trans('navmenu.purchase_date')}}</label>
                                <div class="input-group date"> 
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="date" id="purchase_date" ng-model="pmtemp.date" ng-change="updatePmTempInfo(pmtemp)"  placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div> 
                                </div>
                            </div>

                            
                            <div class="col-sm-3" id="purchase_type_field">
                                <label class="form-label">{{trans('navmenu.purchase_type')}}</label>
                                <select name="purchase_type" id="purchase_type" onchange="wegPurchaseType(this)" ng-model="pmtemp.purchase_type" ng-change="updatePmTempInfo(pmtemp)"  class="form-select form-select-sm mb-1" required>
                                     <option value="">{{trans('navmenu.select_purchase_type')}}</option>
                                    <option value="cash">{{trans('navmenu.cash_purchases')}}</option>
                                    <option value="credit">{{trans('navmenu.credit_purchases')}}</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="account" style="display: none;">
                                <label for="account" class="form-label">{{trans('navmenu.paid_from')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb3" name="account_id" required>
                                    @foreach($accounts as $acc)
                                    <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($shop->subscription_type_id == 3 || $shop->subscription_type_id == 4 )
                            <div class="col-sm-3">
                                <div class="form-group" id="order_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-1" id="ord_no" ng-model="pmtemp.order_no" ng-blur="updatePmTempInfo(pmtemp)" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" />
                                </div> 
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" id="delivery_note_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.delivery_note_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-1" id="dn_no" ng-model="pmtemp.delivery_note_no" ng-blur="updatePmTempInfo(pmtemp)" placeholder="{{trans('navmenu.hnt_delivery_note_no')}}" name="delivery_note_no" />
                                </div> 
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" id="invoice_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                                    <input type="text" ng-model="pmtemp.invoice_no" ng-blur="updatePmTempInfo(pmtemp)" class="form-control form-control-sm mb-1" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" />
                                </div> 
                            </div>
                            @endif
                            <div class="col-sm-3" id="due_date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.due_date')}}</label>
                                <div class="input-group date">
                                    <input type="text" name="due_date" id="due_date" placeholder="{{trans('navmenu.pick_date')}}" ng-model="pmtemp.due_date" ng-change="updatePmTempInfo(pmtemp)" class="form-control form-control-sm mb-1">
                                </div>
                            </div>

                            @if($settings->allow_multi_currency)
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.currency')}}</label>
                                    <select name="currency" id="currency" class="form-select form-select-sm mb-1" ng-model="pmtemp.currency" ng-change="updatePmTempInfo(pmtemp)" ng-options="curr.code as curr.code for curr in currencies" required>
                                    </select>
                                </div>
                                <div class="col-sm-3" ng-if="pmtemp.currency != pmtemp.defcurr">
                                    <label class="form-label">Exchange Rate Mode</label>
                                    <select name="ex_rate_mode"  class="form-select form-select-sm mb-1" ng-model="pmtemp.ex_rate_mode">
                                        <option value="Locale" selected>1 @{{pmtemp.defcurr}} Equals ? @{{pmtemp.currency}}</option>
                                        <option value="Foreign">1 @{{pmtemp.currency}} Equals ? @{{pmtemp.defcurr}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3" ng-if="pmtemp.currency != pmtemp.defcurr && pmtemp.ex_rate_mode == 'Locale'">
                                    <label class="form-label">Rate Amount in @{{pmtemp.currency}}</label>
                                    <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-1" string-to-number ng-model="pmtemp.foreign_ex_rate" ng-blur="updatePmTempInfo(pmtemp)">
                                </div>
                                <div class="col-sm-3" ng-if="pmtemp.currency != pmtemp.defcurr && pmtemp.ex_rate_mode == 'Foreign'">
                                    <label class="form-label">Rate Amount in @{{pmtemp.defcurr}}</label>
                                    <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-1" string-to-number ng-model="pmtemp.local_ex_rate" ng-blur="updatePmTempInfo(pmtemp)">
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-sm-12">
                                <table class="items mt-0" style="width: 100%; display: block; overflow: scroll; overflow: auto; white-space: nowrap;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.packing_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newpmitemtemp in pmitemtemp" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newpmitemtemp.name}}</td>
                                        <td><input type="number" name="qty" ng-blur="updateStockTemp(newpmitemtemp)" string-to-number ng-model="newpmitemtemp.qty" min="0" step="0.25"  autocomplete="off" style="text-align:center; size: 70px;" ></td>
                                        <td><input type="number" name="unit_cost" ng-blur="updateStockTemp(newpmitemtemp)" string-to-number ng-model="newpmitemtemp.unit_cost" min="0" step="any"  style="text-align:center;" autocomplete="off"></td>
                                        <td style="text-align:center;">@{{newpmitemtemp.total | number:0}}</td>

                                        <td><a href="#" ng-click="removeStockTemp(newpmitemtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align:center;"><b>@{{sum(pmitemtemp) | number:2}}</b></th>
                                        <th></th>
                                    </tr>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        @if($shop->subscription_type_id <= 1)
                                        <div class="col-sm-4" id="amount_paid" style="display: none;">
                                            <label for="total" class="form-label">{{trans('navmenu.amount_paid')}}</label>
                                            <input type="number" min="0" class="form-control form-control-sm mb-1" id="add_payment" string-to-number ng-model="add_payment" placeholder="{{trans('navmenu.hnt_amount_paid')}}" name="amount_paid" />
                                        </div>
                                        @endif 
                                        
                                        <div class="col-sm-12">
                                            <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                            <textarea rows="1" ng-model="pmtemp.comments" ng-blur="updatePmTempInfo(pmtemp)" class="form-control form-control-sm mb-1" name="comments" id="comments" ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 p-4">
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm mb-1">{{trans('navmenu.btn_submit')}}</button>
                                    <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm mb-1">{{trans('navmenu.btn_cancel')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <!-- Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-validate" method="POST" action="{{route('suppliers.store')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="supplier_for" value="Packing Materials">
                    <div class="col-md-6">
                          <label class="form-label">Supplier Name</label>
                          <input id="register-username" type="text" name="name" required placeholder="Please enter supplier name" class="form-control form-control-sm mb-1">
                    </div>
                    
                    <div class="col-md-6">
                          <label class="form-label">Phone number</label>
                          <input id="register-username" type="text" name="contact_no" placeholder="Please enter supplier mobile number" class="form-control form-control-sm mb-1">
                    </div>
                    
                    <div class="col-md-6">
                          <label class="form-label">Email Address</label>
                          <input id="register-email" type="text" name="email" placeholder="Please enter supplier email address" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6">
                          <label class="form-label">Address</label>
                          <input id="address" type="text" name="address" placeholder="Please enter supplier address" class="form-control form-control-sm mb-1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn btn-success">Save</button>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="packing_materialModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">{{trans('navmenu.new_packing_material')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-validate" method="POST" action="{{route('packing-materials.store')}}">
                <div class="modal-body"> 
                    @csrf
                    <div class="row">
                        <div class="form-group">
                            <label class="form-label">{{trans('navmenu.packing_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_packing_material_name')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{trans('navmenu.basic_uom')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control form-select-sm mb-1" name="basic_uom" required style="width: 100%;">
                                @foreach($units as $key => $unit)
                                <option>{{$unit->unit_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{trans('navmenu.description')}}</label>
                                <textarea name="description" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.hnt_description')}}"></textarea>
                        </div>
                    </div> 
                </div>                    
                <div class="modal-footer">
                    <button type="submit" class="btn btn btn-success">Save</button>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[id="purchase_date"]'),
                $max = document.querySelector('[name="due_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

        });
    </script>