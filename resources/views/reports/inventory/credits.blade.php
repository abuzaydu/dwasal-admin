@extends('layouts.gen')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
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
                <form class="dashform row g-1" action="{{url('supplier-credit-reports')}}" method="POST">
                    @csrf
                    <div class="col-md-6"></div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-6 text-right">
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
                    <div class="row">
                        <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#creditors-inv" role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list-minus font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.creditors')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#creditors-total" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.creditors_total')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ url('supplier-aging-report')}}" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                                        </div>
                                        <div class="tab-title">{{trans('navmenu.creditors-aging-report')}}</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="creditors-inv" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="creditors" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>{{trans('navmenu.purchase_date')}}</th>
                                            <th>{{trans('navmenu.supplier')}}</th>
                                            <th>{{trans('navmenu.invoice_no')}}</th>
                                            <th>{{trans('navmenu.amount')}}</th>
                                            <th>{{trans('navmenu.amount_paid')}}</th>
                                            <th>{{trans('navmenu.unpaid')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <<?php $tp_amount = 0; $tp_amount_paid = 0; ?>
                                        @foreach($purchases as $index => $purchase)
                                        <<?php 
                                            $tp_amount += $purchase->total_amount;
                                            $tp_amount_paid += $purchase->amount_paid;
                                        ?>
                                        <tr>
                                            <td>{{date('d-m-Y', strtotime($purchase->time_created))}}</td>
                                            <td><a href="{{ route('purchases.show', encrypt($purchase->id))}}">{{$purchase->name}}</a></td>
                                            <td>{{ sprintf('%04d', $purchase->invoice_no)}}</td>
                                            <td>{{number_format($purchase->total_amount,2,'.', ',')}}</td>
                                            <td>{{number_format($purchase->amount_paid,2,'.', ',')}}</td>
                                            <td>{{number_format($purchase->total_amount-$purchase->amount_paid,2,'.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th><b>{{trans('navmenu.total')}}</b></th>
                                            <th></th>
                                            <th><b>{{number_format($tp_amount, 2,'.',',')}}</b></th>
                                            <th><b>{{number_format($tp_amount_paid, 2,'.',',')}}</b></th>
                                            <th><b>{{number_format($tp_amount-$tp_amount_paid, 2,'.',',')}}</b></th>
                                        </tr>
                                    </tfoot>
                              </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <div class="tab-pane fade" id="creditors-total" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="totalcredits" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.supplier_name')}}</th>
                                            <th>{{trans('navmenu.contact_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.opening_balance')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.new_invoices')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($totalsupdebts as $index => $debt)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td><a href="{{ route('suppliers.show', encrypt($debt['supplier_id'])) }}">{{$debt['name']}}</a></td>
                                            <td>{{$debt['contact_no']}}</td>
                                            <td style="text-align: center;">{{number_format($debt['opening_balance'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($debt['new_invoices'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($debt['total'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total_sup_ob,2, '.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_sup_invoices,2, '.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_sup_ob+$total_sup_invoices,2, '.', ',')}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.col -->
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

            var columns = [{
                    "sType": "date-uk"
                },
                null,
                null,
                null,
                null,
                null
            ];

            var creditable = $('#creditors').DataTable({
                "scrollX": true,
                "aoColumns": columns,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.credit_report') }}_" + date,
                        title: "{{ trans('navmenu.credit_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.credit_report') }}_" + date,
                        title: shop_name + " \n {{ trans('navmenu.credit_report') }} \n"+duration
                    }
                ],
            });    
            creditable.buttons().container().appendTo('#creditors_wrapper .col-md-6:eq(1)');

            var tdcolumns = [
                null,
                null,
                null,
                null,
                null,
                null
            ];

            var tocreditable = $('#totalcredits').DataTable({
                "scrollX": true,
                "aoColumns": tdcolumns,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.creditors_total') }}_" + date,
                        title: "{{ trans('navmenu.creditors_total') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.creditors_total') }}_" + date,
                        title: shop_name + " \n {{ trans('navmenu.creditors_total') }} \n"+duration
                    }
                ],
            });    
            tocreditable.buttons().container().appendTo('#totalcredits_wrapper .col-md-6:eq(1)');

        })
    </script>
@endsection
