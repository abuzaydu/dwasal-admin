@extends('layouts.adm')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"/>
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

                    @include('admin.users.nav')

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_1-0" role="tabpanel">
                            <div class="table-responsive pt-2">
                                <table id="staffs" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>FirstName</th>
                                            <th>LastName</th>
                                            <th>Mobile Phone</th>
                                            <th>Email</th>
                                            <th>Country Code</th>
                                            <th>Dial Code</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($staffs as $key => $staff)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $staff->first_name }}</td>
                                                <td>{{ $staff->last_name }} </td>
                                                <td>{{ $staff->phone }}</td>
                                                <td>{{ $staff->email }} </td>
                                                <td style="text-align: center;">{{ $staff->country_code }} </td>
                                                <td style="text-align: center;">{{ $staff->dial_code }} </td>
                                                <td>
                                                    <a href="{{ route('users.edit', $staff->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a> |
                                                        <a href="{{ url('admin/payments/destroy', ['id' => $staff->id]) }}"
                                                            onclick="return confirm('Are you sure you want to delete this record')">
                                                            <i class="fa fa-trash"></i>
                                                    </a>
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
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
            $('#staffs').DataTable({
                "scrollX": true,
            });
        });
    </script>
@endsection
