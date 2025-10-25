@extends('layouts.prof')

<script>
    function allowAccess(id) {
        Swal.fire({
            text: "Are you sure you want to Grant Access for this user to the select shop?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes, Grant it",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('shop_id').value = id;
                document.getElementById('assign-b-form').submit();
                Swal.fire(
                    "Access Granted",
                    "Granted",
                    'success'
                )
            }
        })
    }

    function removeAccess(id) {
        Swal.fire({
            text: "Are you sure you want to remove Access for this user from the select shop?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "Yes Remove",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('shop-id').value = id;
                document.getElementById('detach-b-form').submit();
                Swal.fire(
                    "Access Removed",
                    "Removed",
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
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Account & Settings</li>  
                    <li class="breadcrumb-item"><a href="{{ url('users-and-roles') }}">Users & Roles</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix mt-3">
        <div class="col-md-12">
            <div class="card radius-10">
                <div class="card-body row">
                    <div class="col-md-4 print_invoice">
                        <table class="table table-striped" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        @if(!is_null($user->user_photo))
                                        <img src="{{asset('storage/'.$user->user_photo)}}" alt="" class="rounded-circle p-1 bg-primary" height="100" width="100">
                                        @else
                                        <img src="{{ asset('assets/img/user.jpg') }}" alt="" class="rounded-circle p-1 bg-primary" width="110">
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-striped" style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td>{{ trans('navmenu.name') }}</td>
                                    <td><b>{{ $user->first_name }} {{ $user->last_name }}</b></td>
                                    <td>{{ trans('navmenu.date_registered') }}</td> 
                                    <td><b>{{ $user->created_at->toDayDateTimeString() }}</b></td>
                                </tr>
                                <tr>
                                    <td>{{ trans('navmenu.mobile') }}</td> 
                                    <td><b>{{ $user->phone }}</b></td>
                                    <td>{{ trans('navmenu.email') }}</td> 
                                    <td><b>{{ $user->email }}</b></td>
                                </tr>
                                <tr>
                                    <td>{{ trans('navmenu.user_role') }}</td>
                                    <td><b>@if(!is_null($urole)){{ $urole->display_name }}@else User has No Role in This Shop/Store @endif</b></td>
                                </tr>
                            </tbody>
                        </table>
                        @if($user->id != Auth::user()->id)
                            @if(Auth::user()->can('edit-user'))
                            <form class="mt-0" action="{{ url('change-user-role') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <label for="shop_id" class="form-label">{{ trans('navmenu.change_user_role') }}</label>
                                <select name="role" class="form-select form-select-sm mb-1" required onchange='this.form.submit()'>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                    @if(!is_null($urole) && $urole->id == $role->id)
                                    <option value="{{ $role->id }}" selected>{{ $role->display_name }}</option>
                                    @else
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </form>
                            @endif
                        @endif
                    </div>
                    <div class="col-md-12 print_invoice">
                        <h6>Stores/Shops User can Access</h6>
                        <div class="table-responsive">
                            <table class="table table-striped display nowrap" style="width: 100%; white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.business_name')}}</th>
                                        <th style="text-align: center;">Is Access Granted</th>
                                        <th style="text-align: center;">Is User's Default?</th>
                                        @if(Auth::user()->can('edit-user'))
                                        <th style="text-align: center;">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usershops as $key => $shop)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td><b>{{ $shop['name'] }}</b><br>
                                        <small>({{ $shop['company'] }})</small></td>
                                        <td style="text-align: center;">@if($shop['access']) Yes @else No @endif</td>
                                        <td style="text-align: center;">@if($shop['is_default']) Yes @else No @endif</td>
                                        @if(Auth::user()->can('edit-user'))
                                        <td>
                                            @if($user->id != Auth::user()->id)
                                            @if($shop['access'])
                                            <a href="javascript:;" onclick="removeAccess({{$shop['id']}})" class="text-danger"><i class="fa fa-x-circle"></i> Remove Access</a>
                                            @else
                                            <a href="javascript:;" onclick="allowAccess({{$shop['id']}})" class="text-success"><i class="fa fa-check"></i> Grant Access</a>
                                            @endif
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <form id="assign-b-form" action="{{ url('assign-business') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <input type="hidden" name="shop_id" id="shop_id">
                        </form>

                        <form id="detach-b-form" action="{{ url('detach-business') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <input type="hidden" name="shop_id" id="shop-id">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-5">
            <div class="main-body">
                <div class="row">
                    <div class="row">
                        <div class="d-lg-flex align-items-center mb-4 gap-3">
                            <div class="position-relative">
                                <h6 class="mb-0 text-uppercase" id="list-title">{{ trans('navmenu.user_permissions') }} (@if(!is_null($user_permissions)){{$user_permissions->count()}}@else 0 @endif)
                                </h6>
                            </div>
                        </div>
                        <hr>
                        <div class="row g-1">
                            @if (!empty($user_permissions))
                                @foreach ($user_permissions as $v)
                                    <div class="col-md-3" tabindex="1">
                                        <div
                                            class="d-flex align-items-center theme-icons shadow-sm p-2 cursor-pointer rounded">
                                            <div class="font-22 text-primary"> <i class="fadeIn animated fa fa-pencil"></i>
                                            </div>
                                            <div class="ms-2">{{ $v->display_name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
