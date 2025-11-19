@extends('layouts.inv')
    <script>
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');

            if (elem == 'show') {
                newform.style.display = 'block';
                newbtn.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newbtn.style.display = 'block';
            }
        }

        function confirmDelete(id){
            Swal.fire({
              title: "{{trans('navmenu.are_you_sure')}}",
              text: "{{trans('navmenu.no_revert')}}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{trans('navmenu.cancel_it')}}",
              cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
              if (result.value) {
                document.getElementById('delete-form-'+id).submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }
    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
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
                        <form class="form row g-3" method="POST" action="{{route('vehicles.update', encrypt($vehicle->id))}}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label class="form-label">Plate Number<span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="plate_no" value="{{$vehicle->plate_no}}" required placeholder="Enter Vehicle Plate Number" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Chassis Number <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="location" type="text" name="chassis_no" value="{{$vehicle->chassis_no}}" required placeholder="Enter vehicle Address Or GPS Coordinates" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Vehicles & Trucks <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="type" class="form-select form-select-sm mb-1" required>
                                    <option>{{$vehicle->type}}</option>
                                    <option>Dump Trucks</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ownership <span style="color: red; font-weight: bold;">*</span></label>
                                <select id="unit" name="ownership" class="form-select form-select-sm mb-1" required>
                                    @if($vehicle->ownership == 'Company')
                                    <option>Company</option>
                                    <option>Vendor</option>
                                    @else
                                    <option>Vendor</option>
                                    <option>Company</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-12">
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