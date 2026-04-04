@extends('layouts.vms')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
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

                <form class="dashform" action="{{ url('f-vehicles') }}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <button type="button" class="btn btn-default btn-sm w-auto" id="reportrange"
                        style="white-space: nowrap;">
                        <i class="fa fa-calendar"></i>
                        <span id="reportrange-label" class="mx-1"></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </form>

                <a href="{{ route('vehicles.create') }}"
                    class="btn btn-primary btn-sm w-auto"
                    style="white-space: nowrap; font-size: 13px; padding: 4px 10px;">
                    <i class="fa fa-plus-square me-1"></i> New Vehicle
                </a>

                <button type="button"
                    class="btn btn-success btn-sm w-auto"
                    style="white-space: nowrap; font-size: 13px; padding: 4px 10px;"
                    data-bs-toggle="modal" data-bs-target="#newTypeModal">
                    <i class="fa fa-plus me-1"></i> Add Vehicle Type
                </button>

                <button type="button"
                    class="btn btn-secondary btn-sm w-auto"
                    style="white-space: nowrap; font-size: 13px; padding: 4px 10px;"
                    data-bs-toggle="modal" data-bs-target="#newOwnModal">
                    <i class="fa fa-plus me-1"></i> Add Ownership Type
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
                            Vehicles List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab_1">
                            Vehicle Types
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab_2">
                            Ownership Types
                        </a>
                    </li>
                </ul>

                <div class="tab-content pt-2">

                    {{-- TAB 1: Vehicles List --}}
                    <div class="tab-pane fade show active table-responsive" id="tab_0" role="tabpanel">
                        <table id="vehicles" class="table table-striped table-bordered nowrap data-table"
                            style="width:100%; font-size:13px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Plate Number</th>
                                    <th>Vehicle Name</th>
                                    <th>Type</th>
                                    <th>Ownership</th>
                                    <th class="text-center">Capacity</th>
                                    <th>Reg. Date</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicles as $key => $vehicle)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <a href="{{ route('vehicles.show', encrypt($vehicle->id)) }}">
                                            {{ $vehicle->plate_no }}
                                        </a>
                                    </td>
                                    <td>{{ $vehicle->vehicle_name }}</td>
                                    <td>{{ $vehicle->type }}</td>
                                    <td>{{ $vehicle->ownership }}</td>
                                    <td class="text-center">{{ $vehicle->capacity }} {{ $vehicle->uom }}</td>
                                    <td>{{ date('d/m/Y', strtotime($vehicle->reg_date)) }}</td>
                                    <td>
                                        @if($vehicle->status === 'Available')
                                            <span class="badge bg-success">Available</span>
                                        @elseif($vehicle->status === 'UnAvailable')
                                            <span class="badge bg-danger">Unavailable</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $vehicle->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <a href="{{ route('vehicles.edit', encrypt($vehicle->id)) }}" class="text-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        |
                                        <form method="POST"
                                            action="{{ route('vehicles.destroy', encrypt($vehicle->id)) }}"
                                            id="delete-form-{{ $key }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDelete({{ $key }})"
                                                class="text-danger">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- TAB 2: Vehicle Types --}}
                    <div class="tab-pane fade table-responsive" id="tab_1">
                        <table id="vehicle-types" class="table table-striped table-bordered nowrap data-table"
                            style="width:100%; font-size:13px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehtypes as $key => $type)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <a href="{{ route('vehicle-types.show', encrypt($type->id)) }}">
                                            {{ $type->name }}
                                        </a>
                                    </td>
                                    <td>{{ $type->description }}</td>
                                    <td>
                                        @if($type->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($type->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($type->updated_at)->format('d-m-Y') }}</td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <a href="{{ route('vehicle-types.edit', encrypt($type->id)) }}" class="text-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        |
                                        <form method="POST"
                                            action="{{ route('vehicle-types.destroy', encrypt($type->id)) }}"
                                            id="delete-vt-form-{{ $key }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDeleteVehicleType({{ $key }})"
                                                class="text-danger">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane fade table-responsive" id="tab_2">
                        <table id="ownerships" class="table table-striped table-bordered nowrap data-table"
                            style="width:100%; font-size:13px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Updated</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ownerships as $key => $owner)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <a href="{{ route('ownerships.show', encrypt($owner->id)) }}">
                                            {{ $owner->type }}
                                        </a>
                                    </td>
                                    <td>{{ $owner->description }}</td>
                                    <td>
                                        @if($owner->active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($owner->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($owner->updated_at)->format('d-m-Y') }}</td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <a href="{{ route('ownerships.edit', encrypt($owner->id)) }}" class="text-primary">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        |
                                        <form method="POST"
                                            action="{{ route('ownerships.destroy', encrypt($owner->id)) }}"
                                            id="delete-ot-form-{{ $key }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDeleteOwnership({{ $key }})"
                                                class="text-danger">
                                                <i class="fa fa-trash"></i>
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

<div class="modal fade" id="newTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">New Vehicle Type</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('vehicle-types.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                            <input type="text" name="name" required
                                placeholder="Enter Vehicle type name"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description"
                                placeholder="Enter Description"
                                class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        {{ trans('navmenu.btn_save') }}
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">
                        {{ trans('navmenu.btn_cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="newOwnModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">New Ownership Type</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('ownerships.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label">Ownership Type <span class="text-danger">*</span></label>
                            <input type="text" name="type" required
                                placeholder="Enter Ownership type"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <input type="text" name="description"
                                placeholder="Enter Description"
                                class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">
                        {{ trans('navmenu.btn_save') }}
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">
                        {{ trans('navmenu.btn_cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    $(document).ready(function () {

        $('.data-table').DataTable({
            scrollX: true,
            responsive: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [
                { orderable: false, targets: [-1] }
            ]
        });

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
        });

    });

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

    function confirmDeleteVehicleType(id) {
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
                document.getElementById('delete-vt-form-' + id).submit();
            }
        });
    }

    function confirmDeleteOwnership(id) {
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
                document.getElementById('delete-ot-form-' + id).submit();
            }
        });
    }
</script>
@endsection