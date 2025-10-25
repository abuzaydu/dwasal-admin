@extends('layouts.app')
    <script>


        function confirmRemoveService(id) {
            Swal.fire({
              title: "Are you sure you want to remove this rate from this Agent",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Yes, Remove",
              cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
              if (result.value) {
                window.location.href="{{url('remove-rate')}}/"+id;
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }
    </script> 
@section('content')
    <!--breadcrumb-->
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('booking-agents') }}">Booking Agents</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" id="mycontroller" ng-controller="SearchItemCtrl">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-3 border rounded print_invoice">
                        <div class="row g-1 mb-1">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Agent Name</td>
                                        <th>{{$bagent->name}}</th>
                                        <td>Mobile No</td>
                                        <th>{{$bagent->mobile}}</th>
                                    </tr>
                                    <tr>
                                        <td>Email Address</td>
                                        <th>{{$bagent->email}}</th>
                                        <td>Address</td>
                                        <th>{{$bagent->address}}</th>
                                    </tr>
                                    <tr>
                                        <td>Agent TIN</td>
                                        <th>{{$bagent->tin}}</th>
                                        <td>Agent VRN</td>
                                        <th>{{$bagent->vrn}}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row g-1">
                            <hr>
                            <div class="col-md-12" id="msg"></div>
                            <input type="hidden" id="agent-id" value="{{$bagent->id}}">
                            <div class="col-md-7" style="overflow: auto;">
                                <label class="form-label">{{trans('navmenu.services')}} Rates</label>
                                <select class="form-select form-select-sm mb-1" id="service-id">
                                    <option value="">--Select Service--</option>
                                    @foreach($services as $service)
                                    <option value="{{$service->service_id}}">@if(!is_null($service->code)) {{$service->code}} - @endif{{$service->name}}</option>
                                    @endforeach
                                </select>
                                <table class="items mt-0" style="width: 100%;">
                                    <tr>
                                        <th>#</th>
                                        <th>Service</th>
                                        <th>Unit</th>
                                        <th style="text-align: center;">Rate ({{$bagent->currency}})</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    @foreach($agentrates as $index => $rate)
                                    <tr>
                                        <td style="text-align: center;">{{$index + 1}}</td>
                                        <td>@if(!is_null($rate->code)) {{$rate->code}} - @endif{{$rate->name}}</td>
                                        <td>1 Night/Person</td>
                                        <td style="text-align: center;">
                                            <input class="edit" id="price_{{$rate->id}}" type="number" name="price" value="{{$rate->price}}" style="text-align: center; width: 70px;">
                                        </td>
                                        <td style="text-align: center;"><a href="#" onclick="confirmRemoveRate('<?php echo encrypt($rate->id); ?>')"><span class="fa fa-close" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
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
            $('#update-btn').on('click',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var bookingid = $('#booking-id').val();
                var indate = $('#check_in_date').val();
                var outdate = $('#check_out_date').val();
                var btype = $('#booking_type').val();
                var agentid = $('#agent_id').val();
                $.ajax({
                    url:"{{ url('save-booking-changes') }}",
                    type:'POST',
                    data:{ booking_id: bookingid, check_in_date: indate, check_out_date: outdate, booking_type: btype, booking_agent_id: agentid},
                    success:function (response) {
                        if(response.success == 1){
                            $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                    window.location.reload();
                                });
                            }, 1300);
                        }else{
                            $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                    // location.reload();
                                    
                                });
                            }, 1300);
                        }
                    }
                })
            });

            $('#service-id').on('change',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var id = $(this).val();
                var agentid = $('#agent-id').val();
                $.ajax({
                    url:"{{ url('add-agent-rate') }}",
                    type:'POST',
                    data:{ service_id: id, booking_agent_id: agentid},
                    success:function (response) {
                        if(response.success == 1){
                            $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                    window.location.reload();
                                });
                            }, 1300);
                        }else{
                            $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                    // location.reload();
                                    
                                });
                            }, 1300);
                        }
                    }
                })
            });

            $(".edit").focusout(function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                // $(this).removeClass("editMode");
                var id = this.id;
                var split_id = id.split("_");
                var field_name = split_id[0];
                var edit_id = split_id[1];
                var value = $(this).val();

                $.ajax({
                    url: "{{ url('update-agent-rate') }}",
                    type: 'POST',
                    data: { price: value, id:edit_id },
                    success:function(response){
                        if(response.success == 1){
                            $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                    window.location.reload();
                                });
                            }, 1300);
                        }else{
                            $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div >');
                            setTimeout(function() {
                                $('.hideit').fadeOut('slow', function() {
                                    $(this).remove();
                                    // location.reload();
                                    
                                });
                            }, 1300);
                        }
                    }
                });
            });
        });

        function setSelectedCustomer(element) {
            var value = $(element).text();
            var custId = $(element).val();
            $('#cust-id').val(custId); 
            $("#search_customer_key").val(value);
            $("#searchResult2").empty();
            saveUpdate();
        }

        function addServSaleTemp(element) {
            var value = $(element).text();
            var serv_id = $(element).val();
            angular.element(document.getElementById('mycontroller')).scope().addSaleTemp(serv_id);
            setTimeout(function(){
                $("#search_serv_key").val('');
                $("#searchServiceResult").empty();
            })
        }
    </script>   


<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="check_in_date"]');
            var $max = document.querySelector('[name="check_out_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd'
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min
            });
        });
    </script>