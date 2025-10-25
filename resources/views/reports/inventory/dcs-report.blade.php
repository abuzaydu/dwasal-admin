@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
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
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <form class="row g-3 dashform" action="{{url('daily-closing-stock-report')}}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-4"></div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <div class="col-md-8 float-md-end">
                        <div class="input-group">
                            <button type="button" class="btn btn-white pull-right mb-3" id="reportrange">
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
                <div class="card-header">
                    <div class="table-responsive">
                        <table id="dcsvalues" class="table table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0; text-align: center;">
                                <tr>
                                    <!-- <th style="text-align: center;">#</th> -->
                                    <th>{{trans('navmenu.date')}}</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">Opening</th>
                                    <th style="text-align: center;">Opening Value</th>
                                    <th style="text-align: center;">Opening Retail Value</th>
                                    @if($settings->retail_with_wholesale)
                                    <th style="text-align: center;">Opening Wholesale Value</th>
                                    @endif
                                    <th style="text-align: center;">Purchased</th>
                                    <th style="text-align: center;">Sold</th>
                                    <th style="text-align: center;">Returned</th>
                                    <th style="text-align: center;">Transfered</th>
                                    <th style="text-align: center;">Damaged</th>
                                    <th style="text-align: center;">Closing</th>
                                    <th style="text-align: center;">Closing Value</th>
                                    <th style="text-align: center;">Closing Retail Value</th>
                                    @if($settings->retail_with_wholesale)
                                    <th style="text-align: center;">Closing Wholesale Value</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <?php $opentotal = 0; $openretail = 0; $openwholesale = 0; $closingtotal = 0; $closeretail = 0; $closewholesale = 0; ?>
                                @foreach($dcsvalues as $index => $stock)
                                <?php 
                                    $opentotal += $stock->start_value;
                                    $openretail += $stock->start_retail_value;
                                    $openwholesale += $stock->start_wholesale_value;
                                    $closingtotal += $stock->end_value;
                                    $closeretail += $stock->end_retail_value;
                                    $closewholesale += $stock->end_wholesale_value;
                                ?>
                                <tr>
                                    <td>{{$stock->date}}</td>
                                    <td>{{$stock->name}}</td>
                                    <td style="text-align: center;">{{$stock->start_qty+0}}</td>
                                    <td style="text-align: right;">{{number_format($stock->start_value, 2, '.', ',')}}</td>
                                    <td style="text-align: right;">{{number_format($stock->start_retail_value, 2, '.', ',')}}</td>
                                    @if($settings->retail_with_wholesale)
                                    <td style="text-align: right;">{{number_format($stock->start_wholesale_value, 2, '.', ',')}}</td>
                                    @endif
                                    <td style="text-align: center;">{{$stock->purchase_qty+0}}</td>
                                    <td style="text-align: center;">{{$stock->sold_qty+0}}</td>
                                    <td style="text-align: center;">{{$stock->return_qty+0}}</td>
                                    <td style="text-align: center;">{{$stock->transfer_qty+0}}</td>
                                    <td style="text-align: center;">{{$stock->dam_qty+0}}</td>
                                    <td style="text-align: center;">{{$stock->end_qty+0}}</td>
                                    <td style="text-align: right;">{{number_format($stock->end_value, 2, '.', ',')}}</td>
                                    <td style="text-align: right;">{{number_format($stock->end_retail_value, 2, '.', ',')}}</td>
                                    @if($settings->retail_with_wholesale)
                                    <td style="text-align: right;">{{number_format($stock->end_wholesale_value, 2, '.', ',')}}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td><b>{{trans('navmenu.total')}}</b></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: right;"><b>{{number_format($opentotal, 2, '.', ',')}}</b></td>
                                    <td style="text-align: right;"><b>{{number_format($openretail, 2, '.', ',')}}</b></td>
                                    @if($settings->retail_with_wholesale)
                                    <td style="text-align: right;"><b>{{number_format($openwholesale, 2, '.', ',')}}</b></td>
                                    @endif
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: center;"></td>
                                    <td style="text-align: right;"><b>{{number_format($closingtotal, 2, '.', ',')}}</b></td>
                                    <td style="text-align: right;"><b>{{number_format($closeretail, 2, '.', ',')}}</b></td>
                                    @if($settings->retail_with_wholesale)
                                    <td style="text-align: right;"><b>{{number_format($closewholesale, 2, '.', ',')}}</b></td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
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
    <script>
        $(function(){
                    
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var shop_name = "<?php echo $shop->name; ?>";

            var stocktable = $('#dcsvalues').DataTable({
                "scrollX": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{trans('navmenu.daily_closing_stock_report')}}_" + date,
                        title: "{{trans('navmenu.daily_closing_stock_report')}} " + date,
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{trans('navmenu.daily_closing_stock_report')}}_" + date,
                        title: shop_name + "\n {{trans('navmenu.daily_closing_stock_report')}} \n Date : " + date,
                        orientation: 'landscape'
                    }
                ],
            });
            stocktable.buttons().container().appendTo('#dcsvalues_wrapper .col-md-6:eq(1)');

        })
    </script>
@endsection