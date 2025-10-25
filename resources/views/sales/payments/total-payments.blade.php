@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('f-total-sale-payments')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="inv-content" class="print_invoice p-3" style="border: 1px solid gray;">
                        <div class="row">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" height="50" alt="">
                                </figure>
                                @endif
                                <h5>{{$shop->name}}</h5>
                                <span> {{ $title }}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                            </div>
                            <div class="col-xs-12 invoice-content" style="border-top: 2px solid #82B1FF;">
                                <table class="table mt-0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;" colspan="5">Cash Sales Payments</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray;" colspan="5">Credit Sales Payments</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center; text-transform: uppercase; border-right: 1px solid gray;">{{trans('navmenu.date')}}</th>
                                            @foreach($paytypes as $type)
                                            <th style="text-align: center; text-transform: uppercase;">{{str_replace('Money', '', $type)}}</th>
                                            @endforeach
                                            <th style="text-align: center; text-transform: uppercase; border-right: 1px solid gray;">{{trans('navmenu.total')}}</th>             
                                            @foreach($paytypes as $type)
                                            <th style="text-align: center; text-transform: uppercase;">{{str_replace('Money', '', $type)}}</th>
                                            @endforeach
                                            <th style="text-align: center; text-transform: uppercase; border-right: 1px solid gray;">{{trans('navmenu.total')}}</th>
                                            <th style="">Total Payments</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $typetotals = array(); $totalpay = 0; $typecredittotals = array(); $totalcred = 0; ?>
                                        @foreach($tpayments as $total)
                                        <?php  $totalpay += $total['netpay']; $totalcred += $total['total_credit'] ?>
                                        <tr>
                                            <td style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">{{date('d/m/Y', strtotime($total['pay_date']))}}</td>

                                            @foreach($paytypes as $type)
                                            <?php 
                                                if (isset($typetotals[$type])) {
                                                    $typetotals[$type]['t_amount'] += $total[$type];
                                                }else{
                                                    $typetotals[$type]['t_amount'] = $total[$type];
                                                }
                                             ?>
                                            <td style="text-align: center; border-bottom: 1px solid gray;">{{ number_format($total[$type], 2, '.', ',') }}</td>
                                            @endforeach
                                            <td style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">{{number_format($total['netpay'], 2, '.', ',')}}</td>
                                            @foreach($paytypes as $type)
                                            <?php 
                                                if (isset($typecredittotals['Credit '.$type])) {
                                                    $typecredittotals['Credit '.$type]['tc_amount'] += $total['Credit '.$type];
                                                }else{
                                                    $typecredittotals['Credit '.$type]['tc_amount'] = $total['Credit '.$type];
                                                }
                                             ?>
                                            <td style="text-align: center; border-bottom: 1px solid gray;">{{ number_format($total['Credit '.$type], 2, '.', ',') }}</td>
                                            @endforeach
                                            <td style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">{{number_format($total['total_credit'], 2, '.', ',')}}</td>
                                            <td style="text-align: right; border-bottom: 1px solid gray;">{{number_format($total['netpay']+$total['total_credit'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="text-align: right; text-transform: uppercase; border-right: 1px solid gray; border-bottom: 2px solid gray;">{{trans('navmenu.total')}}</th>
                                            @if(count($typetotals) > 0)
                                            @foreach($typetotals as $st)
                                            <th style="text-align: center; border-bottom: 2px solid gray;"><b>{{number_format($st['t_amount'], 2, '.', ',')}}</b></th>
                                            @endforeach
                                            @else
                                            <th style="text-align: center; border-bottom: 2px solid gray;">0.00</th>
                                            <th style="text-align: center; border-bottom: 2px solid gray;">0.00</th>
                                            <th style="text-align: center; border-bottom: 2px solid gray;">0.00</th>
                                            @endif
                                            <th style="text-align: center; border-right: 1px solid gray; border-bottom: 2px solid gray;"><b>{{number_format($totalpay, 2, '.', ',')}}</b></th>
                                            @if(count($typecredittotals) > 0)
                                            @foreach($typecredittotals as $st)
                                            <th style="text-align: center; border-bottom: 2px solid gray;"><b>{{number_format($st['tc_amount'], 2, '.', ',')}}</b></th>
                                            @endforeach
                                            @else
                                            <th style="text-align: center; border-bottom: 2px solid gray;">0.00</th>
                                            <th style="text-align: center; border-bottom: 2px solid gray;">0.00</th>
                                            <th style="text-align: center; border-bottom: 2px solid gray;">0.00</th>
                                            @endif
                                            <th style="text-align: center; border-right: 1px solid gray; border-bottom: 2px solid gray;"><b>{{number_format($totalcred, 2, '.', ',')}}</b></th>
                                            <th style="text-align: right; border-bottom: 2px solid gray;"><b>{{number_format($totalpay+$totalcred, 2, '.', ',')}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo $title.'_'.$duration; ?>";
          var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
            };

            html2pdf().set(opt).from(element).toPdf().save();
            // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                // window.open(pdf.output('bloburl'), '_blank');
            // });
          
        }
    </script>