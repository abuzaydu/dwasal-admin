@extends('layouts.adm')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Service Payments</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <form class="dashform row g-3" action="{{ url('admin/agent-activations') }}" method="get">
                @csrf
                
            </form>
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
                            <a class="nav-link active" href="{{ url('/admin/agent-activations') }}" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-minus font-18 me-1'></i></div>
                                    <div class="tab-title">Activations By Agent</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/activations-once') }}" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-ul font-18 me-1'></i></div>
                                    <div class="tab-title">Activated At Least Once</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_1-3" role="tabpanel">
                            <div class="table-responsive">
                                <table id="agent-activations" class="table table-striped display nowrap"
                                    style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>Agent</th>
                                            <th>Agent Code</th>
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
                                        @foreach ($paybyagents as $key => $payment)
                                            <tr>
                                                <td> {{ $key + 1 }}</td>
                                                <td>{{ App\User::find($payment->agent_id)->first_name }}</td>
                                                <td>{{ $payment->agent_code }}</td>
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
            var aptable = $('#agent-activations').DataTable({
                "scrollX": true,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Agents Activations_" + date,
                        title: "Agents Activations",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "Agents Activations_" + date,
                        title: "Agents Activations \n"+duration
                    }
                ],
            });    
            aptable.buttons().container().appendTo('#agent-activations_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection
