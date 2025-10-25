@extends('layouts.prod')
<script type="text/javascript">
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {

            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmDeleteDLCI(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {

            if (result.value) {
                document.getElementById('delete-lci-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmDeletePM(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {

            if (result.value) {
                document.getElementById('delete-pm-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }


    function confirmDeletePCI(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {

            if (result.value) {
                document.getElementById('delete-pci-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
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
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{ url('prod-costs') }}">Production Records</a></li>
                    <li class="breadcrumb-item active">{{$title}} {{date('d M, Y', strtotime($prod_cost->date))}}</li>                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

<!-- =========================================================== -->
<div class="row">
    <div class="col-md-12">
        <div class="card radius-10">
            <div class="card-body">   
                <ul class="nav nav-tabs nav-tabs-new2 " role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#tab_0-0" class="nav-link active" role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15"> 
                                        {{trans('navmenu.raw_materials')}}  
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab_2-2" class="nav-link" role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15"> 
                                        Direct Labour Costs  
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab_1-1" class="nav-link" role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15">Manufacturing Overhead(MOH) Costs</div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tab_3-3" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon">
                                    <div class="tab-title font-15">
                                        {{trans('navmenu.packing_materials')}} 
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                </ul>
                    
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="tab_0-0" role="tabpanel">
                        <button type="button" class="btn btn-success btn-sm pull-right" data-bs-toggle="modal" data-bs-target="#mitemModal"><i class="fa fa-cart"></i> Add Raw Material Item</button>
                        <table id="rm_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.material_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @if(!is_null($rms))
                                @foreach($rms as $ref => $rmitem)
                                <tr>
                                    <td>{{$ref+1}}</td>
                                    <td>{{$rmitem->name}}</td>
                                    <td class="qty" style="text-align: center;">
                                        {{$rmitem->quantity+0}}
                                    </td>
                                    <td style="text-align: center;">
                                        {{$rmitem->basic_uom}}</td>
                                    <td style="text-align: center;">{{number_format($rmitem->unit_cost, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($rmitem->total, 2, '.', ',')}}</td>
                                    <td>
                                        <a href="{{route('rm-used-items.edit', encrypt($rmitem->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>|
                                        <form id="delete-form-{{$ref}}" style="display: inline;" method="POST" action="{{route('rm-used-items.destroy' , encrypt($rmitem->id))}}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" onclick="confirmDelete('{{$ref}}')">
                                            <i class="fa fa-trash" style="color: red;"></i></a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                        
                    <div class="tab-pane fade " id="tab_2-2" role="tabpanel">
                        <button type="button" class="btn btn-primary btn-sm pull-right" data-bs-toggle="modal" data-bs-target="#lciModal"><i class="fa fa-cart"></i> Add Labour Cost Item</button>
                        <table id="rm_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th style="text-align: center;">Action</th>
                            </thead>
                            <tbody>
                                @if(!is_null($dlcitems))
                                @foreach($dlcitems as $ref => $item)
                                <tr>
                                    <td>{{$ref+1}}</td>
                                    <td>{{$item->stage}}</td>
                                    <td style="text-align: center;">{{$item->qty+0}}</td>
                                    <td style="text-align: center;">{{number_format($item->unit_cost, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($item->total, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('dlc-items.edit', encrypt($item->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>|
                                        <form id="delete-lci-form-{{$ref}}" style="display: inline;" method="POST" action="{{ route('dlc-items.destroy' , encrypt($item->id))}}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" onclick="confirmDeleteDLCI('{{$ref}}')">
                                            <i class="fa fa-trash" style="color: red;"></i></a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade" id="tab_1-1" role="tabpanel">
                        <table id="mro_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.mro_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @if(!is_null($mros))
                                @foreach($mros as $key => $mroitem)
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$mroitem->name}}</td>
                                    <td style="text-align: center;">{{$mroitem->qty+0}}</td>
                                    <td style="text-align: center;">{{number_format($mroitem->unit_cost, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($mroitem->total, 2, '.', ',')}}</td>
                                    <td>
                                        
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade " id="tab_3-3" role="tabpanel">
                        <button type="button" class="btn btn-warning btn-sm pull-right" data-bs-toggle="modal" data-bs-target="#pitemModal"><i class="fa fa-cart"></i> Add Packing Material Item</button>
                        <table id="pm_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.packing_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                <th style="text-align: center;">Action</th>
                            </thead>
                            <tbody>
                                @if(!is_null($pms))
                                @foreach($pms as  $index => $pm )
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$pm->name}}</td>
                                    <td style="text-align: center;">{{$pm->quantity+0}}</td>
                                    <td style="text-align: center;">{{$pm->package_unit}}</td>
                                    <td style="text-align: center;">{{number_format($pm->unit_cost, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($pm->total, 2, '.', ',')}}</td>
                                    <td>
                                        <a href="{{route('pm-used-items.edit', encrypt($pm->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>|
                                        <form id="delete-pm-form-{{$index}}" style="display: inline;" method="POST" action="{{route('pm-used-items.destroy' , encrypt($pm->id))}}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" onclick="confirmDeletePM('{{$index}}')">
                                            <i class="fa fa-trash" style="color: red;"></i></a>
                                        </form>
                                    </td>
                                </tr> 
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>       
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row pb-8" >
    <div class="col-md-12">
        <div class="card radius-6">
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-3" >
                        <h6>
                            {{trans('navmenu.batch_no')}} : <span>{{$prod_cost->prod_batch}}</span>
                        </h6>
                    </div> 
                    <div class="col-md-4" style="text-align: center;">
                        <h6>
                            {{trans('navmenu.total_production_volume')}} : <span>{{$prod_cost->total_vol}}</span>
                        </h6>
                    </div>  
                    <div class="col-md-5" >
                        <h6>
                            Total Cost of Production : <span> {{number_format($prod_cost->total_cost, 2, '.', ',')}}</span>
                        </h6>
                    </div>  
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-sm pull-right" data-bs-toggle="modal" data-bs-target="#endProdModal"><i class="fa fa-cart"></i> Add End Product</button>
                        <table class="table">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.product_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_packed')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">Action</th>
                            </thead>
                            <tbody>
                                @foreach($prod_cost_items as $ky => $prod_cost_item)
                                <tr>
                                    <td>{{$ky+1}}</td>
                                    <td>{{$prod_cost_item->name}}</td>
                                    <td style="text-align: center;">{{$prod_cost_item->unit_packed+0}}</td>
                                    <td style="text-align: center;">{{$prod_cost_item->quantity+0}}</td>
                                    <td style="text-align: center;">{{ number_format($prod_cost_item->cost_per_unit, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">
                                        <a href="{{route('prod-cost-items.edit', encrypt($prod_cost_item->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a>|
                                        <form id="delete-pci-form-{{$ky}}" style="display: inline;" method="POST" action="{{route('prod-cost-items.destroy' , encrypt($prod_cost_item->id))}}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="#" onclick="confirmDeletePCI('{{$ky}}')">
                                            <i class="fa fa-trash" style="color: red;"></i></a>
                                        </form>
                                    </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <form class="row g-1" action="{{ route('prod-costs.update', encrypt($prod_cost->id))}}" method="POST">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label class="form-label">Production Date <span style="color: red;">*</span></label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="date" value="{{$prod_cost->date}}"  placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" ng-change="updateRmTempInfo(rmtemp)">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" value="{{$prod_cost->remarks}}" placeholder="Enter Production Remarks" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

    
    <!-- Modal -->
    <div class="modal fade" id="mitemModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Raw Material Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('rm-used-items.store') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="production_cost_id" value="{{$prod_cost->id}}">
                    <div class="col-md-6">
                        <label>Item Name <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="raw_material_id" required style="width: 100%;">
                            <option value="">Select Item</option>
                            @foreach($rmaterials as $key => $rm)
                            <option value="{{$rm->id}}">{{$rm->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>    

 <!-- Modal -->
    <div class="modal fade" id="pitemModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Packing Material Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('pm-used-items.store') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="production_cost_id" value="{{$prod_cost->id}}">
                    <div class="col-md-6">
                        <label>Item Name <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="packing_material_id" required style="width: 100%;">
                            <option value="">Select Item</option>
                            @foreach($pmaterials as $key => $pm)
                            <option value="{{$pm->id}}">{{$pm->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.product_packed')}}</label>
                        <select name="product_id" class="form-select form-select-sm mb-4" required>
                            <option value="">Select product</option>
                            @foreach($products as $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.unit_packed')}}</label>
                        <input id="name" type="text" name="unit_packed" placeholder="Please enter quantity" class="form-control form-control-sm mb-4">
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>    

    <!-- Modal -->
    <div class="modal fade" id="lciModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Labour Cost Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('dlc-items.store') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="production_cost_id" value="{{$prod_cost->id}}">
                    <div class="col-md-12 mb-4">
                        <label>Item Name <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="production_stage_id" required style="width: 100%;">
                            <option value="">Select Item</option>
                            @foreach($stages as $key => $stg)
                            <option value="{{$stg->id}}">{{$stg->stage}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="qty" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.unit_cost')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="unit_cost" placeholder="Enter Unit Cost" class="form-control form-control-sm mb-1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>


 <!-- Modal -->
    <div class="modal fade" id="endProdModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Packing Material Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('prod-cost-items.store') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="production_cost_id" value="{{$prod_cost->id}}">
                    
                    <div class="col-md-6">
                        <label class="form-label">End Product</label>
                        <select name="product_id" class="form-select form-select-sm mb-4" required>
                            <option value="">Select product</option>
                            @foreach($products as $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" step="any" min="0" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.unit_packed')}}</label>
                        <input id="name" type="text" name="unit_packed" placeholder="Please enter quantity" class="form-control form-control-sm mb-4">
                    </div> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>    

@endsection 
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>