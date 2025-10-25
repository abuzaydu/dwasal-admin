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
                <form class="dashform row g-3" action="{{ url('admin/users') }}" method="get">
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

                    @include('admin.users.nav')

                    <div class="tab-content pt-3">
                        <div class="tab-pane fade show active" id="tab_1-0" role="tabpanel">
                            <div class="table-responsive">
                                <table id="reg-users" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>FirstName</th>
                                            <th>LastName</th>
                                            <th>Mobile Phone</th>
                                            <th>Email</th>
                                            <th>Country Code</th>
                                            <th>Dial Code</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users->chunk(200) as $user)
                                            @foreach ($user as $key => $detail)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $detail->first_name }}</td>
                                                    <td>{{ $detail->last_name }} </td>
                                                    <td>{{ $detail->phone }}</td>
                                                    <td>{{ $detail->email }} </td>
                                                    <td style="text-align: center;">{{ $detail->country_code }} </td>
                                                    <td style="text-align: center;">{{ $detail->dial_code }} </td>
                                                    <td>
                                                        <a  href="{{  route('users.edit', encrypt($detail->id)) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a> |
                                                        <a href="{{ url('admin/users/destroy', encrypt($detail->id)) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
                                                            <i class="fa fa-trash text-danger"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <H5 class="mb-md-0 p-2 text-uppercase">REGISTER NEW USER</H5>
                        <form class="form" method="post" id="new-user" action="{{ route('users.store') }}" validate>
                            {{ csrf_field() }}
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">First Name<span
                                                style="margin-left: 5px; color: red;">
                                                *</span></label>
                                        <input class="form-control form-control-sm border-primary" type="text" name="first_name"
                                            placeholder="Enter First Name" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Last Name<span
                                                style="margin-left: 5px; color: red;">
                                                *</span></label>
                                        <input class="form-control form-control-sm border-primary" type="text" name="last_name"
                                            placeholder="Enter Last Name" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Phone Number<span
                                                style="margin-left: 5px; color: red;"> *</span></label>
                                        <input class="form-control form-control-sm border-primary" type="text" name="phone"
                                            placeholder="Enter Phone Number" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Email Address<span
                                                style="margin-left: 5px; color: red;"> *</span></label>
                                        <input class="form-control form-control-sm border-primary" type="email" name="email"
                                            placeholder="Enter Email address" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Password<span
                                                style="margin-left: 5px; color: red;">
                                                *</span></label>
                                        <input class="form-control form-control-sm border-primary" type="password" name="password"
                                            placeholder="Enter Password" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Confirm Password<span
                                                style="margin-left: 5px; color: red;"> *</span></label>
                                        <input class="form-control form-control-sm border-primary" type="password"
                                            name="confirm_password" placeholder="Re-enterPassword" id="userinput8"
                                            required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">User Role<span
                                                style="margin-left: 5px; color: red;">
                                                *</span></label>
                                        <select name="role" class="form-control form-control-sm">
                                            <option>Choose a Role</option>
                                            @foreach ($roles as $role)
                                                <option>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 form-group mt-2">
                                        <a href="{{ url('admin/users') }}" type="button" class="btn btn-warning mx-2">
                                            <i class="icon-cross2"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
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
            var aptable = $('#reg-users').DataTable({
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
            aptable.buttons().container().appendTo('#reg-users_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection