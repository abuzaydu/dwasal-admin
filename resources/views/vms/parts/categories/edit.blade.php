@extends('layouts.vms')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>   
                    <li class="breadcrumb-item">Vehicle Managment</li>
                    <li class="breadcrumb-item"><a href="{{ url('parts') }}">Parts</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form row g-3" method="POST" action="{{route('part-categories.update', encrypt($pcate->id))}}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="col-md-4">
                            <label class="form-label">Name <span style="color: red;">*</span></label>
                            <input id="register-username" type="text" name="name" value="{{$pcate->name}}" required placeholder="Enter part type name" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Save Changes</button>
                            <a href="javascript:history()" class="btn btn-warning btn-sm">{{ trans('navmenu.btn_cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection