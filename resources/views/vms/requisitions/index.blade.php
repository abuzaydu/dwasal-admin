@extends('layouts.vms')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
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
                <button type="button" id="new-req-btn" class="btn btn-primary btn-sm float-end ml-2" data-bs-toggle="modal" data-bs-target="#vehicleRequisitionModal"><i class="fa fa-plus-square"></i> New Vehicle Requesition.</button>
                <button type="button" id="new-req-purpose-btn" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#RequisitionPurposeModal"><i class="fa fa-plus-square"></i> New Requesition Purpose.</button>

            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">Requesition List</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_1">Requisition Purposes</a></li>
                    </ul>
                    
                    <div class="tab-content pt-2">

                        <div class="tab-pane fade show active" id="tab_0">
                            <table id="vehicles" class="table table-striped display nowrap datatable" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Employee</th>
                                        <th>Vehicle Type</th>
                                        <th>Purpose</th>
                                        <th>Pick up</th>
                                        <th>Requisition Date</th>
                                        <th>Status</th>
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vrequisitions as $key => $requisition)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td><a href="{{ route('vehicle-requisitions.show', $requisition->id)}}">{{ $requisition->employee->fname }}</a></td>
                                            <td>{{$requisition->vehicleType->name}}</td>
                                            <td>{{$requisition->purpose->purpose}}</td>
                                            <td>{{$requisition->pick_up}}</td>
                                            <td>{{date('d/m/Y', strtotime($requisition->requisition_date)) }}</td>
                                            <td>{{$requisition->status}}</td>
                                            <td style="text-align: center;">
                                                @if ($requisition->status !== 'Approved')
                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editVehicleRequisitionModal{{ $requisition->id }}">
                                                        <i class="fa fa-edit" style="color: blue;"></i>
                                                    </a>|
                                                    <form method="POST" action="{{route('vehicle-requisitions.destroy' , encrypt($requisition->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="javascript:;" onclick="return confirmDelete({{$key}})">
                                                            <i class="fa fa-trash" style="color: red;"></i>
                                                        </a>  
                                                                                
                                                    </form> |
                                                @endif  
                                                 
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#assignModal{{ $requisition->id }}">
                                                    <i class="fa fa-check-circle"></i> Approve
                                                </a>|
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $requisition->id }}">
                                                    <i class="fa fa-times-circle" style="color: red;"></i> Reject   
                                                </a>  
                                            </td>
                                        </tr>

                                        <!-- Approve & Assign Modal -->
                                        <div class="modal fade" id="assignModal{{ $requisition->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <form method="POST" action="{{ route('vehicle-requisitions.assign-driver', $requisition->id) }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Approve & Assign Vehicle</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">

                                                            <div class="mb-3">
                                                                <p><strong>Employee:</strong> {{ $requisition->employee->fname }}</p>
                                                                <p><strong>From:</strong> {{ $requisition->from }}</p>
                                                                <p><strong>To:</strong> {{ $requisition->to }}</p>
                                                                <p><strong>Date:</strong> {{ $requisition->requisition_date }}</p>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Assign Driver *</label>
                                                                <select name="driver_id" class="form-select" required>
                                                                    <option value="">-- Select Driver --</option>
                                                                    @foreach ($drivers as $driver)
                                                                        <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                @php
                                                                    $filteredVehicles = $vehicles->where('vehicle_type_id', $requisition->vehicle_type_id);
                                                                @endphp
                                                                <label class="form-label">Assign Vehicle *</label>
                                                                <select name="vehicle_id" class="form-select" required>
                                                                    <option value="">-- Select Vehicle --</option>
                                                                    @foreach ($filteredVehicles as $vehicle)
                                                                        <option value="{{ $vehicle->id }}">
                                                                            {{ $vehicle->vehicle_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fa fa-check-circle"></i> Approve & Assign
                                                            </button>
                                                        </div>

                                                    </form>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $requisition->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                    <form method="POST" action="{{ route('vehicle-requisitions.reject', $requisition->id) }}">
                                                        @csrf
                                                        @method('PUT') 

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject Vehicle Requisition</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <p><strong>Employee:</strong> {{ $requisition->employee->fname }}</p>
                                                            <p><strong>From:</strong> {{ $requisition->from }}</p>
                                                            <p><strong>To:</strong> {{ $requisition->to }}</p>
                                                            <p><strong>Date:</strong> {{ $requisition->requisition_date }}</p>

                                                            <div class="mb-3">
                                                                <label class="form-label">Reason for Rejection <span style="color: red">*</span></label>
                                                                <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Enter reason for rejection" required></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fa fa-times-circle"></i> Reject
                                                            </button>
                                                        </div>

                                                    </form>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Vehicle Requisition Modal -->
                                        <div class="modal fade" id="editVehicleRequisitionModal{{ $requisition->id }}" tabindex="-1" >
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Vehicle Requisition</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form  method="POST" action="{{ route('vehicle-requisitions.update', $requisition->id) }}" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row g-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Employee <span style="color: red; font-weight: bold;">*</span></label>
                                                                    <select id="employee_id" name="employee_id" class="form-select form-select-sm mb-1" required>
                                                                        <option value="">-- Select Employee --</option>
                                                                        @foreach ($employees as $emp)
                                                                            <option value="{{ $emp->id }}" {{ $emp->id == $requisition->employee_id ? 'selected' : '' }}>
                                                                                {{ $emp->fname }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Requisition Purpose <span style="color: red; font-weight: bold;">*</span></label>
                                                                    <select id="requisition_purpose_id" name="requisition_purpose_id" class="form-select form-select-sm mb-1" required>
                                                                        <option value="">-- Select Purpose --</option>
                                                                        @foreach ($requisitionPurpose as $purpose)
                                                                            <option value="{{ $purpose->id }}" {{ $purpose->id == $requisition->requisition_purpose_id ? 'selected' : '' }}>
                                                                                {{ $purpose->purpose }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Vehicle Type <span style="color: red; font-weight: bold;">*</span></label>
                                                                    <select id="vehicle_type_id" name="vehicle_type_id" class="form-select form-select-sm mb-1" required>
                                                                        <option value="">-- Select Type --</option>
                                                                        @foreach ($vehicleTypes as $vType)
                                                                            <option value="{{ $vType->id }}" {{ $vType->id == $requisition->vehicle_type_id ? 'selected' : '' }}>
                                                                                {{ $vType->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">From <span style="color:red">*</span></label>
                                                                    <input type="text" name="from" value="{{ $requisition->from }}" class="form-control form-control-sm mb-1" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">To <span style="color:red">*</span></label>
                                                                    <input type="text" name="to" value="{{ $requisition->to }}" class="form-control form-control-sm mb-1" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Pick Up <span style="color:red">*</span></label>
                                                                    <input type="text" name="pick_up" value="{{ $requisition->pick_up }}" class="form-control form-control-sm mb-1" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Requisition Date <span style="color:red">*</span></label>
                                                                    <input type="date" name="requisition_date" value="{{ $requisition->requisition_date }}" class="form-control form-control-sm mb-1" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Time From <span style="color:red">*</span></label>
                                                                    <input type="time" name="time_from" value="{{ $requisition->time_from }}" class="form-control form-control-sm mb-1" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Time To <span style="color:red">*</span></label>
                                                                    <input type="time" name="time_to" value="{{ $requisition->time_to }}" class="form-control form-control-sm mb-1" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Tolerance Duration</label>
                                                                    <input type="text" name="tolerance_duration" value="{{ $requisition->tolerance_duration }}" class="form-control form-control-sm mb-1">
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Details</label>
                                                                    <input type="text" name="details" value="{{ $requisition->details }}" class="form-control form-control-sm mb-1">
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <button type="submit" class="btn btn-success btn-sm px-4 radius-30">Update</button>
                                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane fade" id="tab_1">
                            <div class="table-responsive">
                                <table id="requisitionPurpose" class="table table-striped display nowrap datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Purpose</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requisitionPurpose as $key => $purpose)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$purpose->purpose}}</td>
                                            <td>{{$purpose->description}}</td>
                                            <td>{{$purpose->active? 'Active' : 'InActive'}}</td>
                                            <td style="text-align: center;">
                                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editRequisitionPurposeModal{{ $purpose->id }}">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('requisitions-purpose.destroy' , encrypt($purpose->id))}}" id="delete-req-purpose-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteReqPurpose({{$key}})">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a>                        
                                                </form>    
                                            </td>
                                        </tr>

                                          <div class="modal animated zoomIn" id="editRequisitionPurposeModal{{ $purpose->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-m">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Requisition Purpose</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <form class="form row g-3" method="POST" action="{{ route('requisitions-purpose.update', $purpose->id) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="row g-2">
                                                                <div class="col-md-6">
                                                                    <label class="form-label">Purpose <span style="color:red">*</span></label>
                                                                    <input type="text" name="purpose"
                                                                        value="{{ old('purpose', $purpose->purpose) }}"
                                                                        class="form-control form-control-sm mb-1" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Description</label>
                                                                    <input type="text" name="description"
                                                                        value="{{ old('description', $purpose->description) }}"
                                                                        class="form-control form-control-sm mb-1">
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label">Status</label>
                                                                    <select name="active" class="form-select form-select-sm mb-1">
                                                                        <option value="1" {{ $purpose->active == 1 ? 'selected' : '' }}>Active</option>
                                                                        <option value="0" {{ $purpose->active == 0 ? 'selected' : '' }}>Inactive</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-12">
                                                                    <button type="submit" class="btn btn-primary btn-sm px-4 radius-30">
                                                                        Update
                                                                    </button>
                                                                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">
                                                                        Cancel
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                          </div>
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
    <div class="modal animated zoomIn" id="vehicleRequisitionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-m">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Vehicle Requisition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form row g-3" method="POST" action="{{route('vehicle-requisitions.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Employee <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="employee_id" name="employee_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Employee --</option>
                                @foreach ($employees as $emp )
                                    <option value="{{ $emp->id }}">{{ $emp->fname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                           <label class="form-label">Requisition Purpose <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="requisition_purpose_id" name="requisition_purpose_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select purpose --</option>
                                @foreach ($requisitionPurpose as $purpose )
                                    <option value="{{ $purpose->id }}">{{ $purpose->purpose }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vehicles Type <span style="color: red; font-weight: bold;">*</span></label>
                            <select id="vehicle_type_id" name="vehicle_type_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Type --</option>
                                @foreach ($vehicleTypes as $vTypes )
                                    <option value="{{ $vTypes->id }}">{{ $vTypes->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">From <span style="color: red">*</span></label>
                            <input type="text" name="from" placeholder="From" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To <span style="color:red">*</span></label>
                            <input type="text" name="to" placeholder="To" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pick Up <span style="color:red">*</span></label>
                            <input type="text" name="pick_up" placeholder="Pick up" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Requisition Date <span style="color: red">*</span></label>
                            <div class="inner-addon left-addon"> 
                                <i class="myaddon fa fa-calendar"></i>
                                <input id="requisition-date" type="date" name="requisition_date" placeholder="Enter Requisition Date" class="form-control form-control-sm mb-1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time From <span style="color:red">*</span></label>
                            <input type="time" name="time_from" placeholder="Time From" class="form-control form-control-sm mb-1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time To <span style="color:red">*</span></label>
                            <input type="time" name="time_to" placeholder="Time To" class="form-control form-control-sm mb-1" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tolerance duration </label>
                            <input type="text" name="tolerance_duration" placeholder="Tolerance duration" class="form-control form-control-sm mb-1" >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Details </label>
                            <input type="text" name="details" placeholder="Details(Optional)" class="form-control form-control-sm mb-1">
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

     <div class="modal animated zoomIn" id="RequisitionPurposeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-m">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Requisition Purpose</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form row g-3" method="POST" action="{{route('requisitions-purpose.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Purpose<span style="color: red; font-weight: bold;">*</span></label>
                            <input type="text" name="purpose" class="form-control form-control-sm mb-1" required>
                                
                        </div>
                        <div class="col-md-6">
                           <label class="form-label">Description </label>
                            <input type="text" name="description" class="form-control form-control-sm mb-1" >
                            
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select type="text" name="active" class="form-select form-select-sm mb-1" >
                                <option value="1" selcted>Active</option>
                                <option value="0">InActive</option>
                            </select>
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

     @section('page-scripts')
        <script>
            function confirmDelete(id) {
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

            function confirmDeleteReqPurpose(id) {
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
                    document.getElementById('delete-req-purpose-form-'+id).submit();
                    Swal.fire(
                        "{{trans('navmenu.deleted')}}",
                        "{{trans('navmenu.cancelled')}}",
                        'success'
                    )
                    }
                })
            }

            $(document).ready(function () {

                $('.tab-pane.active .datatable').DataTable();

                $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
                    let table = $($(this).attr('href')).find('.datatable');

                    if (!$.fn.DataTable.isDataTable(table)) {
                        table.DataTable();
                    } else {
                        table.DataTable().columns.adjust();
                    }
                });

            });

        </script>
     @endsection
@endsection


