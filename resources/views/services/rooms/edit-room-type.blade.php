@extends('layouts.inv')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('rooms') }}">Room Settings</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="tab_1" role="tabpanel">
                            <div class="p-4 border rounded">
                                <form class="form row g-3" method="POST" action="{{route('room-types.update', encrypt($rtype->id))}}">
                                    @csrf
                                    {{ method_field('PATCH') }}
                                    <div class="col-md-4">
                                        <label class="form-label">Room type<span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="name" value="{{$rtype->name}}" required placeholder="Enter Room Type name" class="form-control form-control-sm mb-1">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Description <span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="description" value="{{$rtype->description}}" required placeholder="Enter Room Type description" class="form-control form-control-sm mb-1">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Price Per Night ({{$defcurr->code}})</label>
                                        <input id="name" type="number" name="price_per_night" value="{{$rtype->price_per_night}}" placeholder="Enter Price Per Night" class="form-control form-control-sm mb-1">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Capacity</label>
                                        <input id="name" type="number" name="capacity" value="{{$rtype->capacity}}" placeholder="Enter Capacity" class="form-control form-control-sm mb-1">
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                        <a href="{{ url('rooms') }}" class="btn btn-warning btn-sm px-4 radius-30">{{trans('navmenu.btn_cancel')}}</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection