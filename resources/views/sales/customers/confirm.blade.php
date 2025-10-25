@extends('layouts.app')
<script type="text/javascript">
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
            window.location.href="{{ url('customers/delete-customer') }}/"+id;
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
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>           
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row g-1">
        <div class="col-lg-11 col-md-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <span><i class="fa fa-warning"></i> Do You want to Delete all details for your Customer  : <b>{{$customer->name}}</b></span>
                    </div>
                    <hr>
                    <ul class="list-group list-group-custom list-group-flush">
                        <li class="list-group-item">
                            <div class="row g-1">
                                <div class="col-sm-12">
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-times-circle"></i>
                                        {{$info}}.
                                    </div>
                                </div>
                                <div class="col-sm-3" style="vertical-align: middle;">
                                    <a href="#" class="btn btn-outline-danger btn-sm" onclick="confirmDelete('<?php echo encrypt($customer->id); ?>')" style=" color: red;"> Delete Customer</a>
                                </div>
                                <div class="col-sm-3">
                                    <a href="{{ url('customers') }}" class="btn btn-outline-primary btn-sm">Cancel</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection