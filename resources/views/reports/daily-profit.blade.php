@extends('layouts.gen')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/highcharts/css/highcharts.css') }}" rel="stylesheet" />
@endsection
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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <form class="row g-3 dashform" action="{{url('total-report')}}" method="POST">
                    @csrf
                    <div class="col-md-3">
                        <div class="inner-addon left-addon">
                            <i class="myaddon fa fa-calendar"></i>
                            <input type="text" name="sale_date" id="saledate" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" autocomplete="off">
                        </div>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class=" col-md-9">
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
                                    <div class="tab-title">{{trans('navmenu.daily_profit_loss_report')}} (Excel)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#report-chart" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.daily_profit_loss_report')}} Chart</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="report-excel" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="totals" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>{{trans('navmenu.date')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sales')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.cost_of_sales')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.gross_profit')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.operating_expense')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($totals as $index => $total)
                                        <tr>
                                            <td>{{$total['date']}}</td>
                                            <td style="text-align: center;">{{number_format(($total['price']-$total['discount'])+$total['tax_amount'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($total['buying_price'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format(((($total['price']-$total['discount'])+$total['tax_amount'])-$total['buying_price']), 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($total['amount'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format(((($total['price']-$total['discount'])+$total['tax_amount'])-$total['buying_price'])-$total['amount'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th><b>{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;"><b>{{number_format($tsales, 2, '.', ',')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tcsales, 2, '.', ',')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tsales-$tcsales, 2, '.', ',')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($texpenses, 2, '.', ',')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format(($tsales-$tcsales)-$texpenses, 2, '.', ',')}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <div class="tab-pane fade" id="report-chart" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div id="chart14"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b>
    </th>
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

            var totable = $('#totals').DataTable({
                "scrollX": true,
                "order": [
                    [0, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.daily_profit_loss_report') }}" + date,
                        title: "{{ trans('navmenu.daily_profit_loss_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.daily_profit_loss_report') }}" + date,
                        title: shop_name + "\n {{ trans('navmenu.daily_profit_loss_report') }} \n "+duration
                    }
                ],
            });
            totable.buttons().container().appendTo('#totals_wrapper .col-md-6:eq(1)');

            var labels = <?php echo json_encode($labels); ?>;
            var grosses = <?php echo json_encode($grosses); ?>;
            var expenses = <?php echo json_encode($expensesdata); ?>;
            var netprofits = <?php echo json_encode($netprofits); ?>;
            // chart 14
            Highcharts.chart('chart14', {
                chart: {
                    type: 'column',
                    styledMode: true
                },
                title: {
                    text: "{{trans('navmenu.daily_profit_loss_report')}}"
                },
                xAxis: {
                    categories: labels
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: "{{trans('navmenu.gross_profit')}}",
                    data: grosses
                }, {
                    name: "{{trans('navmenu.operating_expense')}}",
                    data: expenses
                }, {
                    name: "{{trans('navmenu.profit')}}",
                    data: netprofits
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