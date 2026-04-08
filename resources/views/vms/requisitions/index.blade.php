@extends('layouts.vms')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<!--breadcrumb-->
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicles Management</li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>

        <div class="col-lg-7 col-md-4 col-sm-12 mt-2 mt-md-0">
            <div class="d-flex flex-wrap justify-content-start justify-content-md-end align-items-center gap-2">

                <form class="dashform" action="{{ url('f-vehicle-requisitions') }}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <button type="button" class="btn btn-default btn-sm" id="reportrange" style="white-space: nowrap;">
                        <i class="fa fa-calendar"></i>
                        <span id="reportrange-label" class="mx-1"></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </form>

                <button type="button" class="btn btn-primary btn-sm" style="white-space: nowrap;"
                    data-bs-toggle="modal" data-bs-target="#vehicleRequisitionModal">
                    <i class="fa fa-plus-square me-1"></i> New Requisition
                </button>

                <button type="button" class="btn btn-secondary btn-sm" style="white-space: nowrap;"
                    data-bs-toggle="modal" data-bs-target="#RequisitionPurposeModal">
                    <i class="fa fa-plus-square me-1"></i> New Purpose
                </button>

            </div>
        </div>
    </div>
</div>
<!--end breadcrumb-->

<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-new2">
                    <li class="nav-item">
                        <a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">
                            Requisition List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab_1">
                            Requisition Purposes
                        </a>
                    </li>
                </ul>

                <div class="tab-content pt-2">

                    <div class="tab-pane fade show active table-responsive" id="tab_0">
                        <table id="vehicles" class="table table-striped table-bordered datatable nowrap" style="width:100%; font-size:13px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Vehicle Type</th>
                                    <th>Purpose</th>
                                    <th>Pick Up</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vrequisitions as $key => $requisition)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <a href="{{ route('vehicle-requisitions.show', $requisition->id) }}">
                                            {{ $requisition->employee->fname }}
                                        </a>
                                    </td>
                                    <td>{{ $requisition->vehicleType->name }}</td>
                                    <td>{{ $requisition->purpose->purpose }}</td>
                                    <td>{{ $requisition->pick_up }}</td>
                                    <td>{{ date('d/m/Y', strtotime($requisition->requisition_date)) }}</td>
                                    <td>
                                        @php $status = $requisition->status; @endphp
                                        @if($status === 'Approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($status === 'Rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($status === 'Pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <a href="{{ route('vehicle-requisitions.show', $requisition->id) }}" class="text-info">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        @if($requisition->status !== 'Approved')
                                            |
                                            <a href="javascript:void(0);" class="text-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editVehicleRequisitionModal{{ $requisition->id }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            |
                                            <form method="POST"
                                                action="{{ route('vehicle-requisitions.destroy', encrypt($requisition->id)) }}"
                                                id="delete-form-{{ $key }}" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <a href="javascript:;" onclick="return confirmDelete({{ $key }})" class="text-danger">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </form>
                                        @endif
                                        |
                                        <a href="javascript:void(0);" class="text-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#assignModal{{ $requisition->id }}">
                                            <i class="fa fa-check-circle"></i> Approve
                                        </a>
                                        |
                                        <a href="javascript:void(0);" class="text-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $requisition->id }}">
                                            <i class="fa fa-times-circle"></i> Reject
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="assignModal{{ $requisition->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('vehicle-requisitions.assign-driver', $requisition->id) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Approve & Assign Vehicle</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Employee:</strong> {{ $requisition->employee->fname }}</p>
                                                    <p><strong>From:</strong> {{ $requisition->from }}</p>
                                                    <p><strong>To:</strong> {{ $requisition->to }}</p>
                                                    <p><strong>Date:</strong> {{ $requisition->requisition_date }}</p>

                                                    <div class="mb-3">
                                                        <label class="form-label">Assign Driver <span class="text-danger">*</span></label>
                                                        <select name="driver_id" class="form-select form-select-sm" required>
                                                            <option value="">-- Select Driver --</option>
                                                            @foreach($drivers as $driver)
                                                                <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        @php $filteredVehicles = $vehicles->where('vehicle_type_id', $requisition->vehicle_type_id); @endphp
                                                        <label class="form-label">Assign Vehicle <span class="text-danger">*</span></label>
                                                        <select name="vehicle_id" class="form-select form-select-sm" required>
                                                            <option value="">-- Select Vehicle --</option>
                                                            @foreach($filteredVehicles as $vehicle)
                                                                <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fa fa-check-circle"></i> Approve & Assign
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="rejectModal{{ $requisition->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('vehicle-requisitions.reject', $requisition->id) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Requisition</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Employee:</strong> {{ $requisition->employee->fname }}</p>
                                                    <p><strong>From:</strong> {{ $requisition->from }}</p>
                                                    <p><strong>To:</strong> {{ $requisition->to }}</p>
                                                    <p><strong>Date:</strong> {{ $requisition->requisition_date }}</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Reason <span class="text-danger">*</span></label>
                                                        <textarea name="rejection_reason" class="form-control form-control-sm" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fa fa-times-circle"></i> Reject
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="editVehicleRequisitionModal{{ $requisition->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Vehicle Requisition</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="{{ route('vehicle-requisitions.update', $requisition->id) }}">
                                                    @csrf @method('PUT')
                                                    <div class="row g-2">
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                                                            <select name="employee_id" class="form-select form-select-sm" required>
                                                                <option value="">-- Select Employee --</option>
                                                                @foreach($employees as $emp)
                                                                    <option value="{{ $emp->id }}" {{ $emp->id == $requisition->employee_id ? 'selected' : '' }}>
                                                                        {{ $emp->fname }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">Purpose <span class="text-danger">*</span></label>
                                                            <select name="requisition_purpose_id" class="form-select form-select-sm" required>
                                                                <option value="">-- Select Purpose --</option>
                                                                @foreach($requisitionPurpose as $purpose)
                                                                    <option value="{{ $purpose->id }}" {{ $purpose->id == $requisition->requisition_purpose_id ? 'selected' : '' }}>
                                                                        {{ $purpose->purpose }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                                                            <select name="vehicle_type_id" class="form-select form-select-sm" required>
                                                                <option value="">-- Select Type --</option>
                                                                @foreach($vehicleTypes as $vType)
                                                                    <option value="{{ $vType->id }}" {{ $vType->id == $requisition->vehicle_type_id ? 'selected' : '' }}>
                                                                        {{ $vType->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">From <span class="text-danger">*</span></label>
                                                            <input type="text" name="from" value="{{ $requisition->from }}" class="form-control form-control-sm" required>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">To <span class="text-danger">*</span></label>
                                                            <input type="text" name="to" value="{{ $requisition->to }}" class="form-control form-control-sm" required>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">Pick Up <span class="text-danger">*</span></label>
                                                            <input type="text" name="pick_up" value="{{ $requisition->pick_up }}" class="form-control form-control-sm" required>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">Date <span class="text-danger">*</span></label>
                                                            <input type="date" name="requisition_date" value="{{ $requisition->requisition_date }}" class="form-control form-control-sm" required>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">Time From <span class="text-danger">*</span></label>
                                                            <input type="time" name="time_from" value="{{ $requisition->time_from }}" class="form-control form-control-sm" required>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">Time To <span class="text-danger">*</span></label>
                                                            <input type="time" name="time_to" value="{{ $requisition->time_to }}" class="form-control form-control-sm" required>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label">Tolerance Duration</label>
                                                            <input type="text" name="tolerance_duration" value="{{ $requisition->tolerance_duration }}" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label">Details</label>
                                                            <input type="text" name="details" value="{{ $requisition->details }}" class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <button type="submit" class="btn btn-success btn-sm">Update</button>
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

                    {{-- TAB 2: Requisition Purposes --}}
                    <div class="tab-pane fade table-responsive" id="tab_1">
                        <table id="requisitionPurpose" class="table table-striped table-bordered datatable nowrap" style="width:100%; font-size:13px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purpose</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requisitionPurpose as $key => $purpose)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $purpose->purpose }}</td>
                                    <td>{{ $purpose->description }}</td>
                                    <td>
                                        @if($purpose->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:void(0);" class="text-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editRequisitionPurposeModal{{ $purpose->id }}">
                                            <i class="fa fa-edit"></i>
                                        </a> |
                                        <form method="POST"
                                            action="{{ route('requisitions-purpose.destroy', encrypt($purpose->id)) }}"
                                            id="delete-req-purpose-form-{{ $key }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDeleteReqPurpose({{ $key }})" class="text-danger">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Edit Purpose Modal --}}
                                <div class="modal fade" id="editRequisitionPurposeModal{{ $purpose->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Requisition Purpose</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="{{ route('requisitions-purpose.update', $purpose->id) }}">
                                                    @csrf @method('PUT')
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <label class="form-label">Purpose <span class="text-danger">*</span></label>
                                                            <input type="text" name="purpose"
                                                                value="{{ old('purpose', $purpose->purpose) }}"
                                                                class="form-control form-control-sm" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Description</label>
                                                            <input type="text" name="description"
                                                                value="{{ old('description', $purpose->description) }}"
                                                                class="form-control form-control-sm">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Status</label>
                                                            <select name="active" class="form-select form-select-sm">
                                                                <option value="1" {{ $purpose->active == 1 ? 'selected' : '' }}>Active</option>
                                                                <option value="0" {{ $purpose->active == 0 ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <button type="submit" class="btn btn-success btn-sm">Update</button>
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

                </div>
            </div>
        </div>
    </div>
</div>

{{-- New Requisition Modal --}}
<div class="modal fade" id="vehicleRequisitionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Vehicle Requisition</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('vehicle-requisitions.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->fname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Purpose <span class="text-danger">*</span></label>
                            <select name="requisition_purpose_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Purpose --</option>
                                @foreach($requisitionPurpose as $purpose)
                                    <option value="{{ $purpose->id }}">{{ $purpose->purpose }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                            <select name="vehicle_type_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Type --</option>
                                @foreach($vehicleTypes as $vType)
                                    <option value="{{ $vType->id }}">{{ $vType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">From <span class="text-danger">*</span></label>
                            <input type="text" name="from" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">To <span class="text-danger">*</span></label>
                            <input type="text" name="to" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Pick Up <span class="text-danger">*</span></label>
                            <input type="text" name="pick_up" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="requisition_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Time From <span class="text-danger">*</span></label>
                            <input type="time" name="time_from" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Time To <span class="text-danger">*</span></label>
                            <input type="time" name="time_to" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Tolerance Duration</label>
                            <input type="text" name="tolerance_duration" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Details</label>
                            <input type="text" name="details" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-sm">Add</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- New Purpose Modal --}}
<div class="modal fade" id="RequisitionPurposeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Requisition Purpose</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('requisitions-purpose.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Purpose <span class="text-danger">*</span></label>
                            <input type="text" name="purpose" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description" class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select name="active" class="form-select form-select-sm">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success btn-sm">Add</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }

    function confirmDeleteReqPurpose(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-req-purpose-form-' + id).submit();
            }
        });
    }

    $(document).ready(function () {
        $('.tab-pane.active .datatable').DataTable({
            scrollX: true,
            responsive: false
        });

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            let target = $($(this).attr('href')).find('.datatable');
            if (!$.fn.DataTable.isDataTable(target)) {
                target.DataTable({ scrollX: true, responsive: false });
            } else {
                target.DataTable().columns.adjust();
            }
        });
    });
</script>
@endsection