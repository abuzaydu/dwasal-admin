@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <form class="dashform row g-1" action="{{url('general-report')}}" method="POST">
                    @csrf

                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-6">
                        <div class="input-group">
                            <button type="button" class="btn btn-white float-end" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                            </button>
                        </div>
                    </div>
                    <!-- /.form group -->
                    <div class="col-md-6">
                        <a href="#" onclick="javascript:savePdf()" class="btn btn-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <div  class="row g-1 print_invoice"  id="report-pdf">
                        <div  style="text-align: center; text-transform: uppercase; color: blue;">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" height="50"  alt="">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <span>General Production Report<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}} @endif</b></span>
                            <hr>
                        </div>
                        <div class="col-md-12 pt-3">
                            <h5 class="card-title">{{trans('navmenu.raw_materials')}}</h5>
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>{{trans('navmenu.name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.purchased')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.damaged')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.used')}}</th>
                                        <th style="text-align: right;">Material Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totalcost = 0; ?>
                                    @foreach($raw_col as $key => $raw_co)
                                    <?php 
                                        $tamount = $raw_co['rmu_cost'];
                                        $totalcost += $tamount; ?>
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$raw_co['name']}}</td>
                                        <td style="text-align: center;">{{$raw_co['purchased_qty']}}</td>
                                        <td style="text-align: center;">{{$raw_co['damaged']}}</td>
                                        <td style="text-align: center;">{{$raw_co['used_qty']}}</td>
                                        <td style="text-align: right;">{{ number_format($tamount, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                    <tr style="border-top: 1px solid gray;">
                                        <td></td>
                                        <td><b>TOTAL</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;"><b>{{ number_format($totalcost, 2, '.', ',')}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php $totalpcost = 0; ?>
                        @if($settings->enable_packaging)
                        <div class="col-md-12 pt-3">
                            <h5 class="card-title">{{trans('navmenu.paking_materials')}}</h5>
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>{{trans('navmenu.name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.purchased')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.damaged')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.used')}}</th>
                                        <th style="text-align: right;">Material Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pack_col as $key => $pack_co)
                                    <?php $tamt = $pack_co['pmu_cost'];
                                        $totalpcost += $tamt; ?>
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$pack_co['name']}}</td>
                                        <td style="text-align: center;">{{$pack_co['purchased_qty']}}</td>
                                        <td style="text-align: center;">{{$pack_co['damaged']}}</td>
                                        <td style="text-align: center;">{{$pack_co['used_qty']}}</td>
                                        <td style="text-align: right;">{{ number_format($tamt, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach 
                                    <tr style="border-top: 1px solid gray;">
                                        <td></td>
                                        <td><b>TOTAL</b></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;"><b>{{ number_format($totalpcost, 2, '.', ',')}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @endif
                        <div class="col-md-6 pt-3">
                            <h5 class="card-title">Direct Labour Costs</h5>
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>{{trans('navmenu.stage')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.total_cost')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totallcost = 0; ?>
                                    @foreach($dlcitems as $key => $item)
                                    <?php $totallcost += $item->total; ?>
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$item->stage}}</td>
                                        <td style="text-align: right;">{{$item->total}}</td>
                                    </tr>
                                    @endforeach 
                                    <tr style="border-top: 1px solid gray;">
                                        <td></td>
                                        <td><b>TOTAL</b></td>
                                        <td style="text-align: right;"><b>{{ number_format($totallcost, 2, '.', ',')}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 pt-3">
                            <h5 class="card-title">{{trans('navmenu.overhead_expenses')}}</h5>
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>{{trans('navmenu.name')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.total_cost')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totalmcost = 0; ?>
                                    @foreach($mros as $key => $mro)
                                    <?php $totalmcost += $mro->total; ?>
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>{{$mro->name}}</td>
                                        <td style="text-align: right;">{{$mro->total}}</td>
                                    </tr>
                                    @endforeach 
                                    <tr style="border-top: 1px solid gray;">
                                        <td></td>
                                        <td><b>TOTAL</b></td>
                                        <td style="text-align: right;"><b>{{ number_format($totalmcost, 2, '.', ',')}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 pt-3">
                            <h5 class="card-title">{{trans('navmenu.production')}}</h5>
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>{{trans('navmenu.product_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($production as $k => $prod)
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{$prod->name}}</td>
                                        <td style="text-align: center;">{{$prod->quantity}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div><div class="col-md-6 pt-3">
                            <h5 class="card-title">{{trans('navmenu.production_costs')}}</h5>
                            <table class="table table-striped table-bordered">
                                <tbody>
                                    <tr>
                                        <td><span>{{trans('navmenu.total_rm_cost')}}</span></td> 
                                        <td style="text-align: right;"> {{number_format($totalcost, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td><span>{{trans('navmenu.total_pm_cost')}}</span></td>
                                        <td style="text-align: right;"> {{number_format($totalpcost, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td><span>Total Labour Costs</span></td>
                                        <td style="text-align: right;"> {{number_format($totallcost, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <Td><span>{{trans('navmenu.total_mro_cost')}}</span></Td>
                                        <td style="text-align: right;"> {{number_format($totalmcost, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td><b>TOTAL</b></td>
                                        <td style="text-align: right;"><b>{{number_format($totalcost+$totalpcost+$totallcost+$totalmcost, 2, '.', ',') }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 pt-2">
                            <h5 class="card-title">{{trans('navmenu.produced_products')}}</h5>
                            <hr>
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th >#</th>
                                        <th>{{trans('navmenu.product_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.batch_no')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.total_cost')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($production_logs as $k => $prod)
                                    <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{$prod->name}}</td>
                                        <td style="text-align: center;">{{$prod->quantity}}</td>
                                        <td style="text-align: center;">{{date("d/m/Y" , strtotime($prod->date))}}</td>
                                        <td style="text-align: center;">{{$prod->prod_batch}}</td>
                                        <td style="text-align: right;">{{ number_format(($prod->cost_per_unit*$prod->quantity), 2, '.', ',') }}</td>
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
@endsection

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <script type="text/javascript">
        function savePdf() {
          const element = document.getElementById("report-pdf");
          var filename = "general production report";
          var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).toPdf().save();

            // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                    // window.open(pdf.output('bloburl'), '_blank');
                // });
        }
    </script>
