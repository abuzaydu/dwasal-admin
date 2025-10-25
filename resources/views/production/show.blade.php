@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{ url('prod-costs') }}">Production Records</a></li>
                    <li class="breadcrumb-item active">{{$title}} {{date('d M, Y', strtotime($prod_cost->date))}}</li>
                </ul>
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12 text-right pt-0">
                @if(!$prod_cost->is_transferred)
                <a href="{{url('prod-transfer-to/'.encrypt($prod_cost->id))}}" class="btn btn-success btn-sm">
                    <span class="fa fa-send"></span> {{trans('navmenu.stock_transfer')}}
                </a>
                @endif
                <a href="{{ route('prod-costs.edit', encrypt($prod_cost->id))}}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit Production</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 pt-2">
    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-primary">{{trans('navmenu.rm_cost')}}</p>
                        <h4 class="my-1">@if(!is_null($rmuse)) {{number_format($rmuse->total_cost, 2, '.', ',')}}@endif</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-primary">Direct Labour Costs</p>
                        <h4 class="my-1">@if(!is_null($dlc)){{number_format($dlc->total_cost, 2, '.', ',')}}@endif</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-primary">MOH Costs</p>
                        <h4 class="my-1">@if(!is_null($mrouse)){{number_format($mrouse->total_cost, 2, '.', ',')}}@endif </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card radius-10 ">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <p class="mb-0 text-primary">{{trans('navmenu.pm_cost')}}</p>
                        <h4 class="my-1">@if(!is_null($pmuse)){{ number_format($pmuse->total_cost, 2, '.', ',')}}@endif</h4>
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
                        <h6>{{trans('navmenu.batch_no')}} : 
                        <span>{{$prod_cost->prod_batch}}</span>
                        </h6>
                    </div> 
                    <div class="col-md-4" >
                        <h6>{{trans('navmenu.total_production_volume')}} : 
                        <span>{{$prod_cost->total_vol}}</span>
                        </h6>
                    </div>  
                    <div class="col-md-5" >
                        <h6>Total Cost of Production : 
                            <span> {{number_format($prod_cost->total_cost, 2, '.', ',')}}</span>
                        </h6>
                    </div> 
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.product_name')}}</th>
                                <th style="text-align: center;">Is By Product?</th>
                                <th style="text-align: center;">{{trans('navmenu.ratio')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_packed')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                            </thead>
                            <tbody>
                                <?php $total_percent = 0; $total_qty = 0; ?>
                                @foreach($prod_cost_items as $ky => $prod_cost_item)
                                <?php 

                                    if ($total_vol > 0){
                                        $percent = round((($prod_cost_item->quantity*$prod_cost_item->unit_packed)/$total_vol)*100, 2);
                                    }

                                    $total_percent += $percent;
                                    $total_qty += $prod_cost_item->quantity;
                                ?>
                                <tr>
                                    <td>{{$ky+1}}</td>
                                    <td>{{$prod_cost_item->name}}</td>
                                    <td style="text-align: center;">
                                        @if($prod_cost_item->is_by_product)
                                        Yes
                                        @else
                                        No
                                        @endif
                                    </td>
                                    <td style="text-align: center;"> {{$percent}}%</td>
                                    <td style="text-align: center;">{{$prod_cost_item->unit_packed+0}}</td>
                                    <td style="text-align: center;">{{$prod_cost_item->quantity+0}}</td>
                                    <td style="text-align: center;">{{ number_format($prod_cost_item->cost_per_unit, 2, '.', ',')}}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th colspan="2">Total Product Qty</th>
                                    <th style="text-align: center;">{{$total_percent}}%</th>
                                    <th></th>
                                    <th style="text-align: center;">{{$total_qty}}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <b>NOTE</b> : <span style="color: red;">By-Product are not assigned the production cost. It's Revenue will be included in Income statement as Other Income</span> 
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </tr>
                </div>
            </div>
        </div>
    </div>    
</div>

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
                        <table id="rm_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.material_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                            </thead>
                            <tbody>
                                @if(!is_null($rms))
                                @foreach($rms as $ref => $rmitem)
                                <tr>
                                    <td>{{$ref+1}}</td>
                                    <td>{{$rmitem->name}}</td>
                                    <td style="text-align: center;">{{$rmitem->quantity+0}}</td>
                                    <td><span style="color: gray; text-align: center;">
                                        {{$rmitem->basic_uom}}</span></td>
                                    <td style="text-align: center;">{{number_format($rmitem->unit_cost, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($rmitem->total, 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                        
                    <div class="tab-pane fade " id="tab_2-2" role="tabpanel">
                        <table id="rm_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
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
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade " id="tab_3-3" role="tabpanel">
                        <table id="pm_used" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <th>#</th>
                                <th>{{trans('navmenu.packing_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                <th style="text-align: center;">UOM</th>
                                <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
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
@endsection 

