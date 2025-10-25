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
                    <div class="row">
                        <form class="form row g-1" method="post" id="new-user" action="{{ route('users.update', encrypt($user->id)) }}" validate>
                            @csrf
                            @method('PUT')
                            <div class="col-md-4">
                                <label class="control-label py-2">First Name<span style="margin-left: 5px; color: red;">*</span></label>
                                <input value="{{ $user->first_name }}" class="form-control form-control-sm border-primary" type="text" name="first_name" placeholder="Enter First Name" id="userinput8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label py-2">Last Name<span style="margin-left: 5px; color: red;">*</span></label>
                                <input value="{{ $user->last_name }}" class="form-control form-control-sm border-primary" type="text" name="last_name" placeholder="Enter Last Name" id="userinput8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label py-2">Phone Number<span style="margin-left: 5px; color: red;"> *</span></label>
                                <input value="{{ $user->phone }}" class="form-control form-control-sm border-primary" type="text" name="phone" placeholder="Enter Phone Number" id="userinput8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label py-2">Email Address<span style="margin-left: 5px; color: red;"> *</span></label>
                                <input value="{{ $user->email }}" class="form-control form-control-sm border-primary" type="email" name="email"  placeholder="Enter Email address" id="userinput8" required>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label py-2">User Role<span style="margin-left: 5px; color: red;">*</span></label>
                                <select name="role" class="form-select form-select-sm" required>
                                    <option value="">Choose a Role</option>
                                    @foreach ($roles as $role)
                                    @if(!is_null($urole) && $urole->id == $role->id)
                                    <option value="{{ $role->id }}" selected>{{ $role->display_name }}</option>
                                    @else
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endif
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
