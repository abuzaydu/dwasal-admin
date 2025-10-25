@extends('layouts.hr')
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
        <script type="text/javascript">

            function downloadJPG() {
                /*var container = document.getElementById("image-wrap");*/ /*specific element on page*/
                var container = document.getElementById("s_stats");; /* full page */
                html2canvas(container, { allowTaint: true }).then(function (canvas) {

                    var link = document.createElement("a");
                    document.body.appendChild(link);
                    link.download = "Salary_Statistics.jpg";
                    link.href = canvas.toDataURL();
                    link.target = '_blank';
                    link.click();
                });
            }

            function downloadPNG() {
                /*var container = document.getElementById("image-wrap");*/ /*specific element on page*/
                var container = document.getElementById("s_stats");; /* full page */
                html2canvas(container, { allowTaint: true }).then(function (canvas) {

                    var link = document.createElement("a");
                    document.body.appendChild(link);
                    link.download = "Salary_Statistics.png";
                    link.href = canvas.toDataURL();
                    link.target = '_blank';
                    link.click();
                });
            }

            function downloadJPG2() {
                /*var container = document.getElementById("image-wrap");*/ /*specific element on page*/
                var container = document.getElementById("t_salary");; /* full page */
                html2canvas(container, { allowTaint: true }).then(function (canvas) {

                    var link = document.createElement("a");
                    document.body.appendChild(link);
                    link.download = "Salary_By_Unit.jpg";
                    link.href = canvas.toDataURL();
                    link.target = '_blank';
                    link.click();
                });
            }

            function downloadPNG2() {
                /*var container = document.getElementById("image-wrap");*/ /*specific element on page*/
                var container = document.getElementById("t_salary");; /* full page */
                html2canvas(container, { allowTaint: true }).then(function (canvas) {

                    var link = document.createElement("a");
                    document.body.appendChild(link);
                    link.download = "Salary_By_Unit.png";
                    link.href = canvas.toDataURL();
                    link.target = '_blank';
                    link.click();
                });
            }
        </script>

        <style>
            #htmltoimage {
                width: 65%;
                margin: auto;
            }
        </style>

@section('content')
    <div class="block-header pt-4 py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                <form class="row g-3 report-form" action="{{ url('hr-dash') }}" method="POST">
                    @csrf
                    <div class="col-md-12">
                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">
                        <!-- Date and time range -->
                        <div class="input-group mb-1">
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

    <div class="row g-2 clearfix row-deck">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card top_counter">
                <div class="list-group list-group-custom list-group-flush">
                    <div class="list-group-item d-flex align-items-center py-3">
                        <div class="icon text-center me-3"><i class="fa fa-user"></i> </div>
                        <div class="content">
                            <div>New Employee</div>
                            <h5 class="mb-0">{{$new_employees}}</h5>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center py-3">
                        <div class="icon text-center me-3"><i class="fa fa-users"></i> </div>
                        <div class="content">
                            <div>Total Employee</div>
                            <h5 class="mb-0">{{$total_employees}}</h5>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center py-3">
                        <div class="icon text-center me-3"><i class="fa fa-university"></i> </div>
                        <div class="content">
                            <div>Total Salary</div>
                            <h5 class="mb-0">{{ number_format($total_salary, 2, '.', ',') }}</h5>
                        </div>
                    </div>
                    <div class="list-group-item d-flex align-items-center py-3">
                        <div class="icon text-center me-3"><i class="fa fa-university"></i> </div>
                        <div class="content">
                            <div>Avg. Salary</div>
                            <h5 class="mb-0">{{ number_format($avarage_salary, 2, '.',',')}}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header border-0">
                    <h6 class="card-title">Salary Statistics</h6>
                    <div class="d-flex align-items-center">
                        <ul class="header-dropdown list-unstyled">
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                <ul class="dropdown-menu dropdown-menu-end dropstart list-unstyled">
                                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="downloadJPG()">Downloa JPG</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="downloadPNG()">Download PNG</a></li>
                                    <!-- <li><a class="dropdown-item" href="javascript:void(0);">Something else</a></li> -->
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body" id="s_stats">
                    <div id="Salary_Statistics"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">Total Salary by Unit</h6>
                    <div class="d-flex align-items-center">
                        <ul class="header-dropdown list-unstyled">
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                <ul class="dropdown-menu dropdown-menu-end dropstart list-unstyled">
                                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="downloadJPG2()">Downloa JPG</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="downloadPNG2()">Download PNG</a></li>
                                    <!-- <li><a class="dropdown-item" href="javascript:void(0);">Something else</a></li> -->
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body" id="t_salary">
                    <div id="total_Salary" class="ct-chart"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">ToDo List</h6>
                </div>
                <div class="card-body todo_list">
                    @foreach($events as $key => $event)
                    <?php $date_now = new DateTime();
                        $date    = new DateTime($event->end); 
                    ?>
                    @if($date_now >= $date)
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="Makers" checked>
                            <label class="form-check-label" for="Makers">
                                <strong>{{$event->title}}</strong>
                            </label>
                            <span class="text-muted d-flex small text-uppercase">SCHEDULED FOR {{ date('d, M Y h:i a', strtotime($event->start)) }}</span>
                        </div>
                    </div>
                    @else
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="Makers">
                            <label class="form-check-label" for="Makers">
                                <strong>{{$event->title}}</strong>
                            </label>
                            <span class="text-muted d-flex small text-uppercase">SCHEDULED FOR {{ date('d, M Y h:i a', strtotime($event->start)) }}</span>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        
    </div> <!-- Row end  -->   
