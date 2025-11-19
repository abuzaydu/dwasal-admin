@extends('layouts.app')
    <script>
        
        function confirmRemove(id){
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
                window.location.href="{{url('remove-delivery-note-item')}}/"+id;
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function confirmDelete() {
        Swal.fire({
          title: "Are you sure you wan't to cancel this Delivery Note?",
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
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class=" col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <tr>
                                    <td>DELIVERY NOTE TO : </td>
                                    <th>{{$sale->name}}</th>
                                    <td>Issued By</td>
                                    <th>{{$user->first_name}} {{$user->last_name}}</th>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            @include('flash-message')
                            <div id="msg">
                                
                            </div>
                            <table class="mt-3" style="width: 100%;">
                                <thead>
                                    <tr style="background: #0459c6; color: #fff; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: right; border-left: 1px solid #fff;">Code</th>
                                        <th style="">Item Description</th>
                                        <th style="text-align: center; border-left: 1px solid #fff;">Qty</th>
                                        <th style="text-align: center;; border-left: 1px solid #fff;">UOM</th>
                                        <th style="text-align: center;">
                                            Actions
                                        </th>                             
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $tqty = 0; ?>
                                    @foreach($dnoteitems as $key => $item)
                                    <?php $tqty += $item->delivery_qty; ?>
                                    <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                        <td style="text-align: center; "> {{$key+1}} </td>
                                        <td style="text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;">@if(!is_null($item->product_code)){{$item->product_code}}@endif</td>
                                        <td class="desc" style="">{{$item->slug}}</td>
                                        <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">
                                            <input class="edit" id="qty_{{$item->id}}" type="number" min="0" name="quantity" value="{{$item->delivery_qty+0}}" style="text-align: center; width: 120px;">
                                        </td>
                                        <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">{{$item->uom}}</td>
                                        <td style="text-align: center;">
                                            <a href="#" onclick="confirmRemove('<?php echo encrypt($item->id); ?>')"><span class="fa fa-close" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <form class="row g-3" method="POST" action="{{route('delivery-notes.update', encrypt($dnote->id))}}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <input type="hidden" name="an_sale_id" value="{{$sale->id}}">

                            <div class="col-md-4">
                                <label class="form-label">Vehicle<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="vehicle_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($vehicles as $key => $vehicle)
                                    <option value="{{$vehicle->id}}">{{$vehicle->plate_no}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Delivery Address<span style="color: red; font-weight: bold;">*</span></label>  <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addressModal" data-bs-backdrop="static" data-bs-keyboard="false" class="pull-right"><i class="fa fa-plus"></i> New</a>
                                <select name="delivery_address_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($delivaddresses as $key => $address)
                                    <option value="{{$address->id}}">{{$address->plus_code}} - {{$address->state}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Receiver</label>
                                <input type="text" name="received_by" class="form-control form-control-sm mb-1" placeholder="Enter Receiver's name (optional)">
                            </div>
                            <div class="col-md-8">
                                <label>Comments: <span style="color: red;">*</span></label>
                                <textarea class="form-control form-control-sm mb-1" rows="1" name="comments" placeholder="Enter Comments here" required>{{$dnote->comments}}</textarea>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary btn-sm" style="margin-left: 5px;">Update</button>
                                <a href="#" onclick="confirmDelete()" class="btn btn-warning btn-sm">Cancel</a>
                            </div>
                        </form>
                        <form id="delete-form" method="POST" action="{{ route('delivery-notes.destroy', encrypt($dnote->id))}}" style="display: inline;">
                            @csrf
                            @method("DELETE")
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal animated zoomIn" id="addressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Delivery Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{ route('delivery-addresses.store')}}">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{$sale->customer_id}}">
                        <div class="row g-1">
                            <div class="col-sm-6">
                                  <label class="form-label">Plus Code /Street <span style="color: red; font: bold;">*</span></label>
                                  <input type="text" name="plus_code" required placeholder="Enter Plus Code" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Postcode</label>
                                <input type="text" name="postcode" placeholder="Please enter Postcode" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">Latitude</label>
                                  <input type="text" name="latitude" placeholder="Please enter Latitude" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                  <label class="form-label">Longitude</label>
                                  <input type="text" name="longitude" placeholder="Please enter Longitude" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Region/State</label>
                                <input type="text" name="state" placeholder="Please enter Region" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label for="register-email" class="form-label">Country</label>
                                <select class="form-select form-select-sm mb-1" name="country" id="country">
                                    <option>Tanzania</option>
                                </select>
                            </div>
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

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
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
                    url: "{{ url('update-delivery-note-item') }}",
                    type: 'POST',
                    data: { delivery_qty: value, id:edit_id },
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