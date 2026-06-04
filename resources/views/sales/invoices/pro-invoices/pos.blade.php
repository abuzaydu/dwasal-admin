@extends('layouts.app')
@section('page-styles')
  <!-- Application Vendor CSS URL -->
  <link rel="stylesheet" href="{{ asset('side/assets/cssbundle/summernote.min.css') }}">
@endsection
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/invpos/pos.js')}}"></script>
@if(Session::has('code'))
<script type="text/javascript">

    $(document).ready(function(){

        $("#myModal").modal('show');

        $('#myModal').on('hidden.bs.modal', function () {
            closeFunction();
        });
    });
</script>
@endif 
<script>
    function validateform(form) {
        var items = document.invoiceform.no_items.value;
        if (items == 0) {
            alert('Please select at least one item to continue.');
            return false;
        }

        if (document.invoiceform.due_date.value === '') {
            Swal.fire(
              'Due Date Required!',
              'Please set invoice Due date.',
              'info'
            )
            return false;
        }

        form.myButton.disabled = true;
        form.myButton.value = "Please wait...";
        return true;        
    }


    function weg(elem) {
      var x = document.getElementById("invoice_date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#invoice_date").val('');
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


</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('pro-invoices') }}">Proforma Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#customerModal"><i class="fa fa-plus"></i>New Customer</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" id="mycontroller" ng-controller="SearchItemCtrl">
        <div class="col-xl-12 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    @if ($message = Session::get('error'))
                    <div class="row mb-1">
                        <div class="alert alert-danger alert-block">
                          <button type="button" class="close" data-dismiss="alert">×</button> 
                          <strong>{{ $message }}</strong>
                        </div>
                    </div>
                    @endif
                    <div class="p-3 border rounded" style="overflow-x: auto;">
                        <form class="row g-3" name="invoiceform" method="POST" action="{{route('pro-invoices.store')}}" onsubmit="return validateform(this)">
                            @csrf
                            <input type="hidden" name="customer_id" id="cust-id" class="form-control form-control-sm mb-1">
                            <div class="col-sm-4">
                                <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                <input id="search_customer_key" placeholder="Search customer" class="form-control form-control-sm mb-1" autocomplete="off">
                                <ul id="searchResult2"></ul>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label">RFQ. No.</label>
                                <input type="text" name="ref_no" placeholder="Enter RFQ No" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label">PFI Date</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="invoice_date" value="{{$invoice_date}}" id="invoice_date" placeholder="Choose Sale date" class="form-control form-control-sm mb-3">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <label class="form-label">Due/Validity date <span style="color: red; font-weight: bold;">*</span></label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="due_date" value="{{ $duedate }}" placeholder="Choose Due date" class="form-control form-control-sm mb-3" required>
                                </div>
                            </div>
                            @if($settings->use_barcode)
                            <div class="col-sm-4">
                                <label class="form-label">Scan Barcode</label>
                                <input id="scanner_input" name="barcode" type="text" ng-model="barcode" class="form-control form-control-sm mb-1" placeholder="Scan barcode from an item ..." type="text" autofocus/>
                                </div>
                            @endif
                            <div class="col-sm-8">
                                <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                <div class="input-group mb-0">
                                    <input type="text" class="form-control form-control-sm mb-1" id="search_key" placeholder="{{trans('navmenu.search_product')}}" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                    <button class="btn btn-outline-danger empty-search" type="submit" id="button-addon2"><i class='fa fa-close'></i></button>
                                </div>
                                <ul id="searchResult3" class="list-group"></ul>
                            </div>
                            <div class="col-md-12">
                                <table id="discount_field" class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th>#</th>
                                        <th>Item name</th>
                                        <th>Quantity</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit')}}</th>
                                        @if($settings->retail_with_wholesale)
                                        <th style="text-align: center;">{{trans('navmenu.sold_in')}}</th>
                                        @endif
                                        <th>Price</th>
                                        @if($settings->allow_unit_discount)
                                        <th style="text-align: center;">{{trans('navmenu.unit_discount')}}</th>
                                        @endif
                                        <th>Total</th>
                                        @if($settings->discount_by_percent)
                                        <th style="text-align: center;">{{trans('navmenu.discount')}} (%)</th>
                                        @else
                                        <th style="text-align: center;">{{trans('navmenu.discount')}} </th>
                                        @endif
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newinvoicetemp in invoicetemp">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newinvoicetemp.slug}}</td>
                                        <td><input type="number" style="text-align: center; width: 60px;" autocomplete="off" name="quantity" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.quantity" min="0" step="0.25" max="@{{newinvoicetemp.curr_stock}}" value="@{{newinvoicetemp.quantity}}"></td>
                                        <td>
                                            <select ng-model="newinvoicetemp.product_unit_id" name="product_unit_id" ng-change="updateSaleTemp(newinvoicetemp)" ng-options="unit.id as unit.unit_name for unit in newinvoicetemp.units">
                                            </select>
                                        </td>
                                        @if($settings->retail_with_wholesale)
                                        <td><select ng-model="newinvoicetemp.sold_in" name="sold_in" ng-change="updateSaleTemp(newinvoicetemp)" class="form-select form-select-sm mb-1" style="border: 1px solid #e0e0e0;">
                                            <option value="Retail Price">{{trans('navmenu.retail_price')}}</option>
                                            <option value="Wholesale Price">{{trans('navmenu.wholesaleprice')}}</option>
                                        </select></td>
                                        @endif
                                        <td><input type="number" style="text-align: center; width: 80px;" autocomplete="off" name="cost_per_unit" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.cost_per_unit" value="@{{newinvoicetemp.cost_per_unit}}"></td>
                                        @if($settings->allow_unit_discount)
                                        <td><input type="number" min="0" step="any" style="text-align:center; width: 60px;" name="discount" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.discount"></td>
                                        @endif
                                        <td>@{{(newinvoicetemp.cost_per_unit * newinvoicetemp.quantity) | number:0}}</td>
                                        @if($settings->discount_by_percent)
                                        <td><input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="disc_percent" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.disc_percent"></td>
                                        <!-- <td style="text-align: center;">@{{newinvoicetemp.total_discount | number:2}}</td> -->
                                        @else
                                        <td><input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="total_discount" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.total_discount"></td>
                                        @endif
                                        
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newinvoicetemp.with_vat" name="with_vat" ng-change="updateSaleTemp(newinvoicetemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no" selected>{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td ng-model="newinvoicetemp.vat_amount">@{{newinvoicetemp.vat_amount | number:2}}</td>
                                        @endif
                                        <td><a href="#" ng-click="removeSaleTemp(newinvoicetemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                </table>
                                <table id="discount_value" class="table table-responsive table-striped display nowrap" style="width: 100%; overflow: scroll; overflow: auto; display: none;">
                                    <tr>
                                        <th>#</th>
                                        <th>Item name</th>
                                        <th>Quantity</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit')}}</th>
                                        @if($settings->retail_with_wholesale)
                                        <th style="text-align: center;">{{trans('navmenu.sold_in')}}</th>
                                        @endif
                                        <th>Price</th>
                                        @if($settings->allow_unit_discount)
                                        <th style="text-align: center;">{{trans('navmenu.unit_discount')}}</th>
                                        @endif
                                        <th>Total</th>
                                        @if($settings->discount_by_percent)
                                        <th style="text-align: center;">{{trans('navmenu.discount')}} (%)</th>
                                        @else
                                        <th style="text-align: center;">{{trans('navmenu.discount')}} </th>
                                        @endif
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newinvoicetemp in invoicetemp">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newinvoicetemp.slug}}</td>
                                        <td><input type="number" style="text-align: center; width: 60px;" autocomplete="off" name="quantity" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.quantity" min="0" step="0.25" max="@{{newinvoicetemp.curr_stock}}" value="@{{newinvoicetemp.quantity}}"></td>
                                        <td>
                                            <select ng-model="newinvoicetemp.product_unit_id" name="product_unit_id" ng-change="updateSaleTemp(newinvoicetemp)" ng-options="unit.id as unit.unit_name for unit in newinvoicetemp.units">
                                            </select>
                                        </td>
                                        @if($settings->retail_with_wholesale)
                                        <td><select ng-model="newinvoicetemp.sold_in" name="sold_in" ng-change="updateSaleTemp(newinvoicetemp)" class="form-select form-select-sm mb-1" style="border: 1px solid #e0e0e0;">
                                            <option value="Retail Price">{{trans('navmenu.retail_price')}}</option>
                                            <option value="Wholesale Price">{{trans('navmenu.wholesaleprice')}}</option>
                                        </select></td>
                                        @endif
                                        <td><input type="number" style="text-align: center; width: 80px;" autocomplete="off" name="cost_per_unit" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.cost_per_unit" value="@{{newinvoicetemp.cost_per_unit}}"></td>
                                        @if($settings->allow_unit_discount)
                                        <td><input type="number" min="0" step="any" style="text-align:center; width: 60px;" name="discount" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.discount"></td>
                                        @endif
                                        <td>@{{(newinvoicetemp.cost_per_unit * newinvoicetemp.quantity) | number:0}}</td>
                                        @if($settings->discount_by_percent)
                                        <td><input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="disc_percent" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.disc_percent"></td>
                                        <!-- <td style="text-align: center;">@{{newinvoicetemp.total_discount | number:2}}</td> -->
                                        @else
                                        <td style="text-align: center;">@{{newinvoicetemp.total_discount | number:2}}</td>
                                        @endif
                                        
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newinvoicetemp.with_vat" name="with_vat" ng-change="updateSaleTemp(newinvoicetemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no" selected>{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td ng-model="newinvoicetemp.vat_amount">@{{newinvoicetemp.vat_amount | number:2}}</td>
                                        @endif
                                        <td><a href="#" ng-click="removeSaleTemp(newinvoicetemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <table class="table table-striped" style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td>Subtotal</td>
                                                <td><b>@{{sum(invoicetemp) | number:2}}</b></td>
                                            </tr>
                                            <tr>
                                                <td>Discount</td>
                                                <td id="total_discount_field" style="display: none;"><input type="number" style="text-align:center; width: 100px; height: 20px;" name="sale_discount" id="sale_discount" value="@{{ sumDiscount(invoicetemp) }}"></td>
                                                <td id="total_discount_value"><b>@{{sumDiscount(invoicetemp) | number:2}}</b></td>
                                            </tr>
                                            @if($settings->is_vat_registered) 
                                            <tr>
                                                <td>Tax {{$settings->tax_rate}} %</td>
                                                <td><b>@{{(sumTax(invoicetemp)) | number:2}}</b></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td>Grand Total</td>
                                                <td><b>@{{(sum(invoicetemp)-sumDiscount(invoicetemp))+(shipping_cost+adjustment)+(sumTax(invoicetemp)) | number:2}}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Invoice Notes</label>
                                    <textarea  class="form-control form-control-sm mb-3" name="notes" id="notes" >@if(!is_null($notes)){!! $notes->content !!}@endif</textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm">Create</button>
                                    <a href="{{url('cancel-invoice')}}" type="button" class="btn btn-warning btn-sm">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>      
    </div>
    

    <!-- Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-bs-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">{{trans('navmenu.new_customer')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-bs-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3 form-validate" method="POST" action="{{url('new-customer')}}">
                   @csrf
                   <div class=" col-md-6">
                        <label for="register-username" class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
                        <input id="register-username" type="text" name="name" required placeholder="{{trans('navmenu.hnt_customer_name')}}" class="form-control form-control-sm mb-1">
                    </div>
                
                    <div class=" col-md-6">
                        <label for="register-username" class="form-label">{{trans('navmenu.phone_number')}}</label>
                        <input id="register-username" type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control form-control-sm mb-1">
                    </div>
                
                    <div class=" col-md-6">
                        <label for="register-email" class="form-label">{{trans('navmenu.email_address')}}</label>
                        <input id="register-email" type="text" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class=" col-md-6">
                        <label for="address" class="form-label">{{trans('navmenu.postal_address')}}</label>
                        <input id="address" type="text" name="postal_address" placeholder="{{trans('navmenu.hnt_postal_address')}}" class="form-control form-control-sm mb-1">
                    </div>

                    <div class=" col-md-6">
                        <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                        <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control form-control-sm mb-1">
                    </div>

                    <div class=" col-md-6">
                        <label for="address" class="form-label">{{trans('navmenu.street')}}</label>
                        <input id="address" type="text" name="street" placeholder="{{trans('navmenu.hnt_street')}}" class="form-control form-control-sm mb-1">
                    </div>
                
                    <div class=" col-md-6">
                        <label for="register-username" class="form-label">{{trans('navmenu.tin')}}</label>
                        <input id="register-username" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-1"  data-inputmask='"mask": "999-999-999"' data-mask>
                    </div>
                    <div class=" col-md-6">
                        <label for="register-username" class="form-label">{{trans('navmenu.vrn')}}</label>
                        <input id="register-username" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-1" data-inputmask='"mask": "99-999999-A"' data-mask>
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
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
    <script src="{{ asset('side/assets/js/bundle/summernote.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#notes').summernote({
              toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
              ]
            });
            $('.note-editor .note-btn').on('click', function() {
                $(this).next().toggleClass("show");
            });
        });
    </script>
@endsection
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#search_key').on('keyup', function(){
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
                            if (qty > 0) {
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-8'>"+slug+"</div><div class='col-sm-3'><span style='color: blue;'>("+(qty+0)+")</span></div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                            }else{
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-8'>"+slug+"</div><div class='col-sm-3'><span style='color: red;'>("+(qty+0)+")</span></div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
                            }
                        }

                        // binding click event to li
                        $("#searchResult3 li").bind("click",function(){
                            addSaleTemp(this);
                        });

                    }
                })
            });


            $('#scanner_input').focus();
            $('#scanner_input').on('change', function() {
                var query = $('#scanner_input').val();
                $.ajax({
                    url:"{{ url('add-invoiceitem-by-barcode') }}",
                    type:'GET',
                    data:{'barcode':query},
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

            $('#empty-search').on('click', function(){
                $("#search_key").val('');
                $("#searchResult3").empty();
            });

            $('#sale_discount').on('blur', function(){
                var discount = $('#sale_discount').val();
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
            var $min = document.querySelector('[name="invoice_date"]'),
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