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
                <form class="dashform row g-1" id="filter-form" action="{{ url('over-deposited') }}" method="POST">
                    @csrf
                    <div class="col-md-2">
                        <select name="month" class="form-select form-select-sm mb-1" id="year">
                            <option value="">Select Month</option>
                            @foreach($months as $month)
                            <option>{{$month['month']}}</option>
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
                                                <td colspan="5" style="text-align: center;">
                                                    <span style="font-size: 18px">{{$company->name}} <br>(<b>{{$shop->name}}</b>)</span>
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
                                                        <span><b>{{$curmonth}}</b></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-12 invoice-content" style="overflow-x: auto;">
                                        <table class="mt-0" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;"><b>Total Overpaid Amount</b></th>
                                                    <th style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center; background-color: #fae1de;"><b>{{number_format(($toverpaiddays*$depperday), 2,'.',',')}}</b></th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: center; border-left: 1px solid gray; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">TL Name</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Driver Name</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Contract Start Date</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Working Month</th>
                                                    <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray; border-top: 2px solid #82B1FF;">Overpaid Days</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($deposits as $index =>  $dep)
                                                <tr>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$dep['tl_name']}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$dep['driver']}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$dep['start_date']}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$dep['month']}}</td>
                                                    <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$dep['overpaiddays']}}</td>
                                                </tr>
                                                @endforeach
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
            var filename = "<?php echo $title.'_'.$curmonth; ?>";
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
            var filename = "<?php echo $title.'_'.$curmonth; ?>";
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