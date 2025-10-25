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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" id="filter-form" action="{{ url('f-general-ledger') }}" method="POST">
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
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-8 mb-1">
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

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body" style="padding: 35px;">
                    <div class="alert alert-info alert-block">
                      <strong>This report will begin displaying data after all expense categories have been updated with their respective transaction accounts. Please go to Expense Categories to update. <a href="{{ url('expenses') }}"></strong>
                    </div>

                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="print_invoice" id="print-stmt" style="border: 1px solid #e0e0e0; padding: 5;">
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
                                    <?php $total_debit = 0; $total_credit = 0; ?>
                                    <div class="col-md-12 invoice-content" style="overflow-x: auto;">
                                        <table class="table mb-0" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th  style="text-align: center; border-left: 1px solid gray; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Date</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Branch</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Reference</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Type</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Account Number</th>
                                                    <th style="text-align: left; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Account Name</th>
                                                    <th style="text-align: left; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Description</th>
                                                    <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Debit Amount ({{$defcurr->code}})</th>
                                                    <th style="text-align: right; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Debit Amount ({{$defcurr->code}})</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($gledgers as $key => $ledger)
                                                <?php 
                                                    $total_debit += $ledger['debit_amount'];
                                                    $total_credit += $ledger['credit_amount'];
                                                ?>
                                                <tr>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{ date('d/m/Y', strtotime($ledger['date']))}}</td>
                                                    <td style="text-align: left; border-right: 1px solid gray; border-bottom: 1px solid gray;">{{ $ledger['branch'] }}</td>
                                                    <td style="text-align: center; border-right: 1px solid gray; border-bottom: 1px solid gray;">{{ $ledger['reference'] }}</td>
                                                    <td style="text-align: center; border-right: 1px solid gray; border-bottom: 1px solid gray;">{{ $ledger['type'] }}</td>
                                                    <td style="text-align: center; border-right: 1px solid gray; border-bottom: 1px solid gray;">{{ $ledger['account_number'] }}</td>
                                                    <td style="text-align: left; border-right: 1px solid gray; border-bottom: 1px solid gray;">{{ $ledger['account_name'] }}</td>
                                                    <td style="text-align: left; border-right: 1px solid gray; border-bottom: 1px solid gray;">{{ $ledger['transaction_description'] }}</td>
                                                    <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">{{ number_format($ledger['debit_amount'], 2, '.', ',') }}</td>
                                                    <td style="text-align: right; border-right: 1px solid gray; border-bottom: 1px solid gray;">{{ number_format($ledger['credit_amount'], 2, '.', ',') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pt-4">
                                <a href="#" onclick="javascript:exportToExcel()" class="btn btn-secondary btn-sm"><i class="fa fa-download"></i> Export to Excel</a>
                                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
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

    $('#year').on('change', function(){
        $('#filter-form').submit();
    });
</script>
@endsection
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
            const element = document.getElementById("print-stmt");
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
                document.getElementById("print-stmt").innerHTML +
                '</body> ' +
                '</html>'
                var a = document.createElement('a');
                a.href = location + window.btoa(excelTemplate);
                a.download = filename + '.xls';
                a.click();
        }
    </script>