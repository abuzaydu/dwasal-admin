@extends('layouts.gen')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-4 col-sm-12 text-right pt-0">
                <form class="row g-3 dashform" action="{{url('monthly-sales-report')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class=" col-md-7">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="bx bx-calendar"></i></span>
                                <i class="bx bx-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="inv-content" class="print_invoice p-3" style="border: 1px solid gray;">
                        <div class="row">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" width="200" style="border: 1px solid gray;">
                                </figure>
                                @endif
                                <h5>{{$shop->name}}</h5>
                                <span> {{ $title }}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                            </div>
                            <div class="col-xs-12 invoice-content" style="border-top: 2px solid #82B1FF;">
                                <table class="table mt-0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; border-left: 1px solid gray; border-bottom: 1px solid gray; border-right: 1px solid gray;">Date</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">Shops</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;" colspan="2">Cash Sales</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray;border-right: 1px solid gray;" colspan="2">Credit Sales</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $nshops = $shops->count(); $tcashsales = 0; $tcreditsales = 0; $tsales = 0; ?>
                                        @foreach($totalsales as $index => $total)
                                        <?php $tsales += $total['netamount']; $netcash = 0; $netcredit = 0; ?>
                                        <tr>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{ $total['date']}}</td>
                                            <td style="border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                <table class="table mt-0 mb-0">
                                                    @foreach($shops as $key => $store)
                                                    @if($key == $nshops-1)
                                                    <tr>
                                                         <td style="text-align: left;">{{$store->name}}</td>
                                                    </tr>
                                                    @else
                                                    <tr>
                                                         <td style="text-align: left; border-bottom: 1px solid gray;">{{$store->name}}</td>
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                </table>
                                            </td>
                                            <td style="border-bottom: 1px solid gray;">
                                                <table class="table mt-0 mb-0">
                                                    @foreach($shops as $key => $store)
                                                    <?php $netcash += $total[$key][$store->name]['netcash']; ?>
                                                    @if($key == $nshops-1)
                                                    <tr>
                                                        <td style="text-align: right; border-right: 1px solid gray;">{{ number_format($total[$key][$store->name]['netcash'], 2, '.', ',') }}</td>
                                                    </tr>
                                                    @else
                                                    <tr>
                                                        <td style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray;">{{ number_format($total[$key][$store->name]['netcash'], 2, '.', ',') }}</td>
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                </table>
                                            </td>
                                            <td style="text-align: right; vertical-align: bottom; border-right: 1px solid gray; border-bottom: 1px solid gray;"><b>{{number_format($netcash, 2, '.', ',')}}</b></td>
                                            <?php $tcashsales += $netcash; ?>
                                            <td style="border-bottom: 1px solid gray;">
                                                <table class="table mt-0 mb-0">
                                                    @foreach($shops as $key => $store)
                                                    <?php $netcredit += $total[$key][$store->name]['netcredit']; ?>
                                                    @if($key == $nshops-1)
                                                    <tr>
                                                        <td style="text-align: right; border-right: 1px solid gray;">{{ number_format($total[$key][$store->name]['netcredit'], 2, '.', ',') }}</td>
                                                    </tr>
                                                    @else
                                                    <tr>
                                                        <td style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray;">{{ number_format($total[$key][$store->name]['netcredit'], 2, '.', ',') }}</td>
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                </table>
                                            </td>
                                            <td style="text-align: right; vertical-align: bottom; border-right: 1px solid gray; border-bottom: 1px solid gray;"><b>{{number_format($netcredit, 2, '.', ',')}}</b></td>
                                            <?php $tcreditsales += $netcredit; ?>
                                            <td style="text-align: right; vertical-align: bottom; border-right: 1px solid gray; border-bottom: 1px solid gray;"><b>{{number_format($total['netamount'], 2, '.', ',')}}</b></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="border-bottom: 1px solid gray;"><b>{{trans('navmenu.total')}}</th>
                                            <th colspan="2" style="text-align: right; border-bottom: 1px solid gray;"><b>{{number_format($tcashsales, 2, '.', ',')}}</b></th>
                                            <th colspan="2" style="text-align: right; border-bottom: 1px solid gray;"><b>{{number_format($tcreditsales, 2, '.', ',')}}</b></th>
                                            <th colspan="2" style="text-align: right; border-bottom: 1px solid gray;"><b>{{number_format($tsales, 2, '.', ',')}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                </div>
            </div>
        </b>
    </th>
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