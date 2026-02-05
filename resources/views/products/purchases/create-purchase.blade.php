@extends('layouts.inv')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/stockentries.js')}}"></script>
    <script>
        function validateform(form) {
            var items = document.stockform.no_items.value;
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

        function confirmCancel(id) {
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
                window.location.href="{{url('cancel-purchase')}}/"+id;
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function weg(elem) {
          var x = document.getElementById("stock_date_field");
          if(elem.value !== "auto") {
            x.style.display = "block";
          } else {
            x.style.display = "none";
            $("#stock_date").val('');
          }
        }

        function wegPurchaseType(elem) {
            var paid = document.getElementById('paid-field');
            var ad = document.getElementById('amount_due');
            var acc = document.getElementById('account');

            var sbscr = "<?php echo $shop->subscription_type_id; ?>";
            if (sbscr >= 3) {
                var or = document.getElementById('order_no');
                var dn = document.getElementById('delivery_note_no');
                var inv = document.getElementById('invoice_no');
                if (elem.value === "credit") {
                    acc.style.display = "none";
                    or.style.display = "block";
                    dn.style.display = "block";                    
                    inv.style.display = " block";
                }else{
                    acc.style.display = "block";
                    or.style.display = "none";
                    dn.style.display = "none";
                    inv.style.display = "none";
                }
            }else{
                if (elem.value === "credit") {
                    acc.style.display = "none";
                    paid.style.display = "block";
                }else{
                    acc.style.display = "block";
                    paid.style.display = "none";
                }
            }
        }

        function submitTemp(index) {
            document.getElementById('ptemp-form-'+index).submit();
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="purchaseTempId('<?php echo $purchasetemp->id; ?>')">
        <div class="col-md-3 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_product')}}</h6>
            <hr>
            <div class="card radius-6">
                <div class="card-body row g-3">
                    <div class="col-sm-12 mb-3 text-center" >
                        <button type="button" class="btn btn-primary btn-sm"   data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="fa fa-plus mr-1"></i>
                            {{trans('navmenu.new_product')}}
                        </button>
                    </div>
                    @if($settings->use_barcode)
                    <div class="col-sm-12">
                        <label class="form-label">Scan Barcode</label>
                        <input id="scanner_input_purchase" name="barcode" type="text" class="form-control form-control-sm mb-1" placeholder="Scan barcode from an item ..." autofocus/>
                    </div>
                    @endif
                    <div class="col-sm-12">
                        <input id="search_key" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                        <ul id="searchResult" class="list-group"></ul>
                    </div>  
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.purchase_items')}}</h6>
            <hr>
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6 d-lg-flex align-items-center mb-1 gap-1">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="fa fa-user-plus"></i>{{trans('navmenu.new_supplier')}}</button>
                        </div>
                        <div class="btn-group col-sm-6" role="group">
                            <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingtemps->count()}}</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Pending Bills/Invoices <i class="fa fa-caret-down"></i></button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                                @foreach($pendingtemps as $key => $temp) 
                                <form class="row g-3" method="POST" action="{{'pt-purchase'}}" id="ptemp-form-{{$key}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$temp->id}}">
                                    <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">{{$temp->name}} (<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                                </form>  
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" name="stockform" method="POST" action="{{route('purchases.store')}}" onsubmit="return validateform(this)" ng-if="purchasetemp">
                            @csrf
                            <input type="hidden" name="purchase_temp_id" placeholder="" value="{{$purchasetemp->id}}" class="form-control form-control-sm mb-3">
                            <input type="hidden" name="is_production" value="0">
                            <div class="col-sm-3">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}} <span style="color: red;">*</span></label>
                                <select name="supplier_id" id="supplier_id" required class="form-select form-select-sm mb-3" ng-model="purchasetemp.supplier_id" ng-change="updatePurchaseTempInfo(purchasetemp)" ng-options="supplier.id as supplier.name for supplier in suppliers">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="date_set" class="form-label">{{trans('navmenu.purchase_date')}}</label>
                                <select name="date_set" id="date_set" ng-model="purchasetemp.date_set" ng-change="updatePurchaseTempInfo(purchasetemp)" onchange="weg(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manaul">Manual</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="stock_date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.purchase_date')}}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="purchase_date" id="purchase_date" ng-model="purchasetemp.purchase_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                </div>
                            </div>
                            <div class="col-sm-3" id="purchase_type_field">
                                <label class="form-label">{{trans('navmenu.purchase_type')}} <span style="color: red;">*</span></label>
                                <select name="purchase_type" id="purchase_type" ng-model="purchasetemp.purchase_type" ng-change="updatePurchaseTempInfo(purchasetemp)" onchange="wegPurchaseType(this)" class="form-select form-select-sm mb3" required>
                                    <option value="">{{trans('navmenu.select_purchase_type')}}</option>
                                    <option value="cash">{{trans('navmenu.cash_purchases')}}</option>
                                    <option value="credit">{{trans('navmenu.credit_purchases')}}</option>
                                </select>
                            </div>

                            <div class="col-md-3" id="account" style="display: none;">
                                <label for="account" class="form-label">{{trans('navmenu.paid_from')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb3" name="account" required>
                                    <option value="Cash">{{trans('navmenu.cash')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>
                            @if($settings->allow_multi_currency)
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-3" ng-model="purchasetemp.currency" ng-change="updatePurchaseTempInfo(purchasetemp)" ng-options="curr.code as curr.code for curr in currencies" required>
                                </select>
                            </div>
                            <div class="col-sm-3" ng-if="purchasetemp.currency != purchasetemp.defcurr">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select name="ex_rate_mode"  class="form-select form-select-sm mb-3" ng-model="purchasetemp.ex_rate_mode">
                                    <option value="Locale" selected>1 @{{purchasetemp.defcurr}} Equals ? @{{purchasetemp.currency}}</option>
                                    <option value="Foreign">1 @{{purchasetemp.currency}} Equals ? @{{purchasetemp.defcurr}}</option>
                                </select>
                            </div>
                            <div class="col-sm-3" ng-if="purchasetemp.currency != purchasetemp.defcurr && purchasetemp.ex_rate_mode == 'Locale'">
                                <label class="form-label">Rate Amount in @{{purchasetemp.currency}}</label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="purchasetemp.foreign_ex_rate" ng-blur="updatePurchaseTempInfo(purchasetemp)">
                            </div>
                            <div class="col-sm-3" ng-if="purchasetemp.currency != purchasetemp.defcurr && purchasetemp.ex_rate_mode == 'Foreign'">
                                <label class="form-label">Rate Amount in @{{purchasetemp.defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="purchasetemp.local_ex_rate" ng-blur="updatePurchaseTempInfo(purchasetemp)">
                            </div>
                            @endif
                            
                            @if($shop->subscription_type_id >= 3)

                            <div class="col-md-3">
                                <div id="order_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="ord_no" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" value="{{$purchasetemp->order_no}}" />
                                </div> 
                            </div>

                            <div class="col-md-3">
                                <div id="delivery_note_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.delivery_note_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="dn_no" placeholder="{{trans('navmenu.hnt_delivery_note_no')}}" name="delivery_note_no" />
                                </div> 
                            </div>

                            <div class="col-md-3">
                                <div  id="invoice_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                                    <input type="text"  class="form-control form-control-sm mb-3" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" />
                                </div> 
                            </div>
                            @endif

                            <div class="col-md-12">
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.product_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        @if($shop->business_type_id != 1)
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.selling_price')}}</th>
                                        @if($settings->enable_exp_date)
                                        <th style="text-align: center;">{{trans('navmenu.expire_date')}}</th>
                                        @endif
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newstocktemp in stocktempitems" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newstocktemp.name}}</td>
                                        <td><input type="number" name="quantity_in" ng-blur="updateStockTemp(newstocktemp)" string-to-number ng-model="newstocktemp.quantity_in" min="0" step="any" style="text-align:center; height: 20px; width: 60px; border: 1px solid #e0e0e0;" autocomplete="off"></td>

                                        @if($shop->business_type_id != 1)
                                        <td><input type="number" name="unit_cost" ng-blur="updateStockTemp(newstocktemp)" string-to-number ng-model="newstocktemp.unit_cost" min="0" step="any" style="text-align:center;height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        <td><input type="number" name="total" ng-blur="updateStockTemp(newstocktemp)" string-to-number ng-model="newstocktemp.total" min="0" step="any" style="text-align:center; height: 20px; width: 120px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        @endif
                                        <td><input type="number" name="retail_price" ng-blur="updateStockTemp(newstocktemp)" string-to-number ng-model="newstocktemp.retail_price" min="0" step="any" style="text-align:center;height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off"></td>

                                        @if($settings->enable_exp_date)
                                        <td><input type="text" name="expire_date" ng-blur="updateStockTemp(newstocktemp)" ng-model="newstocktemp.expire_date" value="@{{newstocktemp.expire_date}}" style="text-align:center; height: 20px; width: 120px; border: 1px solid #e0e0e0;" autocomplete="off" class="form-control" placeholder="yyyy-mm-dd" onkeyup="
                                            var v = this.value;
                                            if (v.match(/^\d{4}$/) !== null) {
                                                this.value = v + '-';
                                            } else if (v.match(/^\d{4}\-\d{2}$/) !== null) {
                                                this.value = v + '-';
                                            }"
                                        maxlength="10"></td>
                                        @endif
                                        <td><a href="#" ng-click="removeStockTemp(newstocktemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        @if($shop->business_type_id != 1)
                                        <th style="text-align: center;"><b>{{trans('navmenu.total')}} (@{{purchasetemp.currency}})</b></th>
                                        <th style="text-align: center;"><b>@{{sum(stocktempitems) | number:2}}</b></th>
                                        @endif
                                        <th></th>
                                        @if($settings->enable_exp_date)
                                        <th></th>
                                        @endif
                                        <th></th>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12">
                                <h6>Additional Costs(<small class="text-info">This will not affect Supplier statement(s)</small>)</h6>
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">Item Description</th>
                                        <th style="text-align: center;">Percentage(%)</th>
                                        <th style="text-align: center;">Amount</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newcosttemp in costtemps" id="costtemps">
                                        <td>@{{$index + 1}}</td>
                                        <td>
                                            <textarea type="text" ng-model="newcosttemp.item_desc" ng-blur="updateCostTemp(newcosttemp)" placeholder="Enter Item description" rows="1"></textarea>
                                        </td>
                                        <td>
                                            <input type="number" ng-blur="updateCostTemp(newcosttemp)" string-to-number ng-model="newcosttemp.percent" min="0" step="any" style="text-align:center;height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off">
                                        </td>
                                        <td><input type="number" ng-blur="updateCostTemp(newcosttemp)" string-to-number ng-model="newcosttemp.amount" min="0" step="any" style="text-align:center; height: 20px; width: 120px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        <td><a href="#" ng-click="removeCostTemp(newcosttemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr class="hiderow">
                                        <td colspan="5"><a href="javascript:;" ng-click="addCostTemp(purchasetemp)" title="Add a row">Add a row</a></td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th style="text-align: center;"><b>{{trans('navmenu.total')}} (@{{purchasetemp.currency}})</b></th>
                                        <th style="text-align: center;"><b>@{{sumPercentCosts(costtemps) | number:2}}</b></th>
                                        <th style="text-align: center;"><b>@{{sumCosts(costtemps) | number:2}}</b></th>
                                        <th></th>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-6">
                                <label for="comments" class="col-sm-3 form-label">{{trans('navmenu.comments')}}</label>
                                <textarea rows="1" class="form-control form-control-sm mb-3" name="comments" id="comments" ng-model="purchasetemp.comments"></textarea>
                            </div>
                            <div class="col-sm-6 pt-4">
                                <button onclick="confirmCancel('<?php echo encrypt($purchasetemp->id); ?>')" type="button" class="btn btn-warning btn-sm float-end" style="margin-left: 5px;">{{trans('navmenu.btn_cancel')}}</button>
                                <button type="submit" name="myButton" class="btn btn-success btn-sm float-end">{{trans('navmenu.btn_submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
        </div>      
    </div>

    <!-- Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New Supplier</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-validate" method="POST" action="{{route('suppliers.store')}}">
                <div class="modal-body row">
                @csrf
                    <input type="hidden" name="supplier_for" value="Stock">
                    <div class="col-md-6 pt-2">
                          <label class="form-label">Supplier Name</label>
                          <input id="register-username" type="text" name="name" required placeholder="Please enter supplier name" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6 pt-2">
                          <label class="form-label">Phone number</label>
                          <input id="register-username" type="text" name="contact_no" placeholder="Please enter supplier mobile number" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6 pt-2">
                          <label class="form-label">Email Address</label>
                          <input id="register-email" type="text" name="email" placeholder="Please enter supplier email address" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6 pt-2">
                          <label class="form-label">Address</label>
                          <input id="address" type="text" name="address" placeholder="Please enter supplier address" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6 pt-2">
                        <label for="account" class="form-label">Account Number</label>
                        <input type="text" class="form-control form-control-sm mb-1" id="account_number" name="account_number" placeholder="Account Number">
                    </div>
                    <div class="col-md-6 pt-2">
                        <label for="account" class="form-label">Account Name</label>
                        <input type="text" class="form-control form-control-sm mb-1" id="account_name" name="account_name" placeholder="Account Name">
                    </div>
                    <div class="col-md-6 pt-2">
                        <label for="swift_code" class="form-label">Swift Code</label>
                        <input type="text" class="form-control form-control-sm mb-1" id="swift_code" name="swift_code" placeholder="Swift Code">
                    </div>
                    <div class="col-md-6 pt-2">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <input type="text" class="form-control form-control-sm mb-1" id="bank_name" name="bank_name" placeholder="Bank Name">
                    </div>
                    <div class="col-md-6 pt-2">
                        <label for="bank_name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control form-control-sm mb-1" id="branch_name" name="branch_name" placeholder="Branch Name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_product')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>   
            </div>
            <form class="form-validate" method="POST" action="{{ route('products.store')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="from-purch" value="1">
                    <div class="col-md-6 pt-2">
                        <label class="form-label">{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6 pt-2">
                        <label class="form-label">{{trans('navmenu.basic_uom')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="basic_uom" required style="width: 100%;">
                            @foreach($units as $key => $unit)
                            <option>{{$unit->unit_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 pt-2">
                        <label class="form-label">{{trans('navmenu.product_code')}}</label>
                        <input id="name" type="text" name="product_code" placeholder="{{trans('navmenu.hnt_product_code')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6 pt-2">
                        <label class="form-label">{{trans('navmenu.location')}}</label>
                        <input id="location" type="text" name="location" placeholder="{{trans('navmenu.hnt_location')}} (Optional)" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6 pt-2">
                        <label class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                        <input id="unit_price" type="number" min="0" name="retail_price" placeholder="{{trans('navmenu.hnt_selling_price')}}" class="form-control form-control-sm mb-1">
                    </div>
                </div>                    
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="button" data-bs-dismiss="modal" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                    {{-- <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button> --}}
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // $('#search-form').on('submit', function(e){
                // e.preventDefault();
            $('#search_key').on('keyup',function () {
                var query = $('#search_key').val();
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
                            var name = response[i]['name'];
                            var slug = response[i]['slug'];
                            var qty = +response[i]['in_stock'];
                            var path = "<?php echo asset('storage/products/'); ?>";
                            var img = response[i]['img'];
                            var img_path = path+'/'+img;
                            if (img != null) {
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'><img src='"+img_path+"' width='60'>"+slug+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                            }else{
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'>"+slug+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                            }
                        }

                        // binding click event to li
                        $("#searchResult3 li").bind("click",function(){
                            addOrderTemp(this);
                        });

                    }
                })
            });

            $('#scanner_input').focus();
            $('#scanner_input').on('change', function() {
                var query = $('#scanner_input').val();
                var purchase_temp_id = '<?php echo $purchasetemp->id; ?>';
                $.ajax({
                    url:"{{ url('grn-fetch-by-barcode') }}",
                    type:'GET',
                    data:{'barcode':query, purchase_temp_id: purchase_temp_id},
                    success:function (response) {
                        $('#scanner_input').val('');
                        $('#scanner_input').focus();
                        if (response.status == 400) {
                            alert(response.msg)
                            console.log(response);
                            $('#bermsg').append('<div class="alert alert-danger hideit alertSuc">'+response.msg+'</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                });
                            }, 1300);
                        }
                        angular.element(document.getElementById('mycontroller')).scope().getData();
                    }
                });
            });
            $('.empty-search').on('click', function(){
                console.log('')
                $("#search_key").val('');
                $("#searchResult3").empty();
            });

            $('#btn-submit').on('click', function(e){
                e.preventDefault();
                var supplier = document.getElementById('supplier_id').value;
                var items = document.getElementById('no_items').value;
                if (supplier == '?') {
                    $('#ermsg').append('<div class="alert alert-danger hideit alertSuc">Please select a Supplier</div >');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1300);
                }else{
                    document.getElementById('stockform').submit();
                }
            })
        });

        function addOrderTemp(element) {
            var value = $(element).text();
            var productid = $(element).val();
            $.ajax({
                url:"{{ url('fetch-product') }}",
                type:'GET',
                data:{'product_id':productid},
                success:function (response) {
                    var item = response;
                    angular.element(document.getElementById('mycontroller')).scope().addStockTemp(item);
                    setTimeout(function(){
                        $("#search_key").val('');
                        $("#searchResult3").empty();
                    }, 2000);
                }
            })   
        }

    </script> 

    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="purchase_date"]');
        
            var mind = "<?php echo $mindays; ?>";
            var d = new Date();
            d.setDate(d.getDate() - mind);
            $min.DatePickerX.init({
                mondayFirst: true,
                minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>