@extends('layouts.gen')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12 text-right pt-0">
                <form class="dashform row g-1" id="filter-form" action="{{ url('f-company-income-stmt') }}" method="POST">
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
                        <select name="year" class="form-select form-select-sm mb-1" id="year">
                            <option value="">Filter by Year</option>
                            @foreach($years as $year)
                            <option>{{$year['year']}}</option>
                            @endforeach
                        </select>
                    </div>
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
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div id="inv-content" class="print_invoice p-0" style="border: 1px solid gray;">
                                <div class="row g-1">
                                    <div class="col-md-12">
                                        <table style="width: 100%;">
                                            <tr>
                                                <td colspan="2" style="text-align: left; padding-left: 15px;">
                                                    @if(!is_null($company->logo_url))
                                                    <figure>
                                                        <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="200">
                                                    </figure>
                                                    @endif
                                                </td>
                                                <td colspan="7" style="text-align: right;">
                                                    <span style="font-size: 18px">{{$company->name}}</span><br>
                                                    @if(!is_null($company->slogan))<small>{{$company->slogan}}</small><br>@endif
                                                    <?php $shop = $company->shops()->first(); ?> 
                                                    <p>
                                                        {{$shop->postal_address}} {{$shop->physical_address}}<br> @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}} {{$shop->country}} <br>  Tel: <b>{{$shop->tel}}</b> | <b>{{$shop->mobile}}</b> WhatsApp : <b>{{$shop->whatsapp}}</b><br> Email: <b>{{$shop->email}}</b> Website: <b>{{$shop->website}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td colspan="9" style="background: #0459c6; padding-left: 15px;  border-radius: 0px; text-align: center; color: #fff; font-size: 20px; text-transform: uppercase;">
                                                        <span> {{ $title }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="9" style="text-align: center; text-transform: uppercase; color: blue;">
                                                        <span><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-12 invoice-content" style="overflow-x: auto;">
                                        <table class="table mt-0" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center; border-left: 1px solid gray; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;"></th>
                                                    @foreach($months as $month)
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">{{$month['name']}}</th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Total</th>
                                                </tr>
                                                <tr>
                                                    <th style="border-left: 1px solid gray; border-bottom: 1px solid gray; text-transform: capitalize;">Revenue</th>
                                                    @foreach($months as $month)
                                                    <th style="text-align: center; border-bottom: 1px solid gray;"></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $rev_total = 0; ?>
                                                @foreach($sales as $index =>  $tsale)
                                                <tr>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$tsale['name']}}</td>
                                                    <?php $bt = 0; ?>
                                                    @foreach($tsale['shopsales'] as $key => $msale)
                                                    <?php $bt += $msale['amount']; ?>
                                                    <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                        {{ number_format($msale['amount'], 2, '.', ',') }}
                                                    </td>
                                                    @endforeach
                                                    <td style="text-align: right; border-bottom: 1px solid gray;">
                                                        {{ number_format($bt, 2, '.', ',') }}
                                                    </td>
                                                    <?php $rev_total += $bt; ?>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <th style="border-bottom: 1px solid gray;">Total Income</th>
                                                    @foreach($msaletotals as $key => $total)
                                                    <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray;"><b>{{ number_format($total['total_rev'], 2, '.', ',') }}</b></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"><b>{{ number_format($rev_total, 2, '.', ',') }}</b></th>
                                                </tr>

                                                <tr>
                                                    <th style="border-left: 1px solid gray; border-bottom: 1px solid gray; padding-top: 20px; text-transform: capitalize;">Cost Of Sales</th>
                                                    @foreach($months as $month)
                                                    <th style="text-align: center; border-bottom: 1px solid gray;"></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"></th>
                                                </tr>
                                                <?php $cos_total = 0; ?>
                                                @foreach($costofsales as $index =>  $tcsale)
                                                <tr>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$tcsale['name']}}</td>
                                                    <?php $bct = 0; ?>
                                                    @foreach($tcsale['shopcosales'] as $key => $msale)
                                                    <?php $bct += $msale['cos']; ?>
                                                    <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                        {{ number_format($msale['cos'], 2, '.', ',') }}
                                                    </td>
                                                    @endforeach
                                                    <td style="text-align: right; border-bottom: 1px solid gray;">
                                                        {{ number_format($bct, 2, '.', ',') }}
                                                    </td>
                                                    <?php $cos_total += $bct; ?>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <th style="border-bottom: 1px solid gray;">Total Cost of Sale</th>
                                                    @foreach($mcostotals as $key => $total)
                                                    <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray;"><b>{{ number_format($total['total_cos'], 2, '.', ',') }}</b></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"><b>{{ number_format($cos_total, 2, '.', ',') }}</b></th>
                                                </tr>

                                                <tr>
                                                    <th style="border-left: 1px solid gray; border-bottom: 1px solid gray; padding-top: 20px; text-transform: capitalize;">Margin</th>
                                                    @foreach($months as $month)
                                                    <th style="text-align: center; border-bottom: 1px solid gray;"></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"></th>
                                                </tr>
                                                <?php $margin_total = 0; ?>
                                                @foreach($margins as $index =>  $tmg)
                                                <tr>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$tmg['name']}}</td>
                                                    <?php $mt = 0; ?>
                                                    @foreach($tmg['shopmargins'] as $key => $msale)
                                                    <?php $mt += $msale['margin']; ?>
                                                    <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                        {{ number_format($msale['margin'], 2, '.', ',') }}
                                                    </td>
                                                    @endforeach
                                                    <td style="text-align: right; border-bottom: 1px solid gray;">
                                                        {{ number_format($mt, 2, '.', ',') }}
                                                    </td>
                                                    <?php $margin_total += $mt; ?>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <th style="border-bottom: 1px solid gray;">Total Margin</th>
                                                    @foreach($margintotals as $key => $total)
                                                    <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray;"><b>{{ number_format($total['total_mag'], 2, '.', ',') }}</b></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"><b>{{ number_format($margin_total, 2, '.', ',') }}</b></th>
                                                </tr>

                                                <tr>
                                                    <th style="border-left: 1px solid gray; border-bottom: 1px solid gray; padding-top: 20px; text-transform: capitalize;">Operating/Overhead Expenses</th>
                                                    @foreach($months as $month)
                                                    <th style="text-align: center; border-bottom: 1px solid gray;"></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"></th>
                                                </tr>
                                                <?php $exp_total = 0; ?>
                                                @foreach($expenses as $index =>  $exp)
                                                <tr>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$exp['category']}}</td>
                                                    <?php $expt = 0; ?>
                                                    @foreach($exp['catexpenses'] as $key => $mexp)
                                                    <?php $expt += $mexp['amount']; ?>
                                                    <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">
                                                        {{ number_format($mexp['amount'], 2, '.', ',') }}
                                                    </td>
                                                    @endforeach
                                                    <td style="text-align: right; border-bottom: 1px solid gray;">
                                                        {{ number_format($expt, 2, '.', ',') }}
                                                    </td>
                                                    <?php $exp_total += $expt; ?>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <th style="border-bottom: 1px solid gray;">Total Expenses/Overheads</th>
                                                    @foreach($expensetotals as $key => $total)
                                                    <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray;"><b>{{ number_format($total['total_exp'], 2, '.', ',') }}</b></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"><b>{{ number_format($exp_total, 2, '.', ',') }}</b></th>
                                                </tr>


                                                <tr>
                                                    <th style="border-left: 1px solid gray; border-bottom: 1px solid gray; padding-top: 20px; text-transform: capitalize;">Net Income</th>
                                                    @foreach($months as $month)
                                                    <th style="text-align: center; border-bottom: 1px solid gray;"></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"></th>
                                                </tr>
                                                <?php $net_income = $margin_total-$exp_total; ?>
                                                <tr>
                                                    <th style="border-bottom: 1px solid gray;">Net Income</th>
                                                    @foreach($netincometotals as $key => $total)
                                                    <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray;"><b>{{ number_format($total['total_net'], 2, '.', ',') }}</b></th>
                                                    @endforeach
                                                    <th style="text-align: right; border-bottom: 1px solid gray;"><b>{{ number_format($net_income, 2, '.', ',') }}</b></th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </div>
                            <div class="col-md-12 pt-4">
                                <a href="#" onclick="javascript:exportToExcel()" class="btn btn-secondary btn-sm"><i class="fa fa-download"></i> Export to Excel</a>
                                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab_1" role="tabpanel">

                        </div>
                    </div>
                </div>
            </div>
        </b>
    </th>
@endsection
@section('page-scripts')
<script type="text/javascript">
    $('#shop-id').on('change', function(){
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