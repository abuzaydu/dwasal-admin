@extends('layouts.prof')
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "Are you sure you want to Deactivate this user?",
          text: "The user will no longer access the system",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Deactivate",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "Deactivated",
              "Deactivated",
              'success'
            )
          }
        })
    }

    function confirmDeletePermanently(id) {
        Swal.fire({
          title: "Are you sure you want to Delete this user?",
          text: "The user will no longer access the system",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Deactivate",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('remove-user') }}/"+id
            Swal.fire(
              "Removed",
              "Removed",
              'success'
            )
          }
        })
    }

    function confirmActivate(id) {
        Swal.fire({
          title: "Are you sure you want to activate this user",
          text: "This will allow this user to access the granted shop access",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Activate",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('activate-user')}}/"+id;
            Swal.fire(
              "Activated",
              "Activated",
              'success'
            )
          }
        })
    }

    function confirmDeleteRole(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-role-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmShopDelete(id){
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-shop-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Account & Settings</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form method="POST" action="{{ url('users-and-roles')}}">
                    @csrf
                    <select name="company_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                        @foreach(Auth::user()->companies()->get() as $c)
                        @if($c->id == $company->id)
                        <option value="{{$c->id}}" selected>{{$c->name}}</option>
                        @else
                        <option value="{{$c->id}}">{{$c->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                 <div class="card-body">
                    <div class="d-flex align-items-end  px-1 py-1">
                        <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist"  >
                            @if(Auth::user()->can('view-user'))
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-toggle="tab" href="#bizusers" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-group font-18 me-1'></i></div>
                                        <div class="tab-title"> Active {{trans('navmenu.business_users')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-toggle="tab" href="#roles" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list-ul font-18 me-1'></i></div>
                                        <div class="tab-title">Roles</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-toggle="tab" href="#in-active-users" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa  fa-user-md font-18 me-1'></i></div>
                                        <div class="tab-title"> Inactive {{trans('navmenu.business_users')}}</div>
                                    </div>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="bizusers" role="tabpanel">
                            <!-- <div class="d-flex justify-content-end mb-3"> -->
                                <a href="{{route('user-profile.create')}}" class="btn btn-success btn-sm"><i class="fa fa-user-plus"></i>{{trans('navmenu.new-user')}}</a>
                            <!-- </div> -->
                            <div class="table-responsive">
                                <table id="shop-users" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.name')}}</th>
                                            <th>{{trans('navmenu.mobile')}}</th>
                                            <th>Role</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $key => $user)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            @if($user->id === Auth::user()->id)
                                            <td>{{$user->first_name}} {{ $user->last_name}}</td>
                                            @else
                                            <td><a href="{{route('user-profile.show', encrypt($user->id))}}">{{$user->first_name}} {{ $user->last_name}}</a></td>
                                            @endif
                                            <td>{{$user->phone}}</td>
                                            <td>
                                                @if($user->roles->count() > 0)
                                                {{$user->roles[0]['display_name']}}
                                                @endif
                                            </td>
                                            <td>{{$user->created_at}}</td>
                                            <td>
                                                <a href="{{route('user-profile.edit', encrypt($user->id))}}"><i class="fa fa-edit"> Edit</i></a> | 
                                                @if(!$user->can('delete-user'))
                                                <form id="delete-form-{{$key}}" method="POST" action="{{route('user-profile.destroy' , encrypt($user->id))}}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDelete({{$key}})" ><i class="fa fa-trash" style="color: red;"> De-Activate User</i></a>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="roles" role="tabpanel">
                            <!-- <div class="d-flex justify-content-end mb-3"> -->
                                <a href="{{route('company-roles.create')}}" class="btn btn-warning btn-sm"><i class="fa fa-plus"></i>Add New Role</a>
                            <!-- </div> -->
                            <div class="table-responsive">
                                <table id="shop-users" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.name')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($roles as $key => $role)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('company-roles.edit', encrypt($role->id))}}">{{$role->display_name}}</a></td>
                                            <td>{{$role->created_at}}</td>
                                            <td>
                                                @if(Auth::user()->can('delete-role'))
                                                <form id="delete-form-role-{{$key}}" method="POST" action="{{route('company-roles.destroy' , encrypt($role->id))}}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteRole({{$key}})" ><i class="fa fa-trash" style="color: red;">Delete</i></a>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="in-active-users" role="tabpanel">
                            <div class="table-responsive">
                                <table id="inactive-users" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.name')}}</th>
                                            <th>{{trans('navmenu.mobile')}}</th>
                                            <th>Role</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inactiveusers as $key => $user)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$user->first_name}} {{ $user->last_name}}</td>
                                            <td>{{$user->phone}}</td>
                                            <td>
                                                @if($user->roles->count() > 0)
                                                {{$user->roles[0]['display_name']}}
                                                @endif
                                            </td>
                                            <td>{{$user->created_at}}</td>
                                            <td>
                                                <a href="javascript:;" onclick="return confirmActivate('<?php echo encrypt($user->id); ?>')" ><i class="fa fa-check" style="color: green;"> Activate User</i></a>
                                                @if($user->shops()->count() == 0) | 
                                                <a href="javascript:;" onclick="return confirmDeletePermanently('<?php echo encrypt($user->id); ?>')" ><i class="fa fa-x-circle" style="color: red;"> Remove User</i></a>
                                                @endif
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