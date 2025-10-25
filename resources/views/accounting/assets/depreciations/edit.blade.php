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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <a href="{{ url('asset-records')}}" class="btn btn-warning btn-sm">Asset Records</a>
                <button type="button" id="new-depreciation-btn" class="btn btn-primary btn-sm" onclick="showHidedepreciationForm('show')"><i class="bx bxs-plus-square"></i>New depreciation</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="form row g-3" method="POST" action="{{ route('depreciations.update', encrypt($depreciation->id)) }}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-4">
                                <label class="form-label">Asset Name<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="asset_record_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($assets as $asset)
                                    @if($depreciation->asset_record_id == $asset->id)
                                    <option value="{{$asset->id}}" selected>{{$asset->asset_number}} - {{$asset->asset_name}}</option>
                                    @else
                                    <option value="{{$asset->id}}">{{$asset->asset_number}} - {{$asset->asset_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Year <span style="color: red; font-weight: bold;">*</span></label>
                                <select name="year" class="form-select form-select-sm ">
                                    <option value="">--Select--</option>
                                    @foreach($years as $y)
                                    @if($depreciation->year == $y)
                                    <option selected>{{$y}}</option>
                                    @else
                                    <option>{{$y}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="{{ url('depreciations')}}" class="btn btn-warning btn-sm px-4 radius-30">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection