@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
  
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('delete-invoice-item') }}/"+id;
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
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ route('pro-invoices.show', encrypt($invoice->id)) }}"> Proforma Invoice - {{ sprintf('%06d', $invoice->invoice_no)}}</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
              <div class="card-body">
          <div class="row g-3 print_invoice">
            <div class="col-md-12">
              @if($shop->business_type_id == 3)
              <form class="row" method="POST" action="{{url('add-invocie-servitem')}}">
                @csrf
                <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                <div class="col-md-6">
                  <label class="form-label"><i class="fa fa-plus"></i> Add Item</label>
                  <select class="form-select form-select-sm mb-1 select2" onchange='if(this.value != 0) { this.form.submit(); }' name="service_id" required>
                    <option value="">Select Service</option>
                      @foreach($services as $key => $service)
                        <option value="{{$service->id}}">{{$service->name}}</option>
                        @endforeach
                  </select>
                </div>
              </form>
              @elseif($shop->business_type_id == 4)
              <div><span><i class="fa fa-plus"></i> Add Item</span></div>
              <div class="row" style="padding-bottom: 4;">
                <div class="col-md-6">
                  <form method="POST" action="{{url('add-invoice-item')}}">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                      <select class="form-select form-select-sm mb-1 select2" onchange='if(this.value != 0) { this.form.submit(); }' name="product_id" required>
                        <option value="">Select Product</option>
                        @foreach($products as $key => $product)
                            <option value="{{$product->id}}">{{$product->slug}}</option>
                            @endforeach
                      </select>
                  </form>
                </div> 
                <div class="col-md-6">
                  <form method="POST" action="{{url('add-invocie-servitem')}}">
                    @csrf 
                    <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                      <select class="form-select form-select-sm mb-1 select2" onchange='if(this.value != 0) { this.form.submit(); }' name="service_id" required>
                        <option value="">Select Service</option>
                        @foreach($services as $key => $service)
                            <option value="{{$service->id}}">{{$service->name}}</option>
                            @endforeach
                      </select>
                    </form>
                </div>
              </div>
              @else
              <form class="row" method="POST" action="{{url('add-invoice-item')}}">
                  @csrf
                  <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                  <div class="col-md-6">
                  <label><i class="fa fa-plus"></i> Add Item</label>
                  <select class="form-select form-select-sm mb-1 select2" id="add-item" onchange='if(this.value != 0) { this.form.submit(); }' name="product_id" required>
                      <option value="">Select Product</option>
                      @foreach($products as $key => $product)
                          <option value="{{$product->id}}">{{$product->slug}}</option>
                          @endforeach
                  </select>
                </div>
              </form>
                @endif
            </div>
            <div class="col-md-12">
              <a href="{{ route('pro-invoices.show', encrypt($invoice->id)) }}" class="btn btn-primary btn-sm float-end"> Preview Proforma Invoice - {{ sprintf('%06d', $invoice->invoice_no)}}</a>
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
                    <td class="desc">
                      {{$item->name}} {{$item->description}}
                    </td>
                    <td class="qty" style="text-align: center;">{{$item->repeatition}}</td>
                    <td class="unit" style="text-align: center;">{{$item->cost_per_unit}}</td>
                    <td class="total" style="text-align: center;">{{$item->amount}}</td>
                    <td style="text-align: center;">{{$item->total_discount}}</td>
                    <td style="text-align: center;">
                      @if($item->with_vat == 'no') 
                      No 
                      @else
                      Yes
                      @endif
                    </td>
                    <td>{{$item->vat_amount}}</td>
                    <td class="del">
                      <a href="{{url('edit-invoice-servitem/'.encrypt($item->id))}}"><i class="fa fa-edit"  style="color: blue;"></i></a> | 
                      <a href="{{url('delete-invoice-servitem/'.encrypt($item->id))}}"><i class="fa fa-trash"  style="color: red;"></i></a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              @endif
              @if($items->count() > 0)
              <label class="form-label mt-4">Product Items</label>
              <table class="items mt-0" style="width: 100%;">
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
                    <th style="text-align: center;">Delete</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($items as $key => $item)
                   <?php
                    $punit = App\Models\ProductUnit::find($item->product_unit_id);
                    $quantity = $item->quantity/$punit->qty_equal_to_basic;
                    $retail_price = $item->retail_price*$punit->qty_equal_to_basic;
                    $unit_discount = $item->discount*$punit->qty_equal_to_basic;
                  ?>
                  <tr style="border-bottom: 1px solid #e0e0e0;">  
                    <td class="desc">
                      {{$item->slug}} {{$item->description}}
                    </td>
                    <td style="text-align: center;">
                     {{$quantity+0}}
                    </td> 
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
              </table>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('page-scripts')

    <script src="{{ asset('side/assets/vendor/select2/js/select2.min.js') }}"></script>
    <script>
        $(function () {
            $('#add-item').select2();
        });
    </script>
@endsection
<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $max = document.querySelector('[name="due_date"]');
            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date()
            });
        });
    </script>