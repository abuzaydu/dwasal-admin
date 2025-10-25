@extends('layouts.pr')
    
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('payrolls')}}">Payrolls</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                <form class="row g-3 report-form" action="{{ url('payroll-dash') }}" method="POST">
                @csrf
                <div class="col-md-3">
                    <select class="form-select form-select-sm mb-1 col-md-6" name="year" onchange="this.form.submit()">
                        @foreach($data as $d)
                        @if($d['year'] == $curyear)
                        <option selected value="{{$d['year']}}">{{$d['year']}}</option>
                        @else
                        <option value="{{$d['year']}}">{{$d['year']}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-9">
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="chart12"></div>
                </div>            
            </div>
        </div>
        <div class="col-xl-3 mx-auto">
            <div class="row g-2 row-deck mb-4">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="oneExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item" data-bs-interval="1500">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill me-3 text-truncate">
                                                <div class="text-muted mb-2 text-uppercase" style="font-size: 9px;">Total Employees</div>
                                                <h4 class="mb-0" style="font-size: 13px;">{{$employees}}</h4>
                                            </div>
                                            <div class="avatar lg rounded-circle no-thumbnail chart-color1 text-light"><i class="fa fa-users fa-lg"></i></div>
                                        </div>
                                    </div>
                                    <div class="carousel-item active" data-bs-interval="1500">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill me-3 text-truncate">
                                                <div class="text-muted mb-2 text-uppercase" style="font-size: 9px;">Total Employees</div>
                                                <h4 class="mb-0" style="font-size: 13px;">{{$employees}}</h4>
                                            </div>
                                            <div class="avatar lg rounded-circle no-thumbnail chart-color2 text-light"><i class="fa fa-users fa-lg"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="twoExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active" data-bs-interval="2000">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill me-3 text-truncate">
                                                <div class="text-muted mb-2 text-uppercase" style="font-size: 9px;">Total Earnings</div>
                                                <h4 class="mb-0" style="font-size: 13px;">{{ number_format($total_earns, 2, '.', ',') }}</h4>
                                            </div>
                                            <div class="avatar lg rounded-circle no-thumbnail chart-color1 text-light"><i class="fa fa-plus-square-o fa-lg"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item" data-bs-interval="2000">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill me-3 text-truncate">
                                                <div class="text-muted mb-2 text-uppercase" style="font-size: 9px;">Total Earnings</div>
                                                <h4 class="mb-0" style="font-size: 13px;">{{ number_format($total_earns, 2, '.', ',') }}</h4>
                                            </div>
                                            <div class="avatar lg rounded-circle no-thumbnail chart-color2 text-light"><i class="fa fa-plus-square-o fa-lg"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="threeExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item carousel-item-next carousel-item-start" data-bs-interval="1700">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill me-3 text-truncate">
                                                <div class="text-muted mb-2 text-uppercase" style="font-size: 9px;">Total Deductions</div>
                                                <h4 class="mb-0" style="font-size: 13px;">{{ number_format($total_deductions, 2, '.', ',') }} </h4>
                                            </div>
                                            <div class="avatar lg rounded-circle no-thumbnail chart-color3 text-light"><i class="fa fa-minus-square fa-lg"></i></div>
                                        </div>
                                    </div>
                                    <div class="carousel-item active carousel-item-start" data-bs-interval="1700">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill me-3 text-truncate">
                                                <div class="text-muted mb-2 text-uppercase" style="font-size: 9px;">Total Deductions</div>
                                                <h4 class="mb-0" style="font-size: 13px;">{{ number_format($total_deductions, 2, '.', ',') }} </h4>
                                            </div>
                                            <div class="avatar lg rounded-circle no-thumbnail chart-color4 text-light"><i class="fa fa-minus-square fa-lg"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="fourExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item carousel-item-next carousel-item-start" data-bs-interval="2200">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill me-3 text-truncate">
                                                <div class="text-muted mb-2 text-uppercase" style="font-size: 9px;">Total Net Pay</div>
                                                <h4 class="mb-0" style="font-size: 13px;">{{ number_format($total_net_pay, 2, '.', ',') }} </h4>
                                            </div>
                                            <div class="avatar lg rounded-circle no-thumbnail chart-color1 text-light"><i class="fa fa-money fa-lg"></i></div>
                                        </div>
                                    </div>
                                    <div class="carousel-item active carousel-item-start" data-bs-interval="2200">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-fill me-3 text-truncate">
                                                <div class="text-muted mb-2 text-uppercase" style="font-size: 9px;">Total Net Pay</div>
                                                <h4 class="mb-0" style="font-size: 13px;">{{ number_format($total_net_pay, 2, '.', ',') }} </h4>
                                            </div>
                                            <div class="avatar lg rounded-circle no-thumbnail chart-color2 text-light"><i class="fa fa-money fa-lg"></i></div>
                                        </div>
                                    </div>
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
    <link href="{{ asset('assets/vendor/highcharts/css/highcharts.css') }}" rel="stylesheet" />

    <!-- highcharts js -->
    <script src="{{ asset('assets/vendor/highcharts/js/variable-pie.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/solid-gauge.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/highcharts-3d.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/cylinder.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/funnel3d.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/highcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/highcharts-more.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/exporting.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/export-data.js') }}"></script>
    <script src="{{ asset('assets/vendor/highcharts/js/accessibility.js') }}"></script>
    <script>
        $(function () {
            "use strict";

            var labels = <?php echo json_encode($months); ?>;
            var earnings = <?php echo json_encode($earns); ?>;
            var deductions = <?php echo json_encode($deducts); ?>;
            var netpays = <?php echo json_encode($nets); ?>;
            var tearnings = <?php echo $total_earns; ?>;
            var tdeductions = <?php echo $total_deductions; ?>;
            var tnetpay = <?php echo $total_net_pay; ?>;
            // chart 12
            Highcharts.chart('chart12', {
                chart: {
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Payrolls for Year {{$curyear}}'
                },
                xAxis: {
                    categories: labels
                },
                labels: {
                    items: [{
                        html: 'Monthly Payrolls Summary',
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
                    name: 'Earnings',
                    data: earnings
                }, {
                    type: 'spline',
                    name: 'Deductions',
                    data: deductions
                }, {
                    type: 'spline',
                    name: 'Net Pay',
                    data: netpays,
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }, {
                    type: 'pie',
                    name: 'Amount',
                    data: [{
                        name: 'Total Earnings',
                        y: tearnings,
                        color: Highcharts.getOptions().colors[0] // Jane's color
                    }, {
                        name: 'Total Deductions',
                        y: tdeductions,
                        color: Highcharts.getOptions().colors[1] // John's color
                    }, {
                        name: 'Total Net Pay',
                        y: tnetpay,
                        color: Highcharts.getOptions().colors[2] // Joe's color
                    }],
                    center: [100, 80],
                    size: 100,
                    showInLegend: false,
                    dataLabels: {
                        enabled: false
                    }
                }]
            });
            
        });

    </script>
@endsection