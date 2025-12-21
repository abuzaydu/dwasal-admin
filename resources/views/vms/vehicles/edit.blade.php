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
                        <form class="form row g-3" method="POST" action="{{route('vehicles.update', encrypt($vehicle->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label class="form-label">Plate Number<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="plate_no" value="{{$vehicle->plate_no}}" required placeholder="Enter Vehicle Plate Number" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Name Name</label>
                                <input id="location" type="text" name="vehicle_name" value="{{$vehicle->vehicle_name}}" placeholder="Enter Vehicle Name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Chassis Number</label>
                                <input id="location" type="text" name="chassis_no" value="{{$vehicle->chassis_no}}" placeholder="Enter vehicle Address Or GPS Coordinates" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Vehicles Type <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="vehicle_type_id" class="form-select form-select-sm mb-1" required>
                                    @foreach($vehtypes as $type)
                                    @if($vehicle->vehicle_type_id == $type->id)
                                    <option value="{{ $type->id }}" selected>{{ $type->name }}</option>
                                    @else
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ownership <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="ownership_id" class="form-select form-select-sm mb-1" required>
                                    @foreach($ownerships as $key => $owner)
                                    @if($vehicle->ownership_id == $owner->id)
                                    <option value="{{ $owner->id }}" selected>{{$owner->type}}</option>
                                    @else
                                    <option value="{{ $owner->id }}">{{$owner->type}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Capacity </label>
                                <input id="capacity" type="text" name="capacity" value="{{$vehicle->capacity}}" placeholder="Enter Truck capacity" class="form-control form-control-sm mb-1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Capacity Unit <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="uom" class="form-select form-select-sm mb-1" required>
                                    <option value="">Select Unit</option>
                                    @foreach ($units as $key => $unit)
                                    @if($key < 3)
                                    @if($vehicle->uom == $unit->unit_name)
                                    <option selected>{{ $unit->unit_name }}</option>
                                    @else
                                    <option>{{ $unit->unit_name }}</option>
                                    @endif
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Department</label>
                                <select id="unit" name="department_id" class="form-select form-select-sm mb-1">
                                    <option value="">--Select--</option>
                                    @foreach($departments as $key => $dept)
                                    @if($vehicle->department_id == $dept->id)
                                    <option value="{{ $dept-->id }}" selected>{{$dept->name}}</option>
                                    @else
                                    <option value="{{ $dept-->id }}">{{$dept->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Registration Date </label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input id="reg-date" type="text" name="reg_date" value="{{$vehicle->reg_date}}" placeholder="Enter Registration Date" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Save Changes</button>
                                <a href="{{ url('vehicles')}}" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css')}}">
<script src="{{ asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="reg_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>
