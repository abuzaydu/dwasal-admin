@extends('layouts.app')

@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('api-pay-transactions')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-12 mb-1">
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

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="all-collections" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="sale-transs" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.created_at')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">APC ID</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">Reference</th>
                                            <th style="text-align: center; text-transform: uppercase;">Msisdn</th>   
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_name')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">TXN Status</th>
                                            <th style="text-align: center; text-transform: uppercase;">Reference 2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($transactions as $trans)
                                        <tr>
                                            <td style="text-align: center;">{{date('d-m-Y H:i:s A', strtotime($trans->created_at))}}</td>
                                            <td style="text-align: center;">
                                                {{ $trans->reference1 }}</td>
                                            <td style="text-align: center;">{{number_format($trans->amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{$trans->reference}}</td>
                                            <td style="text-align: center;">{{ $trans->customer_msisdn }}</td>
                                            <td style="text-align: center;">{{$trans->customer_name}}</td>
                                            <td style="text-align: center;">{{$trans->trans_stat}}</td>
                                            <td style="text-align: center;">
                                                {{$trans->reference2}}
                                            </td>
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

            var collectable = $('#sale-transs').DataTable({
                "scrollX": true,
                "order": [
                    [5, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "API Payment Transactions",
                        title: " API Payment Transactions",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        orientation: 'landscape',
                        filename: "API Payment Transactions",
                        title: shop_name + "\n API Payment Transactions \n"+duration
                    }
                ],
            });
            collectable.buttons().container().appendTo('#sale-transs_wrapper .col-md-6:eq(1)');
        })
    </script>
@endsection