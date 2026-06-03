@extends('layouts.inv')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function weg(elem) {
        var x = document.getElementById("date_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#datepicker").val('');
        }
    }

    function wegDam(elem) {
        var x = document.getElementById("dam_date_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#dam_date").val('');
        }
    }

    function showHideForm(elem) {
        var newform = document.getElementById('new-form');
        if (elem == 'show') {
            newform.style.display = 'block';
        }else{
            newform.style.display = 'none';
        }
    }

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
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }


    function confirmDeactivation() {
        Swal.fire({
          title: "Are you sure you want to DEACTIVATE this product?",
          text: "This product will not be able to be transacted after this action",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes Deactivate",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('activate-deactivate-product/'.encrypt($product->id))}}";
            Swal.fire(
              "DEACTIVATED",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmActivation() {
        Swal.fire({
          title: "Are you sure you want to ACTIVATE this product?",
          text: "This product will be able to be transacted after this action",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes Activate",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('activate-deactivate-product/'.encrypt($product->id))}}";
            Swal.fire(
              "ACTIVATED",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeleteDamage(id) {
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
            document.getElementById('delete-form-damage-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }


    function confirmDeleteUnit(id) {
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
            document.getElementById('delete-unit-form-'+id).submit();
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
                    <li class="breadcrumb-item"><a href="{{ url('products') }}"> Products & Services</a></li>
                    <li class="breadcrumb-item">{{$page}}</li>
                    <li class="breadcrumb-item active"><b>{{$title}}</b></li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right pt-0">
                @if(Auth::user()->can('deactivate-product'))
                @if($product->is_active)
                <a href="javascript:;" onclick="return confirmDeactivation()" class="btn btn-warning btn-sm">Deactivate Product</a>
                @else
                <a href="javascript:;" onclick="return confirmActivation()" class="btn btn-success btn-sm">Activate Product</a>
                @endif
                @endif
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body row">
                    <div class="col-md-8">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;"><h5>{{$product->slug}}</h5></th>
                                    <td style="text-align: center;">
                                        @if(!is_null($product->image_url))
                                            <img src="{{ asset('storage/' . $product->image_url) }}" width="60">
                                        @endif  
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-striped" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td>{{trans('navmenu.buying_price')}}</td>
                                    <td>{{number_format($product->unit_cost, 2, '.', ',')}}</td>
                                    @if($settings->retail_with_wholesale)
                                    <td></td>
                                    <td></td>
                                    @endif
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>{{trans('navmenu.retail_price')}}</td>
                                    <td>{{number_format($product->retail_price, 2, '.', ',')}}</td>
                                    @if($settings->retail_with_wholesale)
                                    <td>{{trans('navmenu.wholesaleprice')}}</td>
                                    <td>{{number_format($product->wholesale_price, 2, '.', ',')}}</td>
                                    @endif 
                                    <td>
                                        <button  type="button" class="font-13  btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#sellingModal">{{trans('navmenu.new')}}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-striped" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td>
                                        @if($settings->is_filling_station)
                                            {{trans('navmenu.total_g_or_l')}}
                                        @else
                                            {{trans('navmenu.damaged')}}
                                        @endif
                                    </td>
                                    <td>
                                        @if($settings->is_filling_station) 
                                            {{-($t_dam+0)}}
                                        @else
                                            {{$t_dam+0}}
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="mb-0 font-13  btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#damageModal">
                                            @if($settings->is_filling_station)
                                                {{trans('navmenu.new_depth_measure')}}
                                            @else
                                                {{trans('navmenu.new')}}
                                            @endif
                                        </button>
                                    </td>
                                    <td>{{trans('navmenu.reorder_point')}}</td>
                                    <td>{{number_format($product->reorder_point)}}</td>
                                    <td>
                                        <button type="button" class=" font-13 btn btn-dark btn-sm"  data-bs-toggle="modal" data-bs-target="#reorderModal">{{trans('navmenu.new')}}</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{trans('navmenu.location')}}</td>
                                    <td>{{$product->location}}</td>
                                    <td>
                                        <button type="button" class=" font-13 btn  btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#locationModal" data-backdrop="static" data-keyboard="false">
                                            {{trans('navmenu.new')}}
                                        </button>
                                    </td>
                                    <td>
                                                
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @if(!is_null($product->description))
                        <div class="col">
                            <p class="mb-0 font-18 text-success" >@if($product->description != 'null'){{$product->description}}@endif </p>
                        </div>
                        @endif 
                    
                        <h6 class="mb-0 text-center">Product Unit(s) @if($productunits->count() > 0)<a href="#" class=" font-13 btn  btn-primary btn-sm float-end" onclick="showHideForm('show')">{{trans('navmenu.new')}}</a>@else <a href="{{ url('create-basic-unit/'.encrypt($product->id)) }}" class="btn btn-primary btn-sm float-end"> Set Basic Unit</a> @endif</h6>
                        <hr>
                        <form class="my-form" method="POST" action="{{ route('product-units.store') }}" id="new-form" style="display: none;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{$product->id}}">
                            <div class="row">
                                <div class="col-sm-4">
                                    <label class="form-label">Unit <span style="color: red;">*</span></label>
                                    <select class="form-select form-select-sm mb-1" name="unit_name" required>
                                        <option value=""> ---Select--</option>
                                        @foreach($units as $key => $unit)
                                            <option>{{$unit->unit_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-8">
                                    <label class="form-label">Qty equivalent to Basic Unit <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm mb-3" type="number" min="0" step="any" name="qty_equal_to_basic" placeholder="Enter quantity" required>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Unit Price <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm mb-3" type="number" name="unit_price" placeholder="Enter Unit Price" required>
                                </div>
                                <div class="col-sm-6">
                                    <a href="#" onclick="showHideForm('hide')" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                                    <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-striped" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th>Is Basic</th>
                                    <th>QTY Equivalent</th>
                                    <th>Unit Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productunits as $index => $punit)
                                <tr>
                                    <td>({{$punit->unit_name}})</td>
                                    @if($punit->is_basic)
                                    <td>True</td>
                                    <td>1</td>
                                    @else
                                    <td>False</td>
                                    <td>1{{$punit->unit_name}} == {{$punit->qty_equal_to_basic+0}} {{$product->basic_uom}}</td>
                                    @endif
                                    <td>{{number_format($punit->unit_price, 2, '.', ',')}}</td>
                                    <td>
                                        <a href="{{ route('product-units.edit', encrypt($punit->id))}}"><i class="fa fa-edit"></i></a> |
                                        <form id="delete-unit-form-{{$index}}" method="POST" action="{{ route('product-units.destroy', encrypt($punit->id))}}" style="display: inline;">
                                            @csrf
                                            @method("DELETE")
                                            <a href="#" onclick="confirmDeleteUnit('<?php echo $index; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="table table-striped" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td>Product code</td>
                                    <td><b>{{$product->product_code}}</b></td>
                                    <td>Brand Name</td>
                                    <td><b>{{$product->brand}}</b></td>
                                </tr>
                                <tr>
                                    <td>Model Number</td>
                                    <td><b>{{$product->model}}</b></td>
                                
                                    <td>Type</td>
                                    <td><b>{{$product->type}}</b></td>
                                </tr>
                            </tbody>
                        </table>

                        @if(!empty($product->barcode))
                        <?php 
                            $pattern = '/^[0-9]+$/';
                        ?>
                        
                        <div class="col">
                            <div class="card radius-10 ">
                                <div class="card-body">
                                    @if(preg_match($pattern, $product->barcode))
                                    <div class="row">
                                        <div class="col">
                                            <h4 class="my-1">
                                                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($product->barcode, $bsetting->code_type, $bsetting->width, $bsetting->height, [0, 0, 0], $bsetting->showcode)}}" alt="barcode" />
                                            </h4>
                                        </div>
                                        <div class="col">
                                            <a href="#" class="btn btn-flat bg-light-primary font-13" onclick="PrintImage('data:image/png;base64,{{DNS1D::getBarcodePNG($product->barcode, $bsetting->code_type, $bsetting->width, $bsetting->height, [0, 0, 0], $bsetting->showcode)}}'); return false;">
                                                <i class="fa fa-barcode"></i> PRINT</a>
                                        </div>
                                    </div>
                                    @else
                                    <div class="alert alert-danger hideit alertSuc">Barcode number should contains ONLY Digits (0-9) to preview. {{$product->barcode}} contains other characters which are not digits</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-0 text-center">Product Summary</h6>
                        <hr>
                        <table class="table table-striped" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <th>{{trans('navmenu.purchased')}}</th>
                                    <td><b>{{$t_in+0}}</b></td>
                                </tr>
                                <tr>
                                    <th>Stock Corrections</th>
                                    <td><b>{{$diff_qty+0}}</b></td>
                                </tr>
                                <tr>
                                    <th>{{trans('navmenu.sold')}}</th>
                                    <td><b>{{$t_out+0}}</b></td>
                                </tr>
                                <tr>
                                    <th>{{trans('navmenu.returned')}}</th>
                                    <td><b>{{$returned+0}}</b></td>
                                </tr>
                                <tr>
                                    <th>{{trans('navmenu.transfered')}}</th>
                                    <td><b>{{$t_transfer+0}}</b></td>
                                </tr>
                                <tr>
                                    <th>@if($settings->is_filling_station) {{trans('navmenu.total_g_or_l')}} @else {{trans('navmenu.damaged')}} @endif</th>
                                    <td><b>@if($settings->is_filling_station) {{-($t_dam+0)}} @else {{$t_dam+0}} @endif</b></td>
                                </tr>
                                <tr>
                                    <th>{{trans('navmenu.in_stock')}}</th>
                                    <td><b>{{$product->in_stock+0}}</b></td>
                                </tr>
                            </tbody>
                        </table>
                        <small style="color: blue;">{{trans('navmenu.in_stock')}} = ({{trans('navmenu.purchased')}}+{{trans('navmenu.returned')}})-(Stock Corrections+{{trans('navmenu.sold')}}+{{trans('navmenu.transfered')}}+@if($settings->is_filling_station) {{trans('navmenu.total_g_or_l')}} @else {{trans('navmenu.damaged')}} @endif)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.stock_history')}}</h6>
            <hr>
            <div class="card radius-10">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#stock_purchases" class="nav-link active" role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">{{trans('navmenu.stock_purchases')}}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @if($stockcorrections->count() > 0)
                        <li class="nav-item" role="presentation">
                            <a href="#corrections" class="nav-link" role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">Stock Corrections</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <a href="{{ url('product-sale-history/'.encrypt($product->id))}}" class="nav-link">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">{{trans('navmenu.sales_history')}}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @if($transfers->count() > 0)
                        <li class="nav-item" role="presentation">
                            <a href="#transfered" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">{{trans('navmenu.transfered')}}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <a href="#damaged_tab" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">
                                            @if($settings->is_filling_station)
                                            {{trans('navmenu.depth_measures')}}@else
                                            {{trans('navmenu.damaged')}}
                                            @endif 
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#price_tab" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">
                                            Selling Price Change History 
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active table-responsive" id="stock_purchases" role="tabpanel">
                            <table id="example2" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">{{trans('navmenu.purchase_date')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    @if(Auth::user()->can('view-purchase-cost'))
                                    <th style="text-align: center;">{{trans('navmenu.buying_price')}}</th>
                                    @endif
                                    <th style="text-align: center;">{{trans('navmenu.source')}}</th>
                                    <td style="text-align: center;">Is Utilized</td>
                                    <th style="text-align: center;">QTY Utilized</th>
                                    @if($settings->enable_exp_date)
                                    <th style="text-align: center;">{{trans('navmenu.expire_date')}}</th>
                                    @endif
                                    <th style="text-align: center;">{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($stocks as $index => $stock)
                                    <tr>
                                        <td style="text-align: center;">{{$index+1}}</td>
                                        <td>{{date('d-m-Y', strtotime($stock->created_at))}}</td>
                                        <td style="text-align: center;">
                                            {{$stock->quantity_in+0}}
                                        </td>
                                        @if(Auth::user()->can('view-purchase-cost'))
                                        <td style="text-align: center;">{{number_format($stock->unit_cost, 2, '.', ',')}}</td>
                                        @endif
                                        <td>{{$stock->source}}</td>
                                        <td style="text-align: center;">
                                            @if($stock->is_utilized)
                                            Yes
                                            @else
                                            No
                                            @endif
                                        </td>
                                        <td style="text-align: center;">{{$stock->quantity_out+0}}</td>
                                        @if($settings->enable_exp_date)
                                        <td style="text-align: center;">{{$stock->expire_date}}</td>     
                                        @endif
                                        <td>
                                            @if(is_null($stock->purchase_id))
                                            @if(Auth::user()->can('edit-stock'))
                                            <a href="{{route('stocks.edit' , encrypt($stock->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>
                                            @endif
                                            @if(Auth::user()->can('delete-stock'))
                                            <form id="delete-form-{{$index}}" method="POST" action="{{route('stocks.destroy' , encrypt($stock->id))}}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete({{$index}})"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </form>    
                                            @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade table-responsive" id="corrections" role="tabpanel">
                            <table id="example" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.in_stock')}}</th>
                                        <th>Correction QTy</th>
                                        <th>Diff Qty</th>
                                        <th>Corrected BY</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockcorrections as $index => $item)
                                    <tr>
                                        <td>{{$item->id}}</td>
                                        <td>{{date('d-m-Y', strtotime($item->time_created))}}</td>
                                        <td>{{$item->in_stock+0}}</td>
                                        <td>{{$item->correction_qty+0}}</td>
                                        <td>{{$item->diff_qty+0}}</td>
                                        <td>{{$item->first_name}} {{$item->last_name}}</td>
                                        <td> 
                                            <form id="delete-form-{{$index}}" method="POST" action="{{ route('stock-corrections.destroy', encrypt($item->id))}}" style="display: inline;"> 
                                                @csrf
                                                @method("DELETE")
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $index; ?>')"><i class='fa fa-trash'></i></a>
                                            </form>  
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="tab-pane fade table-responsive" id="transfered" role="tabpanel">
                            <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    <th>{{trans('navmenu.order_no')}}</th>
                                    <th>{{trans('navmenu.transfer_date')}}</th>
                                    <th>{{trans('navmenu.quantity')}}</th>
                                    <th>{{trans('navmenu.destination')}}</th>
                                    <th>{{trans('navmenu.reason')}}</th>
                                    <th>{{trans('navmenu.transfer_by')}}</th>
                                    <th>{{trans('navmenu.record_at')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($transfers as $index => $transfer)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{sprintf('%05d', $transfer->order_no)}}</td>
                                        <td>{{$transfer->order_date}}</td>
                                        <td>{{$transfer->quantity+0}}</td>
                                        <td>{{App\Models\Shop::find($transfer->destination_id)->name}}</td>
                                        <td>{{$transfer->reason}}</td>
                                        <td>@if(!is_null($transfer->user_id)){{App\Models\User::find($transfer->user_id)->first_name}}@endif</td>
                                        <td>{{$transfer->created_at}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>                                    
                            </table>
                        </div>

                        <div class="tab-pane fade table-responsive" id="damaged_tab" role="tabpanel">
                            <table id="example3" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    @if($settings->is_filling_station)
                                    <td>{{trans('navmenu.depth_measure')}}</td>
                                    <td>{{trans('navmenu.in_stock')}}</td>
                                    @endif
                                    <th>{{trans('navmenu.quantity')}}</th>
                                    <th>{{trans('navmenu.damage_cause')}}</th>
                                    <th>{{trans('navmenu.damage_date')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($damages as $index => $damage)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        @if($settings->is_filling_station)
                                        <td>{{$damage->deph_measure+0}}</td>
                                        <td>{{$damage->in_stock+0}}</td>
                                        <td>{{-($damage->quantity+0)}} 
                                        </td>
                                        @else
                                        <td>{{$damage->quantity+0}} 
                                        </td>
                                        @endif
                                        <td>{{$damage->reason}}</td>
                                        <td>{{$damage->created_at}}</td>
                                        <td>
                                            <!-- <a href="{{route('damages.edit' , encrypt($damage->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a> -->
                                            <form id="delete-form-damage-{{$index}}" method="POST" action="{{route('damages.destroy' , encrypt($damage->id))}}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteDamage({{$index}})"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </form>   
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade table-responsive" id="price_tab" role="tabpanel">
                            <table id="example4" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    <td>{{trans('navmenu.date')}}</td>
                                    <td>{{trans('navmenu.user')}}</td>
                                    <th>Retail Price</th>
                                    <th>Wholesale Price</th>
                                </thead>
                                <tbody>
                                    @foreach($pchanges as $key => $pchange)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$pchange->created_at}}</td>
                                        <td>{{$pchange->first_name}}</td>
                                        <td>{{number_format($pchange->retail_price, 2, '.', ',')}} </td>
                                        <td>{{number_format($pchange->wholesale_price, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>
      
    <!-- Modal -->
    <div class="modal animated zoomIn" id="reorderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_reorder_point')}} </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form-horizontal" method="POST" action="{{url('new-reorder-point')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="product_id" value="{{$product->id}}">
                        <div class="col-md-12">
                            <label class="form-label">{{trans('navmenu.reorder_point')}}</label>
                            <input id="register-username" type="number" min="0" name="reorder_point" required placeholder="{{trans('navmenu.hnt_reorder_point')}}" class="form-control form-control-sm mb-1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal animated zoomIn" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_location')}} </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form-horizontal" method="POST" action="{{url('new-location')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="product_id" value="{{$product->id}}">
                        <div class="col-md-12">
                            <label class="form-label">{{trans('navmenu.location')}}</label>
                            <input id="register-username" type="text" min="0" name="location" required placeholder="{{trans('navmenu.hnt_location')}}" class="form-control form-control-sm mb-1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal animated zoomIn" id="sellingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_selling_price')}} </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <form class="form" method="POST" action="{{url('new-sell-price')}}">
                <div class="modal-body row g-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                    <div class="col-md-6">
                        <label for="register-username" class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                        <input id="register-username" type="number" min="0" step="any" name="new_unit_price" required placeholder="{{trans('navmenu.hnt_selling_price')}}" value="{{$product->retail_price}}" class="form-control form-control-sm mb-1">  
                    </div>
                    @if($settings->retail_with_wholesale)
                    <div class="col-md-6">
                        <label for="register-username" class="form-label">{{trans('navmenu.wholesale_price')}}</label>
                        <input id="register-username" type="number" min="0" step="any" name="wholesale_price" placeholder="{{trans('navmenu.hnt_selling_price')}}" value="{{$product->wholesale_price}}" class="form-control form-control-sm mb-1">  
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                </div>
            </form>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal animated zoomIn" id="damageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">
                    @if($settings->is_filling_station)
                    {{trans('navmenu.new_depth_measure')}}
                    @else{{trans('navmenu.new_damage')}}@endif </h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="my-form" method="POST" action="{{route('damages.store')}}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="product_id" value="{{$product->id}}">
                        @if($settings->is_filling_station)
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.depth_measure')}}<span style="color: red;"> *</span></label>
                            <input id="deph_measure" type="number" step="any" name="deph_measure" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1">
                        </div>
                        @else
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.quantity')}}<span style="color: red;"> *</span></label>
                            <input id="damaged" type="number" min="0" step="any" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1">
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.date')}}</label>
                            <select onchange="wegDam(this)" class="form-select form-select-sm mb-3">
                                <option value="auto">Auto</option>
                                <option value="manual">Manual</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="dam_date_field" style="display: none;">
                            <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="dam_date" id="dam_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{trans('navmenu.damage_cause')}}<span style="color: red;"> *</span></label>
                            <textarea name="reason" placeholder="{{trans('navmenu.hnt_damage_cause')}}" class="form-control form-control-sm mb-3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 

    <script>
        $(function () {
              var table = $('#example2').DataTable({
                'scrollX': true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(1)');

            var table7 = $('#example7').DataTable({
                'scrollX': true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table7.buttons().container()
                .appendTo('#example7_wrapper .col-md-6:eq(1)');
            $('#example').DataTable({
                'scrollX': true,
            });
            $('#example1').DataTable({
                'scrollX': true,
            });
            $('#example3').DataTable({
                'scrollX': true,
            });
            $('#example4').DataTable({
                'scrollX': true,
            });
        });
    </script>
@endsection
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">

<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $dam = document.querySelector('[name="dam_date"]');

            $dam.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    :  new Date(),
            });

        });
    </script>

    <script>

        function ImagetoPrint(source)
        {
            return "<html><head><scri"+"pt>function step1(){\n" +
                    "setTimeout('step2()', 10);}\n" +
                    "function step2(){window.print();window.close()}\n" +
                    "</scri" + "pt></head><body onload='step1()'>\n" +
                    "<img src='" + source + "' /></body></html>";
        }

        function PrintImage(source)
        {
            var Pagelink = "about:blank";
            var pwa = window.open(Pagelink, "_new");
            pwa.document.open();
            pwa.document.write(ImagetoPrint(source));
            pwa.document.close();
        }

    </script>
