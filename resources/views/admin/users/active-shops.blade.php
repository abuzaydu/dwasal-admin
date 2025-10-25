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
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="active-shops" class="table table-striped display nowrap" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Mobile</th>
                                    <th>Business Name</th>
                                    <th>Date registered</th>
                                    <th>Is Default?</th>
                                    <th>Expire Date</th>
                                    <!-- <th>Is Expired?</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shops as $key => $shop)
                                <tr>
                                    <td>{{ $key+1  }}</td>
                                    <td>{{ $shop->first_name }}</td> 
                                    <td>{{$shop->last_name}} </td>
                                    <td>{{ $shop->phone }}</td>
                                    <td>{{ $shop->name }} </td>
                                    <td>{{ $shop->created_at}} </td>
                                    <td>{{ $shop->is_default}}</td>
                                    <td>{{ $shop->expire_date }}</td>
                                    <!-- <td>{{ $shop->is_expired}}</td> -->
                                </tr>
                                @endforeach
                            </tbody>
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
            var duration = "<?php echo $duration; ?>";
            var aptable = $('#active-shops').DataTable({
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
            aptable.buttons().container().appendTo('#active-shops_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection
