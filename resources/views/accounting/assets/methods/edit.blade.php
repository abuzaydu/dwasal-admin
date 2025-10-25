@extends('layouts.acc')
    
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
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="form row g-3" method="POST" action="{{route('dep-methods.update', encrypt($depmethod->id))}}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label class="form-label">Depreciation Method<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="dep_method" value="{{$depmethod->dep_method}}" required placeholder="Enter metho name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Abbreviation <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="abbreviation" value="{{$depmethod->abbreviation}}" required placeholder="Enter method Abbreviation" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Description </label>
                                <input id="name" type="text" name="description" value="{{$depmethod->description}}" placeholder="Enter description (Optional)" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="{{ url('dep-methods')}}" class="btn btn-warning btn-sm px-4 radius-30">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection