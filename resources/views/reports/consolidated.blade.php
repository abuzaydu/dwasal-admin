@extends('layouts.gen')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/highcharts/css/highcharts.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
                <form class="row g-3 dashform" action="{{url('consolidated')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class=" col-md-12">
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
                    <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                        
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#report-excel" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.consolidated_report')}} (Excel)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#report-chart" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.consolidated_report')}} Chart</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="report-excel" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="consolidated" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>{{trans('navmenu.business_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sales')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.cost_of_sales')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.gross_profit')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.operating_expense')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $bizs = array();
                                            $sales = array();
                                            $cosales = array();
                                            $gps = array();
                                            $oexps = array();
                                            $profits = array();
                                        ?>
                                        @foreach($totals as $index => $total)
                                        <?php 
                                            array_push($bizs, $total['bizname']);
                                            array_push($sales, ($total['price']-$total['discount'])+$total['tax_amount']);
                                            array_push($cosales, $total['buying_price']+0);
                                            array_push($gps, (($total['price']-$total['discount'])+$total['tax_amount'])-$total['buying_price']);
                                            array_push($oexps, $total['amount']);
                                            array_push($profits, ((($total['price']-$total['discount'])+$total['tax_amount'])-$total['buying_price'])-$total['amount']);
                                        ?>
                                        <tr>
                                            <td>{{$total['bizname']}}</td>
                                            <td style="text-align: center;">{{number_format((($total['price']-$total['discount'])+$total['tax_amount']), 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($total['buying_price'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format((($total['price']-$total['discount'])+$total['tax_amount'])-$total['buying_price'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($total['amount'])}}</td>
                                            <td style="text-align: center;">{{number_format(((($total['price']-$total['discount'])+$total['tax_amount'])-$total['buying_price'])-$total['amount'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tsales)}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tcsales)}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tsales-$tcsales)}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($texpenses)}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format(($tsales-$tcsales)-$texpenses)}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>      
                        </div>
                        <div class="tab-pane fade" id="report-chart" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div id="chart7"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- highcharts js -->
    <script src="{{ asset('assets/vendor/highcharts/js/highcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/highcharts-more.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/variable-pie.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/solid-gauge.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/highcharts-3d.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/cylinder.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/funnel3d.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/exporting.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/export-data.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/accessibility.js') }}"></script>
    <script>
        $(function () {
            "use strict";
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var duration = "<?php echo $duration; ?>";
            var shop_name = "<?php echo $shop->name; ?>";
            var constable = $('#consolidated').DataTable({
                "scrollX": true,
                "order": [
                    [1, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.consolidated_report') }}_" + date,
                        title: "{{ trans('navmenu.consolidated_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.consolidated_report') }}_" + date,
                        title: "All Businesses \n {{ trans('navmenu.consolidated_report') }} \n"+duration
                    }
                ],
            });
            constable.buttons().container().appendTo('#consolidated_wrapper .col-md-6:eq(1)');


            var bizs = <?php echo json_encode($bizs); ?>;
            var sales = <?php echo json_encode($sales); ?>;
            var cosales = <?php echo json_encode($cosales); ?>;
            var gps = <?php echo json_encode($gps); ?>;
            var oexps = <?php echo json_encode($oexps); ?>;
            var profits = <?php echo json_encode($profits); ?>;
            // chart7
            Highcharts.chart('chart7', {
                chart: {
                    type: 'bar',
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: "{{trans('navmenu.consolidated_report')}}"
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: bizs,
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: "{{trans('navmenu.amount')}}",
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },
                tooltip: {
                    valueSuffix: ''
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    x: -40,
                    y: 80,
                    floating: true,
                    borderWidth: 1,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                    shadow: true
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: "{{trans('navmenu.profit')}}",
                    data: profits
                }, {
                    name: "{{trans('navmenu.operating_expense')}}",
                    data: oexps
                }, {
                    name: "{{trans('navmenu.gross_profit')}}",
                    data: gps
                }, {
                    name: "{{trans('navmenu.cost_of_sales')}}",
                    data: cosales
                }, {
                    name: "{{trans('navmenu.sales')}}",
                    data: sales
                }]
            });

        });
    </script>
@endsection
    <link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
    <script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="sale_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>