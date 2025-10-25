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
                    <div class="col-md-12 print_invoice ms-2 me-2 ">
                        <table class="items" style="width: 100%; overflow-x: auto; white-space: nowrap; display: block;">
                            <thead>
                                <tr>
                                    <th class="Item">Item Description</th>
                                    <th class="source">Source Stock</th>
                                    <th class="destin">Destin Stock</th>
                                    <th style="text-align: center;">Transfer Qty</th>
                                    <th style="text-align: center;">Received Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderitems as $key => $item)
                                <tr>    
                                    <td class="item">@if(!is_null($item->product_code)){{ $item->product_code }} - @endif{{$item->name}}</td>
                                    <td class="source" style="text-align: center;">{{$item->source_stock+0}}</td>
                                    <td class="destin" style="text-align: center;">{{$item->destin_stock+0}}</td>
                                    <td style="text-align: center;">{{$item->quantity+0}}</td>
                                    <td class="qty" style="text-align: center;">
                                        <input class="edit" type="number" min="0" id="qty_{{$item->id}}" value="{{$item->rec_qty+0}}" style="text-align: center;">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div id="msg">
                            
                        </div>
                    </div>
                    <div class="col-md-12 mt-5">
                        <form class="form row g-3" method="POST" id="receive-form" action="{{ url('confirm-receive-transfer') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{$transorder->id}}">
                            <div class="col-sm-12">
                                <label for="reason" class="form-label">Remarks </label>
                                <input type="text" name="on_receive_remarks" placeholder="Enter your Remarks" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-12">
                                <a href="javascript:;" class="btn btn-primary btn-sm float-end"  onclick="confirmReceive()">Confirm Receive</a>
                                <a href="{{url('transfer-orders')}}" class="btn btn-warning btn-sm float-end" id="btn-create" style="margin-right: 5px;"> Cancel</a>
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
    function confirmReceive() {
        document.getElementById('receive-form').submit();
    }
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
                url: "{{url('update-received-item')}}",
                type: 'POST',
                data: { rec_qty: value, id:edit_id },
                success:function(response){
                    if(response.success == 1){
                        $('#msg').append('<div class="alert alert-success hideit alertSuc">' + response.msg + '.</div>');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 1300);
                    }else{
                        $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div>');
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