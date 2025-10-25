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
                    <div class="p-2 rounded">
                        <form class="form" method="post" action="{{ route('permissions.store') }}" validate>
                            {{csrf_field()}}
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Feature <span style="color: red;"></span></label>
                                    <select name="feature_id" class="form-select form-select-sm mb-1" required>
                                        <option value="">Select Feature</option>
                                        @foreach($features as $f)
                                        <option value="{{$f->id}}">{{$f->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label" for="userinput8">Permission Name</label>
                                    <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name" placeholder="Enter Permission Name" id="userinput8" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label" for="userinput8">Permission Display Name</label>
                                    <input class="form-control form-control-sm mb-1 border-primary" type="text" name="display_name" placeholder="Enter Permission Display Name" id="userinput8" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="userinput8">Permission Description</label>
                                    <textarea name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="Enter permission description"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-check2"></i> Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table id="permissions" class="table table-striped" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>Name</th>
                                <!-- <th>Gaurd</th> -->
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $key => $permission)
                            <tr>
                                <td>{{ $key+1  }}</td>
                                <td>{{ $permission->display_name }}</td>
                                <!-- <td>{{ $permission->guard_name }} </td> -->
                                <td>{{ $permission->description }}</td>
                                <td>
                                    <a  href="{{  route('permissions.edit', encrypt($permission->id)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a> |
                                    <a href="{{ url('admin/permissions/destroy', encrypt($permission->id)) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div>
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
            $('#permissions').DataTable({
                "scrollX": true,
            });
        });
    </script>
@endsection