@endsection
@section('page-scripts')
    <script>
        $(function() {
            ("use strict");

            var labels = <?php echo json_encode($labels); ?>;
            var salaries = <?php echo json_encode($payrolls); ?>;
            var salary_series = <?php echo json_encode($series); ?>;
            // Salary_Statistics
            var options = {
                series: [{
                    name: "Total Salaries",
                    data: salaries,
                }],
                chart: {
                    type: "bar",
                    height: 245,
                    stacked: true,
                    toolbar: {
                      show: false,
                    },
                },
                plotOptions: {
                  bar: {
                    horizontal: false,
                  },
                },
                stroke: {
                  width: 1,
                  colors: ["#fff"],
                },
                colors: ['var(--chart-color1)', 'var(--chart-color2)', 'var(--chart-color3)'],
                dataLabels: {
                  enabled: false,
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        formatter: function(val) {
                            return val + "";
                        },
                    },
                },
                yaxis: {
                    title: {
                        text: undefined,
                    },
                },
                tooltip: {
                  y: {
                    formatter: function(val) {
                      return val + "K";
                    },
                  },
                },
                fill: {
                  opacity: 1,
                },
                legend: {
                  position: "top",
                  horizontalAlign: "center",
                  offsetX: 0,
                },
            };
            var chart = new ApexCharts(document.querySelector("#Salary_Statistics"), options);
            chart.render();

            // Total Salary Chart
            var optionsSpark1 = {
                series: salary_series,
                colors: ['var(--chart-color1)', 'var(--chart-color2)', 'var(--chart-color3)', 'var(--chart-color4)', 'var(--chart-color5)'],
                chart: {
                  type: "line",
                  height: 245,
                  sparkline: {
                    enabled: false,
                  },
                  toolbar: {
                    show: false,
                  },
                },
                stroke: {
                  show: true,
                  curve: "smooth",
                  // lineCap: "butt",
                  colors: undefined,
                  width: 1,
                  dashArray: 0,
                },
                dataLabels: {
                  enabled: false,
                },
                tooltip: {
                  y: {
                    formatter: function(val) {
                      return val + "K";
                    },
                  },
                },
                xaxis: {
                  show: true,
                  categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec", ],
                },
                yaxis: {
                  show: true,
                  categories: ["12K", "11K", "10K", "9K", "8K", "7K", "6K", "5K", "4K", "3K", "2K", "1K", ],
                },
                legend: {
                  position: "top",
                  horizontalAlign: "center",
                },
            };
            var chartSpark1 = new ApexCharts(document.querySelector("#total_Salary"), optionsSpark1);
            chartSpark1.render();
        });
    </script>
@endsection