@extends('layouts.adm')
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
                    <li class="breadcrumb-item">Service Payments</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form class="dashform row g-1" action="{{ url('admin/activations-once') }}" method="POST">
                    @csrf
                    <div class="col-md-5">
                       
                    </div>
                    <!-- Date and time range -->
                    <div class="col-md-7">
                        <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                        <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                        <!-- Date and time range -->
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
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
                    <ul class="nav nav-tabs nav-tabs-new2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/payments') }}" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">All Payment Transactions</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/activated-payments') }}" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">All Activated Payments</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/agent-activations') }}" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-minus font-18 me-1'></i></div>
                                    <div class="tab-title">Activations By Agent</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="{{ url('/admin/activations-once') }}" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-ul font-18 me-1'></i></div>
                                    <div class="tab-title">Activated At Least Once</div>
                                </div>
                            </a>
                        </li>
                        
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_1-2" role="tabpanel">
                            <div class="table-responsive">
                                <table id="activation-once" class="table table-striped display nowrap"
                                    style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>Business Name</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Mobile</th>
                                            <th>Pay number</th>
                                            <th>TXN ID</th>
                                            <th>Code</th>
                                            <th>Amount paid</th>
                                            <th>Period</th>
                                            <th>Created At</th>
                                            <th>Expire date</th>
                                            <th>Is expired?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($shops as $key => $payment)
                                        <tr>
                                            <td> {{ $key + 1 }}</td>
                                            <td>{{ $payment->shopname }} </td>
                                            <td> {{ $payment->first_name }}</td>
                                            <td> {{ $payment->last_name }} </td>
                                            <td> {{ $payment->phone }} </td>
                                            <td>{{ $payment->phone_number }}</td>
                                            <td>{{ $payment->transaction_id }} </td>
                                            <td>{{ $payment->code }}</td>
                                            <td>{{ $payment->amount_paid }} </td>
                                            <td>{{ $payment->period }} </td>
                                            <td>{{ $payment->created_at }} </td>
                                            <td>{{ $payment->expire_date }} </td>
                                            <td>{{ $payment->is_expired }} </td>
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
    <!--end row-->
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
            var aptable = $('#activation-once').DataTable({
                "scrollX": true,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Activation At Least Once_" + date,
                        title: "Activation At Least Once",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "Activation At Least Once_" + date,
                        title: "Activation At Least Once \n"+duration
                    }
                ],
            });    
            aptable.buttons().container().appendTo('#activation-once_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection
