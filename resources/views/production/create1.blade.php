@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/production.js') }}"></script>
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
                        <div class="col-md-4">
                            <h6 class="mb-0">OverHead Expenses</h6>
                        </div>
                        <div class="col-md-4">
                            <select ng-model="mro_id" ng-change="addMro()" ng-options="mro.id as mro.name for mro in mros" class="form-select form-select-sm mb-1">
                                <option value="">---Select OverHead Expense---</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-sm btn-warning float-end mb-1" data-bs-toggle="modal" data-bs-target="#mroModal">
                            <i class="fa fa-plus"></i>
                            {{trans('navmenu.new_type')}}
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
                        <div class="col-md-6">
                            <h6 class="mb-0">Packing Materials</h6>
                        </div>
                        <div class="col-md-6">
                            <select ng-model="pm_id" ng-change="addPM()" ng-options="pm.id as pm.name for pm in pms" class="form-select form-select-sm mb-1">
                                <option value="">---Select Packing Material---</option>
                            </select>
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
                        <div class="col-md-6">
                            <h6 class="mb-0">END PRODUCTS</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select unpacked product to add</label>
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
                                        <th style="text-align: center;">Profit Margin</th>
                                        <th style="text-align: center;">Selling Price</th>
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
                                            <td style="text-align: center;" ><input type="number" string-to-number ng-model="list.profit_margin" ng-blur="updateProducts(list)" name="profit_margin"  min="0" step="any" value="@{{list.profit_margin}}" class="form-control form-control-sm mb-0" style="text-align:center; width: 140px;" ></td>
                                            <td style="text-align: center;" ><input min="0" step="any" string-to-number type="number" name="selling_price" ng-model="list.selling_price" ng-blur="updateProducts(list)" value="@{{list.selling_price}}" class="form-control form-control-sm mb-0" style="text-align:center; width: 140px;" ></td> 
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
                                            <th style="text-align: center;"><b>@{{ sumRM(rmusedtemps)+sumPM(pmusedtemps)+sumMro(mrousedtemps) | number:2}}</b></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <form method="POST" action="{{url('prod-costs/savepanel')}}" class="row g-1 pt-3">
                                    @csrf
                                    <input type="hidden" name="prod_batch" value="{{$prod_batch}}">
                                    <div class="col-md-12 ">
                                        <input type="submit" style="width : 140px;" name="submit" class="btn btn-success btn-sm float-end">
                                    </div>
                                </form>
                            </div>
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
