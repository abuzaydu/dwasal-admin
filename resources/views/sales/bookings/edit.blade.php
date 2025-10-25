@extends('layouts.app')
@section('page-styles')
  <!-- Application Vendor CSS URL -->
  <link rel="stylesheet" href="{{ asset('side/assets/cssbundle/summernote.min.css') }}">
@endsection
    <script>
        function confirmRemoveRoom(id) {
            Swal.fire({
              title: "Are you sure you want to remove a room from this booking",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Yes, Remove",
              cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
              if (result.value) {
                window.location.href="{{url('remove-room')}}/"+id;
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function confirmRemoveService(id) {
            Swal.fire({
              title: "Are you sure you want to remove this service from this booking",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Yes, Remove",
              cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
              if (result.value) {
                window.location.href="{{url('remove-service')}}/"+id;
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
                    <li class="breadcrumb-item"><a href="{{ url('bookings') }}">Bookings</a></li>
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
                        <form class="form" id="pos-form"  method="POST" action="{{ route('bookings.update', encrypt($booking->id)) }}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="row g-1 mb-1">
                                <input type="hidden" name="booking_id" id="booking-id" value="{{$booking->id}}">
                                <div class="col-md-4">
                                    <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                    <select name="customer_id" class="form-select form-select-sm mb-1">
                                        <option value="{{$customer->id}}">{{$customer->name}}</option>
                                    </select>
                                </div>                                
                                <div class="col-sm-2">
                                    <label class="form-label">Check In Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="check_in_date" id="check_in_date" value="{{$booking->check_in_date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Check Out Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="check_out_date" id="check_out_date" value="{{$booking->check_out_date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Booking Type <span style="color: red;">*</span></label>
                                    <select name="booking_type" id="booking_type" class="form-select form-select-sm mb-1" required>
                                        @if($booking->booking_type == 'Direct')
                                        <option>Direct</option>
                                        <option>Agent</option>
                                        @else
                                        <option>Agent</option>
                                        <option>Direct</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-2" id="agent">
                                    <label class="form-label">Agent </label>
                                    <select class="form-select form-select-sm mb-1" name="booking_agent_id" id="agent_id">
                                        <option value="">--Select Agent--</option>
                                        @foreach($bagents as $agent)
                                        @if($booking->booking_agent_id == $agent->id)
                                        <option value="{{$agent->id}}" selected>{{$agent->name}}</option>
                                        @else
                                        <option value="{{$agent->id}}">{{$agent->name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-select form-select-sm mb-1">
                                        <option>{{$booking->status}}</option>
                                    </select>
                                </div>
                                @if($settings->allow_multi_currency)
                                <div class="col-sm-2">
                                    <label class="form-label">{{trans('navmenu.currency')}}</label>
                                    <select name="currency" id="currency" class="form-select form-select-sm mb-1" required>
                                        @foreach($currencies as $curr)
                                        @if($curr->code == $booking->currency)
                                        <option selected>{{$curr->code}}</option>
                                        @else
                                        <option>{{$curr->code}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                @if($booking->currency != $booking->defcurr)
                                <div class="col-sm-3">
                                    <label class="form-label">Ex Rate Mode</label>
                                    <select name="ex_rate_mode" id="ex_rate_mode" class="form-select form-select-sm mb-1">
                                        @if($booking->ex_rate_mode == 'Locale')
                                        <option value="Locale">1 {{$booking->defcurr}} Equals ? {{$booking->currency}}</option>
                                        <option value="Foreign">1 {{$booking->currency}} Equals ? {{$booking->defcurr}}</option>
                                        @else
                                        <option value="Foreign">1 {{$booking->currency}} Equals ? {{$booking->defcurr}}</option>
                                        <option value="Locale">1 {{$booking->defcurr}} Equals ? {{$booking->currency}}</option>
                                        @endif
                                    </select>
                                </div>
                                @if($booking->ex_rate_mode == 'Locale')
                                <div class="col-sm-2">
                                    <label class="form-label">Rate in {{$booking->currency}}</label>
                                    <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" value="{{$booking->foreign_ex_rate+0}}" class="form-control form-control-sm mb-1">
                                </div>
                                @else
                                <div class="col-sm-2">
                                    <label class="form-label">Rate in {{$booking->defcurr}}</label>
                                    <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" value="{{$booking->local_ex_rate+0}}" class="form-control form-control-sm mb-1">
                                </div>
                                @endif
                                @endif
                                @endif

                                <div class="col-sm-2 pt-4">
                                    <a href="javascript:;" id="update-btn" class="btn btn-secondary btn-sm"><i class="fa fa-refresh"></i> Save Changes</a>
                                </div>
                            </div>
                            <div class="row g-1">
                                <hr>
                                <div class="col-md-12" id="msg"></div>
                                <div class="col-md-4" style="overflow: auto;">
                                    <label class="form-label">Booked Rooms</label>
                                    <select class="form-select form-select-sm mb-1" name="room_id" id="room-id">
                                        <option value="">--Select Room--</option>
                                        @foreach($rooms as $room)
                                        <option value="{{$room->id}}">{{$room->room_no}}  {{$room->name}}</option>
                                        @endforeach
                                    </select>
                                    <table class="items mt-0" style="width: 100%;">
                                        <tr>
                                            <th>#</th>
                                            <th>Room</th>
                                            <th>Room Type</th>
                                            <!-- <th style="text-align: center;">Capacity</th> -->
                                            <th>&nbsp;</th>
                                        </tr>
                                        @foreach($bookedrooms as $index => $broom)
                                        <tr>
                                            <td style="text-align: center;">{{$index + 1}}</td>
                                            <td>{{$broom->room_no}} @if(!is_null($broom->name))- {{$broom->name}} @endif</td>
                                            <td>{{$broom->type}}</td>
                                            <!-- <td style="text-align: center;">{{$broom->capacity}}</td> -->
                                            <td style="text-align: center;"><a href="#" onclick="confirmRemoveRoom('<?php echo encrypt($broom->id); ?>')"><span class="fa fa-close" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="col-md-8" style="overflow: auto;">
                                    <label class="form-label">{{trans('navmenu.services')}}</label>
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
                                            <th style="text-align: center;">No. Persons/Rooms</th>
                                            <th style="text-align: center;">No. Nights</th>
                                            <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}} ({{$booking->currency}})</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        <?php $total = 0; ?>
                                        @foreach($bookedservices as $index => $bservice)
                                        <?php $total += $bservice->total; ?>
                                        <tr>
                                            <td style="text-align: center;">{{$index + 1}}</td>
                                            <td>@if(!is_null($bservice->code)) {{$bservice->code}} - @endif{{$bservice->name}}</td>
                                            <td style="text-align: center;">
                                                <input class="edit" id="persons_{{$bservice->id}}" type="number" name="persons" value="{{$bservice->persons}}" style="text-align: center; width: 70px;">
                                            </td>
                                            <td style="text-align: center;">
                                                <input class="edit" id="nights_{{$bservice->id}}" type="number" name="nights" value="{{$bservice->nights}}" style="text-align: center; width: 70px;">
                                            </td>
                                            <td style="text-align: center;">
                                                <input class="edit" id="price_{{$bservice->id}}" type="number" name="price" value="{{ round($bservice->price/$ex_rate, 2) }}" style="text-align: center; width: 70px;">
                                            </td>
                                            <td style="text-align: right;">{{ number_format($bservice->total/$ex_rate, 2, '.', ',') }}</td>
                                            <td style="text-align: center;"><a href="#" onclick="confirmRemoveService('<?php echo encrypt($bservice->id); ?>')"><span class="fa fa-close" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr style="border-top: 1px solid gray;">
                                            <td style="text-align: center;"></td>
                                            <td><b>Total</b></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: right;"><b>{{number_format($total/$ex_rate, 2, '.', ',')}}</b></td>
                                            <td style="text-align: center;">
                                                
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div class="row g-2 pt-3">
                                <div class="col-sm-12" id="ermsg"></div>
                                <div class="col-xl-12 col-md-12 col-sm-12">
                                    <label for="employee" class="form-label">{{trans('navmenu.comments')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-1" name="comments" id="comments" value="{{$booking->comments}}">
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12">
                                    <label for="employee" class="form-label">Remarks</label>
                                    <textarea class="form-control form-control-sm mb-1" name="remarks" id="remarks">{!! $booking->remarks !!}</textarea>
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 pt-4">
                                    <button type="submit" id="btn-submit-inv" class="btn btn-primary btn-sm">Confirm Booking</button>
                                    <a href="{{ url('bookings') }}" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
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

@section('page-scripts')
    <script src="{{ asset('side/assets/js/bundle/summernote.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#remarks').summernote({
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

            $('#currency').on('change',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var currency = $(this).val();
                var bookingid = $('#booking-id').val();
                $.ajax({
                    url:"{{ url('change-currency') }}",
                    type:'POST',
                    data:{ currency: currency, booking_id: bookingid},
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

            $('#ex_rate_mode').on('change',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var ex_rate_mode = $(this).val();
                var bookingid = $('#booking-id').val();
                $.ajax({
                    url:"{{ url('change-rate-mode') }}",
                    type:'POST',
                    data:{ ex_rate_mode: ex_rate_mode, booking_id: bookingid},
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

            $('#foreign-ex-rate').on('blur',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var foreignRate = $(this).val();
                var bookingid = $('#booking-id').val();
                $.ajax({
                    url:"{{ url('update-foreign-ex-rate') }}",
                    type:'POST',
                    data:{ foreign_ex_rate: foreignRate, booking_id: bookingid},
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

            $('#local-ex-rate').on('blur',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var localRate = $(this).val();
                var bookingid = $('#booking-id').val();
                $.ajax({
                    url:"{{ url('update-local-ex-rate') }}",
                    type:'POST',
                    data:{ local_ex_rate: localRate, booking_id: bookingid},
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

            $('#room-id').on('change',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var id = $(this).val();
                var bookingid = $('#booking-id').val();
                $.ajax({
                    url:"{{ url('add-selected-room') }}",
                    type:'POST',
                    data:{ room_id: id, booking_id: bookingid},
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
                var bookingid = $('#booking-id').val();
                $.ajax({
                    url:"{{ url('add-selected-service') }}",
                    type:'POST',
                    data:{ service_id: id, booking_id: bookingid},
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
                    url: "{{ url('update-selected-service') }}",
                    type: 'POST',
                    data: { value: value, field_name: field_name, id:edit_id },
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

            $('#btn-submit-inv').on('click', function(e) {
                e.preventDefault();

                var rooms = "<?php echo $bookedrooms->count(); ?>";
                var services = "<?php echo $bookedservices->count(); ?>";

                if (rooms > 0 && services > 0) {
                    $('#pos-form').submit();
                }else{
                    $('#ermsg').append('<div class="alert alert-danger hideit alertSuc">Please select at least one room and one service to continue</div >');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();
                            // location.reload();
                            
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