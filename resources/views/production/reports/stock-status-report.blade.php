@extends('layouts.prod')

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

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.raw_materials')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2-2" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.packing_materials')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                      <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                        <div style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                  @if(!is_null($shop->logo_location))
                                  <figure>
                                      <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                  </figure>
                                  @endif
                                  <h6>{{$shop->name}}</h6>
                                  <h5 class="title">{{trans('navmenu.rm_stock_status_report')}}</h5>
                                  <p> {{$reporttime}}</p>          
                        </div>
                        <div class="invoice-content">
                          <table id="rm_stock_report" class="table table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0; text-align: center;">
                              <tr>
                                <th style="text-align: left;">{{trans('navmenu.material_name')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.in_stock')}}</th>
                                <th style="text-align: center;">{{trans('navmenu.stock_value')}}</th>
                              </tr>
                            </thead>
                            <tbody>
                                @foreach($rm_status as $index => $stock)
                                <tr>
                                  <td style="text-align: left; border-bottom: 1px solid #e0e0e0; padding-left: 2px;">{{$stock['name']}}</td>
                                  <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ $stock['in_store']}}</td>
                                  <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ $stock['cost']}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <footer>
                                <tr>
                                    <td style="text-align: left;">TOTAL</td>
                                    <td></td>
                                    <td style="text-align: center;">{{number_format($total_rm)}}</td>
                                </tr>
                            </footer>
                          </table>
                        </div>
                        </div>
                        <div class="tab-pane fade" id="tab_2-2" role="tabpanel">
                            <div style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                  @if(!is_null($shop->logo_location))
                                  <figure>
                                      <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                  </figure>
                                  @endif
                                  <h6>{{$shop->name}}</h6>
                                  <h5 class="title">{{trans('navmenu.pm_stock_status_report')}}</h5>
                                  <p> {{$reporttime}}</p>          
                            </div>
                          <div class="invoice-content">
                            <table  class="table table-striped display nowrap" id="pm_stock_report" style="width:100%;">
                              <thead style="background:#E0E0E0; text-align: center;">
                                  <tr>
                                    <th style="text-align: left;">{{trans('navmenu.packing_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.in_stock')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.stock_value')}}</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach($pm_status as $index => $stock)
                                  <tr>
                                    <td style="text-align: left; border-bottom: 1px solid #e0e0e0; padding-left: 2px;">{{$stock['name']}}</td>
                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ $stock['in_store']}}</td>
                                    <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{ $stock['cost']}}</td>
                                  </tr>
                                @endforeach
                              </tbody>
                              <footer>
                                <tr>
                                    <td style="text-align: left;">TOTAL</td>
                                    <td ></td>
                                    <td style="text-align: center;">{{number_format($total_pm)}}</td>
                                </tr>
                            </footer>
                            </table>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection