@extends('layouts.sand')
@section('page-styles')
    <!-- CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="icon-home"></i></a></li>   
                    <li class="breadcrumb-item">Washed Sand Productions</li>
                    <li><a href="{{ url('sand-productions') }}">Production Records</a></li>
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
                        <form class="form row g-3" method="POST" action="{{route('sand-productions.update', encrypt($prodrun->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label class="form-label">Washing Plant<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="washing_plant_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($plants as $key => $plant)
                                    @if($prodrun->washing_plant_id == $plant->id)
                                    <option value="{{$plant->id}}" selected>{{$plant->plant_name}}</option>
                                    @else
                                    <option value="{{$plant->id}}">{{$plant->plant_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Storage Location<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="storage_location_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($slocations as $key => $location)
                                    @if($prodrun->storage_location_id == $location->id)
                                    <option value="{{$location->id}}" selected>{{$location->location_name}}</option>
                                    @else
                                    <option value="{{$location->id}}">{{$location->location_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start DateTime <span style="color: red; font-weight: bold;">*</span></label><div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="start_time" id="start-time" value="{{$prodrun->start_time}}" class="form-control form-control-sm mb-1" placeholder="Enter Production Start Time" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End DateTime</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="end_time" id="end-time" value="{{$prodrun->end_time}}" class="form-control form-control-sm mb-1" placeholder="Enter Production Start Time">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Input Quantity <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="input-qty" type="number" min="0" step="any" name="input_quantity" value="{{$prodrun->input_quantity+0}}" placeholder="Enter Raw Material Input Qty" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Output Quantity </label>
                                <input id="output-qty" type="number" min="0" step="any" name="output_quantity" value="{{$prodrun->output_quantity+0}}" placeholder="Enter Output Produced Qty" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waste Water Quantity </label>
                                <input id="waste-qty" type="number" min="0" step="any" name="waste_water_quantity" value="{{$prodrun->waste_water_quantity+0}}" placeholder="Enter Waste Water Qty" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-9">
                                <label class="form-label">Remarks </label>
                                <input type="text" name="remarks" value="{{$prodrun->remarks}}" class="form-control form-control-sm mb-1" placeholder="Enter Production Remarks">
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Create</button>
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

@section('page-scripts')
    
    <!-- datetimepicker jQuery CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function(){
            $("#start-time").each(function () {
                $(this).datetimepicker({
                    maxDate:'+1970/01/01'
                });
            });

            $("#end-time").each(function () {
                $(this).datetimepicker({
                    maxDate:'+1970/01/01'
                });
            });
        });
    </script>
@endsection