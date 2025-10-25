@extends('layouts.app')

@section('page-styles')
    <!-- Application Vendor CSS URL -->
    <link rel="stylesheet" href="{{ asset('side/assets/cssbundle/summernote.min.css') }}">
@endsection
    <!-- <script src="http://cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.20/angular.min.js"></script> -->
    <script type="text/javascript" src="{{asset('js/angular.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/invpos/servpos.js')}}"></script>
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
            var servitems = document.invoiceform.no_servitems.value;
            if (servitems == 0) {
                alert('Please select at least one servitem to continue.');
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
                <div class="card-body" style="overflow-x: auto;">
                    @if ($message = Session::get('error'))
                    <div class="row mb-1">
                        <div class="alert alert-danger alert-block">
                          <button type="button" class="close" data-dismiss="alert">×</button> 
                          <strong>{{ $message }}</strong>
                        </div>
                    </div>
                    @endif
                    <div class="p-3 border rounded">
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
                                    <input type="text" name="due_date" placeholder="Choose Due date" class="form-control form-control-sm mb-3" required>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                <div class="input-group mb-0">
                                    <input type="text" class="form-control form-control-sm mb-1" id="search_serv_key" placeholder="{{trans('navmenu.search_service')}}" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                    <a href="javascript:;" class="btn btn-outline-danger btn-sm empty-search" id="button-addon2"><i class='fa fa-close'></i></a>
                                </div>
                                <ul id="searchServiceResult" class="list-group"></ul>
                            </div>
                            <div class="col-md-12">
                                <table id="discount_field" class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th>#</th>
                                        <th>Item name</th>
                                        <th>repeatition</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}} </th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newservinvoicetemp in servinvoicetemp">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newservinvoicetemp.name}}</td>
                                        <td><input type="number" style="text-align: center; width: 60px;" autocomplete="off" name="repeatition" ng-blur="updateServiceSaleTemp(newservinvoicetemp)" string-to-number ng-model="newservinvoicetemp.repeatition" min="0" step="0.25" max="@{{newservinvoicetemp.curr_stock}}" value="@{{newservinvoicetemp.repeatition}}"></td>
                                        <td><input type="number" style="text-align: center; width: 80px;" autocomplete="off" name="cost_per_unit" ng-blur="updateServiceSaleTemp(newservinvoicetemp)" string-to-number ng-model="newservinvoicetemp.cost_per_unit" value="@{{newservinvoicetemp.cost_per_unit}}"></td>
                                        <td>@{{(newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition) | number:0}}</td>
                                        <td><input type="number" min="0" step="any" style="text-align:center; width: 80px;" name="total_discount" ng-blur="updateServiceSaleTemp(newservinvoicetemp)" string-to-number ng-model="newservinvoicetemp.total_discount"></td>
                                        
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newservinvoicetemp.with_vat" name="with_vat" ng-change="updateServiceSaleTemp(newservinvoicetemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no" selected>{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td ng-model="newservinvoicetemp.vat_amount">@{{newservinvoicetemp.vat_amount | number:2}}</td>
                                        @endif
                                        <td><a href="#" ng-click="removeSaleTemp(newservinvoicetemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                </table>
                                <table id="discount_value" class="table table-responsive table-striped display nowrap" style="width: 100%; overflow: scroll; overflow: auto; display: none;">
                                    <tr>
                                        <th>#</th>
                                        <th>Item name</th>
                                        <th>repeatition</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}} </th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newservinvoicetemp in servinvoicetemp">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newservinvoicetemp.name}}</td>
                                        <td><input type="number" style="text-align: center; width: 60px;" autocomplete="off" name="repeatition" ng-blur="updateServiceSaleTemp(newservinvoicetemp)" string-to-number ng-model="newservinvoicetemp.repeatition" min="0" step="0.25" max="@{{newservinvoicetemp.curr_stock}}" value="@{{newservinvoicetemp.repeatition}}"></td>
                                        <td><input type="number" style="text-align: center; width: 80px;" autocomplete="off" name="cost_per_unit" ng-blur="updateServiceSaleTemp(newservinvoicetemp)" string-to-number ng-model="newservinvoicetemp.cost_per_unit" value="@{{newservinvoicetemp.cost_per_unit}}"></td>
                                        <td>@{{(newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition) | number:0}}</td>
                                        <td style="text-align: center;">@{{newservinvoicetemp.total_discount | number:2}}</td>
                                        
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newservinvoicetemp.with_vat" name="with_vat" ng-change="updateServiceSaleTemp(newservinvoicetemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no" selected>{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td ng-model="newservinvoicetemp.vat_amount">@{{newservinvoicetemp.vat_amount | number:2}}</td>
                                        @endif
                                        <td><a href="#" ng-click="removeSaleTemp(newservinvoicetemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
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
                                                <td><b>@{{sum(servinvoicetemp) | number:2}}</b></td>
                                            </tr>
                                            <tr>
                                                <td>Discount</td>
                                                <td id="total_discount_field" style="display: none;"><input type="number" style="text-align:center; width: 100px; height: 20px;" name="sale_discount" id="serv_discount" value="@{{ sumDiscount(servinvoicetemp) }}"></td>
                                                <td id="total_discount_value"><b>@{{sumDiscount(servinvoicetemp) | number:2}}</b></td>
                                            </tr>
                                            @if($settings->is_vat_registered) 
                                            <tr>
                                                <td>Tax {{$settings->tax_rate}} %</td>
                                                <td><b>@{{(sumTax(servinvoicetemp)) | number:2}}</b></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td>Grand Total</td>
                                                <td><b>@{{ (sum(servinvoicetemp)-sumDiscount(servinvoicetemp))+(shipping_cost+adjustment)+(sumTax(servinvoicetemp)) | number:2}}</b></td>
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
            $('#search_serv_key').on('keyup',function () {
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
                angular.element(document.getElementById('mycontroller')).scope().updateSaleTempServiceDiscount(discount);
            })

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

        function addServSaleTemp(element) {
            var value = $(element).text();
            var serv_id = $(element).val();
            angular.element(document.getElementById('mycontroller')).scope().addServiceSaleTemp(serv_id);
            setTimeout(function(){
                $("#search_serv_key").val('');
                $("#searchServiceResult").empty();
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