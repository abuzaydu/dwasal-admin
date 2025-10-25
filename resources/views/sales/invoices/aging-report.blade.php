@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.aging_report')}} @if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</h6>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="aging-report" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_id')}}</th>
                                    <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_name')}}</th>
                                    <th style="text-align: center; text-transform: uppercase;">0-30({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">31-60({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">61-90({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">91-120({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">121-150({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">151-180({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">181-210({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">211-240({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">241-270({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">271-300({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">301-330({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">331-360({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">Over 360({{trans('navmenu.days')}})</th>
                                    <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $d3total =0; $d6total=0; $d9total=0; $d12total=0; $d15total=0; $d18total=0; $d21total=0; $d24total=0; $d27total=0; $d30total=0; $d33total=0; $d36total=0; $dab36total = 0; $total = 0; ?>
                                @foreach($agings as $aging)
                                <?php 
                                    $d3total += $aging['0-30'];
                                    $d6total += $aging['31-60'];
                                    $d9total += $aging['61-90'];
                                    $d12total += $aging['91-120'];
                                    $d15total += $aging['121-150'];
                                    $d18total += $aging['151-180'];
                                    $d21total += $aging['181-210'];
                                    $d24total += $aging['211-240'];
                                    $d27total += $aging['241-270'];
                                    $d30total += $aging['271-300'];
                                    $d33total += $aging['301-330'];
                                    $d36total += $aging['331-360'];
                                    $dab36total += $aging['>360'];
                                    $total += $aging['ctotal'];
                                ?>
                                <tr>
                                    <td style="text-align: center;">{{sprintf('%03d', $aging['cust_no'])}}</td>
                                    <td style="text-align: center;">{{$aging['name']}}</td>
                                    <td style="text-align: center;">{{number_format($aging['0-30'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['31-60'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['61-90'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['91-120'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['121-150'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['151-180'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['181-210'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['211-240'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['241-270'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['271-300'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['301-330'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['331-360'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['>360'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($aging['ctotal'], 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{number_format($d3total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d6total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d9total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d12total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d15total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d18total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d21total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d24total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d27total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d30total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d33total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($d36total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($dab36total, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($total, 2, '.', ',')}}</th>
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
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var duration = "<?php echo $duration; ?>";
            var shop_name = "<?php echo $shop->name; ?>";


            var agtable = $('#aging-report').DataTable({
                "scrollX": true,
                "order": [
                    [1, "asc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.aging_report') }}_" + date,
                        title: " {{ trans('navmenu.aging_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.aging_report') }}_" + date,
                        title: shop_name + " \n {{ trans('navmenu.aging_report') }} \n"+duration
                    }
                ],
            });
            agtable.buttons().container().appendTo('#aging-report_wrapper .col-md-6:eq(1)');

        });
    </script>
@endsection