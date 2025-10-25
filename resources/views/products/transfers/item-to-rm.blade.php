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
        <div class="col-xl-8 col-md-8 mx-auto">
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" name="orderform" method="POST" action="{{ url('transfer-item-to-rm') }}">
                            @csrf
                            <div class="col-sm-8">
                                <label class="form-label">Source Product <span style="color: red; font-weight: bold;">*</span></label>
                                <select name="product_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->name}} ({{$product->basic_uom}})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Quantity <span style="color: red; font-weight: bold;">*</span></label>
                                <input type="text" name="quantity" class="form-control form-control-sm mb-1" placeholder="Enter Source Quantity"  aria-describedby="calendar" required>
                            </div>
                            <div class="col-sm-8">
                                <label for="shop_id" class="form-label">Raw Material </label>
                                <select name="rm_id" class="form-select form-select-sm mb-1">
                                    <option value="">--Select--</option>
                                    @foreach($rmaterials as $rm)
                                    <option value="{{$rm->id}}">{{$rm->name}} ({{$rm->basic_uom}})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="date_set" class="form-label">Quantity <span style="color: red; font-weight: bold;">*</span></label>
                                <input type="text" name="rm_qty" class="form-control form-control-sm mb-1" placeholder="Enter End Product Quantity"  aria-describedby="calendar" required>
                            </div>
                            <div class="col-sm-3">
                                <label for="order_date" class="form-label">{{trans('navmenu.date')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input type="text" name="order_date" value="{{$orderdate}}" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.pick_date')}}"  aria-describedby="calendar">
                            </div>
                            <div class="col-sm-9">
                                <label for="reason" class="form-label">{{trans('navmenu.reason')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <textarea name="reason" rows="1" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.hnt_transfer_reason')}}"></textarea>
                            </div> 
                            <div class="col-md-12">  
                                <button type="submit" class="btn btn-success btn-sm mb-1"><i class="fa fa-send" ></i> Transfer</button>
                                <a href="{{ url('transfer-orders')}}" class="btn btn-warning btn-sm mr-1 card-subtitle" id="btn-cancel"><i class="fa fa-close"></i> {{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>  
                </div>
            </div>
        </div>      
    </div>
@endsection  
    
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
