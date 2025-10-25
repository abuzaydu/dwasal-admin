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
                <form class="dashform row g-1" id="filter-form" action="{{ url('monthly-profit-report') }}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-12 mb-1">
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
                                                <td colspan="7" style="text-align: center;">
                                                    <span style="font-size: 18px">{{$company->name}}</span>
                                                </td>
                                            </tr>
                                        </table>
                                        <table style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td colspan="7" style="background: #0459c6; padding-left: 15px;  border-radius: 0px; text-align: center; color: #fff; font-size: 20px; text-transform: uppercase;">
                                                        <span> {{ $title }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" style="text-align: center; text-transform: uppercase; color: blue;">
                                                        <span><b>{{$duration}}</b></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-12 invoice-content" style="overflow-x: auto;">
                                        <table class="mt-0" style="width: 100%; white-space: nowrap;">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;"></th>
                                                    <th colspan="4" style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF; background-color: #d7dbdd;">Expected payment per month</th>
                                                    <th colspan="4" style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF; background-color: #f8c471;">Payment per Month</th>
                                                    <th colspan="3" style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF; background-color: #7dcea0;"> Actual Deposited per Month</th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Working Month</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Working Days</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Revenue</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Cost of Sales</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF; background-color: #d7dbdd;">Profit</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Paid Days</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Revenue</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Cost of Sales</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF; background-color: #f8c471;">Proofit</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Revenue</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Cost of Sales</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF; background-color: #7dcea0;">Profit</th>
                                                    <!-- <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Deposit Days</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $twdays = 0; $texpdeposit = 0; $texpcost = 0; $tpdays = 0; $tpaidamt = 0; $tcostpaidamt = 0; $tdeposited = 0; $tcostdeposited = 0; $tdepositdays = 0; ?>
                                                @foreach($mprofits as $index =>  $dep)
                                                <?php 
                                                    $twdays += $dep['working_days'];
                                                    $texpdeposit += $dep['exp_deposit'];
                                                    $texpcost += $dep['exp_cost'];
                                                    $tpdays += $dep['paid_days']; 
                                                    $tpaidamt += $dep['paid_amt'];
                                                    $tcostpaidamt += $dep['costof_paid_amt'];
                                                    $tdeposited += $dep['deposited'];
                                                    $tcostdeposited += $dep['costof_deposited'];
                                                    $tdepositdays += $dep['depositdays'];
                                                ?>
                                                <tr>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{date('M Y', strtotime($dep['month']))}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$dep['working_days']}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{number_format($dep['exp_deposit'], 2,'.',',')}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{number_format($dep['exp_cost'], 2,'.',',')}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center; background-color: #d7dbdd;">{{number_format($dep['exp_deposit']-$dep['exp_cost'], 2,'.',',')}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$dep['paid_days']}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{number_format($dep['paid_amt'], 2,'.',',')}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{number_format($dep['costof_paid_amt'], 2,'.',',')}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center; background-color: #f8c471;">{{number_format(($dep['paid_amt']-$dep['costof_paid_amt']), 2,'.',',')}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{number_format($dep['deposited'], 2,'.',',')}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{number_format($dep['costof_deposited'], 2,'.',',')}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center; background-color: #7dcea0;">{{number_format(($dep['deposited']-$dep['costof_deposited']), 2,'.',',')}}</td>
                                                    <!-- <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$dep['depositdays']}}</td> -->
                                                </tr>
                                                @endforeach
                                                <tr style="height: 50px !important;">
                                                    <td colspan="12" style="border-bottom: 1px solid gray;">
                                                        
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;"><b>{{trans('navmenu.total')}}</b></th>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$twdays}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;"><b>{{number_format($texpdeposit, 2,'.',',')}}</b></td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;"><b>{{number_format($texpcost, 2,'.',',')}}</b></td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center; background-color: #d7dbdd;"><b>{{number_format(($texpdeposit-$texpcost), 2,'.',',')}}</b></td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$twdays}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;"><b>{{number_format($tpaidamt, 2,'.',',')}}</b></td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;"><b>{{number_format($tcostpaidamt, 2,'.',',')}}</b></td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center; background-color: #f8c471;"><b>{{number_format(($tpaidamt-$tcostpaidamt), 2,'.',',')}}</b></td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;"><b>{{number_format($tdeposited, 2,'.',',')}}</b></td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;"><b>{{number_format($tcostdeposited, 2,'.',',')}}</b></td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center; background-color: #7dcea0;"><b>{{number_format(($tdeposited-$tcostdeposited), 2,'.',',')}}</b></td>
                                                    <!-- <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$tdepositdays}}</td> -->
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