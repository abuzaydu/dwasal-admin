@extends('layouts.app')
@section('page-styles')
  <!-- Application Vendor CSS URL -->
  <link rel="stylesheet" href="{{ asset('side/assets/cssbundle/summernote.min.css') }}">
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('pro-invoices') }}">Proforma Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
     <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#itemModal"><i class="bx bx-cart"></i> Add Item</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#servitemModal"><i class="bx bx-cart"></i> Add Service Item</button>
                        </div>
                        <div class="col-md-12 p-2 border rounded">
                            @if($items->count() > 0)
                            <label class="form-label mt-4">Product Items</label>
                            <table class="table mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th style="text-align: center;">Quantity</th>
                                        <th style="text-align: center;">UOM</th>
                                        <th style="text-align: center;">Unit price</th>
                                        <th style="text-align: center;">Total</th>
                                        <th style="text-align: center;">Discount</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total = 0; $totaldisc = 0; $totalvat = 0; ?>
                                    @foreach($items as $key => $item)
                                    <?php
                                    $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                    $quantity = $item->quantity/$punit->qty_equal_to_basic;
                                    $retail_price = $item->retail_price*$punit->qty_equal_to_basic;
                                    $unit_discount = $item->discount*$punit->qty_equal_to_basic;

                                    $total += $item->amount; $totaldisc += $item->total_discount; $totalvat += $item->tax_amount;
                                    ?>
                                    <tr style="border-bottom: 1px solid #e0e0e0;">  
                                        <td class="desc">
                                            @if(!is_null($item->product_code)){{$item->product_code}} - @endif{{$item->name}} {{$item->description}}
                                        </td>
                                        <td style="text-align: center;">{{$quantity+0}}</td> 
                                        <td style="text-align: center;">{{$punit->unit_name}}</td>
                                        <td style="text-align: center;">{{$item->cost_per_unit}}</td>
                                        <td style="text-align: center;">{{$item->amount}}</td>
                                        <td style="text-align: center;">{{$item->total_discount}}</td>
                                        @if($settings->is_vat_registered)
                                        <td style="text-align: center;">
                                            @if($item->with_vat == 'no')
                                            No
                                            @else
                                            Yes
                                            @endif
                                        </td>
                                        <td style="text-align: center;">{{$item->tax_amount}}</td>
                                        @endif
                                        <td style="text-align: center;">
                                          <a href="{{url('edit-invoice-item/'.encrypt($item->id))}}"><i class="fa fa-edit"  style="color: blue;"></i></a> | 
                                          <a href="javascript:;" onclick="confirmDelete('<?php echo encrypt($item->id); ?>')"><i class="fa fa-trash"  style="color: red;"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <thead>
                                    <tr>
                                        <th>Total</th>
                                        <th style="text-align: center;"></th>
                                        <th style="text-align: center;"></th>
                                        <th style="text-align: center;"></th>
                                        <th style="text-align: center;">{{number_format($total, 2, '.', ',') }}</th>
                                        <th style="text-align: center;">{{number_format($totaldisc, 2, '.', ',')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th></th>
                                        <th style="text-align: center;">{{number_format($totalvat, 2, '.',',') }}</th>
                                        @endif
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                            </table>
                            @endif
                            @if($servitems->count() > 0)
                            <label class="form-label">Service Items</label>
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="desc">Description</th>
                                        <th class="qty" style="text-align: center;">Quantity</th>
                                        <th class="unit" style="text-align: center;">Unit price</th>
                                        <th class="total" style="text-align: center;">Total</th>
                                        <th style="text-align: center;">Discount</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th class="del">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($servitems as $key => $item)
                                    <tr style="border-bottom: 1px solid #e0e0e0;">  
                                        <td class="desc">{{$item->name}} {{$item->description}}</td>
                                        <td class="qty" style="text-align: center;">{{$item->repeatition}}</td>
                                        <td class="unit" style="text-align: center;">{{$item->cost_per_unit}}</td>
                                        <td class="total" style="text-align: center;">{{$item->amount}}</td>
                                        <td style="text-align: center;">{{$item->total_discount}}</td>
                                        @if($settings->is_vat_registered)
                                        <td style="text-align: center;">
                                          @if($item->with_vat == 'no') 
                                          No 
                                          @else
                                          Yes
                                          @endif
                                        </td>
                                        @endif
                                        <td class="del">
                                            <a href="{{url('edit-invoice-servitem/'.encrypt($item->id))}}"><i class="fa fa-edit"  style="color: blue;"></i></a> | 
                                            <a href="{{url('delete-invoice-servitem/'.encrypt($item->id))}}"><i class="fa fa-trash"  style="color: red;"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                        <div class="col-md-12 p-4 border rounded">
                            <form class="form-validate row g-1" method="POST" action="{{ route('pro-invoices.update', encrypt($invoice->id))}}">
                                @csrf  
                                {{ method_field('PATCH') }} 
                                <input type="hidden" name="customer_id" id="cust-id" value="{{$invoice->customer_id}}" class="form-control form-control-sm mb-1">
                                <div class="col-sm-3">
                                    <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                    <input id="search_customer_key" placeholder="Search customer" value="{{$customer->name}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                    <ul id="searchResult3"></ul>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">PFI Date</label>
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="invoice_date" value="{{date('Y-m-d', strtotime($invoice->time_created)) }}" id="invoice_date" placeholder="Choose Sale date" class="form-control form-control-sm mb-3">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" id="duedatepicker" name="due_date" placeholder="Choose Due date" class="form-control form-control-sm mb-1" value="{{$invoice->due_date}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="total" class="form-label">RFQ No.</label>
                                    <input type="text" name="ref_no" class="form-control form-control-sm mb-1" value="{{$invoice->ref_no}}">
                                </div>
                                <div class="col-md-12">
                                    <label for="employee"class="form-label">Notice</label>
                                    <textarea  class="summernote" name="notes" id="notes" >{!! $invoice->notes !!}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Terms & Conditions</label>
                                    <div>
                                        {!! $invoice->terms_and_conditions !!}
                                    </div>
                                    @if(!is_null($terms))
                                    <input type="hidden" name="terms_and_conditions" value="{{$terms->content}}">
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn btn-success btn-sm">Save</button>
                                    <a href="javascript:history.back();" class="btn btn-warning btn-sm">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ url('add-invoice-item') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                        <input type="hidden" name="product_id" id="product-id">
                        <div class="col-md-6">
                            <label>{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="search_key" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                            <ul id="searchResult2"></ul>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" min="0" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="servitemModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Service Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{url('add-invocie-servitem')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                        <div class="col-md-6">
                            <label>{{trans('navmenu.service')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="service_id" required>
                                <option value="">Select Service</option>
                                @foreach($services as $key => $service)
                                <option value="{{$service->id}}">{{$service->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" min="0" name="repeatition" value="1" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
    <script src="{{ asset('side/assets/js/bundle/summernote.bundle.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#notes').summernote({
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
            $('#search_key').on('keyup',function () {
                var query = $(this).val();
                $.ajax({
                    url:"{{ url('search-product') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchResult2").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            var code = response[i]['product_code'];
                            if (code != null) {
                                name = code+" - "+name;
                            }
                            $("#searchResult2").append("<li value='"+id+"'>"+name+"</li>");
                        }

                        // binding click event to li
                        $("#searchResult2 li").bind("click",function(){
                            searchProduct(this);
                        });

                    }
                })
            });

            $('#search_customer_key').on('keyup',function () {
                var query = $(this).val();
                $.ajax({
                    url:"{{ url('search-customer') }}",
                    type:'GET',
                    data:{'search_customer_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchResult3").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#searchResult3").append("<li value='"+id+"'>"+name+"</li>");
                        }

                        // binding click event to li
                        $("#searchResult3 li").bind("click",function(){
                            setSelectedCustomer(this);
                        });

                    }
                })
            });
        });

        function searchProduct(element) {
            var value = $(element).text();
            var productid = $(element).val();
            $('#product-id').val(productid);
            $("#search_key").val(value);
            $("#searchResult2").empty();  
        }


        function setSelectedCustomer(element) {
            var value = $(element).text();
            var custId = $(element).val();
            $('#cust-id').val(custId); 
            $("#search_customer_key").val(value);
            $("#searchResult3").empty();
        }
    </script>

<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="invoice_date"]'),
                $max = document.querySelector('[name="due_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date()
            });
        });
    </script>