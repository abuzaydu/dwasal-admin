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
                            <div class="table-responsive">
                                <table id="reset-reqs" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>Phone number</th>
                                            <th>Reset Code</th>
                                            <td>Expired</td>
                                            <th>Requested At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($passcodes as $key => $ucode)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $ucode->phone }} </td>
                                            <td>{{ $ucode->code }} </td>
                                            <td>{{ $ucode->is_expired }} </td>
                                            <td>{{ $ucode->created_at }} </td>
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
            $('#reset-reqs').DataTable({
                "scrollX": true                
            });    
        });
    </script>
@endsection