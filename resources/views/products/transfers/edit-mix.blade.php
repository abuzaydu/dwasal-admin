@extends('layouts.inv')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/transorder.js')}}"></script>
<script type="text/javascript">
    
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#sale_date").val('');
      } 
    }

    function submitTemp(index) {
        document.getElementById('ptemp-form-'+index).submit();
    }

</script>

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
        <div class="col-xl-12 col-md-12 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" id="orderform" method="POST" action="{{ url('update-transfer-mix-items') }}">
                            @csrf
                            <div class="col-md-12">
                                <div class="row g-1">
                                    <div class="col-md-7">
                                        <label class="form-label">Mixed Items</label>
                                        <div class="input-group mb-0">
                                            <input type="text" class="form-control form-control-sm mb-1" id="search_key" placeholder="{{trans('navmenu.search_product')}}" autocomplete="off" aria-label="Recipient's username" aria-describedby="button-addon2">
                                            <a class="btn btn-outline-danger btn-sm" id="empty-search"><i class='fa fa-close'></i></a>
                                        </div>
                                        <ul id="searchResult3" class="list-group"></ul>

                                        <div class="print_invoice">
                                            <div id="msg">
                                                
                                            </div>
                                            <table class="items mt-0" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th class="desc">Item Name</th>
                                                        <th class="qty" style="text-align: center;">Quantity</th>
                                                        <th class="del" style="text-align: center;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orderitems as $key => $item)
                                                    <tr>
                                                        <td> {{$key+1}} </td>
                                                        <td class="desc">{{$item->name}}</td>
                                                        <td class="qty" style="text-align: center;">
                                                            <input class="edit" id="qty_{{$item->id}}" type="number" name="quantity" value="{{$item->quantity+0}}" style="text-align: center;">
                                                        </td>
                                                        <td class="del" style="text-align: center;">
                                                            <a href="{{ url('remove-sto-mix-item/'.encrypt($item->id))}}"><i class="fa fa-close" style="color: red;"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <input type="hidden" name="" id="sel-items" value="{{$orderitems->count()}}">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="row g-1">
                                            <input type="hidden" name="order_id" id="order-id" value="{{$transorder->id}}">
                                            <div class="col-sm-3">
                                                <label for="order_date" class="form-label">{{trans('navmenu.date')}} <span style="color: red; font-weight: bold;">*</span></label>
                                                <input type="text" name="order_date" value="{{$transorder->order_date}}" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.pick_date')}}"  aria-describedby="calendar">
                                            </div>
                                            <div class="col-sm-9">
                                                <label for="reason" class="form-label">{{trans('navmenu.reason')}} </label>
                                                <textarea name="reason" rows="1" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.hnt_transfer_reason')}}">{{$transorder->reason}}</textarea>
                                            </div>
                                            <div class="col-sm-8">
                                                <div id="errmsg"></div>
                                                <label class="form-label">End Product</label>
                                                <select name="product_id" class="form-select form-select-sm mb-1" required>
                                                    <option value="{{$endproduct->product_id}}">{{$endproduct->name}}</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-label">Quantity <span style="color: red;">*</span></label>
                                                <input type="number" name="quantity_in" value="{{$endproduct->quantity_in+0}}" class="form-control form-control-sm mb-1" placeholder="Enter Produced Quantity" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">  
                                <button id="btn-create" class="btn btn-success btn-sm mb-1"><i class="fa fa-send" ></i> Transfer</button>
                                <a href="{{ url('transfer-orders')}}" class="btn btn-warning btn-sm mr-1 card-subtitle" id="btn-cancel"><i class="fa fa-close"></i> {{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>  
                </div>
            </div>
        </div>      
    </div>
@endsection  
    
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // $('#search-form').on('submit', function(e){
                // e.preventDefault();
            $('#search_key').on('keyup',function () {
                var query = $('#search_key').val();
                $.ajax({
                    url:"{{ url('search-product') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchResult3").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            var qty = +response[i]['in_stock'];
                            if (qty > 0) {
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'>"+name+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-share' aria-hidden='true'></span></span></div></li>");
                            }else{
                                $("#searchResult3").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'>"+name+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-share' aria-hidden='true'></span></span></div></li>");
                            }
                        }

                        // binding click event to li
                        $("#searchResult3 li").bind("click",function(){
                            addOrderItemTemp(this);
                        });

                    }
                })
            });

            $('#empty-search').on('click', function(){
                $("#search_key").val('');
                $("#searchResult3").empty();
            });

            $('#search_end_key').on('keyup',function () {
                var query = $('#search_end_key').val();
                $.ajax({
                    url:"{{ url('search-product') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchResult").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            var qty = +response[i]['in_stock'];
                            if (qty > 0) {
                                $("#searchResult").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'>"+name+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-share' aria-hidden='true'></span></span></div></li>");
                            }else{
                                $("#searchResult").append("<li class='list-group-item d-flex justify-content-between align-items-center' value='"+id+"'><div class='col-sm-11'>"+name+"</div><div class='col-sm-1'><span class='badge bg-success rounded-pill'><span class='fa fa-share' aria-hidden='true'></span></span></div></li>");
                            }
                        }

                        // binding click event to li
                        $("#searchResult li").bind("click",function(){
                            setSelectedItem(this);
                        });

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
                    url: "{{ url('update-sto-mix-item') }}",
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


            $('#btn-create').on('click', function(e)  {
                e.preventDefault();
                var items = $('#sel-items').val();
                var endprod = $('#end-product').val();
                if(items < 2) {
                    $('#msg').append('<div class="alert alert-danger hideit alertSuc">Please select at least two items to mix.</div >');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();            
                        });
                    }, 1300);
                }else if (endprod == '') {
                    $('#errmsg').append('<div class="alert alert-danger hideit alertSuc">Please select end product.</div >');
                    setTimeout(function() {
                        $('.hideit').fadeOut('slow', function() {
                            $(this).remove();            
                        });
                    }, 1300);
                }else{
                    $('#orderform').submit();
                }
            });
        });

        function addOrderItemTemp(element) {
            var value = $(element).text();
            var productid = $(element).val();
            var orderid = $('#order-id').val();
            $.ajax({
                url:"{{ url('add-sto-mix-item') }}",
                type:'GET',
                data:{'order_id': orderid, 'product_id':productid},
                success:function (response) {
                    $("#search_key").val('');
                    $("#searchResult3").empty();
                    if (response.result == 0) {
                        $('#msg').append('<div class="alert alert-danger hideit alertSuc">' + response.msg + '.</div >');
                        setTimeout(function() {
                            $('.hideit').fadeOut('slow', function() {
                                $(this).remove();            
                            });
                        }, 1300);
                    }else{
                        window.location.reload();                        
                    }
                }
            })   
        }

        function setSelectedItem(elem) {
            var value = $(elem).text();
            var productid = $(elem).val();
            $('#end-product').val(productid);
            $('#search_end_key').val(value);
            $("#searchResult").empty();
        }

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
