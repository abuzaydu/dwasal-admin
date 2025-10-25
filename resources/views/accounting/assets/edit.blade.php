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
                        <form class="form row g-3" method="POST" action="{{route('asset-records.update', encrypt($asset->id))}}">
                            @csrf
                            {{ method_field('PATCH')}}
                            <div class="col-md-3">
                                <label class="form-label">Asset Name <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="asset_name" value="{{$asset->asset_name}}" required placeholder="Enter asset name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Asset Class <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="asset_class" value="{{$asset->asset_class}}" required placeholder="Enter Asset Class" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Description <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="description" value="{{$asset->description}}" placeholder="Enter Description" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Physical Location</label>
                                <input id="name" type="text" name="physical_location" value="{{$asset->physical_location}}" placeholder="Enter Physical Location" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Asset Number  <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="asset_number" value="{{$asset->asset_number}}" placeholder="Enter Asset Number" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Serial Number </label>
                                <input id="name" type="text" name="serial_no" value="{{$asset->serial_no}}" placeholder="Enter Serial Number" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Acquisition Date <span style="color: red; font-weight: bold;">*</span></label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="acquisition_date" value="{{$asset->acquisition_date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div> 
                            </div>  
                            <div class="col-md-3">
                                <label class="form-label">Acquisition Cost ({{$currency}}) <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="acquisition_cost" value="{{$asset->acquisition_cost}}" placeholder="Enter Acquisition Cost" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Depreciation Method <span style="color: red; font-weight: bold;">*</span></label>
                                <select name="dep_method" class="form-select form-select-sm mb-1" required>
                                    @foreach($depmethods as $m)
                                    @if($asset->dep_method == $m->dep_method)
                                    <option value="{{$m->abbreviation}}">{{$m->dep_method}} - {{$m->abbreviation}}</option>
                                    @else
                                    <option value="{{$m->abbreviation}}">{{$m->dep_method}} - {{$m->abbreviation}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Useful Life <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="number" name="useful_life" value="{{$asset->useful_life}}" placeholder="Enter Useful life" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">First Year (%) </label>
                                <input id="name" type="number" name="first_year" value="{{$asset->first_year}}" placeholder="Enter First Year" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Salvage Value ({{$currency}}) <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="number" name="salvage_value" value="{{$asset->salvage_value}}" placeholder="Enter Acquisition Cost" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideDevceForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection

<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">

<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="acquisition_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>