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
                <form class="dashform" action="{{url('debts-report')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                    <!-- /.form group -->
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <ul class="col-md-7 nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#debtors-total" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.debtors_total')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#debtors" role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list-minus font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.debtors')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ url('aging-report')}}" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.debtors-aging-report')}}</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <div class="col-md-5">
                            
                        </div>
                    </div>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade" id="debtors" role="tabpanel">
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="debts" class="table table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>{{trans('navmenu.saledate')}}</th>
                                                <th>{{trans('navmenu.customer_name')}}</th>
                                                <th>{{trans('navmenu.phone_number')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                                <th style="text-align: center;">Net Sales Amount</th>
                                                <th style="text-align: center;">{{trans('navmenu.paid')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.unpaid')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total_amount = 0; $total_paid = 0; $total_debts = 0;?>
                                            @foreach($debts as $index => $sale)
                                            <?php 
                                                $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                                                $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                                                $netsales_amount = $tnetsales-$tnetreturn;
                                                $total_amount += $netsales_amount;
                                                $total_paid += $sale->sale_amount_paid;
                                                $total_debts += $total_amount-$total_paid;
                                            ?>
                                            <tr>
                                                <td>{{date('d-m-Y', strtotime($sale->time_created))}}</td>
                                                <td>{{$sale->name}}</td>
                                                <td>{{$sale->phone}}</td>
                                                <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($sale->id)) }}">{{ sprintf('%04d', $sale->invoice_no)}}</a></td>
                                                <td style="text-align: center;">{{number_format($netsales_amount, 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale->sale_amount_paid, 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format(($netsales_amount-$sale->sale_amount_paid), 2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th><b>{{trans('navmenu.total')}}</b></th>
                                                <th></th>
                                                <th></th>
                                                <th style="text-align: center;"><b>{{number_format($total_amount, 2,'.',',')}}</b></th>
                                                <th style="text-align: center;"><b>{{number_format($total_paid, 2,'.',',')}}</b></th>
                                                <th style="text-align: center;"><b>{{number_format($total_debts, 2,'.',',')}}</b></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <div class="tab-pane fade show active" id="debtors-total" role="tabpanel">
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="totaldebts" class="table table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{trans('navmenu.customer_name')}}</th>
                                                <th>{{trans('navmenu.phone_number')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.opening_balance')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.new_invoices')}}</th>
                                                <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($totaldebts as $index => $sale)
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td><a href="{{ url('customer-account-stmt/'.encrypt($sale['customer_id'])) }}">{{$sale['name']}}</a></td>
                                                <td>{{$sale['phone']}}</td>
                                                <td style="text-align: center;">{{number_format($sale['opening_balance'], 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale['new_invoices'], 2, '.', ',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale['total'], 2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th><b>{{trans('navmenu.total')}}</b></th>
                                                <th></th>
                                                <th style="text-align: center;"><b>{{number_format($total_ob, 2, '.', ',')}}</b></th>
                                                <th style="text-align: center;"><b>{{number_format($total_invoices, 2, '.', ',')}}</b></th>
                                                <th style="text-align: center;"><b>{{number_format($total_ob+$total_invoices, 2, '.', ',')}}</b></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->                        
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

            var is_school = "<?php echo $settings->is_school; ?>";
            var columns = [{
                    "sType": "date-uk"
                },
                null,
                null,
                null,
                null,
                null,
                null
            ];

            var debtable = $('#debts').DataTable({
                "scrollX": true,
                "aoColumns": columns,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.debt_report') }}_" + date,
                        title: "{{ trans('navmenu.debt_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.debt_report') }}_" + date,
                        title: shop_name + " \n {{ trans('navmenu.debt_report') }} \n"+duration
                    }
                ],
            });    
            debtable.buttons().container().appendTo('#debts_wrapper .col-md-6:eq(1)');

            var tdcolumns = [
                null,
                null,
                null,
                null,
                null,
                null
            ];

            var todebtable = $('#totaldebts').DataTable({
                "scrollX": true,
                "aoColumns": tdcolumns,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.debtors_total') }}_" + date,
                        title: "{{ trans('navmenu.debtors_total') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.debtors_total') }}_" + date,
                        title: shop_name + " \n {{ trans('navmenu.debtors_total') }} \n"+duration
                    }
                ],
            });    
            todebtable.buttons().container().appendTo('#totaldebts_wrapper .col-md-6:eq(1)');

        });
    </script>
@endsection