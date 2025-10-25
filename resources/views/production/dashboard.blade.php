@extends('layouts.prod')

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
                <form class="row g-3 dashform" action="{{url('prod-home')}}" method="POST" id="dashform">
                    @csrf
                    <div class="col-md-4"></div>
                    
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-8">
                        <div class="input-group">
                            <button type="button" class="btn btn-white float-end" id="reportrange">
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
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 ">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">{{trans('navmenu.rm_cost')}}</h6>
                        <div class="ms-auto">
                            <i class='fa fa-line-chart fs-3 text-primary'></i>
                        </div>
                    </div>
                    <div class="progress my-2" style="height:4px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="mb-0 text-primary">{{$currency}} {{$settings->currency}} {{number_format($total_rm , 2, '.', ',')}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">{{trans('navmenu.pm_cost')}}</h6>
                        <div class="ms-auto">
                            <i class='fa fa-money fs-3 text-success'></i>
                        </div>
                    </div>
                    <div class="progress my-2" style="height:4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 55%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="mb-0 text-success">{{$currency}} {{number_format($total_pm, 2, '.', ',')}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">Direct Labour Cost</h6>
                        <div class="ms-auto">
                            <i class='fa fa-credit-card fs-3 text-danger'></i>
                        </div>
                    </div>
                    <div class="progress my-2" style="height:4px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 55%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="mb-0 text-danger">{{$currency}} {{number_format($total_dlc, 2, '.', ',')}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0">{{trans('navmenu.total_production_cost')}}</h6>
                        <div class="ms-auto">
                            <i class='fa fa-calculator fs-3 text-warning'></i>
                        </div>
                    </div>
                    <div class="progress my-2" style="height:4px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="mb-0 text-warning">{{$currency}} {{number_format($total_pm+$total_rm+$total_dlc, 2, '.', ',')}}<p>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end row-->

    <div class="row">
        <div class="col-12 col-lg-6 col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="chart1"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="chart2"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="chart3"></div>
                </div>
            </div>
        </div>
         <div class="col-12 col-lg-6 col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="chart4"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8 col-xl-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="chart5"></div>
                </div>
            </div>
        </div>
    </div><!--End Row-->
    {{--<div class="card radius-10">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0">{{trans('navmenu.top_selling')}}</h5>
                </div>
                <div class="font-22 ms-auto"><i class="fa fa-dots-horizontal-rounded"></i>
                </div>
            </div>
            <hr>
            <ul class="nav nav-tabs nav-success" role="tablist">
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
            </ul>
            <div class="tab-content py-3">
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
                                    <td style="text-align: right;"><strong>{{$settings->currency}} {{number_format($product->price-$product->discount, 2, '.', ',')}}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="services" role="tabpanel">
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
                                    <td style="text-align: center;"><strong>{{$settings->currency}} {{number_format($service->total-$service->discount, 2, '.', ',')}}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

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
    <script src="{{ asset('assets/data-series.js') }}"></script>
    <script>
        $(function () {
            "use strict";

            var from = <?php echo json_encode($start); ?>;
            var to= <?php echo json_encode($end); ?>;
            var pm_labels = <?php echo json_encode($pm_labels); ?>;
            var pm_uses_data = <?php echo json_encode($pm_uses_data); ?>;
            var pm_use_set = <?php echo json_encode($pm_use_set); ?>;

            var rm_labels = <?php echo json_encode($rm_labels); ?>;
            var rm_uses_data = <?php echo json_encode($rm_uses_data); ?>;

             var rm_stock_labels = <?php echo json_encode($rm_stock_labels); ?>;
            var rm_stock = <?php echo json_encode($rm_stock_data); ?>;
            var rm_stock_data = rm_stock.map(str=>{
                return Number(str);
            });
             var pm_stock_labels = <?php echo json_encode($pm_stock_labels); ?>;
            var pm_stock = <?php echo json_encode($pm_stock_data); ?>;
            var pm_stock_data = pm_stock.map(str=>{
                return Number(str);
            });
            var rm_use_date = <?php echo json_encode($rm_use_date); ?>;
            var pm_use_date = <?php echo json_encode($pm_use_date); ?>;
            var product_labels = <?php echo json_encode($product_lables); ?>;
            var product_qty = <?php echo json_encode($product_qty); ?>;

    
            var rm = {{Js::from($rm_use_series)}};
            var pm = {{Js::from($pm_use_series)}};
            var products_made = {{Js::from($products_made)}};
            var rm_use_lable = {{Js::from($rm_use_lable)}};
            var rm_use_qty = {{Js::from($rm_use_qty)}};

            // chart 12
            Highcharts.chart('chart1', {
                chart: {
                   type : 'column'
                },
                title: {
                    text: "{{trans('navmenu.rm_stock')}}"
                },
                xAxis: {
                    categories: rm_stock_labels
                },

                yAxis:{
                    min: 0,
                    title: {
                        text : 'Amount In Stock'
                    }
                },

                series: [{
                    name: 'Raw Materials',
                    data: rm_stock_data,
                }]
            });

             // chart 13
            Highcharts.chart('chart2', {
                chart: {
                   type : 'column'
                },
                title: {
                    text: "{{trans('navmenu.pm_stock')}}"
                },
                xAxis: {
                    categories: pm_stock_labels
                },

                yAxis:{
                    min: 0,
                    title: {
                        text : 'Amount In Stock'
                    }
                },

                series: [{
                    name: 'Packing Materials',
                    data: pm_stock_data,
                }]
            });

  
            Highcharts.chart('chart4', {
                chart: {
                    type: 'line'
                },
                title: {
                    text: 'Packing Materials Used'
                },
                subtitle: {
                    text: 'From: '+ from +' TO '+ to,
                },
                xAxis: {
                    categories: pm_use_date
                },
                yAxis: {
                    title: {
                        text: 'Quantity Used'
                    }
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },
                series: pm
            });




           

            Highcharts.chart('chart3', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Raw Materials Used'
            },
            subtitle: {
                text: 'From: '+ from +' TO '+ to,
            },
            xAxis: {
                categories: rm_use_date
            },
            yAxis: {
                title: {
                    text: 'Quantity Used'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: rm
        });

       


            console.log(products_made);  

     // chart 12
            Highcharts.chart('chart5', {
                chart: {
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: "Raw Materials Used Vs Product Made"
                },

                xAxis: {
                    categories: rm_use_lable
                }
                ,
                labels: {
                    items: [{
                        html: 'Products Made Summary',
                        style: {
                            left: '50px',
                            top: '18px',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style && Highcharts.defaultOptions.title.style.color) || 'black'
                        }
                    }]
                },
                series: [{
                    type: 'column',
                    name: 'Raw Materials Used',
                    data: rm_use_qty,
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[6],
                        fillColor: 'white'
                    }
                }, {
                    type: 'pie',
                    data: products_made,
                    center: [300, 20],
                    size: 100,
                    showInLegend: false,
                    dataLabels: {
                        enabled: true
                    }
                }]
            });
        });
    </script>
@endsection