@extends('layouts.prof')
<script>
    function confirmDelete(id) {
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
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                 <div class="card-body">
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="userinfo" role="tabpanel">
                            <div class="p-4 border rounded row">
                                <div class="col-md-4 d-flex flex-column align-items-center text-center">
                                    @if(!is_null(Auth::user()->user_photo))
                                    <img src="{{asset('storage/'.Auth::user()->user_photo)}}" alt="" class="rounded-circle p-1 bg-primary" height="100" width="100">
                                    @else
                                    <img src="{{ asset('assets/img/user.jpg') }}" alt="" class="rounded-circle p-1 bg-primary" width="110">
                                    @endif
                                    <div class="mt-3">
                                        <h6>{{Auth::user()->name}}</h6>
                                        <p class="text-secondary mb-1">{{Auth::user()->roles[0]['display_name']}}</p>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"></li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <span class="mb-0">Mobile</span>
                                            <span class="text-secondary">{{Auth::user()->phone}}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <span class="mb-0">E-Mail</span>
                                            <span class="text-secondary">{{Auth::user()->email}}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                            <span class="mb-0">Shops</span>
                                            <span class="text-secondary">{{Auth::user()->shops()->count()}}</span>
                                        </li>
                                        <li class="list-group-item"></li>
                                    </ul>
                                </div>
                                
                                @if(Auth::user()->can('edit-user'))
                                <div class="col-md-4 pt-2">
                                    <a  href="{{route('user-profile.edit', encrypt(Auth::user()->id))}}" class="btn btn-primary btn-sm col-12"><i class="fa fa-edit"></i><b> {{trans('navmenu.edit_profile')}}</b></a>
                                </div>
                                <div class="col-md-4 pt-2">
                                    <a href="{{route('user-profile.show', encrypt(Auth::user()->id))}}" class="btn btn-secondary btn-sm col-12"><i class="fa fa-edit"></i> View More Details</a>
                                </div>
                                @endif
                                <div class="col-md-4 pt-2">
                                    <a href="{{url('change-password')}}" class="btn btn-warning btn-sm col-12"><i class="fa fa-key"></i><b> {{trans('navmenu.change_password')}}</b></a>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </div>
@endsection