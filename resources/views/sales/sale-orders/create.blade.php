@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/sorder.js') }}"></script>
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

        function wegSaleType(elem) {
            var d = document.getElementById('duedate');
            if (elem.value === "credit") {
                d.style.display = "block";
            }else{
                d.style.display = "none";
            }
        }

        function submitTemp(index) {
            document.getElementById('sotemp-form-'+index).submit();
        }
    </script> 
@section('content')
    
     <!--breadcrumb-->
    <div class="block-header pt-4" style="margin-bottom: 0px;">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix pt-2" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="saleOrderId('<?php echo $saleorder->id; ?>')">
        <div class="col-xl-3 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-2 border rounded">
                        @if($settings->use_barcode)
                        <div class="col-sm-12">
                            <label class="form-label">Scan Barcode</label>
                            <input id="scanner_input" name="barcode" type="text" ng-model="barcode" class="form-control form-control-sm mb-3" placeholder="Scan barcode from an item ..." type="text" autofocus/>
                        </div>
                        @endif
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                            <input id="search_key" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                            <ul id="searchResult" class="list-group"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 mx-auto">
            <div class="card" style="overflow: auto;">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-8 d-lg-flex align-items-center mb-1 gap-1">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#customerModal"><i class="fa fa-user-plus"></i>{{trans('navmenu.new_customer')}}</button>
                            @if($settings->is_agent)
                            <a href="{{url('ocamounts')}}" class="btn btn-primary pull-right" style="margin-left: 5px;"><i class="fa fa-file-o"></i>{{trans('navmenu.new_oc_amount')}}</a>
                            @endif
                        </div>
                        <div class="btn-group col-sm-4" role="group">
                            <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingorders->count()}}</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Pending SO's <i class="fa fa-caret-down"></i></button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                                @foreach($pendingorders as $key => $temp) 
                                <form class="row g-3" method="POST" action="{{'pt-so'}}" id="sotemp-form-{{$key}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$temp->id}}">
                                    <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">{{$temp->name}} (<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                                </form>  
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="p-3 border rounded">
                        <form class="row g-3"  name="saleform" method="POST" action="{{ route('sale-orders.store') }}" onsubmit="return validateform(this)" ng-if="saleorder">
                            @csrf
                            <input type="hidden" name="id" placeholder="" value="{{$saleorder->id}}" class="form-control form-control-sm mb-3">
                            <div class="col-sm-6">
                                <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                <select name="customer_id" id="customer_id" required class="form-select form-select-sm mb-3" ng-model="saleorder.customer_id" ng-change="updateSaleOrderInfo(saleorder)" ng-options="customer.id as customer.name for customer in customers">
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                </select>
                            </div> 
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.sales_type')}}</label>
                                <select name="sale_type" id="sale_type" onchange="wegSaleType(this)" class="form-select form-select-sm mb-1" ng-model="saleorder.sale_type" ng-change="updateSaleOrderInfo(saleorder)" required>
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                    <option value="cash">{{trans('navmenu.cash_sales')}}</option>
                                    <option value="credit">{{trans('navmenu.credit_sales')}}</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="duedate" style="display: none;">
                                <label for="total" class="form-label">{{trans('navmenu.due_date')}} <span style="color: red;">*</span></label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="due_date" ng-model="saleorder.due_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th>#</th>
                                        <th style="text-align: center;">{{trans('navmenu.item_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit')}}</th>
                                        @if($settings->retail_with_wholesale)
                                        <th style="text-align: center;">{{trans('navmenu.sold_in')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        @if($settings->discount_by_percent)
                                        <th style="text-align: center;">{{trans('navmenu.discount')}} (%)</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newsaleorder in saleorderitems" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newsaleorder.name}}</td>
                                        <td><input type="number" style="text-align:center; height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off" name="quantity" ng-blur="updateSaleOrder(newsaleorder)" string-to-number ng-model="newsaleorder.quantity" min="0" step="any"></td>
                                        <td>
                                            <select ng-model="newsaleorder.product_unit_id" name="product_unit_id" ng-change="updateSaleOrder(newsaleorder)" ng-options="unit.id as unit.unit_name for unit in newsaleorder.units">
                                                
                                            </select>
                                        </td>
                                        @if($settings->retail_with_wholesale)
                                        <td><select ng-model="newsaleorder.sold_in" name="sold_in" ng-change="updateSaleOrder(newsaleorder)" style="text-align:center; height: 20px; width: 60px; border: 1px solid #e0e0e0;">
                                            <option value="Retail Price">{{trans('navmenu.retail_price')}}</option>
                                            <option value="Wholesale Price">{{trans('navmenu.wholesaleprice')}}</option>
                                        </select></td>
                                        @endif
                                        <td>@{{newsaleorder.retail_price | number:2}}</td>
                                        <td>@{{(newsaleorder.retail_price * newsaleorder.quantity) | number:2}}</td>
                                        @if($settings->discount_by_percent)
                                        <td><input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="disc_percent" ng-blur="updateSaleOrder(newsaleorder)" string-to-number ng-model="newsaleorder.disc_percent"></td>
                                        <td>@{{newsaleorder.total_discount | number:2}}</td>
                                        @else
                                        <td><input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="total_discount" ng-blur="updateSaleOrder(newsaleorder)" string-to-number ng-model="newsaleorder.total_discount"></td>
                                        @endif
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newsaleorder.with_vat" name="with_vat" ng-change="updateSaleOrder(newsaleorder)" style="border: 1px solid #e0e0e0;">
                                            <option value="no">{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td>@{{newsaleorder.vat_amount | number:2}}</td>
                                        @endif
                                        <td><a href="#" ng-click="removeSaleOrder(newsaleorder.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="row">
                                        <input type="hidden" id="no_items" name="no_items" value="@{{saleorderitems.length}}" class="form-control form-control-sm mb-3">
                                        <div class="col-sm-12">
                                            <label for="employee" class="form-label">{{trans('navmenu.comments')}}</label>
                                            <textarea  class="form-control form-control-sm mb-3" name="comments" ng-model="saleorder.comments" ng-blur="updateSaleOrderInfo(saleorder)" id="comments" ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <table class="table " style="width: 100%;">
                                        <tr>
                                            <th>{{trans('navmenu.subtotal')}}</th>
                                            <th style="text-align: right;"><b>@{{sum(saleorderitems) | number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.discount')}}</th>
                                            <th style="text-align: right;"><b>@{{sumDiscount(saleorderitems) | number:2}}</b></th>
                                        </tr>
                                        @if($settings->is_vat_registered)
                                        <tr>
                                            <th>{{trans('navmenu.vat')}}</th>
                                            <th style="text-align: right;"><b>@{{sumVAT(saleorderitems)| number:2}}</b></th>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th style="text-align: right;"><b>@{{(sum(saleorderitems)-sumDiscount(saleorderitems)+sumVAT(saleorderitems)) | number:2}}</b></th>
                                        </tr>   
                                        <tr>
                                            <th>{{trans('navmenu.currency')}}</th>
                                            <th style="text-align: right;"><b>{{$dfcurr->code}}</b></th>
                                        </tr>
                                    </table>

                                    <div class="row">
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
                        <form id="delete-form" method="POST" action="{{ route('sale-orders.destroy', encrypt($saleorder->id))}}" style="display: inline;">
                            @csrf
                            @method("DELETE")
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
                <form class="my-form" method="POST" action="{{url('new-customer')}}">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                              <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
                              <input type="text" name="name" required placeholder="{{ trans('navmenu.hnt_customer_name') }}" class="form-control form-control-sm mb-3">
                        </div>
                        
                        <div class="col-sm-6">
                              <label class="form-label">{{trans('navmenu.phone_number')}}</label>
                              <input type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control form-control-sm mb-3">
                        </div>
                        
                        <div class="col-sm-6">
                              <label for="register-email" class="form-label">{{trans('navmenu.email_address')}}</label>
                              <input id="register-email" type="text" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-6">
                            <label for="address" class="form-label">{{trans('navmenu.postal_address')}}</label>
                            <input id="address" type="text" name="postal_address" placeholder="{{trans('navmenu.hnt_postal_address')}}" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-sm-6">
                            <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                            <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-sm-6">
                            <label for="address" class="form-label">{{trans('navmenu.street')}}</label>
                            <input id="address" type="text" name="street" placeholder="{{trans('navmenu.hnt_street')}}" class="form-control form-control-sm mb-3">
                        </div>
                        
                        <div class="col-sm-6">
                              <label class="form-label">{{trans('navmenu.tin')}}</label>
                              <input type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-3"  data-inputmask='"mask": "999-999-999"' data-mask>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.vrn')}}</label>
                            <input id="vrn" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
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
                            <input type="text" name="custid" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-3">
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
@endsection
    
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                url:"{{ url('api/item') }}",
                type:'GET',
                success:function (response) {
                    // $('#product_list').html(data);
                    var len = response.length;
                    $("#searchResult").empty();
                    for( var i = 0; i<len; i++){
                        var id = response[i]['id'];
                        var name = response[i]['name'];
                        var qty = +response[i]['in_stock'];
                        if (qty > 0) {
                            $("#searchResult").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-8'>"+name+"</div><div class='col-sm-3'><span style='color: blue;'>("+(qty+0)+")</span></div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                        }else{
                            $("#searchResult").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-8'>"+name+"</div><div class='col-sm-3'><span style='color: red;'>("+(qty+0)+")</span></div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                        }
                    }

                    // binding click event to li
                    $("#searchResult li").bind("click",function(){
                        addOrderItem(this);
                    });
                }
            });

            $('#search_key').on('keyup',function () {
                var query = $(this).val();
                $.ajax({
                    url:"{{ url('search-product') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchResult").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            var qty = +response[i]['in_stock'];
                            if (qty > 0) {
                                $("#searchResult").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-8'>"+name+"</div><div class='col-sm-3'><span style='color: blue;'>("+(qty+0)+")</span></div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                            }else{
                                $("#searchResult").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-8'>"+name+"</div><div class='col-sm-3'><span style='color: red;'>("+(qty+0)+")</span></div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                            }
                        }

                        // binding click event to li
                        $("#searchResult li").bind("click",function(){
                            addOrderItem(this);
                        });

                    }
                })
            });

            $('#empty-search').on('click', function(){
                $("#search_key").val('');
                $("#searchResult").empty();
            });
        });

        function addOrderItem(element) {
            var value = $(element).text();
            var productid = $(element).val();
            $.ajax({
                url:"{{ url('fetch-product') }}",
                type:'GET',
                data:{'product_id':productid},
                success:function (response) {
                    var item = response;
                    angular.element(document.getElementById('mycontroller')).scope().addOrderItem(item);
                }
            })   
        }

    </script>   
    
    <link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
    <script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $max = document.querySelector('[name="due_date"]');

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date(
            });
        });
    </script>