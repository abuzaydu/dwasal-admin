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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('collections-report')}}" method="POST">
                    @csrf
                    <div class="col-md-5">
                        <select name="customer_id" class="form-select form-select-sm mb-1 select2">
                            @foreach($customers as $cust)
                            @if(!is_null($customer) && $customer->id == $cust->id)
                            <option value="{{$cust->id}}" selected>{{$cust->name}}</option>
                            @else
                            <option value="{{$cust->id}}">{{$cust->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-7 mb-3">
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
                            <a class="nav-link active" data-bs-toggle="tab" href="#all-collections" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.collections_report')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#debts-collect" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.debt_collections_report')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="all-collections" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="collections-report" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_id')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_name')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.payment_mode')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date_of_pay')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.receipt_no')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.sale_type')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($collections as $payment)
                                        <?php  $total += $payment->amount; ?>
                                        <tr>
                                            <td style="text-align: center;">{{sprintf('%03d', $payment->cust_no)}}</td>
                                            <td style="text-align: center;">{{$payment->name}}</td>
                                            <td style="text-align: center;">{{$payment->pay_mode}}</td>
                                            <td style="text-align: center;">{{$payment->pay_date}}</td>
                                            <td style="text-align: center;">{{sprintf('%05d', $payment->receipt_no)}}</td>
                                            <td style="text-align: center;">{{$payment->sale_type}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total, 2, '.', ',')}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="debts-collect" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="debt-collections-report" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_id')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_name')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.payment_mode')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date_of_pay')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.receipt_no')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.sale_type')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($debt_collections as $payment)
                                        <?php $total += $payment->amount; ?>
                                        <tr>
                                            <td style="text-align: center;">{{sprintf('%03d', $payment->cust_id)}}</td>
                                            <td style="text-align: center;">{{$payment->name}}</td>
                                            <td style="text-align: center;">{{$payment->pay_mode}}</td>
                                            <td style="text-align: center;">{{$payment->pay_date}}</td>
                                            <td style="text-align: center;">{{sprintf('%05d', $payment->receipt_no)}}</td>
                                            <td style="text-align: center;">{{$payment->sale_type}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total, 2, '.', ',')}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
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
            var collectable = $('#collections-report').DataTable({
                "scrollX": true,
                "order": [
                    [5, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.collections_report') }}_" + date,
                        title: " {{ trans('navmenu.collections_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        orientation: 'landscape',
                        filename: "{{ trans('navmenu.collections_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.collections_report') }} \n"+duration
                    }
                ],
            });
            collectable.buttons().container().appendTo('#collections-report_wrapper .col-md-6:eq(1)');

            var debcollectable = $('#debt-collections-report').DataTable({
                "scrollX": true,
                "order": [
                    [5, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.debt_collections_report') }}_" + date,
                        title: " {{ trans('navmenu.debt_collections_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        orientation: 'landscape',
                        filename: "{{ trans('navmenu.debt_collections_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.debt_collections_report') }} \n"+ duration
                    }
                ],
            });
            debcollectable.buttons().container().appendTo('#debt-collections-report_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection