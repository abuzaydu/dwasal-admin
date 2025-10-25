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
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="saleOrderId('<?php echo $saleorder->id; ?>')">
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">Order Items</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-3 border rounded">
                        <form class="row g-3"  name="saleform" method="POST" action="{{ url('confirm-packaged') }}" onsubmit="return validateform(this)" ng-if="saleorder">
                            @csrf
                            <input type="hidden" name="id" placeholder="" value="{{$saleorder->id}}" class="form-control form-control-sm mb-3">
                            <div class="col-sm-6">
                                <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                <select name="customer_id" id="customer_id" required class="form-select form-select-sm mb-3" ng-model="saleorder.customer_id" ng-change="updateSaleOrderInfo(saleorder)" ng-options="customer.id as customer.name for customer in customers" disabled>
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                </select>
                            </div>
                            
                            <div class="col-sm-12">
                                <table class="table table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th>#</th>
                                        <th style="text-align: center;">{{trans('navmenu.item_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">Quantity Packed</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit')}}</th>
                                    </tr>
                                    <tr ng-repeat="newsaleorder in saleorderitems" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newsaleorder.name}}</td>
                                        <td style="text-align: center;">@{{newsaleorder.quantity | number:0}}</td>
                                        <td><input type="number" style="text-align:center; height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off" name="quantity_packed" ng-blur="updateSaleOrder(newsaleorder)" string-to-number ng-model="newsaleorder.quantity_packed" min="0" step="any"></td>
                                        <td>
                                            <select ng-model="newsaleorder.product_unit_id" name="product_unit_id" ng-change="updateSaleOrder(newsaleorder)" ng-options="unit.id as unit.unit_name for unit in newsaleorder.units" style="width: 70px;" disabled>
                                                
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4" style="margin-top: 5px;">
                                        <button type="submit" name="myButton" class="btn btn-success btn-sm">Confirm Packaged</button>
                                    </div>
                                        
                                    <div class="col-sm-4" style="margin-top: 5px;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
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