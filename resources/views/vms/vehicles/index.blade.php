@extends('layouts.vms')
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

         function confirmDeleteVehicleType(id){
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
                document.getElementById('delete-vt-form-'+id).submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function confirmDeleteOwnership(id){
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
                document.getElementById('delete-ot-form-'+id).submit();
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
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>   
                    <li class="breadcrumb-item">Vehicles Managment</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12">
                <button type="button" id="new-btn" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#vehicleModal"><i class="fa fa-plus-square"></i> New Vehicle</button>
                <button type="button" class="btn btn-success btn-sm px-1" data-bs-toggle="modal" data-bs-target="#newTypeModal">
                    <i class="fa fa-plus mr-1"></i>
                    Add Vehicle Type
                </button>
                <button type="button" class="btn btn-secondary btn-sm px-1" data-bs-toggle="modal" data-bs-target="#newOwnModal">
                    <i class="fa fa-plus mr-1"></i>
                    Add Ownership Type
                </button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">Vehicles List</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_1">Vehicle Types</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_2">Ownershi Types</a></li>
                    </ul>
                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="table-responsive" id="vehicle-list">
                                <table id="vehicles" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Plate Number</th>
                                            <th>Vehicle Name</th>
                                            <th>Type</th>
                                            <th>Ownership</th>
                                            <th style="text-align: center;">Capacity</th>
                                            <th>Reg. Date</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vehicles as $key => $vehicle)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('vehicles.show', encrypt($vehicle->id))}}">{{$vehicle->plate_no}}</a></td>
                                            <td>{{$vehicle->vehicle_name}}</td>
                                            <td>{{$vehicle->type}}</td>
                                            <td>{{$vehicle->ownership}}</td>
                                            <td style="text-align: center;">{{$vehicle->capacity}} {{$vehicle->uom}}</td>
                                            <td>{{date('d/m/Y', strtotime($vehicle->reg_date)) }}</td>
                                            <td>{{$vehicle->status}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{route('vehicles.edit', encrypt($vehicle->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('vehicles.destroy' , encrypt($vehicle->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDelete({{$key}})">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a>                        
                                                </form>    
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_1">
                            <div class="table-responsive">
                                <table id="vehicle-types" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Updated</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vehtypes as $key => $type)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('vehicle-types.show', encrypt($type->id))}}">{{$type->name}}</a></td>
                                            <td>{{$type->description}}</td>
                                            <td>
                                                @if($type->active)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                <span class="badge rounded-pill bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <th>{{$type->created_at}}</th>
                                            <th>{{$type->updated_at}}</th>
                                            <td style="text-align: center;">
                                                <a href="{{route('vehicle-types.edit', encrypt($type->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('vehicle-types.destroy' , encrypt($type->id))}}" id="delete-vt-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteVehicleType({{$key}})">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a>                        
                                                </form>    
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_2">
                            <div class="table-responsive">
                                <table id="ownerships" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Updated</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ownerships as $key => $owner)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('ownerships.show', encrypt($owner->id))}}">{{$owner->type}}</a></td>
                                            <td>{{$owner->description}}</td>
                                            <td>
                                                @if($owner->active)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                <span class="badge rounded-pill bg-danger">In Active</span>
                                                @endif
                                            </td>
                                            <th>{{$owner->created_at}}</th>
                                            <th>{{$owner->updated_at}}</th>
                                            <td style="text-align: center;">
                                                <a href="{{route('ownerships.edit', encrypt($owner->id))}}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('ownerships.destroy' , encrypt($owner->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteOwnership({{$key}})">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a>                        
                                                </form>    
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->


    <!-- Modal -->
    <div class="modal animated zoomIn" id="vehicleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-m">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Vehicle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form row g-3" method="POST" action="{{route('vehicles.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Plate Number<span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="plate_no" required placeholder="Enter Vehicle Plate Number" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Name Name</label>
                            <input id="location" type="text" name="vehicle_name" placeholder="Enter Vehicle Name" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chassis Number </label>
                            <input id="location" type="text" name="chassis_no" placeholder="Enter Chassis No. (optional)" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vehicles Type <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="vehicle_type_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Type --</option>
                                @foreach($vehtypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ownership <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="unit" name="ownership_id" class="form-select form-select-sm mb-1" required>
                                <option value="">--Select--</option>
                                @foreach($ownerships as $key => $owner)
                                <option value="{{ $owner->id }}">{{$owner->type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacity </label>
                            <input id="capacity" type="text" name="capacity" placeholder="Enter Truck capacity" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Capacity Unit </label>
                            <select id="unit" name="uom" class="form-select form-select-sm mb-1" required>
                                <option value="">Select Unit</option>
                                @foreach ($units as $key => $unit)
                                @if($key < 3 || $unit->unit_name == 'seats')
                                <option>{{ $unit->unit_name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select id="unit" name="department_id" class="form-select form-select-sm mb-1">
                                <option value="">--Select--</option>
                                @foreach($departments as $key => $dept)
                                <option value="{{ $dept-->id }}">{{$dept->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registration Date </label>
                            <div class="inner-addon left-addon"> 
                                <i class="myaddon fa fa-calendar"></i>
                                <input id="reg-date" type="text" name="reg_date" placeholder="Enter Registration Date" class="form-control form-control-sm mb-1">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Add</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="newTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">New Vehicle Type</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('vehicle-types.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row g-1 align-items-center">
                            <div class="col-md-12 pt-2">
                                <label class="form-label">Vehicle Type <span style="color: red;">*</span></label>
                                <input id="register-username" type="text" name="name" required placeholder="Enter Vehicle type name" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description?</label>
                                <input type="text" name="description" class="form-control form-control-sm mb-1" placeholder="Enter Description">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit-new">{{ trans('navmenu.btn_save') }}</button>
                                <button type="button" class="btn btn-warning btn-sm"
                                    data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newOwnModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">New Ownership</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('ownerships.store') }}">
                    <div class="modal-body">
                        @csrf
                        <div class="row g-1 align-items-center">
                            <div class="col-md-12 pt-2">
                                <label class="form-label">Ownership Type <span style="color: red;">*</span></label>
                                <input id="register-username" type="text" name="type" required placeholder="Enter Ownership type" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description?</label>
                                <input type="text" name="description" class="form-control form-control-sm mb-1" placeholder="Enter Description">
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit-new">{{ trans('navmenu.btn_save') }}</button>
                                <button type="button" class="btn btn-warning btn-sm"
                                    data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
