@extends('layouts.inv')
@section('page-styles')
    <!-- CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
@endsection
@section('content')
    
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-3 border rounded print_invoice">
                        <form class="row g-3"  id="trip-form" method="POST" action="{{ route('trip-logs.store') }}">
                            @csrf
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5>Trip Details</h5>
                                <div class="row g-1">
                                    <!-- <input type="hidden" name="customer_id" id="cust-id" class="form-control form-control-sm mb-1">
                                    <div class="col-sm-6">
                                        <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label> <a href="#" data-bs-toggle="modal" data-bs-target="#customerModal"> <i class="fa fa-user-plus"></i> {{trans('navmenu.new_customer')}}</a>
                                        <input id="search_customer_key" placeholder="Search customer" class="form-control form-control-sm mb-1" autocomplete="off" required>
                                        <ul id="searchResult2"></ul>
                                    </div> -->
                                    <div class="col-sm-6">
                                        <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label> <a href="#" data-bs-toggle="modal" data-bs-target="#customerModal"> <i class="fa fa-user-plus"></i> {{trans('navmenu.new_customer')}}</a>
                                        <select name="customer_id" class="form-select form-select-sm mb-1" required>
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Vehicle <span style="color: red;">*</span> </label><a href="#"  data-bs-toggle="modal" data-bs-target="#deviceModal"><i class="fa fa-plus mr-1" class="float-end"></i> New Vehicle</a>
                                        <select name="device_id" id="device-id" class="form-select form-select-sm mb-1" required>
                                            <option value="">Select Vehicle</option>
                                            @foreach($devices as $device)
                                            <option value="{{$device->id}}">{{$device->device_number}} - {{$device->device_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Driver </label>
                                        <input type="text" name="driver" class="form-control form-control-sm mb-1" placeholder="Enter Driver name" autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label"> Trip Start Date <span style="color: red;">*</span></label>
                                        <div class="inner-addon left-addon"> 
                                            <i class="myaddon fa fa-calendar"></i>
                                            <input type="text" name="trip_date" id="trip-start-date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Trip End Date </label>
                                        <div class="inner-addon left-addon"> 
                                            <i class="myaddon fa fa-calendar"></i>
                                            <input type="text" name="trip_end_date" id="trip-end-date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Trip From <span style="color: red;">*</span></label>
                                        <input type="text" name="from" id="trip-from" class="form-control form-control-sm mb-1" placeholder="Trip From" autocomplete="on" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Trip To </label>
                                        <input type="text" name="to" class="form-control form-control-sm mb-1" placeholder="Trip To" autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Mileage Start (Km) <span style="color: red;">*</span></label>
                                        <input type="number" min="0" step="any" name="mileage_out" id="mileage-out" class="form-control form-control-sm mb-1" placeholder="Mileage Start" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Mileage End (Km) </label>
                                        <input type="number" min="0" step="any" name="mileage_in" class="form-control form-control-sm mb-1" placeholder="Mileage End">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Fuel Start (Ltrs} </label>
                                        <input type="number" min="0" step="any" name="fuel_start" class="form-control form-control-sm mb-1" placeholder="Fuel Start (Ltrs)">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Fuel End (Ltrs} </label>
                                        <input type="number" min="0" step="any" name="fuel_end" class="form-control form-control-sm mb-1" placeholder="Fuel End (Ltrs)">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Fuel Unit Cost </label>
                                        <input type="number" min="0" step="any" name="fuel_unit_cost" class="form-control form-control-sm mb-1" placeholder="Enter Fuel unit cost">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="employee" class="form-label">Trip Description  <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-sm mb-1" name="trip_title" id="trip-title" placeholder="Enter Trip description" id="comments" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Trip Price <span style="color: red;">*</span></label>
                                        <input type="number" min="0" step="any" name="trip_price" id="trip-price" class="form-control form-control-sm mb-1" placeholder="Enter Trip price" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <h5>Container Details</h5>
                                <div class="row g-1">
                                    <div class="col-sm-3">
                                        <label class="form-label">Container No.</label>
                                        <input type="text" name="container_no" class="form-control form-control-sm mb-1" placeholder="Enter Container No." autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Container Size</label>
                                        <input type="text" name="container_size" class="form-control form-control-sm mb-1" placeholder="Enter Container size" autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Billing No.</label>
                                        <input type="text" name="bill_no" class="form-control form-control-sm mb-1" placeholder="Enter Billing Number" autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Shipping</label>
                                        <input type="text" name="shipping" class="form-control form-control-sm mb-1" placeholder="Enter Shipping" autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Gross Weight</label>
                                        <input type="text" name="gross_weight" class="form-control form-control-sm mb-1" placeholder="Enter Container Gross Weight" autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Net Weight</label>
                                        <input type="text" name="net_weight" class="form-control form-control-sm mb-1" placeholder="Enter Container Net Weight" autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Load Type</label>
                                        <input type="text" name="load_type" class="form-control form-control-sm mb-1" placeholder="Enter Load Type" autocomplete="on">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="form-label">Is Transit?</label>
                                        <select name="is_transit" class="form-select form-select-sm mb-1">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-12" id="ermsg"></div>
                                <button type="submit" id="myButton" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
                                <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->


    <div class="modal fade" id="deviceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">New Vehicle</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>   
                </div>
                <div class="modal-body row">
                    <form class="form row g-1" method="POST" action="{{route('devices.store')}}">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Vehicle Plate No.<span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="device_number" required placeholder="{{trans('navmenu.hnt_device_number')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vehicle type/Name. <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="device_name" required placeholder="{{trans('navmenu.hnt_device_name')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vehicle Cost</label>
                            <input id="name" type="text" name="device_cost" placeholder="{{trans('navmenu.hnt_device_cost')}} (Optional)" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <button type="button" class="btn btn-warning btn-sm px-4 radius-30" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Modal -->
    <div class="modal animated zoomIn" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.new_customer')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{url('new-customer')}}">
                        @csrf
                        <div class="row g-1">
                            <div class="col-sm-6">
                                  <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
                                  <input type="text" name="name" required placeholder="{{ trans('navmenu.hnt_customer_name') }}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">{{trans('navmenu.phone_number')}}</label>
                                  <input type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label for="address" class="form-label">Category</label>
                                <select name="customer_category_id" class="form-select form-select-sm mb-1">
                                    <option>--Select--</option>
                                    @foreach($categories as $cat)
                                    <option value="{{$cat->id}}">{{$cat->cat_name}}</option>
                                    @endforeach
                                </select>
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
                            <input type="hidden" name="cust_id_type" value="NIL">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                            </div>         
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
    <!-- datetimepicker jQuery CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function(){
            $("#trip-start-date").each(function () {
                $(this).datetimepicker({
                    // timepicker:false,
                    formatDate:'Y-m-d',
                    // minDate:'-1970/01/02',
                    maxDate:'+1970/01/01'
                });
            });

            $("#trip-end-date").each(function () {
                $(this).datetimepicker({
                    // timepicker:false,
                    formatDate:'Y-m-d',
                    // minDate:'-1970/01/02',
                    maxDate:'+1970/01/01'
                });
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
    </script>
@endsection