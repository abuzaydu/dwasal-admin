@extends('layouts.adm')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"/>
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Accounts & Users</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 text-right pt-0">
                <a class="btn btn-success btn-sm" href="{{ url('/admin/export-shops') }}">Shops With Default users</a>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 pt-0">
                <form class="dashform row g-3" action="{{ url('admin/shops') }}" method="get">
                    @csrf
                    <div class="col-md-6">
                        <select name="company_id" class="form-select form-select-sm select2 mb-1" onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach($companies as $company)
                            @if($currcoid == $company->id)
                            <option value="{{$company->id}}" selected>{{$company->id}} - {{$company->name}}</option>
                            @else
                            <option value="{{$company->id}}">{{$company->id}} - {{$company->name}}</option>
                            @endif
                            @endforeach
                        </select>
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
                        <div class="tab-pane fade show active" id="tab_1-0" role="tabpanel">
                            <div class="table-responsive">
                                <table id="reg-shops" class="table table-striped display nowrap"
                                    style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th>#</th>
                                            <th>Shop ID</th>
                                            <th>Name</th>
                                            <th>Company</th>
                                            <th>Address</th>
                                            <th>City/Town</th>
                                            <th>Date registered</th>
                                            <th>Subscription</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($shops as $key => $shop)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td style="text-align: center;">{{ $shop->id }}</td>
                                            <td>{{ $shop->name }}</td>
                                            <td>{{ $shop->company }}</td>
                                            <td>{{ $shop->street }} </td>
                                            <td>{{ $shop->city }} </td>
                                            <td>{{ $shop->created_at }} </td>
                                            <td>{{ $shop->title }}</td>
                                            <td>
                                                @if($shop->is_warehouse)
                                                <a href="{{ url('admin/update-shop-detail/' .encrypt($shop->id)) }}" title=" Chage Subscription type" style="font-size: 14px;"><i class="fa fa-edit"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.tab-pane -->
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
            var aptable = $('#reg-shops').DataTable({
                "scrollX": true,
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Registered Shops_" + date,
                        title: "Registered Shops",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "Registered Shops_" + date,
                        title: "Registered Shops \n"+duration
                    }
                ],
            });    
            aptable.buttons().container().appendTo('#reg-shops_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection