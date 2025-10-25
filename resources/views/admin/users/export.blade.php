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
                    <li class="breadcrumb-item">Accounts & Users</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <a class="btn btn-primary btn-sm" href="{{ url('/admin/shops') }}">Registered Shops</a>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{ url('admin/export-shops') }}" method="get">
                    @csrf
                    <div class="col-md-6">
                        <input class="form-control form-control-sm mb-1" account="text" name="search_key" placeholder="Enter Search key" id="userinput8" required>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-6 mb-1">
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
                    <div class="tab-content pt-3">
                        <!-- /.tab-pane -->
                        <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                            <table id="example" class="table table-striped display nowrap" style="width: 100%;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th style="width: 10px;">#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Mobile</th>
                                        <th>Shop Name</th>
                                        <th>Date registered</th>
                                        <th>Is Default?</th>
                                        <th>Expire Date</th>
                                        <th>Is Expired?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usershops as $key => $shop)
                                    <tr>
                                        <td>{{ $key+1  }}</td>
                                        <td>{{ $shop->first_name }}</td> 
                                        <td>{{ $shop->last_name}} </td>
                                        <td>{{ $shop->phone }}</td>
                                        <td>{{ $shop->name }} </td>
                                        <td>{{ $shop->created_at}} </td>
                                        <td>{{ $shop->is_default}}</td>
                                        <td>{{ $shop->expire_date }}</td>
                                        <td>{{ $shop->is_expired}}</td>
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
            var aptable = $('#example').DataTable({
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
            aptable.buttons().container().appendTo('#example_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection
