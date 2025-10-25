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
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <form class="dashform row g-1" action="{{ url('admin/f-service-payments') }}" method="POST">
                    @csrf
                    <div class="col-md-6">
                        <div class="input-group">
                            <input class="typeahead form-control form-control-sm me-1" type="text" style="width: 45px;" name="search_key" placeholder="Search here . . .">
                            <button class="btn btn-outline-primary btn-sm" type="submit" id="button-addon2">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Date and time range -->
                    <div class="col-md-6">
                        <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                        <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                        <!-- Date and time range -->
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
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
                            <a class="nav-link active" href="#tab_1-0" role="tab"
                                aria-selected="true">
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
                        <div class="tab-pane fade show active" id="tab_1-4" role="tabpanel">
                            <div class="px-2 pt-3 rounded">
                                <form class="form row needs-validation" method="post"
                                    action="{{ route('payments.store') }}" validate>
                                    {{ csrf_field() }}
                                    <input type="hidden" name="api_key" value="WtUCp2KDdPNzcnCPjHhtJAxYDZl3NVuu">
                                    <div class="col-md-3">
                                        <input class="form-control form-control-sm mb-3" type="text"
                                            placeholder="Enter Sender's Phone number" id="userinput6" name="phone_number"
                                            required>
                                    </div>
                                    <div class="col-md-2">
                                        <input class="form-control form-control-sm mb-3" type="number"
                                            name="amount_paid" id="userinput5" placeholder="Enter amount paid" required>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="is_real" class="form-select form-select-sm mb-3">
                                                <option value="0">Dummy</option>
                                                <option value="1">Real</option>
                                            </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary btn-sm">Create</button>
                                        <!-- <a href="{{ url('admin/payments') }}" class="btn btn-warning btn-sm">Cancel</a> -->
                                    </div>
                                    <div class="col-md-3">
                                        <a target="_blank" href="{{ url('admin/activate-shop')}}" class="btn btn-outline-success">Activate Shops</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade show active" id="tab_1-0" role="tabpanel">
                            <div class="table-responsive">
                                <table id="all-trans" class="table table-striped display nowrap"
                                    style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>Phone number</th>
                                            <th>Reference</th>
                                            <th>Code</th>
                                            <th>Amount paid</th>
                                            <th>Period</th>
                                            <th>Created At</th>
                                            <th>Activated At</th>
                                            <th>Status</th>
                                            <th>Expire date</th>
                                            <th>Is expired?</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $key => $payment)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $payment->phone_number }}</td>
                                            <td>{{ $payment->reference }} </td>
                                            <td>{{ $payment->code }}</td>
                                            <td>{{ $payment->amount_paid }} </td>
                                            <td>{{ $payment->period }} </td>
                                            <td>{{ $payment->created_at }} </td>
                                            <td> {{ $payment->activation_time }} </td>
                                            <td> {{ $payment->status }} </td>
                                            <td>{{ $payment->expire_date }} </td>
                                            <td style="text-align: center;">{{ $payment->is_expired }} </td>
                                            <td style="text-align: center;">
                                                <a href="{{ route('payments.edit', encrypt($payment->id)) }}">
                                                    <i class="fa fa-edit"></i>
                                                </a> 
                                                <!-- | -->
                                                <!-- <a href="{{ url('admin/payments/destroy', ['id' => $payment->id]) }}" onclick="return confirm('Are you sure you want to delete this record')"> -->
                                                    <!-- <i class="fa fa-trash"></i> -->
                                                <!-- </a> -->
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
            var aptable = $('#all-trans').DataTable({
                "scrollX": true,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "All Transactions_" + date,
                        title: "All Transactions",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "All Transactions_" + date,
                        title: "All Transactions \n"+duration
                    }
                ],
            });    
            aptable.buttons().container().appendTo('#all-trans_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection
