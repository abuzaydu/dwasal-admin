@extends('layouts.prod')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{ asset('js/angular-1-8-3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pricing.js') }}"></script>
    <script type="text/javascript">
        function confirmCancel(id) {
            Swal.fire({
                title: 'Are you sure, You want to cancel this pricing?',
                showDenyButton: true,
                confirmButtonText: 'Yes Cancel',
                denyButtonText: `Don't Cancel`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href="{{url('cancel-pricing')}}/"+id;
                    Swal.fire('Cancelled!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('pricing not cancelled', '', 'info')
                }
            });
        }

        function selectedSite(elem) {
            var id = elem.value.replace("number:", "");
            angular.element(document.getElementById('mycontroller')).scope().updateSiteId(id);
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    @if(is_null($pricing))
    <div class="row">
        <div class="col-12">
            <div class="card print_invoice pt-2">
                <div class="card-body">
                    <form class="row g-1 mb-1" method="POST" action="{{ url('product-pricings/create') }}">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="product_id" required onchange="this.form.submit()">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                <option value="{{$product->id}}">{{$product->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </div>
    @else
    <div class="row g-3" id="mycontroller" ng-controller="SearchItemCtrl" ng-init="pricingTempId('<?php echo $pricing->id; ?>')">
        <div class="col-12">
            <div class="card print_invoice pt-2">
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Product <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm mb-1" name="product_id" id="product_id" required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                @if($product->id == $pricing->product_id)
                                <option value="{{$product->id}}" selected>{{$product->name}}</option>
                                @else
                                <option value="{{$product->id}}">{{$product->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i> 
                                <input type="text" class="form-control form-control-sm mb-1" name="date" id="date" value="{{$pricing->date}}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Material Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50%; text-transform: uppercase;">Item Description</th>
                                                        <th style="text-align: center; width: 15%;">Unit Price</th>
                                                        <th style="text-align: center; width: 15%;">No. Pieces Made</th>
                                                        <th style="text-align: center; width: 20%;">Cost Per Piece</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item-row" ng-repeat="newmaterial in materialcosts">
                                                        <td class="description">
                                                            @{{ newmaterial.item_desc }}
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newmaterial.unit_cost" ng-blur="updateMaterialCosts(newmaterial)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newmaterial.no_of_piece_made" ng-blur="updateMaterialCosts(newmaterial)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">@{{ newmaterial.cost_per_piece | number:2 }}</td>
                                                        <td><a class="text-danger" href="javascript:;" ng-click="removeMaterialCost(newmaterial.id)" title="Remove row"><i class="fa fa-close"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"><b>Total Material Cost</b></td>
                                                        <td style="text-align: center;"><b>@{{ sumMaterialCosts(materialcosts) | number:2 }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Labour Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50%; text-transform: uppercase;">Production Stage</th>
                                                        <th style="text-align: center; width: 15%;">Daily Wage Rate</th>
                                                        <th style="text-align: center; width: 15%;">No. Pieces Made</th>
                                                        <th style="text-align: center; width: 20%;">Cost Per Piece</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item-row" ng-repeat="newlabour in labourcosts">
                                                        <td class="description">
                                                            <textarea type="text" rows="1" ng-model="newlabour.stage" ng-blur="updateLabourCosts(newlabour)" style="border: 1px solid grey;"></textarea>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newlabour.daily_wage_rate" ng-blur="updateLabourCosts(newlabour)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newlabour.no_of_piece" ng-blur="updateLabourCosts(newlabour)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">@{{ newlabour.cost_per_piece | number:2 }}</td>
                                                        <td><a class="text-danger" href="javascript:;" ng-click="removeLabourCost(newlabour.id)" title="Remove row"><i class="fa fa-close"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"><b>Total Labour Cost</b></td>
                                                        <td style="text-align: center;"><b>@{{ sumLabourCosts(labourcosts) | number:2 }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="hiderow">
                                        <td><a href="javascript:;" ng-click="addLabourCost()" title="Add a row">Add a row</a></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Transport Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50%; text-transform: uppercase;">Description</th>
                                                        <th style="text-align: center; width: 15%;">Transport Cost</th>
                                                        <th style="text-align: center; width: 15%;">No. Items</th>
                                                        <th style="text-align: center; width: 20%;">Cost Per Item</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item-row" ng-repeat="newtransport in transportcosts">
                                                        <td class="description">
                                                            <textarea type="text" rows="1" ng-model="newtransport.description" ng-blur="updateTransportCosts(newtransport)" style="border: 1px solid grey;"></textarea>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newtransport.transport_cost" ng-blur="updateTransportCosts(newtransport)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newtransport.no_of_items" ng-blur="updateTransportCosts(newtransport)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @{{ newtransport.cost_per_unit | number:2 }}
                                                        </td>
                                                        <td><a class="text-danger" href="javascript:;" ng-click="removeTransportCost(newtransport.id)" title="Remove row"><i class="fa fa-close"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"><b>Total Transport Cost</b></td>
                                                        <td style="text-align: center;"><b>@{{ sumTransportCosts(transportcosts) | number:2 }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="hiderow">
                                        <td><a href="javascript:;" ng-click="addTransportCost()" title="Add a row">Add a row</a></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Summary of Direct Costs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <tbody>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right;"><b>Total Material Costs</b></td>
                                                        <td style="text-align: right;"><b>@{{ sumMaterialCosts(materialcosts) | number:2 }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right;"><b>Total Labour Costs</b></td>
                                                        <td style="text-align: right;"><b>@{{ sumLabourCosts(labourcosts) | number:2 }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="text-align: right;"><b>Total Transport Cost</b></td>
                                                        <td style="text-align: right;"><b>@{{ sumTransportCosts(transportcosts) | number:2 }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" style="text-align: right;"><b>SubTotal 1 </b></th>
                                                        <th style="text-align: right;"><b>@{{ sumDirectCosts(materialcosts, labourcosts, transportcosts) | number:2 }}</b></th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Local Indirect costs (Overhead costs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 65%; text-transform: uppercase;">Description</th>
                                                        <th style="text-align: center; width: 15%;">Percentage(%)</th>
                                                        <th style="text-align: center; width: 20%;">Cost Per Item</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item-row" ng-repeat="newlocalindirect in localindirectcosts">
                                                        <td class="description">
                                                            <textarea type="text" rows="1" ng-model="newlocalindirect.description" ng-blur="updateLocalIndirectCosts(newlocalindirect)" style="border: 1px solid grey;"></textarea>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newlocalindirect.percent" ng-blur="updateLocalIndirectCosts(newlocalindirect)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @{{ newlocalindirect.amount | number:2 }}
                                                        </td>
                                                        <td><a class="text-danger" href="javascript:;" ng-click="removeLocalIndirectCost(newlocalindirect.id)" title="Remove row"><i class="fa fa-close"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Mark-up %</td>
                                                        <td style="text-align: center;"><b>@{{ sumLocalIndirectCostsPercent(localindirectcosts) }}</b></td>
                                                        <td style="text-align: center;"><b>@{{ sumLocalIndirectCosts(localindirectcosts) | number:2 }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;"><b>LOCAL TOTAL / EX Works Price</b></th>
                                                        <th style="text-align: center; font-size: 16px;"><b>@{{ sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumLocalIndirectCosts(localindirectcosts) | number:2 }}</b></th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="hiderow">
                                        <td><a href="javascript:;" ng-click="addLocalIndirectCost()" title="Add a row">Add a row</a></td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Local Packaging Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50%; text-transform: uppercase;">Item Description</th>
                                                        <th style="text-align: center; width: 15%;">Package Cost</th>
                                                        <th style="text-align: center; width: 15%;">No. Items</th>
                                                        <th style="text-align: center; width: 20%;">Cost Per Item</th>
                                                        <td></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item-row" ng-repeat="newlocalpackage in localpackagecosts">
                                                        <td class="description">
                                                            <textarea type="text" rows="1" ng-model="newlocalpackage.item_desc" ng-blur="updateLocalPackageCosts(newlocalpackage)" style="border: 1px solid grey;"></textarea>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newlocalpackage.package_cost" ng-blur="updateLocalPackageCosts(newlocalpackage)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newlocalpackage.no_of_items" ng-blur="updateLocalPackageCosts(newlocalpackage)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @{{ newlocalpackage.unit_cost | number:2 }}
                                                        </td>
                                                        <td><a class="text-danger" href="javascript:;" ng-click="removeLocalPackageCost(newlocalpackage.id)" title="Remove row"><i class="fa fa-close"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"><b>Total Packaging Costs</b></td>
                                                        <td style="text-align: center;"><b>@{{ sumLocalPackageCosts(localpackagecosts) | number:2 }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" style="text-align: right; font-size: 16px;"><b>Local Ex-Works + Packaging </b></th>
                                                        <th colspan="2" style="text-align: center; font-size: 16px;"><b>@{{ sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumLocalIndirectCosts(localindirectcosts)+sumLocalPackageCosts(localpackagecosts) | number:2 }}</b></th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="hiderow">
                                        <td><a href="javascript:;" ng-click="addLocalPackagingCost()" title="Add a row">Add a row</a></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Indirect costs (Overhead costs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 65%; text-transform: uppercase;">Description</th>
                                                        <th style="text-align: center; width: 15%;">Percentage(%)</th>
                                                        <th style="text-align: center; width: 20%;">Cost Per Item</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item-row" ng-repeat="newindirect in indirectcosts">
                                                        <td class="description">
                                                            <textarea type="text" rows="1" ng-model="newindirect.description" ng-blur="updateIndirectCosts(newindirect)" style="border: 1px solid grey;"></textarea>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newindirect.percent" ng-blur="updateIndirectCosts(newindirect)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @{{ newindirect.amount | number:2 }}
                                                        </td>
                                                        <td><a class="text-danger" href="javascript:;" ng-click="removeIndirectCost(newindirect.id)" title="Remove row"><i class="fa fa-close"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Total Mark-up %</td>
                                                        <td style="text-align: center;"><b>@{{ sumIndirectCostsPercent(indirectcosts) }}</b></td>
                                                        <td style="text-align: center;"><b>@{{ sumIndirectCosts(indirectcosts) | number:2 }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="font-size: 16px;"><b>EXPORT TOTAL / EX Works Price</b></th>
                                                        <th style="text-align: center; font-size: 16px;"><b>@{{ sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts) | number:2 }}</b></th>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="hiderow">
                                        <td><a href="javascript:;" ng-click="addIndirectCost()" title="Add a row">Add a row</a></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Export Packaging Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50%; text-transform: uppercase;">Item Description</th>
                                                        <th style="text-align: center; width: 15%;">Package Cost</th>
                                                        <th style="text-align: center; width: 15%;">No. Items</th>
                                                        <th style="text-align: center; width: 20%;">Cost Per Item</th>
                                                        <td></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item-row" ng-repeat="newpackage in packagecosts">
                                                        <td class="description">
                                                            <textarea type="text" rows="1" ng-model="newpackage.item_desc" ng-blur="updatePackageCosts(newpackage)" style="border: 1px solid grey;"></textarea>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newpackage.package_cost" ng-blur="updatePackageCosts(newpackage)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newpackage.no_of_items" ng-blur="updatePackageCosts(newpackage)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @{{ newpackage.unit_cost | number:2 }}
                                                        </td>
                                                        <td><a class="text-danger" href="javascript:;" ng-click="removePackageCost(newpackage.id)" title="Remove row"><i class="fa fa-close"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3"><b>Total Packaging Costs</b></td>
                                                        <td style="text-align: center;"><b>@{{ sumPackageCosts(packagecosts) | number:2 }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3" style="text-align: right; font-size: 16px;"><b>Export Ex-Works + Packaging </b></th>
                                                        <th colspan="2" style="text-align: center; font-size: 16px;"><b>@{{ sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts)+sumPackageCosts(packagecosts) | number:2 }}</b></th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="hiderow">
                                        <td><a href="javascript:;" ng-click="addPackagingCost()" title="Add a row">Add a row</a></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Conversion to EUR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th>Description</th>
                                                        <th style="text-align: center;">Currency</th>
                                                        <th style="text-align: center;">Ex. Rate</th>
                                                        <th style="text-align: center;">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="font-size: 14px;"><b>Ex-Works + Packaging </b></td>
                                                        <td style="text-align: center;">@{{ pricing.currency }}</td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="pricing.ex_rate" ng-blur="updatePricingTemp(pricing)" style="text-align: center;">
                                                        </td>
                                                        <td style="text-align: center; font-size: 14px;"><b>@{{ (sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts)+sumPackageCosts(packagecosts))/pricing.ex_rate | number:2 }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Export handling costs</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 75%; text-transform: uppercase;">Description</th>
                                                        <th style="text-align: center; width: 20%;">Cost Per Item</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="item-row" ng-repeat="newhandling in handlingcosts">
                                                        <td class="description">
                                                            <textarea type="text" rows="1" ng-model="newhandling.description" ng-blur="updateHandlingCosts(newhandling)" style="border: 1px solid grey;"></textarea>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="newhandling.amount" ng-blur="updateHandlingCosts(newhandling)" style="text-align: center;">
                                                        </td>
                                                        <td><a class="text-danger" href="javascript:;" ng-click="removeHandlingCost(newhandling.id)" title="Remove row"><i class="fa fa-close"></i></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>Total Export handling Cost</b></td>
                                                        <td style="text-align: center;"><b>@{{ sumHandlingCosts(handlingcosts) | number:2 }}</b></td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="hiderow">
                                        <td><a href="javascript:;" ng-click="addHandlingCost()" title="Add a row">Add a row</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Calculating FOB percentage margin:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Min. order value / MOQ (enter your minimum order value here)</b></td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="0" step="any" string-to-number ng-model="pricing.min_order_value" ng-blur="updatePricingTemp(pricing)" style="text-align: center;">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>FOB percentage margin to factor into costing and pricing</b></td>
                                                        <td style="text-align: center;"><b>@{{ (sumHandlingCosts(handlingcosts)/pricing.min_order_value)*100 | number:2 }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>Final FOB price:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Sub Total 2: Ex-Works + Packaging</b></td>
                                                        <td></td>
                                                        <td style="text-align: center; font-size: 14px;"><b>@{{ (sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts)+sumPackageCosts(packagecosts))/pricing.ex_rate | number:2 }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>FOB percentage margin</b></td>
                                                        <td style="text-align: center;"><b>@{{ (sumHandlingCosts(handlingcosts)/pricing.min_order_value)*100 | number:2 }}</b></td>
                                                        <td style="text-align: center;"><b>@{{ ((sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts)+sumPackageCosts(packagecosts))/pricing.ex_rate)*(sumHandlingCosts(handlingcosts)/pricing.min_order_value) | number:2 }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><b>FINAL FOB PRICE</b></td>
                                                        <td style="text-align: center;"><b>@{{ ((sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts)+sumPackageCosts(packagecosts))/pricing.ex_rate)+(((sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts)+sumPackageCosts(packagecosts))/pricing.ex_rate)*(sumHandlingCosts(handlingcosts)/pricing.min_order_value)) | number:2 }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-strpped mt-0">
                                <thead>
                                    <tr>
                                        <th>SET pRICING:</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="item-row">
                                        <td>
                                            <table class="items mt-0">
                                                <tbody>
                                                    <tr>
                                                        <td><b>Number of pieces per set</b></td>
                                                        <td style="text-align: center;">
                                                            <input type="number" min="1" step="1" string-to-number ng-model="pricing.no_of_piece_per_set" ng-blur="updatePricingTemp(pricing)" style="text-align: center;">
                                                        </td>
                                                        <td colspan="2"><b>Set FOB price</b></td>
                                                        <td style="text-align: center; font-size: 14px;"><b>@{{ ((sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts)+sumPackageCosts(packagecosts))/pricing.ex_rate)+(((sumDirectCosts(materialcosts, labourcosts, transportcosts)+sumIndirectCosts(indirectcosts)+sumPackageCosts(packagecosts))/pricing.ex_rate)*(sumHandlingCosts(handlingcosts)/pricing.min_order_value))*pricing.no_of_piece_per_set | number:2 }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('product-pricings.store') }}">
            @csrf
            <input type="hidden" name="pricing_id" value="{{$pricing->id}}">        
            <div class="col-12 text-center text-md-end">
                <button type="submit" class="btn btn-sm btn-primary" ><i class="fa fa-save me-2"></i>Save & Preview</button>
                <button type="button" onclick="confirmCancel('<?php echo encrypt($pricing->id); ?>')" class="btn btn-sm btn-danger"><i class="fa fa-close me-2"></i>Cancel</button>
            </div>
        </form>
    </div> <!-- .row end -->
    @endif
@endsection

@section('page-scripts')
    <script>
        $(document).ready(function() {
            $('#product_id').on('change', function(){
                var selection = $('#product_id').val();
                var product_id = selection.replace('number:', '');
                angular.element(document.getElementById('mycontroller')).scope().updatePricingProduct(product_id);
            });

            $('#date').on('change', function(){
                var selection = $('#date').val();
                angular.element(document.getElementById('mycontroller')).scope().updatePricingDate(selection);
            });
        });
    </script>
@endsection
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]')

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>
