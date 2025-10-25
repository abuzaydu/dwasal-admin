@extends('layouts.gen')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" id="filter-form" action="{{ url('f-balance-sheet') }}" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <select name="shop_id" class="form-select form-select-sm mb-1" id="shop-id">
                            <option value="">All Stores</option>
                            @foreach($cshops as $mshop)
                            @if(!is_null($currshop) && $currshop->id == $mshop->id)
                            <option value="{{$mshop->id}}" selected>{{$mshop->name}}</option>
                            @else
                            <option value="{{$mshop->id}}">{{$mshop->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="start_year" class="form-select form-select-sm mb-1" id="start-year">
                            <option value="">Filter by Year</option>
                            @foreach($years as $year)
                            @if($startyear == $year['year'])
                            <option selected>{{$year['year']}}</option>
                            @else
                            <option>{{$year['year']}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="year" class="form-select form-select-sm mb-1" id="year">
                            <option value="">Filter by Year</option>
                            @foreach($years as $year)
                            @if($endyear == $year['year'])
                            <option selected>{{$year['year']}}</option>
                            @else
                            <option>{{$year['year']}}</option>
                            @endif
                            @endforeach
                        </select>
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
                    <div class="tab-content py-0">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="row g-1 print_invoice" id="inv-content" style="border: 1px solid #e0e0e0;"> 
                                <div class="col-md-12">
                                    <table class=" mb-0" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <td colspan="{{ count($curryears)+1 }}" style="text-align: center;">
                                                    <span style="font-size: 18px;">{{$company->name}}<br>
                                                    (<b>@if(!is_null($currshop)) {{$currshop->name}} @else All Stores @endif</b>)</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="{{ count($curryears)+1 }}" style="background: <?php echo $settings->invoice_color; ?>; padding-left: 15px;  border-radius: 0px; text-align: center; color: <?php echo $settings->invoice_title_color; ?>; text-transform: uppercase;"> {{ $title }}
                                                </th>
                                            </tr>
                                            <tr>
                                                <td colspan="{{ count($curryears)+1 }}" style="text-align: center; text-transform: uppercase; color: blue;">
                                                    <span><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="border: 1px solid gray; background-color: #078212; color: white;">ASSETS</th>
                                                @foreach($curryears as $curryear)
                                                <th style="text-align: right; border: 1px solid gray; text-transform: uppercase; background-color: #05a013; color: white;">{{$curryear['name']}}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 14px; background-color: #e0e0e0; text-transform: uppercase;">CURRENT ASSETS</td>
                                                <td colspan="{{ count($curryears) }}" style="font-size: 14px; background-color: #e0e0e0;"></td>
                                            </tr>
                                            <?php $total_ca = 0; ?>
                                            @foreach($mcurrent_assets as $index =>  $tca)
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$tca['name']}}</td>
                                                @foreach($tca['curryearvalues'] as $key => $mvalue)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mvalue['amount'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th style="border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #cbfbcf; text-align: right;">TOTAL CURRENT ASSETS ({{$currency}})</th>
                                                @foreach($mcatotals as $key => $total)
                                                <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #cbfbcf"><b>{{ number_format($total['total_ca'], 2, '.', ',') }}</b></th>
                                                @endforeach
                                            </tr>

                                            <tr>
                                                <td style="font-size: 14px; background-color: #e0e0e0; text-transform: uppercase;">FIXED (LONG TERM) ASSETS</td>
                                                <td colspan="{{ count($curryears) }}" style="font-size: 14px; background-color: #e0e0e0;"></td>
                                            </tr>
                                            @foreach($mfixed_assets as $index =>  $tfa)
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$tfa['name']}}</td>
                                                @foreach($tfa['curryearfavalues'] as $key => $mvalue)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mvalue['amount'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th style="border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #cbfbcf; text-align: right;">TOTAL FIXED ASSETS ({{$currency}})</th>
                                                @foreach($mfatotals as $key => $total)
                                                <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #cbfbcf"><b>{{ number_format($total['total_fa'], 2, '.', ',') }}</b></th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; background-color: #e0e0e0; text-transform: uppercase;">OTHER ASSETS</td>
                                                <td colspan="{{ count($curryears) }}" style="font-size: 14px; background-color: #e0e0e0;"></td>
                                            </tr>
                                            @foreach($mother_assets as $index =>  $toa)
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$toa['name']}}</td>
                                                @foreach($toa['curryearoavalues'] as $key => $mvalue)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mvalue['amount'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th style="border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #cbfbcf; text-align: right;">TOTAL OTHER ASSETS ({{$currency}})</th>
                                                @foreach($moatotals as $key => $total)
                                                <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #cbfbcf"><b>{{ number_format($total['total_oa'], 2, '.', ',') }}</b></th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="border-bottom: 1px solid gray; border-right: 1px solid gray; background-color:  #9deda0; text-align: right;"><b>TOTAL ASSETS</b> ({{$currency}})</td>
                                                @foreach($m_a_totals as $key => $total)
                                                <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #9deda0;"><b>{{ number_format($total['total_a'], 2, '.', ',') }}</b></th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td colspan="{{ count($curryears)+2 }}"></td>
                                            </tr>
                                            <tr>
                                                <th style="border: 1px solid gray; background-color: #062d8d; color: white;">LIABILITIES & OWNER'S EQUITY</th>
                                                @foreach($curryears as $curryear)
                                                <th style="text-align: right; border: 1px solid gray; text-transform: uppercase; background-color: #3a67d4; color: white;">{{$curryear['name']}}</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; background-color: #e0e0e0; text-transform: uppercase;">CURRENT LIABILITIES</td>
                                                <td colspan="{{ count($curryears) }}" style="font-size: 14px; background-color: #e0e0e0;"></td>
                                            </tr>
                                            @foreach($mcurrent_liabilities as $index =>  $tcl)
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$tcl['name']}}</td>
                                                <?php $lbt = 0; ?>
                                                @foreach($tcl['curryearclvalues'] as $key => $mvalue)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mvalue['amount'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th style="border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #dceaed; text-align: right;">TOTAL CURRENT LIABILITIES ({{$currency}})</th>
                                                @foreach($mcltotals as $key => $total)
                                                <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #dceaed"><b>{{ number_format($total['total_cl'], 2, '.', ',') }}</b></th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; background-color: #e0e0e0; text-transform: uppercase;">LONG TERM LIABILITIES</td>
                                                <td colspan="{{ count($curryears) }}" style="font-size: 14px; background-color: #e0e0e0;"></td>
                                            </tr>
                                            @foreach($mlong_term_liabilities as $index =>  $ltl)
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$ltl['name']}}</td>
                                                @foreach($ltl['curryearltlvalues'] as $key => $mvalue)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mvalue['amount'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th style="border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #dceaed; text-align: right;">TOTAL LONG-TERM LIABILITIES ({{$currency}})</th>
                                                @foreach($mltltotals as $key => $total)
                                                <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #dceaed"><b>{{ number_format($total['total_ltl'], 2, '.', ',') }}</b></th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="font-size: 14px; background-color: #e0e0e0; text-transform: uppercase;">OWNER'S EQUITY</td>
                                                <td colspan="{{ count($curryears) }}" style="font-size: 14px; background-color: #e0e0e0;"></td>
                                            </tr>
                                            @foreach($mowners_equity as $index =>  $toe)
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$toe['name']}}</td>
                                                @foreach($toe['curryearoevalues'] as $key => $mvalue)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mvalue['amount'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th style="border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #dceaed; text-align: right;">TOTAL OWNER'S EQUITY ({{$currency}})</th>
                                                @foreach($moetotals as $key => $total)
                                                <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #dceaed;"><b>{{ number_format($total['total_oe'], 2, '.', ',') }}</b></th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <th style="border-bottom: 1px solid gray; border-right: 1px solid gray; background-color:  #b3cbea; text-align: right;"><b>TOTAL LIABILITIES AND OWNER'S EQUITY</b> ({{$currency}})</th>
                                                @foreach($m_lao_totals as $key => $total)
                                                <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; background-color: #b3cbea;"><b>{{ number_format($total['total_lao'], 2, '.', ',') }}</b></th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td colspan="{{ count($curryears)+2 }}"></td>
                                            </tr>
                                            <tr>
                                                <th style="border: 1px solid gray; background-color: #8b7306; color: white;">COMMON FINANCIAL RATIO</th>
                                                @foreach($curryears as $curryear)
                                                <th style="text-align: right; border: 1px solid gray; text-transform: uppercase; background-color: #dab304; color: white;">{{$curryear['name']}}</th>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;"><b>Debt Ratio</b><br><small>(Total Liabilities / Total Assets)</small></td>
                                                @foreach($mdebtratios as $key => $mdr)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mdr['debt_ratio'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;"><b>Current Ratio </b><br><small>(Current Assets / Current Liabilities)</small></td>
                                                @foreach($mcurrentratios as $key => $mcr)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mcr['current_ratio'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;"><b>Working Capital </b><br><small>(Current Assets - Current Liabilities)</small></td>
                                                @foreach($mworking_capitals as $key => $mwc)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mwc['working_capital'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;"><b>Assets-to-Equity Ratio </b><br><small>(Total Assets / Owner's Equity)</small></td>
                                                @foreach($massets_to_equity_ratios as $key => $mater)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mater['assets_to_equity_ratio'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                            <tr>
                                                <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;"><b>Debt-to-Equity Ratio </b><br><small>(Total Liabilities / Owner's Equity)</small></td>
                                                @foreach($mdebt_to_equity_ratios as $key => $mdter)
                                                <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                    {{ number_format($mdter['debt_to_equity_ratio'], 2, '.', ',') }}
                                                </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 pt-4">
                                <a href="#" onclick="javascript:exportToExcel()" class="btn btn-secondary btn-sm"><i class="fa fa-download"></i> Export to Excel</a>
                                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
<script type="text/javascript">
    $('#shop-id').on('change', function(){
        $('#filter-form').submit();
    });
    $('#start-year').on('change', function(){
        $('#filter-form').submit();
    });
    $('#year').on('change', function(){
        $('#filter-form').submit();
    });
</script>
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
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr", mode: "css"},
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
            };

            html2pdf().set(opt).from(element).toPdf().save();
            // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                // window.open(pdf.output('bloburl'), '_blank');
            // });
        }

        function exportToExcel() {
            var filename = "<?php echo $title.'_'.$duration; ?>";
            var location = 'data:application/vnd.ms-excel;base64,';
            var excelTemplate = '<html> ' +
                '<head> ' +
                '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/> ' +
                '</head> ' +
                '<body> ' +
                document.getElementById("inv-content").innerHTML +
                '</body> ' +
                '</html>'
                var a = document.createElement('a');
                a.href = location + window.btoa(excelTemplate);
                a.download = filename + '.xls';
                a.click();
        }
    </script>