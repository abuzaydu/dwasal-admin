@extends('layouts.inv')
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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3" method="POST" action="{{route('devices.update', $device->id)}}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label>{{trans('navmenu.device_number')}}</label>
                                <input type="text" name="device_number" class="form-control" value="{{$device->device_number}}">
                            </div>
                            <div class="col-md-4">
                                <label>{{trans('navmenu.device_name')}}</label>
                                <input type="text" name="device_name" class="form-control" value="{{$device->device_name}}">
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">{{trans('navmenu.device_cost')}}</label>
                                <input id="price" type="number" min="0" name="device_cost" placeholder="{{trans('navmenu.hnt_device_cost')}}" value="{{$device->device_cost}}" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn btn-success px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning px-4 radius-30">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection