@extends('layouts.inv')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/porder.js')}}"></script>
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
                window.location.href="{{url('cancel-porder')}}/"+id;
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
            var c = document.getElementById('paid-field');
            var ad = document.getElementById('amount_due');
            var acc = document.getElementById('account');

            var sbscr = "<?php echo $shop->subscription_type_id; ?>";
            if (sbscr == 2) {
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
                var paid = document.getElementById('paid-field');
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
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{ url('purchase-orders') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12 text-right pt-0">
                <div class="row mb-1">
                    <div class="col-sm-7 d-lg-flex align-items-center mb-1 gap-1">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="fa fa-user-plus"></i>{{trans('navmenu.new_supplier')}}</button>
                        <button type="button" class="btn btn-primary btn-sm"   data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="fa fa-plus mr-1"></i>
                                {{trans('navmenu.new_product')}}
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm"   data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="fa fa-import mr-1"></i>
                                Import From Excel
                        </button>
                    </div>
                    <div class="btn-group col-sm-5" role="group">
                        <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingtemps->count()}}</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Pending Purchase Orders <i class="fa fa-caret-down"></i></button>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                            @foreach($pendingtemps as $key => $temp) 
                            <form class="row g-3" method="POST" action="{{ url('pt-porders') }}" id="ptemp-form-{{$key}}">
                                @csrf
                                <input type="hidden" name="id" value="{{$temp->id}}">
                                <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">{{$temp->name}} (<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                            </form>  
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
 
    <div class="row" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="orderTempId('<?php echo $ordertemp->id; ?>')">
        <div class="col-xl-12 col-md-12 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body"> 
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate name="orderform" method="POST" action="{{route('purchase-orders.store')}}" onsubmit="return validateform(this)">
                            @csrf
                            <input type="hidden" name="order_temp_id" value="{{$ordertemp->id}}">
                            <div class="col-sm-3">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}} <span style="color: red;">*</span></label>
                                <select name="supplier_id"  required class="form-select form-select-sm mb-1" id="supplier_id">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $key => $supplier)
                                    @if($supplier->id == $ordertemp->supplier_id)
                                    <option value="{{$supplier->id}}" selected>{{$supplier->name}}</option>
                                    @else
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">PFI No./Qoute No.</label>
                                <input type="text" name="pfi_no" class="form-control form-control-sm mb-1" ng-model="pordertemp.pfi_no" ng-blur="updateOrderTempInfo(pordertemp)" placeholder="Enter PFI no">
                            </div>
                            <div class="col-sm-6">
                                <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                <textarea  class="form-control form-control-sm mb-3" name="comments" rows="1" id="comments" ng-model="pordertemp.comments" ng-blur="updateOrderTempInfo(pordertemp)" placeholder="Enter comments"></textarea>
                            </div>
                            <div class="col-sm-8">
                                <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                <div class="input-group mb-0">
                                    <input type="text" class="form-control form-control-sm mb-1" id="search_key" placeholder="{{trans('navmenu.search_product')}}" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                    <a class="btn btn-outline-danger btn-sm empty-search" id="button-addon2"><i class='fa fa-close'></i></a>
                                </div>
                                <ul id="searchResult3" class="list-group"></ul>
                            </div>
                            @if($settings->use_barcode)
                            <div class="col-sm-4">
                                <label class="form-label">Scan Barcode</label>
                                <input id="scanner_input_purchase" name="barcode" type="text" class="form-control form-control-sm mb-1" placeholder="Scan barcode from an item ..." autofocus/>
                            </div>
                            @endif
                            <div class="col-md-12">
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                     <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.product_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        @if(Auth::user()->can('view-purchase-cost'))
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                        @endif
                                    </tr>
                                    <tr ng-repeat="neworderitemtemp in orderitemtemp" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{neworderitemtemp.product_code}} @{{neworderitemtemp.slug}}</td>
                                        <td><input type="number" name="qty" ng-blur="updateOrderTemp(neworderitemtemp)" string-to-number ng-model="neworderitemtemp.qty" min="0" step="any" value="@{{neworderitemtemp.qty}}" style="text-align:center; height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        @if(Auth::user()->can('view-purchase-cost'))
                                        <td><input type="number" name="unit_cost" ng-blur="updateOrderTemp(neworderitemtemp)" string-to-number ng-model="neworderitemtemp.unit_cost" min="0" step="any" value="@{{neworderitemtemp.unit_cost}}" style="text-align:center;height: 20px; width: 100px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        <td>@{{(neworderitemtemp.qty*neworderitemtemp.unit_cost)}}</td>
                                        @endif
                                        <td><a href="#" ng-click="removeOrderTemp(neworderitemtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    @if(Auth::user()->can('view-purchase-cost'))
                                    <tr>
                                        <th></th>
                                        <th><b>{{trans('navmenu.total')}}</b></th>
                                        <th></th>
                                        <th></th>
                                        <th><b>@{{sum(orderitemtemp) | number: 2}}</b></th>
                                        <th></th>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <div class="row">
                                <input type="hidden" id="no_items" name="no_items" value="@{{stocktemp.length}}" class="form-control form-control-sm mb-3">
                                <div class="col-xl-6">
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
                                    <button onclick="confirmCancel('<?php echo encrypt($ordertemp->id); ?>')" type="button" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <form class="row g-3" method="POST" action="{{ url('pt-porders') }}" id="ptemp-form-on">
            @csrf
            <input type="hidden" name="id" value="{{$ordertemp->id}}">
        </form>     
    </div>

    <!-- Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New Supplier</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" method="POST" action="{{route('suppliers.store')}}">
                    @csrf
                    <input type="hidden" name="supplier_for" value="Stock">
                    <div class="col-md-6">
                        <label for="register-username" class="form-label">Supplier Name</label>
                        <input id="register-username" type="text" name="name" required placeholder="Please enter supplier name" class="form-control form-control-sm mb-1">
                    </div>
                
                    <div class="col-md-6">
                        <label for="register-username" class="form-label">Phone number</label>
                        <input id="register-username" type="text" name="contact_no" placeholder="Please enter supplier mobile number" class="form-control form-control-sm">
                    </div>
                
                    <div class="col-md-6">
                        <label for="register-email" class="form-label">Email Address</label>
                        <input id="register-email" type="text" name="email" placeholder="Please enter supplier email address" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input id="address" type="text" name="address" placeholder="Please enter supplier address" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
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
            <div class="modal-body">
                <form class="form-validate row g-3" method="POST" action="{{ route('products.store')}}">
                    @csrf
                    <input type="hidden" name="from-purch" value="1">
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.basic_uom')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="basic_uom" required style="width: 100%;">
                            @foreach($units as $key => $unit)
                            <option>{{$unit->unit_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.product_code')}}</label>
                        <input id="name" type="text" name="product_code" placeholder="{{trans('navmenu.hnt_product_code')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.location')}}</label>
                        <input id="location" type="text" name="location" placeholder="{{trans('navmenu.hnt_location')}} (Optional)" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                        <input id="unit_price" type="number" min="0" name="retail_price" placeholder="{{trans('navmenu.hnt_selling_price')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="button" data-bs-dismiss="modal" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Import Purchased Items</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>   
            </div>
            <form class="form-validate" method="POST" action="{{ url('purchase-order-imports')}}" enctype="multipart/form-data">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="order_temp_id" placeholder="" value="{{$ordertemp->id}}" class="form-control form-control-sm mb-3">
                    <div class="col-md-12 pt-2">
                        <label class="form-label"> Download Sample Excel file <span style="color: red; font-weight: bold;">*</span></label>
                        <a href="{{ url('purchase-excel-sample') }}" class="btn btn-primary btn-sm"><i class="fa fa-download"></i> Download</a>
                    </div>
                    <hr>
                    <div class="col-md-12 pt-2">
                        <h4>Instructions</h4>
                        <p>
                            1. Download the sample excel file then use it to create your purchase item list.<br>
                            2. Make sure the product Number/Code and Name Matches registered product in the system. Other wise the system will create as new Product<br>
                            3. Make sure the QTY and prices are for products Basic Unit.
                        </p>
                    </div>
                    <div class="col-md-12 pt-2">
                        <label class="form-label">Choose excel file to Import <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="exampleInputFile" class="form-control form-control-sm mb-1 form-control form-control-sm mb-1-sm mb-1" type="file" name="file" accept=".xlsx,.xls" required>
                    </div>
                </div>                    
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Import</button>
                    <button type="button" data-bs-dismiss="modal" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                    {{-- <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Cancel</button> --}}
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
                            if (qty > 0) {
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'>"+slug+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-arrow-right' aria-hidden='true'></span></span></div></li>");
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

            $('.empty-search').on('click', function(){
                $("#search_key").val('');
                $("#searchResult3").empty();
            });

            $('#supplier_id').on('change', function(){
                var selection = $('#supplier_id').val();
                var supplier_id = selection.replace('number:', '');
                angular.element(document.getElementById('mycontroller')).scope().updateSaleSupplierID(supplier_id);
                setTimeout(function(){
                    $('#ptemp-form-on').submit();
                }, 500);
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
                    angular.element(document.getElementById('mycontroller')).scope().addOrderTemp(item);
                    setTimeout(function(){
                        $("#search_key").val('');
                        $("#searchResult3").empty();
                    }, 2000);
                }
            })   
        }
    </script>