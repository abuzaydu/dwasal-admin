@extends('layouts.sand')
@section('content')
    <!--breadcrumb-->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="icon-home"></i></a></li>   
                    <li class="breadcrumb-item">Washed Sand Productions</li>                         
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
                        <form class="form row g-3" method="POST" action="{{route('rm-sourcings.update', encrypt($rmsourcing->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label class="form-label">Raw Material Source Name<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="raw_material_source_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($rmsources as $key => $source)
                                    @if($rmsourcing->raw_material_source_id == $source->id)
                                    <option value="{{$source->id}}" selected>{{$source->source_name}}</option>
                                    @else
                                    <option value="{{$source->id}}">{{$source->source_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Storage Location<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="storage_location_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($slocations as $key => $location)
                                    @if($rmsourcing->storage_location_id == $location->id)
                                    <option value="{{$location->id}}" selected>{{$location->location_name}}</option>
                                    @else
                                    <option value="{{$location->id}}">{{$location->location_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sourcing Date </label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="sourcing_date" value="{{$rmsourcing->sourcing_date}}" id="sourcing-date" placeholder="Enter Raw Material Sourcing Date" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity Received <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="capacity" type="number" step="any" min="0" name="qty_received" value="{{$rmsourcing->qty_received+0}}" placeholder="Enter Quantity Received" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">UOM <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="unit_of_measure" class="form-select form-select-sm mb-1" required>
                                    @foreach ($units as $key => $unit)
                                    @if($key < 3)
                                    @if($rmsourcing->unit_of_measure == $unit->unit_name)
                                    <option selected>{{ $unit->unit_name }}</option>
                                    @else
                                    <option>{{ $unit->unit_name }}</option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Add</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection
<link rel="stylesheet" href="{{ asset('assets/css/DatePickerX.css') }}">
<script src="{{ asset('assets/js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="sourcing_date"]');

        $min.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });
    });
</script>