@extends('layouts.app')
    <script>
        
        function confirmDelete(){
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
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Ouotes & Orders</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>         
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body p-0">
                    <input type="hidden" name="delivery_id" id="delivery-id" value="{{$delivery->id}}">
                    <div class="p-4 border rounded p-0">
                        <form class="form row g-1" method="POST" action="{{ route('order-deliveries.update', encrypt($delivery->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label class="form-label">Customer Order<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="order_detail_id" class="form-select form-select-sm mb-1" required onchange="this.form.submit()">
                                    @foreach($orders as $key => $order)
                                    @if($delivery->order_detail_id == $order->id)
                                    <option value="{{$order->id}}" selected>{{$order->uuid}} - {{ $order->first_name }} {{ $order->last_name}}</option>
                                    @else
                                    <option value="{{$order->id}}">{{$order->uuid}} - {{ $order->first_name }} {{ $order->last_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="vehicle_id" value="{{$delivery->vehicle_id}}">
                            <input type="hidden" name="remarks" value="{{$delivery->remarks}}">
                        </form>
                        <div class="items mt-2" style="width: 100%;">
                            <label class="form-label">Delivery Items</label>
                            <select class="form-select form-select-sm mb-1" id="item-id">
                                <option value="">--Select Order Item--</option>
                                @foreach($items as $oitem)
                                <option value="{{$oitem->product_id}}">{{$oitem->name}}</option>
                                @endforeach
                            </select>
                            <div id="msg">
                                
                            </div>
                            <table class="table table-bordered mt-0" style="width: 100%;">
                                <tr>
                                    <th>#</th>
                                    <th>Item Description</th>
                                    <th style="text-align: center;">Quantity</th>
                                    <th>UOM</th>
                                    <th>&nbsp;</th>
                                </tr>
                                @foreach($ditems as $index => $ditem)
                                <tr>
                                    <td style="text-align: center;">{{$index + 1}}</td>
                                    <td>{{$ditem->name}}</td>
                                    <td style="text-align: center;">
                                        <input class="edit" id="qty_{{$ditem->id}}" type="number" min="0" name="quantity" value="{{$ditem->quantity+0}}" style="text-align: center; width: 70px;">
                                    </td>
                                    <td>{{$ditem->uom}}</td>
                                    <td style="text-align: center;">
                                        <a href="#" onclick="confirmRemoveService('<?php echo encrypt($ditem->id); ?>')"><span class="fa fa-close" aria-hidden="true" style="color: red"></span></a>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                        <hr>
                        <form class="form row g-3" method="POST" action="{{ route('order-deliveries.update', encrypt($delivery->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <input type="hidden" name="order_detail_id" value="{{$delivery->order_detail_id}}">
                            <div class="col-md-3">
                                <label class="form-label">Vehicle<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="vehicle_id" class="form-select form-select-sm mb-1" required>
                                    @foreach($vehicles as $key => $vehicle)
                                    @if($delivery->vehicle_id == $vehicle->id)
                                    <option value="{{$vehicle->id}}" selected>{{$vehicle->plate_no}}</option>
                                    @else
                                    <option value="{{$vehicle->id}}">{{$vehicle->plate_no}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" placeholder="Enter Remarks (Optional)" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Update Delivery</button>
                                <a href="javascript:;" class="btn btn-warning btn-sm" onclick="return confirmDelete()">
                                        <i class="fa fa-trash" style="color: red;"></i> Cancel
                                </a>
                            </div>
                        </form>
                        <form method="POST" action="{{route('order-deliveries.destroy' , encrypt($delivery->id))}}" id="delete-form" style="display: inline;"> 
                            @csrf
                            @method('DELETE')
                                                        
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
            $('#item-id').on('change',function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var id = $(this).val();
                var deliveryid = $('#delivery-id').val();
                $.ajax({
                    url:"{{ url('add-selected-item') }}",
                    type:'POST',
                    data:{ product_id: id, order_delivery_id: deliveryid},
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
                    url: "{{ url('update-delivery-item') }}",
                    type: 'POST',
                    data: { quantity: value, id:edit_id },
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
                                    location.reload();
                                    
                                });
                            }, 1300);
                        }
                    }
                });
            });
        });

    </script>   

