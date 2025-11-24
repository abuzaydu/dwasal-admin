@extends('layouts.vms')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>   
                    <li class="breadcrumb-item">Vehicles Managment</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-1" method="POST" action="{{ route('vehicle-types.update', encrypt($vehtype->id)) }}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-5 pt-2">
                                <label class="form-label">Vehicle Type <span style="color: red;">*</span></label>
                                <input id="register-username" type="text" name="name" value="{{$vehtype->name}}" required placeholder="Enter Vehicle type name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Description?</label>
                                <input type="text" name="description" value="{{$vehtype->description}}" class="form-control form-control-sm mb-1" placeholder="Enter Description">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit-new">{{ trans('navmenu.btn_save') }}</button>
                                <button type="button" class="btn btn-warning btn-sm"data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection