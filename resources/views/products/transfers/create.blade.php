@extends('layouts.inv')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/transorder.js')}}"></script>
<script type="text/javascript">
    
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#sale_date").val('');
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
            <div class="col-lg-8 col-md-8 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{url('transfer-orders')}}">{{trans('navmenu.stock_transfer')}}</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">
                <div class="btn-group float-end" role="group">
                    <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingtemps->count()}}</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Your Pending STO's <i class="fa fa-caret-down"></i></button>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                        @foreach($pendingtemps as $key => $temp) 
                        <form class="row g-3" method="POST" action="{{ url('pt-sto') }}" id="ptemp-form-{{$key}}">
                            @csrf
                            <input type="hidden" name="temp_id" value="{{$temp->id}}">
                            <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">From {{\App\Models\Shop::find($temp->shop_id)->name}} To @if($temp->destination_id) {{\App\Models\Shop::find($temp->destination_id)->name}} @else Not Selected @endif <br>(<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                        </form>  
                        @endforeach
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
                <div class="card-body" style="overflow: auto;">
                    <div class="p-4 border rounded">
                        <form class="row g-1 needs-validation" novalidate name="orderform" method="POST" action="{{route('transfer-orders.store')}}" onsubmit="return validateform(this)">
                            @csrf
                            <input type="hidden" name="order_temp_id" value="{{$ordertemp->id}}">
                            <input type="hidden" name="transfer_type" ng-model="ordertemp.transfer_type" value="0">
                            <input type="hidden" name="is_request" value="0">
                            <div class="col-sm-4">
                                <label for="shop_id" class="form-label">
                                    {{trans('navmenu.source_shop')}} <span style="color: red; font-weight: bold;">*</span>
                                </label>
                                <select name="shop_id" class="form-select form-select-sm mb-1">
                                    <option value="{{$shop->id}}">{{$shop->name}}</option>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="destin_id" class="form-label">
                                    {{trans('navmenu.destin_shop')}}<span style="color: red; font-weight: bold;">*</span>
                                </label>
                                <select class="form-select form-select-sm mb-1" name="destin_id" ng-model="ordertemp.destination_id" ng-change="updateTransferOrderTemp(ordertemp)" ng-options="destin.id as destin.name for destin in destinations" required>
                                    <option value="">{{trans('navmenu.select_destin_shop')}}</option>
                                </select>
                            </div> 
                            <div class="col-sm-6">
                                <label class="form-label">{{trans('navmenu.search_tap')}}</label>
                                <div class="input-group mb-0">
                                    <input type="text" class="form-control form-control-sm mb-1" id="search_key" placeholder="{{trans('navmenu.search_product')}}" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                    <a class="btn btn-outline-danger btn-sm" id="empty-search"><i class='fa fa-close'></i></a>
                                </div>
                                <ul id="searchResult3" class="list-group"></ul>
                            </div>
                            <div class="col-sm-12 pt-3" style="margin-top: 5px; border-top: 2px solid #BBDEFB;">
                                <table border="0" cellspacing="0" cellpadding="0" class="table" style="width: 100%; display: block; overflow: scroll; overflow: auto; white-space: nowrap;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Code</th>
                                            <th class="Item">{{trans('navmenu.item_name')}}</th>
                                            <th class="source">{{trans('navmenu.source_stock')}}</th>
                                            <th class="destin">{{trans('navmenu.destin_stock')}}</th>
                                            <th class="qty">{{trans('navmenu.transfer_qty')}}</th>
                                            <th class="qty">{{trans('navmenu.source_unit_cost')}}</th>
                                            <th class="qty">{{trans('navmenu.destin_unit_cost')}}</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="neworderitemtemp in orderitemtemp" id="temps">
                                            <td>@{{$index + 1}}</td>
                                            <td>@{{neworderitemtemp.product_code}}</td>
                                            <td class="item">@{{neworderitemtemp.slug}}</td>
                                            <td class="source" style="text-align: center;">@{{neworderitemtemp.source_stock | number:0}}</td>
                                            <td class="destin" style="text-align: center;">@{{neworderitemtemp.destin_stock | number:0}}</td>
                                            <td class="qty">
                                                <input type="number" style="text-align: center; height: 20px;" ng-blur="updateOrderItemTemp(neworderitemtemp)" string-to-number ng-model="neworderitemtemp.quantity" min="0" step="any" value="@{{neworderitemtemp.quantity}}">
                                            </td>
                                            <td class="qty" style="text-align: center;">@{{neworderitemtemp.source_unit_cost}}</td>
                                            <td class="qty">
                                                <input type="number" style="text-align: center; height: 20px;" ng-blur="updateOrderItemTemp(neworderitemtemp)" string-to-number ng-model="neworderitemtemp.destin_unit_cost" min="0" step="any" value="@{{neworderitemtemp.destin_unit_cost}}">
                                            </td>
                                            <td><a href="#" ng-click="removeOrderItemTemp(neworderitemtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-sm-2">
                                <label for="date_set" class="form-label">{{trans('navmenu.date')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i> 
                                    <input type="text" name="order_date" ng-model="ordertemp.order_date" ng-change="updateTransferOrderTemp(ordertemp)" id="datepicker" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.pick_date')}}"  aria-describedby="calendar">
                                </div>
                            </div>
                            @if($settings->is_vat_registered)
                            <div class="col-sm-2">
                                <label class="form-label">Add VAT</label>
                                <select class="form-select form-select-sm mb-1" name="add_vat" >
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            @else
                            <input type="hidden" name="add_vat" value="0">
                            @endif
                            <div class="col-sm-4">
                                <label for="reason" class="form-label">{{trans('navmenu.reason')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <textarea name="reason" rows="1" class="form-control form-control-sm mb-1" ng-model="ordertemp.reason" ng-blur="updateTransferOrderTemp(ordertemp)" required placeholder="{{trans('navmenu.hnt_transfer_reason')}}"></textarea>
                            </div>
                            <div class="col-sm-12">  
                                <button type="submit" class="btn btn-success btn-sm mb-1"><i class="fa fa-user-plus" ></i>{{trans('navmenu.create_order')}}</button>
                                <a href="{{url('cancel-temp-order/'.encrypt($ordertemp->id))}}" class="btn btn-warning btn-sm mr-1 card-subtitle" id="btn-cancel"><i class="fa fa-cancle"></i>{{trans('navmenu.cancel_order')}}</a>
                            </div>
                        </form>
                    </div>  
                </div>
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
                            var path = "<?php echo asset('storage/products/'); ?>";
                            var img = response[i]['img'];
                            var img_path = path+'/'+img;
                            // console.log(img_path);
                            var qty = +response[i]['in_stock'];
                            if (qty > 0) {
                                if (img != null) {
                                    $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><img src='"+img_path+"' width='60'>"+slug+"<span class='badge bg-primary rounded-pill'> ("+(qty+0)+")  <i class='fa fa-arrow-right' aria-hidden='true'></i></span></li>");
                                }else{
                                    $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'>"+slug+"<span class='badge bg-primary rounded-pill'> ("+(qty+0)+")  <i class='fa fa-arrow-right' aria-hidden='true'></i></span></li>");
                                }
                            }else{
                                if (img != null) {
                                    $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><img src='"+img_path+"' width='60'>"+slug+"<span class='badge bg-danger rounded-pill'> ("+(qty+0)+")  <i class='fa fa-arrow-right' aria-hidden='true'></i></span></li>");
                                }else{
                                    $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'>"+slug+"<span class='badge bg-danger rounded-pill'> ("+(qty+0)+")  <i class='fa fa-arrow-right' aria-hidden='true'></i></span></li>");
                                }
                            }
                        }
                        
                        // binding click event to li
                        $("#searchResult3 li").bind("click",function(){
                            addOrderItemTemp(this);
                        });

                    }
                })
            });

            $('#empty-search').on('click', function(){
                $("#search_key").val('');
                $("#searchResult3").empty();
            });
        });

        function addOrderItemTemp(element) {
            var value = $(element).text();
            var productid = $(element).val();
            $.ajax({
                url:"{{ url('fetch-product') }}",
                type:'GET',
                data:{'product_id':productid},
                success:function (response) {
                    var item = response;
                    angular.element(document.getElementById('mycontroller')).scope().addOrderItemTemp(item);
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
            var $min = document.querySelector('[name="order_date"]')

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>
