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
        <div class="col-xl-12 mx-auto">
            <h6 class="mb-0 text-uppercase" id="new-title">{{$title}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('services.update', encrypt($service->id)) }}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label for="emp_id" class="form-label">Service Code</label>
                                <!-- <div class="input-group input-group-sm mb-3" > -->
                                    <input type="text" name="code" value="{{$service->code}}" class="form-control form-control-sm " placeholder="Service Code" aria-describedby="basic-addon1">
                                    <!-- <a href="#"  onclick="AutoCode()"class="input-group-text" id="basic-addon1" >Auto<i class="fa fa-cog"></i></a> -->
                                <!-- </div> -->
                            </div>

                            <div class="col-md-6">
                                <label for="validationCustom01" class="form-label">{{trans('navmenu.service')}}</label>
                                <input type="text" class="form-control form-control-sm mb-1" id="validationCustom01" name="name" placeholder="{{trans('navmenu.hnt_enter_service_name')}}" value="{{$service->name}}" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Service name.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="validationCustom02" class="form-label">{{trans('navmenu.price')}}</label>
                                <input type="number" step="any" class="form-control form-control-sm mb-1" id="validationCustom02" name="price" placeholder="{{trans('navmenu.hnt_service_price')}}" value="{{$service->price}}" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Service Price.</div>
                            </div>
                            <div class="col-md-12">
                                <label for="validationCustom03" class="form-label">{{trans('navmenu.description')}}</label>
                                <input type="tel" class="form-control form-control-sm mb-1" id="validationCustom03" name="description" placeholder="{{trans('navmenu.hnt_service_desc')}}" value="{{$service->description}}">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm px-4 radius-30" type="submit" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning btn-sm px-4 radius-30">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection