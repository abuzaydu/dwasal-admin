@extends('layouts.adm')

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
                        <form class="form row g-1" method="post" action="{{ route('roles.store') }}" validate>
                            {{csrf_field()}}
                            <div class="col-md-2">
                                <label class="form-label" for="userinput8">Role Name</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name" placeholder="Enter Role Name" id="userinput8" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="userinput8">Role Display Name</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="display_name" placeholder="Enter Role Display Name" id="userinput8" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="userinput8">Role Description</label>
                                <textarea name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="Enter role description"></textarea>
                            </div>
                            <div class="col-md-1 pt-4">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="icon-check2"></i> Save</button>
                            </div>
                        </form>
                    </div>
                    <table id="example1" class="table table-striped" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>Name</th>
                                <th>Gaurd</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $key => $role)
                            <tr>
                                <td>{{ $key+1  }}</td>
                                <td>{{ $role->display_name }}</td>
                                <td>{{ $role->guard_name }} </td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    <a  href="{{ route('roles.edit',encrypt($role->id)) }}">
                                        <i class="fa fa-edit"></i>
                                    </a> |
                                    <a href="{{ url('admin/roles/destroy', encrypt($role->id)) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
                                        <i class="fa fa-trash text-danger"></i>
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
@endsection

