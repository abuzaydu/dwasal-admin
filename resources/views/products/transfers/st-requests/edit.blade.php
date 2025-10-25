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
                <div class="card-body">
                    <div class="col-md-12 print_invoice ms-2 me-2 ">
                        <p class="mb-0 text-center">Transfer Items</p>
                        <hr>
                        <table class="items">
                            <thead>
                                <tr>
                                    <th>Item No</th>
                                    <th class="Item">Item name</th>
                                    <th class="source">Source Stock</th>
                                    <th class="destin">Destin Stock</th>
                                    <th style="text-align: center;">Request Qty</th>
                                    <th style="text-align: center;">Confirmed Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderitems as $key => $item)
                                <tr>    
                                    <td>{{ $item->product_code }}</td>
                                    <td class="item">{{$item->name}}</td>
                                    <td class="source">{{$item->source_stock+0}}</td>
                                    <td class="destin">{{$item->destin_stock+0}}</td>
                                    <td class="destin" style="text-align: center;">{{$item->req_qty+0}}</td>
                                    <td class="qty" style="text-align: center;">
                                        @if(Auth::user()->can('confirm-stock-transfer'))
                                        <input class="edit" type="number" min="0" id="qty_{{$item->id}}" value="{{$item->quantity+0}}" style="text-align: center;">
                                        @else
                                        {{$item->quantity+0}}
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="msg">
                            
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <form class="form row g-3" method="POST" action="{{route('transfer-orders.update', encrypt($transorder->id))}}">
                            @csrf
                            {{ method_field('PATCH') }} 
                            <input type="hidden" name="destin_id" value="{{$transorder->destination_id}}">
                            <input type="hidden" name="order_date" value="{{$transorder->order_date}}">
                            <input type="hidden" name="reason" value="{{$transorder->reason}}">
                            <div class="col-sm-6">
                                <label for="reason" class="form-label">Remarks </label>
                                <input type="text" name="on_confirm_remarks" placeholder="Enter your Remarks" class="form-control form-control-sm mb-1">
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
                            <div class="col-sm-4 pt-3">
                                @if($transorder->status != 'Received' || is_null($transorder->user_id))
                                @if(Auth::user()->can('confirm-stock-transfer'))
                                <input type="hidden" name="status" value="Confirmed">
                                <button class="btn btn-primary btn-sm float-end" type="submit">Confirm Order</button>
                                @endif
                                <a href="{{url('transfer-orders')}}" class="btn btn-warning btn-sm float-end" id="btn-create" style="margin-right: 5px;"> Cancel</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
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