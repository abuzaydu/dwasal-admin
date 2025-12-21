@extends('layouts.vms')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/parts.js')}}"></script>
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
                window.location.href="{{url('cancel-part-purchase')}}/"+id;
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
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a  href="{{ url('part-purchases') }}">Part Purchases</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12 text-right pt-0">
                <div class="row mb-1">
                    <div class="col-sm-6 d-lg-flex mb-1 gap-1">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#vendorModal"><i class="fa fa-user-plus"></i> New Vendor</button>
                        <button type="button" class="btn btn-primary btn-sm"   data-bs-toggle="modal" data-bs-target="#partModal">
                            <i class="fa fa-plus mr-1"></i>
                            New Part
                        </button>
                    </div>
                    <div class="btn-group col-sm-6" role="group">
                        <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingtemps->count()}}</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Pending Purchases <i class="fa fa-caret-down"></i></button>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                            @foreach($pendingtemps as $key => $temp) 
                            <form class="row g-3" method="POST" action="{{'ppt-purchase'}}" id="ptemp-form-{{$key}}">
                                @csrf
                                <input type="hidden" name="id" value="{{$temp->id}}">
                                <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">{{$temp->vendor_name}} (<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                            </form>  
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="purchaseTempId('<?php echo $purchasetemp->id; ?>')">
        <div class="col-xl-12 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="needs-validation" id="stockform" method="POST" action="{{route('part-purchases.store')}}" ng-if="purchasetemp">
                            @csrf
                            <input type="hidden" name="part_purchase_temp_id" placeholder="" value="{{$purchasetemp->id}}" class="form-control form-control-sm mb-3">
                            <input type="hidden" name="is_partion" value="0">

                            <div class="row mb-1">
                                <div class="col-sm-12" id="ermsg"></div>
                                <div class="col-sm-3">
                                    <label for="vendor_id" class="form-label">Vendor <span style="color: red;">*</span></label>
                                    <select name="vendor_id" id="vendor_id" required class="form-select form-select-sm mb-3" ng-model="purchasetemp.vendor_id" ng-change="updatePurchaseTempInfo(purchasetemp)" ng-options="vendor.id as vendor.name for vendor in vendors">
                                        <!-- <option value="">Select vendor</option> -->
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Purchase Date</label>
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="pp_date" id="pp_date" ng-model="purchasetemp.pp_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                    </div>
                                </div>
                                @if(Auth::user()->can('view-purchase-cost'))    
                                <div class="col-sm-2" id="purchase_type_field">
                                    <label class="form-label">{{trans('navmenu.purchase_type')}} <span style="color: red;">*</span></label>
                                    <select name="purchase_type" id="purchase_type" ng-model="purchasetemp.purchase_type" ng-change="updatePurchaseTempInfo(purchasetemp)" onchange="wegPurchaseType(this)" class="form-select form-select-sm mb3">
                                        <option value="">{{trans('navmenu.select_purchase_type')}}</option>
                                        <option value="cash">{{trans('navmenu.cash_purchases')}}</option>
                                        <option value="credit">{{trans('navmenu.credit_purchases')}}</option>
                                    </select>
                                </div>

                                <div class="col-md-2" id="account" style="display: none;">
                                    <label for="account" class="form-label">{{trans('navmenu.paid_from')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-select form-select-sm mb3" name="account_id" required>
                                        @foreach($accounts as $acc)
                                        <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($settings->allow_multi_currency)
                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.currency')}}</label>
                                    <select name="currency" id="currency" class="form-select form-select-sm mb-3" ng-model="purchasetemp.currency" ng-change="updatePurchaseTempInfo(purchasetemp)" ng-options="curr.code as curr.code for curr in currencies" required>
                                    </select>
                                </div>
                                <div class="col-sm-2" ng-if="purchasetemp.currency != purchasetemp.defcurr">
                                    <label class="form-label">Exchange Rate Mode</label>
                                    <select name="ex_rate_mode"  class="form-select form-select-sm mb-3" ng-model="purchasetemp.ex_rate_mode">
                                        <option value="Locale" selected>1 @{{purchasetemp.defcurr}} Equals ? @{{purchasetemp.currency}}</option>
                                        <option value="Foreign">1 @{{purchasetemp.currency}} Equals ? @{{purchasetemp.defcurr}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-2" ng-if="purchasetemp.currency != purchasetemp.defcurr && purchasetemp.ex_rate_mode == 'Locale'">
                                    <label class="form-label">Rate Amount in @{{purchasetemp.currency}}</label>
                                    <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="purchasetemp.foreign_ex_rate" ng-blur="updatePurchaseTempInfo(purchasetemp)">
                                </div>
                                <div class="col-sm-2" ng-if="purchasetemp.currency != purchasetemp.defcurr && purchasetemp.ex_rate_mode == 'Foreign'">
                                    <label class="form-label">Rate Amount in @{{purchasetemp.defcurr}}</label>
                                    <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="purchasetemp.local_ex_rate" ng-blur="updatePurchaseTempInfo(purchasetemp)">
                                </div>
                                @endif
                                @endif
                            </div>
                            <div class="row mb-1">
                                <div class="col-sm-8">
                                    <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                    <div class="input-group mb-0">
                                        <input type="text" class="form-control form-control-sm mb-1" id="search_key" placeholder="Search Part" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                        <a class="empty-search" id="button-addon2"><i class='fa fa-close'></i></a>
                                    </div>
                                    <ul id="searchResult3" class="list-group"></ul>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-md-12">
                                    <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th>Category</th>
                                            <th style="text-align: center;">Item</th>
                                            <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_price')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                        <tr ng-repeat="newparttemp in parttempitems" id="temps">
                                            <td>@{{$index + 1}}</td>
                                            <td>@{{newparttemp.category}}</td>
                                            <td>@{{newparttemp.part_no}} @{{newparttemp.part_name}}</td>
                                            <td><input type="number" name="pp_qty" ng-blur="updatePartTemp(newparttemp)" string-to-number ng-model="newparttemp.pp_qty" min="0" step="any" style="text-align:center; height: 20px; width: 60px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                            <td><input type="number" name="unit_price" ng-blur="updatePartTemp(newparttemp)" string-to-number ng-model="newparttemp.unit_price" min="0" step="any" style="text-align:center;height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                            <td><input type="number" name="total_price" ng-blur="updatePartTemp(newparttemp)" string-to-number ng-model="newparttemp.total_price" min="0" step="any" style="text-align:center; height: 20px; width: 120px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                            <td><a href="#" ng-click="removePartTemp(newparttemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: center;"><b>{{trans('navmenu.total')}} </b></th>
                                            <th style="text-align: center;">@{{sumQty(parttempitems)}}</th>
                                            <th></th>
                                            <th style="text-align: center;"><b>@{{sum(parttempitems) | number:2}} (@{{purchasetemp.currency}})</b></th>
                                            <th></th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row mb-1">
                                <input type="hidden" name="" id="no_items" value="@{{selecteditems(parttempitems)}}">
                                <div class="col-sm-6">
                                    <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                    <textarea rows="1" class="form-control form-control-sm mb-3" name="comments" id="comments" ng-model="purchasetemp.comments"></textarea>
                                </div>
                                <div class="col-sm-6 pt-4">
                                    <button onclick="confirmCancel('<?php echo encrypt($purchasetemp->id); ?>')" type="button" class="btn btn-warning btn-sm float-end" style="margin-left: 5px;">{{trans('navmenu.btn_cancel')}}</button>
                                    <button type="submit" name="myButton" id="btn-submit" class="btn btn-success btn-sm float-end">{{trans('navmenu.btn_submit')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
        </div>      
    </div>

    <!-- Modal -->
    <div class="modal fade" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"> New Vendor</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form-validate" method="POST" action="{{route('vendors.store')}}">
                    <div class="modal-body row">
                    @csrf
                        <input type="hidden" name="vendor_for" value="Parts">
                        <div class="col-md-6 pt-2">
                              <label class="form-label">Vendor Name <span style="color: red;">*</span></label>
                              <input id="register-username" type="text" name="name" required placeholder="Please enter vendor name" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6 pt-2">
                              <label class="form-label">Mobile</label>
                              <input id="register-username" type="text" name="phone" placeholder="Please enter vendor mobile number" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6 pt-2">
                              <label class="form-label">Email Address</label>
                              <input id="register-email" type="text" name="email" placeholder="Please enter vendor email address" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6 pt-2">
                              <label class="form-label">Address</label>
                              <input id="address" type="text" name="address" placeholder="Please enter vendor address" class="form-control form-control-sm mb-1">
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

    <div class="modal fade" id="partModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_part')}}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>   
                </div>
                <form class="form-validate" method="POST" action="{{ route('parts.store')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="from-purch" value="1">
                        <div class="col-md-6">
                            <label class="form-label">Part Number <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="part_no" required placeholder="Enter Part Number" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Part Name <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="text" name="part_name" placeholder="Enter Part Name" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Part Category <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="part_category_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Category --</option>
                                @foreach($partcategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Part Locations <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="part_location_id" class="form-select form-select-sm mb-1" required>
                                <option value="">--Select--</option>
                                @foreach($partlocations as $key => $location)
                                <option value="{{ $location->id }}">{{$location->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">UOM </label>
                            <select id="unit" name="uom" class="form-select form-select-sm mb-1" required>
                                <option value="">Select Unit</option>
                                <option>pc</optio<>
                                <option>box</option>
                                <option>set</option>
                                <option>roll</option>
                                <option>gal</option>
                                <option>bottle</option>
                                <option>ft</option>
                                <option>ltr</option>
                                <option>mtr</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description </label>
                            <textarea type="text" name="description" placeholder="Enter Description" class="form-control form-control-sm mb-1"></textarea>
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
                    url:"{{ url('search-part') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#part_list').html(data);
                        var len = response.length;
                        $("#searchResult3").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var code = response[i]['part_no'];
                            var name = response[i]['part_name'];
                            var slug = code+" "+name;
                            var path = "<?php echo asset('storage/parts/'); ?>";
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

            $('.empty-search').on('click', function(){
                console.log('')
                $("#search_key").val('');
                $("#searchResult3").empty();
            });

            $('#btn-submit').on('click', function(e){
                e.preventDefault();
                var vendor = document.getElementById('vendor_id').value;
                var items = document.getElementById('no_items').value;
                if (vendor == '?') {
                    $('#ermsg').append('<div class="alert alert-danger hideit alertSuc">Please select a vendor</div >');
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
            var partid = $(element).val();
            $.ajax({
                url:"{{ url('fetch-part') }}",
                type:'GET',
                data:{'part_id':partid},
                success:function (response) {
                    var item = response;
                    angular.element(document.getElementById('mycontroller')).scope().addPartTemp(item);
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
            var $min = document.querySelector('[name="pp_date"]');
        
            var mind = "<?php echo $settings->sp_mindays; ?>";
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