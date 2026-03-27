@extends('layouts.vms')

@section('content')

    <!-- Breadcrumb -->
    <div class="block-header pt-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a href="{{ url('vehicle-requisitions') }}">Vehicle Requisitions</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-md-6 text-end">
                @if(in_array($requisition->status, ['Pending','Awaiting for Approval']))
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editVehicleRequisitionModal{{ $requisition->id }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Edit
                    </a>
                @endif
                <a href="{{ url('vehicle-requisitions') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Header Card -->
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="fa fa-route text-primary me-2"></i>
                    Requisition #{{ $requisition->id }}
                </h5>
                <small class="text-muted">
                    Created on {{ \Carbon\Carbon::parse($requisition->created_at)->format('d M Y, h:i A') }}
                </small>
            </div>
            <div>
                @php $status = $requisition->status; @endphp
                @if($status == 'Awaiting for Approval')
                    <span class="badge bg-info">Awaiting Approval</span>
                @elseif($status == 'Approved')
                    <span class="badge bg-success">Approved</span>
                @elseif($status == 'Rejected')
                    <span class="badge bg-danger">Rejected</span>
                @else
                    <span class="badge bg-secondary">{{ $status }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h6 class="fw-bold mb-0">
                <i class="fa fa-info-circle text-primary me-2"></i> Requisition Details
            </h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted fw-semibold bg-light" style="width: 20%;">Employee</th>
                        <td style="width: 30%;">{{ $requisition->employee->fname ?? 'N/A' }}</td>
                        <th class="text-muted fw-semibold bg-light" style="width: 20%;">Vehicle Type</th>
                        <td>{{ $requisition->vehicleType->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Purpose</th>
                        <td>{{ $requisition->purpose->purpose ?? 'N/A' }}</td>
                        <th class="text-muted fw-semibold bg-light">Pick Up Location</th>
                        <td>{{ $requisition->pick_up }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">From</th>
                        <td>{{ $requisition->from }}</td>
                        <th class="text-muted fw-semibold bg-light">To</th>
                        <td>{{ $requisition->to }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Date</th>
                        <td>{{ $requisition->requisition_date->format('d M Y') }}</td>
                        <th class="text-muted fw-semibold bg-light">Time Range</th>
                        <td>{{ $requisition->time_from }} – {{ $requisition->time_to }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Details</th>
                        <td>{{ $requisition->details ?? '-' }}</td>
                        <th class="text-muted fw-semibold bg-light">Tolerance Duration</th>
                        <td>{{ $requisition->tolerance_duration ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Status</th>
                        <td>
                            @if($status == 'Pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($status == 'Awaiting for Approval')
                                <span class="badge bg-info">Awaiting Approval</span>
                            @elseif($status == 'Approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($status == 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-secondary">{{ $status }}</span>
                            @endif
                        </td>
                        <th class="text-muted fw-semibold bg-light">Last Updated</th>
                        <td>{{ \Carbon\Carbon::parse($requisition->updated_at)->format('d M Y, h:i A') }}</td>
                    </tr>

                    @if($requisition->status == 'Approved')
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Assigned Driver</th>
                        <td>{{ $requisition->driver->full_name ?? 'N/A' }}</td>
                        <th class="text-muted fw-semibold bg-light">Assigned Vehicle</th>
                        <td>{{ $requisition->vehicle->plate_no ?? '-' }}</td>
                    </tr>
                    @endif

                    @if($requisition->status == 'Rejected' && $requisition->rejection_reason)
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Rejection Reason</th>
                        <td colspan="3" class="text-danger">{{ $requisition->rejection_reason }}</td>
                    </tr>
                    @endif

                    @if($requisition->details)
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Details</th>
                        <td colspan="3">{{ $requisition->details }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Last updated: {{ \Carbon\Carbon::parse($requisition->updated_at)->format('d M Y, h:i A') }}
            </small>
            <div class="d-flex gap-2">
                @php
                    $tripLog = \App\Models\RequisitionTripLog::where('vehicle_requisition_id', $requisition->id)->first();
                @endphp
                @if($requisition->status == 'Awaiting for Approval')
                    <a class="btn btn-success btn-sm" style="font-size:0.75rem;padding:0.25rem 0.6rem;"
                    data-bs-toggle="modal" data-bs-target="#assignModal{{ $requisition->id }}">
                        <i class="fa fa-check me-1"></i> Approve
                    </a>
                    <a class="btn btn-danger btn-sm" style="font-size:0.75rem;padding:0.25rem 0.6rem;"
                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $requisition->id }}">
                        <i class="fa fa-times me-1"></i> Reject
                    </a>
                @elseif($requisition->status == 'Rejected')
                    <a class="btn btn-warning btn-sm" style="font-size:0.75rem;padding:0.25rem 0.6rem;"
                    href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editVehicleRequisitionModal{{ $requisition->id }}">
                        <i class="fa fa-edit me-1"></i> Resubmit
                    </a>
                
                @elseif($requisition->status == 'Approved' && (!$tripLog || !$tripLog->start_time))
                    <a class="btn btn-primary btn-sm" style="font-size:0.75rem;padding:0.25rem 0.6rem;"
                    href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#startTripModal{{ $requisition->id }}">
                        <i class="fa fa-play me-1"></i> Start Trip
                    </a>
                @endif
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
                        <table class="table table-sm table-bordered table-striped mb-3">
                            <tbody>
                                <tr><th class="text-muted bg-light" style="width:40%;">Employee</th><td>{{ $requisition->employee->fname }}</td></tr>
                                <tr><th class="text-muted bg-light">From</th><td>{{ $requisition->from }}</td></tr>
                                <tr><th class="text-muted bg-light">To</th><td>{{ $requisition->to }}</td></tr>
                                <tr><th class="text-muted bg-light">Date</th><td>{{ $requisition->requisition_date }}</td></tr>
                            </tbody>
                        </table>
                        <div class="mb-3">
                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Enter reason for rejection" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa fa-times-circle me-1"></i> Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                        <table class="table table-sm table-bordered table-striped mb-3">
                            <tbody>
                                <tr><th class="text-muted bg-light" style="width:40%;">Employee</th><td>{{ $requisition->employee->fname }}</td></tr>
                                <tr><th class="text-muted bg-light">From</th><td>{{ $requisition->from }}</td></tr>
                                <tr><th class="text-muted bg-light">To</th><td>{{ $requisition->to }}</td></tr>
                                <tr><th class="text-muted bg-light">Date</th><td>{{ $requisition->requisition_date }}</td></tr>
                            </tbody>
                        </table>
                        <div class="mb-3">
                            <label class="form-label">Assign Driver <span class="text-danger">*</span></label>
                            <select name="driver_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Driver --</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            @php $filteredVehicles = $vehicles->where('vehicle_type_id', $requisition->vehicle_type_id); @endphp
                            <label class="form-label">Assign Vehicle <span class="text-danger">*</span></label>
                            <select name="vehicle_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Vehicle --</option>
                                @foreach ($filteredVehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-check-circle me-1"></i> Approve & Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resubmit Modal -->
    <div class="modal fade" id="editVehicleRequisitionModal{{ $requisition->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Vehicle Requisition</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('vehicle-requisitions.resubmit', $requisition->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ $emp->id == $requisition->employee_id ? 'selected' : '' }}>
                                            {{ $emp->fname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Purpose <span class="text-danger">*</span></label>
                                <select name="requisition_purpose_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Purpose --</option>
                                    @foreach ($requisitionPurpose as $purpose)
                                        <option value="{{ $purpose->id }}" {{ $purpose->id == $requisition->requisition_purpose_id ? 'selected' : '' }}>
                                            {{ $purpose->purpose }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                                <select name="vehicle_type_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Type --</option>
                                    @foreach ($vehicleTypes as $vType)
                                        <option value="{{ $vType->id }}" {{ $vType->id == $requisition->vehicle_type_id ? 'selected' : '' }}>
                                            {{ $vType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">From <span class="text-danger">*</span></label>
                                <input type="text" name="from" value="{{ $requisition->from }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">To <span class="text-danger">*</span></label>
                                <input type="text" name="to" value="{{ $requisition->to }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pick Up <span class="text-danger">*</span></label>
                                <input type="text" name="pick_up" value="{{ $requisition->pick_up }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Requisition Date <span class="text-danger">*</span></label>
                                <input type="date" name="requisition_date" value="{{ $requisition->requisition_date }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Time From <span class="text-danger">*</span></label>
                                <input type="time" name="time_from" value="{{ $requisition->time_from }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Time To <span class="text-danger">*</span></label>
                                <input type="time" name="time_to" value="{{ $requisition->time_to }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tolerance Duration</label>
                                <input type="text" name="tolerance_duration" value="{{ $requisition->tolerance_duration }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Details</label>
                                <input type="text" name="details" value="{{ $requisition->details }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="submit" class="btn btn-success btn-sm px-4">Update</button>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Trip Modal -->
    <div class="modal fade" id="startTripModal{{ $requisition->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('trip.start', encrypt($requisition->id)) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-route me-2"></i> Start Trip</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-sm table-bordered table-striped mb-3">
                            <tbody>
                                <tr><th class="text-muted bg-light" style="width:40%;">Employee</th><td>{{ $requisition->employee->fname ?? '' }}</td></tr>
                                <tr><th class="text-muted bg-light">From</th><td>{{ $requisition->from }}</td></tr>
                                <tr><th class="text-muted bg-light">To</th><td>{{ $requisition->to }}</td></tr>
                                <tr><th class="text-muted bg-light">Date</th><td>{{ $requisition->requisition_date }}</td></tr>
                            </tbody>
                        </table>
                        <div class="mb-3">
                            <label class="form-label">Start Odometer (km) <span class="text-danger">*</span></label>
                            <input type="number" name="start_odometer" step="0.01" class="form-control form-control-sm" placeholder="Enter starting mileage" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Remarks (Optional)</label>
                            <textarea name="remarks" class="form-control form-control-sm" rows="2" placeholder="Any notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-play me-1"></i> Start Trip
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
                                <input type="date" name="requisition_date" value="{{ $requisition->requisition_date->format('Y-m-d') }}" class="form-control form-control-sm mb-1" required>
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

@endsection