@extends('layouts.inv')

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
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body row">
                    <div class="col-md-8">
                        <p class="mb-0">Transfer Items</p>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#itemModal">
                            <i class="fa fa-plus-circle"></i>
                            {{trans('navmenu.add_order_item')}}
                        </button>
                    </div>
                    <div class="col-md-12 print_invoice ms-2 me-2 ">
                        <table class="items" style="width: 100%; overflow-x: auto; white-space: nowrap; display: block;">
                            <thead>
                                <tr>
                                    <th class="Item">Item Description</th>
                                    <th class="source">Source Stock</th>
                                    <th class="destin">Destin Stock</th>
                                    <th style="text-align: center;">Qty</th>
                                    <th style="text-align">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderitems as $key => $item)
                                <tr>    
                                    <td class="item">@if(!is_null($item->product_code)){{ $item->product_code }} - @endif{{$item->slug}}</td>
                                    <td class="source" style="text-align: center;">{{$item->source_stock+0}}</td>
                                    <td class="destin" style="text-align: center;">{{$item->destin_stock+0}}</td>
                                    <td class="qty" style="text-align: center;">
                                        <input class="edit" type="number" min="0" id="qty_{{$item->id}}" value="{{$item->quantity+0}}" style="text-align: center;">
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ url('remove-item/'.encrypt($item->id)) }}" class="text-danger" title="Remove Item"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="msg">
                            
                        </div>
                    </div>
                    <div class="col-md-12 mt-5">
                        <form class="form row g-3" method="POST" action="{{route('transfer-orders.update', encrypt($transorder->id))}}">
                            @csrf
                            {{ method_field('PATCH') }} 
                            <input type="hidden" name="destin_id" value="{{$transorder->destination_id}}">
                            <input type="hidden" name="status" value="{{$transorder->status}}">
                            <div class="col-sm-3">
                                <label class="form-label">Order Date <span style="color: red;">*</span></label>
                                <input type="h" name="order_date" value="{{$transorder->order_date}}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-9">
                                <label class="form-label">Reason</label>
                                <input type="text" name="reason" value="{{$transorder->reason}}" class="form-control form-control-sm mb-1">
                            </div>
                            @if($settings->is_vat_registered)
                            <div class="col-sm-2">
                                <label class="form-label">Add VAT</label>
                                <select class="form-select form-select-sm mb-1" name="add_vat">
                                    @if($transorder->add_vat)
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                    @else
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                    @endif
                                </select>
                            </div>
                            @endif
                            <div class="col-sm-12 pt-3">
                                <button class="btn btn-primary btn-sm float-end" type="submit">Update Order</button>
                                <a href="{{url('transfer-orders')}}" class="btn btn-warning btn-sm float-end" id="btn-create" style="margin-right: 5px;"> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="pull-left" id="myModalLabel">{{trans('navmenu.add_order_item')}}</h5>
                    <button type="button"  class="close btn btn-danger pull-right" data-dismiss="modal" aria-label="Close"><span class="fa fa-x-circle"></span></button>
                    
                </div>
                <form class="form-validate" method="POST" action="{{url('add-order-item')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="transfer_order_id" value="{{$transorder->id}}">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3 select2" id="my-select" name="product_id" required style="width: 100%; border: 1px solid gray;">
                                <option value="">Select Product</option>
                                @foreach($products as $key => $product)
                                <option value="{{$product->id}}">{{$product->slug}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-3" required>
                        </div>
                    </div>                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
 
         // Save data
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
                url: "{{url('update-transorder-item')}}",
                type: 'POST',
                data: { quantity: value, id:edit_id },
                success:function(response){
                    if(response.success == 1){
                        $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
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