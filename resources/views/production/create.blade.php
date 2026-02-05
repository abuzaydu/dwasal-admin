@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/production.js') }}"></script>
<script type="text/javascript">
    function confirmDelete() {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Cancel It",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('cancel-prod-panel') }}";
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }
</script>
<style type="text/css">
    .gridCard{
        padding-top: 10px;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .gridCard::-webkit-scrollbar {
      display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .gridCard {
      -ms-overflow-style: none;  /* IE and Edge */
      scrollbar-width: none;  /* Firefox */
    }

    .gridScale{
        width : 8rem; 
        height: 5rem;
    }

    .gridName{
         font-size: 18;
         white-space: nowrap; 
         overflow: hidden; 
         text-overflow: ellipsis; 
    }

    .qtySize{
        text-align:center; 
        height: 20px; 
        width: 10px; 
        border: 1px solid #e0e0e0;
        padding-right: 2px;
    }
</style>

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row print_invoice">
                        <div class="col-md-6">
                            <h6 class="mb-0">Raw Materials</h6>
                        </div>
                        <div class="col-md-6">
                            <select ng-model="rm_id" ng-change="addRM()" ng-options="rm.id as rm.name for rm in rms" class="form-select form-select-sm mb-1">
                                <option value="">---Select Raw Materials---</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="p-2 border rounded">
                                <table  class="items mt-0"  style="width: 100%;  display: block; white-space: nowrap; overflow: auto;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">{{trans('navmenu.material_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="rmusedtemp in rmusedtemps" id="temps">
                                            <td>@{{$index + 1}}</td>
                                            <td>@{{rmusedtemp.name}}</td>
                                            <td><input type="number" name="quantity" ng-blur="updateRMTemp(rmusedtemp)" string-to-number ng-model="rmusedtemp.quantity" min="0" step="any" value="@{{rmusedtemp.quantity}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align:center;">@{{rmusedtemp.unit_cost | number:2}}</td>
                                            <td style="text-align:center;">@{{rmusedtemp.total | number:2}}</td>
                                            <td><a href="#" ng-click="removeRMTemp(rmusedtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="3"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>@{{sumRM(rmusedtemps) | number:2}}</b></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-6">
                            <h6 class="mb-0">Direct Labour Costs</h6>
                        </div>
                        <div class="col-md-6">
                            <select ng-model="stage_id" ng-change="addDLC()" ng-options="stage.id as stage.stage for stage in stages" class="form-select form-select-sm mb-1">
                                <option value="">---Select Item---</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="p-2 border rounded">
                                <table class="items mt-0"  style="width: 100%;  display: block; white-space: nowrap; overflow: auto;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">{{trans('navmenu.mro_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="dlctemp in dlctemps" id="temps">
                                            <td style="text-align: center;">@{{$index + 1}}</td>
                                            <td style="text-align: left;">@{{dlctemp.stage}}</td>
                                            <td style="text-align: center;"><input type="number" name="quantity" string-to-number ng-blur="updateDLCTemp(dlctemp)" ng-model="dlctemp.quantity" min="0" step="any" value="@{{dlctemp.quantity}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align: center;"><input type="number" name="unit_cost" string-to-number ng-blur="updateDLCTemp(dlctemp)" ng-model="dlctemp.unit_cost" min="0" step="any" value="@{{dlctemp.unit_cost}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align: center;">@{{dlctemp.total | number:2}}</td>
                                            <td style="text-align: center;"><a href="#" ng-click="removeDLCTemp(dlctemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="3"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>@{{sumDLC(dlctemps) | number:2}}</b></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-6">
                            <h6 class="mb-0">Manufacturing Overhead(MOH) Costs</h6>
                        </div>
                        <div class="col-md-4">
                            <select ng-model="mro_id" ng-change="addMro()" ng-options="mro.id as mro.name for mro in mros" class="form-select form-select-sm mb-1">
                                <option value="">---Select Item---</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-warning float-end mb-1" data-bs-toggle="modal" data-bs-target="#mroModal">
                            <i class="fa fa-plus"></i>
                            New Item
                            </button>
                        </div>
                        <div class="col-md-12">
                            <div class="p-2 border rounded">
                                <table class="items mt-0"  style="width: 100%;  display: block; white-space: nowrap; overflow: auto;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">{{trans('navmenu.mro_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="mrousedtemp in mrousedtemps" id="temps">
                                            <td style="text-align: center;">@{{$index + 1}}</td>
                                            <td style="text-align: left;">@{{mrousedtemp.name}}</td>
                                            <td style="text-align: center;"><input type="number" name="quantity" string-to-number ng-blur="updateMroTemp(mrousedtemp)" ng-model="mrousedtemp.quantity" min="0" step="any" value="@{{mrousedtemp.quantity}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align: center;"><input type="number" name="unit_cost" string-to-number ng-blur="updateMroTemp(mrousedtemp)" ng-model="mrousedtemp.unit_cost" min="0" step="any" value="@{{mrousedtemp.unit_cost}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align: center;">@{{mrousedtemp.total | number:2}}</td>
                                            <td style="text-align: center;"><a href="#" ng-click="removeMroTemp(mrousedtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="3"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>@{{sumMro(mrousedtemps) | number:2}}</b></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr>
                        @if($settings->enable_packaging)
                        <div class="col-md-6">
                            <h6 class="mb-0">Packing Materials</h6>
                        </div>
                        <div class="col-md-6">
                            <select ng-model="pm_id" ng-change="addPM()" ng-options="pm.id as pm.name for pm in pms" class="form-select form-select-sm mb-1">
                                <option value="">---Select Packing Material---</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <span class="text-danger"><b>NOTE: </b> Sub Packing material costs will be included automatically (e.g Labels and Stickers )</span>
                        </div>
                        <div class="col-md-12">
                            <div class="p-2 border rounded">
                                <table class="items mt-0" style="width: 100%; display: block; white-space: nowrap; overflow: auto;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">{{trans('navmenu.packing_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.product_packed')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_packed')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="pmusedtemp in pmusedtemps" id="temps">
                                            <td style="text-align: center;">@{{$index + 1}}</td>
                                            <td style="text-align: left;">@{{pmusedtemp.name}}</td>
                                            <td style="text-align: center;"><input type="number" name="quantity" string-to-number ng-blur="updatePMTemp(pmusedtemp)" ng-model="pmusedtemp.quantity" min="0" step="any" value="@{{pmusedtemp.quantity}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align: center;">
                                                <select id="prod_packed@{{$index + 1}}" name ="product_packed" class="form-select form-select-sm my_select" ng-model="pmusedtemp.product_packed" ng-change="updatePMTemp(pmusedtemp)" style="width: 200px;" ng-options="product.id as product.name for product in products">
                                                    
                                                </select> 
                                            </td>
                                            <td style="text-align: center;"><input type="number" name="unit_packed" string-to-number ng-blur="updatePMTemp(pmusedtemp)" ng-model="pmusedtemp.unit_packed" min="0" step="any" value="@{{pmusedtemp.unit_packed}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                            <td style="text-align:center;">@{{pmusedtemp.unit_cost | number:2}}</td>
                                            <td style="text-align:center;">@{{pmusedtemp.total | number:2}}</td>
                                            <td style="text-align: center;"><a href="#" ng-click="removePMTemp(pmusedtemp.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="5"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>@{{sumPM(pmusedtemps) | number:2}}</b></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr>
                        @endif
                        <div class="col-md-12">
                            <button ng-click="updateTemp()" class="btn btn-outline-primary btn-sm float-end"><i class="fa fa-rotate"></i> Update</button>
                        </div>

                        <div class="col-md-6">
                            <h6 class="mb-0">Products Produced</h6>
                            <select id="prod_packed@{{$index + 1}}" name ="product_packed" class="form-select form-select-sm mb-1" ng-model="selectedproducts.product_packed" ng-change="AddProducts(selectedproducts)" ng-options="list.id as list.name for list in products">
                                <option value="">{{trans('navmenu.select_product')}}</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="p-3 border rounded">
                                <table class="items mt-0" style="width: 100%; display: block; white-space: nowrap; overflow: auto;">
                                    <thead>
                                        <th style="text-align: center;">Product Name</th>
                                        <th style="text-align: center;">Quantity</th>
                                        <th style="text-align: center;">Unit Packed</th>
                                        <th style="text-align: center;">Ratio(%)</th>
                                        <th style="text-align: center;">Cost per Product</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;"></th>
                                    </thead>
                                    <tbody>
                                        <tr  ng-repeat="list in product_made">
                                            <td style="text-align: left;">@{{list.name}}</td>
                                            <td style="text-align: center;">
                                                <input ng-if="!list.packing_material_id" type="number" name="qty"  string-to-number min="0" step="any" value="@{{list.qty}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm mb-0" ng-blur="updateProducts(list)" ng-model="list.qty">
                                                <input ng-if="list.packing_material_id" type="number" name="qty"  string-to-number min="0" step="any" value="@{{list.qty}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm mb-0" ng-blur="updateProducts(list)" ng-model="list.qty" readonly>
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="number" name="unit_packed"  string-to-number min="0" step="any" value="@{{list.unit_packed}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm mb-0" ng-blur="updateProducts(list)" ng-model="list.unit_packed">
                                            </td>
                                            <td style="text-align: center;">@{{ (list.unit_packed*list.qty/sumVolProduced(product_made)*100 | number:2)}}</td>
                                            <td style="text-align: center;" ng-model="list.cost_per_unit">@{{list.cost_per_unit | number:2}}</td>
                                            <td style="text-align: center;">@{{list.qty*list.cost_per_unit | number:2}}</td>
                                            <td style="text-align: center;"><a href="#" ng-click="removeProduct(list.id)"><span class="fa fa-trash" aria-hidden="true" style="color: red"></span></a></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="text-align: center;"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>@{{sumQty(product_made)}}</b></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: center;"><b>@{{ sumRM(rmusedtemps)+sumPM(pmusedtemps)+sumMro(mrousedtemps)+sumDLC(dlctemps) | number:2}}</b></th>
                                            <!-- <th></th> -->
                                            <!-- <th></th> -->
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12">
                            <form method="POST" action="{{ route('prod-costs.store') }}" class="row g-1 pt-3">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label">Production Date <span style="color: red;">*</span></label>
                                    <div class="inner-addon left-addon">
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" ng-change="updateRmTempInfo(rmtemp)">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Remarks</label>
                                    <input type="text" name="remarks" placeholder="Enter Production Remarks" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-md-6">
                                    <input type="hidden" name="prod_batch" value="{{$prod_batch}}">
                                </div>
                                <div class="col-md-6 ">
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
                                    <a href="javascript:;" onclick="return confirmDelete()" class="btn btn-warning btn-sm float-end" style="margin-right: 5px;">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <div class="modal fade" id="mroModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">{{trans('navmenu.new_type')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form" method="POST" action="{{route('mro.store')}}">
                    @csrf
                    <div class="row ms-10">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.expense_type')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.mro_name')}}" class="form-control form-control-sm mb-4">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="float-start">
                            <button type="submit" class="btn btn-success btn-sm">Save</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
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
            var $min = document.querySelector('[name="date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>