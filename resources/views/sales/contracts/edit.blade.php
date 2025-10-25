@extends('layouts.app')
    <script>
        function confirmRemoveRoom(id) {
            Swal.fire({
              title: "Are you sure you want to remove a room from this contract",
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
              title: "Are you sure you want to remove this service from this contract",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Yes, Remove",
              cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
              if (result.value) {
                window.location.href="{{url('remove-contract-service')}}/"+id;
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
                    <li class="breadcrumb-item"><a href="{{ url('contracts') }}">Contracts</a></li>
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
                        <form class="form" id="pos-form"  method="POST" action="{{ route('contracts.update', encrypt($contract->id)) }}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="row g-1 mb-1">
                                <input type="hidden" name="contract_id" id="contract-id" value="{{$contract->id}}">
                                <div class="col-md-3">
                                    <label for="customer_id" class="form-label">Rider <span style="color: red;">*</span></label>
                                    <select name="customer_id" class="form-select form-select-sm mb-1">
                                        <option value="{{$customer->id}}">{{$customer->name}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">TL Name</label>
                                    <select class="form-select form-select-sm mb-1" name="tl_name" id="tl_name" required>
                                        <option>{{$contract->tl_name}}</option>
                                        @foreach($users as $key => $user)
                                        <option>{{$user->first_name}} {{$user->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Motorcycle</label>
                                    <select class="form-select form-select-sm mb-1" name="device_id" id="device_id" required>
                                        <option value="">Select Motorcycle</option>
                                        <option value="{{$currdevice->id}}" selected>{{$currdevice->device_number}} - {{$currdevice->device_name}}</option>
                                        @foreach($devices as $key => $device)
                                        @if($contract->device_id == $device->id)
                                        @else
                                        <option value="{{$device->id}}">{{$device->device_number}} - {{$device->device_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Contract Type </label>
                                    <select class="form-select form-select-sm mb-1" name="type" required>
                                        @if($contract->type == 'New')
                                        <option>New</option>
                                        <option>Replacement</option>
                                        @else
                                        <option>Replacement</option>
                                        <option>New</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Start Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="start_date" value="{{$contract->start_date}}" id="start_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Contract Period (in Days)</label>
                                    <input type="number" name="period" id="period" value="{{$period}}" class="form-control form-control-sm mb-1" placeholder="Enter Expected number of days" required>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">End Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="end_date" value="{{$contract->end_date}}" id="end_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2 pt-4">
                                    <a href="javascript:;" id="update-btn" class="btn btn-secondary btn-sm"><i class="fa fa-refresh"></i> Save Changes</a>
                                </div>
                            </div>
                            <div class="row g-1">
                                <hr>
                                <div class="col-md-12" id="msg"></div>
                                <div class="col-md-6">
                                    <label class="form-label">Main Service</label>
                                    <select class="form-select form-select-sm mb-1" id="service-id">
                                        <option value="">--Select Service--</option>
                                        @foreach($services as $service)
                                        <option value="{{$service->service_id}}">@if(!is_null($service->code)) {{$service->code}} - @endif{{$service->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Additional Service</label>
                                    <select class="form-select form-select-sm mb-1" id="add-on-service-id">
                                        <option value="">--Select Service--</option>
                                        @foreach($services as $service)
                                        <option value="{{$service->service_id}}">@if(!is_null($service->code)) {{$service->code}} - @endif{{$service->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12" style="overflow: auto;">
                                    <table class="items mt-0" style="width: 100%;">
                                        <tr>
                                            <th>#</th>
                                            <th>Service</th>
                                            <th style="text-align: center;">Qty/Days</th>
                                            <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                            <th style="text-align: right;">{{trans('navmenu.total')}}</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        <?php $total = 0; ?>
                                        @foreach($contractservices as $index => $bservice)
                                        <?php $total += $bservice->total; ?>
                                        <tr>
                                            <td style="text-align: center;">{{$index + 1}}</td>
                                            <td>@if(!is_null($bservice->code)) {{$bservice->code}} - @endif{{$bservice->name}}</td>
                                            <td style="text-align: center;">
                                                <input class="edit" id="qty_{{$bservice->id}}" type="number" name="qty" value="{{$bservice->qty}}" style="text-align: center; width: 70px;">
                                            </td>
                                            <td style="text-align: center;">
                                            {{number_format($bservice->unit_price, 2, '.', ',')}}
                                            </td>
                                            <td style="text-align: right;">{{number_format($bservice->total, 2, '.', ',')}}</td>
                                            <td style="text-align: center;"><a href="#" onclick="confirmRemoveService('<?php echo encrypt($bservice->id); ?>')"><span class="fa fa-close" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr style="border-top: 1px solid gray;">
                                            <td style="text-align: center;"></td>
                                            <td><b>Total</b></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: center;"></td>
                                            <td style="text-align: right;"><b>{{number_format($total, 2, '.', ',')}}</b></td>
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
                                    <input type="text" class="form-control form-control-sm mb-1" name="comments" id="comments" value="{{$contract->comments}}" placeholder="Enter additional comments on contract (optional)">
                                </div>
                                <div class="col-xl-12 col-md-12 col-sm-12 pt-4">
                                    <button type="submit" id="btn-submit-inv" class="btn btn-primary btn-sm">Confirm Contract</button>
                                    <a href="{{ url('contracts') }}" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
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
            $('#update-btn').on('click',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var contractid = $('#contract-id').val();
                var indate = $('#start_date').val();
                var period = $('#period').val();
                var tlname = $('#tl_name').val();
                var deviceid = $('#device_id').val();
                $.ajax({
                    url:"{{ url('save-contract-changes') }}",
                    type:'POST',
                    data:{ contract_id: contractid, start_date: indate, period: period, tl_name: tlname, device_id: deviceid},
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
                var contractid = $('#contract-id').val();
                $.ajax({
                    url:"{{ url('add-contract-service') }}",
                    type:'POST',
                    data:{ service_id: id, contract_id: contractid, is_add_on: 0},
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

            $('#add-on-service-id').on('change',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var id = $(this).val();
                var contractid = $('#contract-id').val();
                $.ajax({
                    url:"{{ url('add-contract-service') }}",
                    type:'POST',
                    data:{ service_id: id, contract_id: contractid, is_add_on: 1},
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
                    url: "{{ url('update-contract-service') }}",
                    type: 'POST',
                    data: { qty: value, id:edit_id },
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

                var services = "<?php echo $contractservices->count(); ?>";

                if (services > 0) {
                    $('#pos-form').submit();
                }else{
                    $('#ermsg').append('<div class="alert alert-danger hideit alertSuc">Please select at least one service to continue</div >');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();
                            // location.reload();
                            
                        });
                    }, 1300);
                }
            });
        });
    </script>   

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="start_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd'
            });
        });
    </script>