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
                        <form class="row g-3"  name="saleform" method="POST" action="{{ route('trip-logs.update', encrypt($trip->id)) }}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-lg-8 col-md-8 col-sm-12">
                                <h5>Trip Details</h5>
                                <div class="row g-1">
                                    <div class="col-sm-12" id="ermsg"></div>
                                    <?php
                                    $custname = '';
                                    $custid = '';
                                    if (!is_null($trip->customer_id)) {
                                         $customer = App\Models\Customer::find($trip->customer_id);
                                         $custname = $customer->name;
                                         $custid = $customer->id;
                                     } ?>
                                    <input type="hidden" name="customer_id" id="cust-id" value="{{$custid}}" class="form-control form-control-sm mb-1">
                                    <div class="col-sm-6">
                                        <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                        <input id="search_customer_key" placeholder="Search customer" value="{{$custname}}" class="form-control form-control-sm mb-1" autocomplete="off" required>
                                        <ul id="searchResult2"></ul>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Vehicle <span style="color: red;">*</span></label>
                                        <select name="device_id" class="form-select form-select-sm mb-1" required>
                                            <option value="">{{trans('navmenu.select_device')}}</option>
                                            @foreach($devices as $device)
                                            @if($trip->device_id == $device->id)
                                            <option value="{{$device->id}}" selected>{{$device->device_number}} - {{$device->device_name}}</option>
                                            @else
                                            <option value="{{$device->id}}">{{$device->device_number}} - {{$device->device_name}}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label"> Trip Start Date <span style="color: red;">*</span></label>
                                        <div class="inner-addon left-addon"> 
                                            <i class="myaddon fa fa-calendar"></i>
                                            <input type="text" name="trip_date" id="trip-start-date" value="{{$trip->trip_date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Trip End Date </label>
                                        <div class="inner-addon left-addon"> 
                                            <i class="myaddon fa fa-calendar"></i>
                                            <input type="text" name="trip_end_date" id="trip-end-date" value="{{$trip->trip_end_date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Trip From <span style="color: red;">*</span></label>
                                        <input type="text" name="from" value="{{$trip->from}}" class="form-control form-control-sm mb-1" placeholder="Trip From" autocomplete="off" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Trip To </label>
                                        <input type="text" name="to" value="{{$trip->to}}" class="form-control form-control-sm mb-1" placeholder="Trip To" autocomplete="off">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Mieage Start (Km) <span style="color: red;">*</span></label>
                                        <input type="number" min="0" step="any" name="mileage_out" value="{{$trip->mileage_out}}" class="form-control form-control-sm mb-1" placeholder="Mileage Out">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Mileage End (Km} </label>
                                        <input type="number" min="0" step="any" name="mileage_in" value="{{$trip->mileage_in}}" class="form-control form-control-sm mb-1" placeholder="Mileage In">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Fuel Start (Ltrs} </label>
                                        <input type="number" min="0" step="any" name="fuel_start" value="{{$trip->fuel_start}}" class="form-control form-control-sm mb-1" placeholder="Fuel Start (Ltrs)">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Fuel End (Ltrs} </label>
                                        <input type="number" min="0" step="any" name="fuel_end" value="{{$trip->fuel_end}}" class="form-control form-control-sm mb-1" placeholder="Fuel End (Ltrs)">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Fuel Unit Cost </label>
                                        <input type="number" min="0" step="any" name="fuel_unit_cost" value="{{$trip->fuel_unit_cost}}" class="form-control form-control-sm mb-1" placeholder="Enter Fuel unit cost">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Driver </label>
                                        <input type="text" name="driver" value="{{$trip->driver}}" class="form-control form-control-sm mb-1" placeholder="Enter Driver name" autocomplete="on">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="employee" class="form-label">Trip Description <span style="color: red;">*</span></label>
                                        <input type="text" class="form-control form-control-sm mb-1" name="trip_title" value="{{$trip->trip_title}}" placeholder="Enter Trip description" id="comments" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Trip Price <span style="color: red;">*</span></label>
                                        <input type="number" min="0" step="any" name="trip_price" value="{{$trip->trip_price}}" class="form-control form-control-sm mb-1" placeholder="Enter Trip price" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <h5>Container Details</h5>
                                <div class="row g-1">
                                    <div class="col-sm-12">
                                        <label class="form-label">Container No.</label>
                                        <input type="text" name="container_no" value="{{$trip->container_no}}" class="form-control form-control-sm mb-1" placeholder="Enter Container No." autocomplete="on">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Container Size</label>
                                        <input type="text" name="container_size" value="{{$trip->container_size}}" class="form-control form-control-sm mb-1" placeholder="Enter Container size" autocomplete="on">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Billing No.</label>
                                        <input type="text" name="bill_no" value="{{$trip->bill_no}}" class="form-control form-control-sm mb-1" placeholder="Enter Billing Number" autocomplete="on">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Shipping</label>
                                        <input type="text" name="shipping" value="{{$trip->shipping}}" class="form-control form-control-sm mb-1" placeholder="Enter Shipping" autocomplete="on">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Gross Weight</label>
                                        <input type="text" name="gross_weight" value="{{$trip->gross_weight}}" class="form-control form-control-sm mb-1" placeholder="Enter Container Gross Weight" autocomplete="on">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Net Weight</label>
                                        <input type="text" name="net_weight" value="{{$trip->net_weight}}" class="form-control form-control-sm mb-1" placeholder="Enter Container Net Weight" autocomplete="on">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Load Type</label>
                                        <input type="text" name="load_type" value="{{$trip->load_type}}" class="form-control form-control-sm mb-1" placeholder="Enter Load Type" autocomplete="on">
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label">Is Transit?</label>
                                        <select name="is_transit" class="form-select form-select-sm mb-1">
                                            @if($trip->is_transit)
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                            @else
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                            @endif
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" name="myButton" class="btn btn-success btn-sm">Update</button>
                                <a href="{{ url('trip-logs') }}" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection

@section('page-scripts')
    <!-- datetimepicker jQuery CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

    <script type="text/javascript">

        $(document).ready(function(){
            $("#trip-start-date").each(function () {
                $(this).datetimepicker();
            });

            $("#trip-end-date").each(function () {
                $(this).datetimepicker();
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

            $('#myButton').on('click', function(e) {
                e.preventDefault();
                var custId = $('#cust-id').val();
                    alert('');
                if (custId == '') {
                    $('#ermsg').append('<div class="alert alert-danger hideit alertSuc">Please select a Customer</div>');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();
                        });
                    }, 1300);
                }
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