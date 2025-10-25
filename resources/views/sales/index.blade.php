@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/highcharts/css/highcharts.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-10 col-md-10 col-sm-12 text-right">
                <form class="row g-1 d-flex justify-content-end dashform" action="{{ url('sales-dash') }}" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <select name="store" class="form-select form-select-sm mb-1" onchange='this.form.submit();'>
                            @if (!is_null($currstore))
                                <option value="{{ $currstore->id }}">{{ $currstore->name }}</option>
                            @endif
                            <option value="">All Stores</option>
                            @foreach ($shops as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{ $start_date }}">
                    <input type="hidden" name="end_date" id="end_input" value="{{ $end_date }}">
                    <!-- Date and time range -->
                    <div class="col-md-5">
                        <div class="input-group d-flex justify-content-end">
                            <button type="button" class="btn btn-white btn-sm mb-1 pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        @if($settings->is_cm_business)
                        <a href="{{ url('cm-dashboard') }}" class="btn btn-primary btn-sm">Riders Dashboard</a>
                        @endif
                    </div>
                </form>
            </div>
            <!-- <div class="col-lg-12 col-md-12 col-sm-12">
                <marquee><small>{{trans('navmenu.expire_notify')}}@if($status > 7) <b style="color: green;">{{date('d M, Y H:m:s:A', strtotime($payment->expire_date))}} ({{$status}} {{trans('navmenu.days')}})</b> @else <b style="color: red;">{{date('d M, Y H:m:s:A', strtotime($payment->expire_date))}} ({{$status}} {{trans('navmenu.days')}})</b>@endif </small></marquee>
            </div> -->
        </div>
    </div>
    <!--end breadcrumb-->
    <div id="dash-section">
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">{{ trans('navmenu.total_sales') }}</p>
                            <p class="mb-0 p-0 ms-auto">
                                <span>
                                    <i class='fa fa-line-chart fs-3 text-primary'></i>
                                </span>
                            </p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-primary">{{ $currency }}
                                {{ number_format($total_sales, 2, '.', ',') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">{{ trans('navmenu.total_collections') }}</p>
                            <p class="mb-0 p-0 ms-auto">
                                <span>
                                    <i class='fa fa-money fs-3 text-success'></i>
                                </span>
                            </p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-success">{{ $currency }}
                                {{ number_format($total_collections, 2, '.', ',') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">{{ trans('navmenu.total_debts') }}</p>
                            <p class="mb-0 p-0 ms-auto">
                                <span>
                                    <i class='fa fa-credit-card -front fs-3 text-danger'></i>
                                </span>
                            </p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-danger">{{ $currency }}
                                {{ number_format($total_debts, 2, '.', ',') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">{{ trans('navmenu.total_expenses') }}</p>
                            <p class="mb-0 p-0 ms-auto">
                                <span>
                                    <i class='icon-pie-chart fs-3 text-warning'></i>
                                </span>
                            </p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="75"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-warning">{{ $currency }}
                                {{ number_format($total_expenses, 2, '.',',') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->

        <div class="row">
            <div class="col-12 col-lg-6 col-xl-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div id="chart12"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 col-xl-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div id="chart13"></div>
                    </div>
                </div>
            </div>
        </div><!--End Row-->
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="mb-0">{{trans('navmenu.top_selling')}}</h5>
                    </div>
                    <div class="font-22 ms-auto"><i class="fa fa-dots-horizontal-rounded"></i>
                    </div>
                </div>
                <hr>
                <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                    @if($shop->business_type_id == 4)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#products" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-shopping-bag font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.products')}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" data-bs-toggle="tab" href="#services" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-cog font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.services')}}</div>
                            </div>
                        </a>
                    </li>
                    @elseif($shop->business_type_id == 3)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#services" role="tab" aria-selected="false">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-cog font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.services')}}</div>
                            </div>
                        </a>
                    </li>
                    @else
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#products" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='fa fa-shopping-bag font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.products')}}</div>
                            </div>
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content py-3">
                    @if($shop->business_type_id != 3)
                    <div class="tab-pane fade show active" id="products" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{trans('navmenu.product')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $key => $product)
                                    <tr>
                                        <td><strong>{{$product->name}}</strong></td>
                                        <td style="text-align: center;">{{$product->quantity}}</td>
                                        <td style="text-align: center;">{{number_format($product->unitprice-$product->unitdiscount, 2, '.', ',')}}</td>
                                        <td style="text-align: right;"><strong>{{$currency}} {{number_format($product->price-$product->discount, 2, '.', ',')}}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    @if($shop->business_type_id == 3)
                    <div class="tab-pane fade show active" id="services" role="tabpanel">
                    @else
                    <div class="tab-pane fade" id="services" role="tabpanel">
                    @endif
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{trans('navmenu.service')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($services as $key => $service)
                                    <tr>
                                        <td><strong>{{$service->name}}</strong></td>
                                        <td style="text-align: center;">{{$service->quantity}}</td>
                                        <td style="text-align: center;">{{number_format($service->unitprice-$service->unitdiscount)}}</td>
                                        <td style="text-align: center;"><strong>{{$currency}} {{number_format($service->total-$service->discount, 2, '.', ',')}}</strong></td>
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

@section('page-scripts')

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

            var labels = <?php echo json_encode($labels); ?>;
            var sales = <?php echo json_encode($salesdata); ?>;
            var totalsales = <?php echo $total_sales; ?>;
            var grossprofit = <?php echo $gross_profit; ?>;
            var totalexpenses = <?php echo $total_expenses; ?>;
            var netprofit = <?php echo $gross_profit-$total_expenses; ?>;

            var gelabels = <?php echo json_encode($gelabels); ?>;
            var grosses = <?php echo json_encode($grosses); ?>;
            var expenses = <?php echo json_encode($expensesdata); ?>;
            // chart 12
            Highcharts.chart('chart12', {
                chart: {
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: "{{trans('navmenu.sales_per_day')}}"
                },
                xAxis: {
                    categories: labels
                },
                labels: {
                    items: [{
                        html: 'Total Profit Summary',
                        style: {
                            left: '50px',
                            top: '18px',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style && Highcharts.defaultOptions.title.style.color) || 'black'
                        }
                    }]
                },
                series: [{
                    type: 'spline',
                    name: 'Sales',
                    data: sales,
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }, {
                    type: 'pie',
                    name: 'Amount',
                    data: [{
                        name: 'Sales',
                        y: totalsales,
                        color: Highcharts.getOptions().colors[0] // Saless color
                    }, {
                        name: 'Gross Profit',
                        y: grossprofit,
                        color: Highcharts.getOptions().colors[2] // Gross profits color
                    }, {
                        name: 'Expenses',
                        y: totalexpenses,
                        color: Highcharts.getOptions().colors[1] // Expenses color
                    }, {
                        name: 'Net Profit',
                        y: netprofit,
                        color: Highcharts.getOptions().colors[3] // Net Profits color
                    }],
                    center: [100, 80],
                    size: 100,
                    showInLegend: false,
                    dataLabels: {
                        enabled: false
                    }
                }]
            });

            // chart 13
            Highcharts.chart('chart13', {
                chart: {
                    zoomType: 'xy',
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Average Gross Profit and Expenses'
                },
                subtitle: {
                    text: 'Business Evaluation'
                },
                xAxis: [{
                    categories: gelabels,
                    crosshair: true
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    title: {
                        text: 'Expenses',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }, { // Secondary yAxis
                    title: {
                        text: 'Gross Profit',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    labels: {
                        format: '',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],
                tooltip: {
                    shared: true
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    x: 120,
                    verticalAlign: 'top',
                    y: 100,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                    'rgba(255,255,255,0.25)'
                },
                series: [{
                    name: "{{trans('navmenu.gross_profit')}}",
                    type: 'column',
                    yAxis: 1,
                    data: grosses,
                }, {
                    name: "{{trans('navmenu.expenses')}}",
                    type: 'spline',
                    data: expenses
                }]
            });
        });
    </script>
@endsection

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">

        function savePdf() {
            
            const element = document.getElementById("dash-section");
            var filename = "<?php echo $title.'_'.date('d M Y', strtotime($start_date)).' to '.date('d M Y', strtotime($end_date)); ?>";
            var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).toPdf().save();
        }
    </script>