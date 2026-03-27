@extends('layouts.vms')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <a href="{{ route('maintenance.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus me-1"></i> New Maintenance
                </a>
                <button type="button" class="btn btn-info btn-sm ms-1" id="btn-open-type-modal">
                    <i class="fa fa-list-alt me-1"></i> Maintenance Types
                </button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active show" data-bs-toggle="tab" href="#tab_vehicle_maintenance">Vehicle Maintenance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_maintenance_types">Maintenance Types</a>
                        </li>
                    </ul>

                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active" id="tab_vehicle_maintenance" role="tabpanel">
                            <div class="table-responsive">
                                <table id="maintenance-table" class="table table-striped display nowrap datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Code</th>
                                            <th>Vehicle</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th style="text-align:center;">Charge</th>
                                            <th style="text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($maintenances as $maintenance)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $maintenance->maintenance_code }}</td>
                                                <td>
                                                    {{ $maintenance->vehicle->plate_no ?? '-' }}
                                                    @if(!empty($maintenance->vehicle) && !empty($maintenance->vehicle->vehicle_name))
                                                        <span class="text-muted">({{ $maintenance->vehicle->vehicle_name }})</span>
                                                    @endif
                                                </td>
                                                <td>{{ $maintenance->maintenanceType->type ?? '-' }}</td>
                                                <td>{{ $maintenance->date }}</td>
                                                <td>{{ $maintenance->priority }}</td>
                                                <td>
                                                    @php $s = strtolower((string) $maintenance->status); @endphp
                                                    @if($s === 'completed')
                                                        <span class="badge rounded-pill bg-success">Completed</span>
                                                    @elseif($s === 'in progress' || $s === 'in_progress')
                                                        <span class="badge rounded-pill bg-info text-dark">In Progress</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-warning text-dark">{{ $maintenance->status }}</span>
                                                    @endif
                                                </td>
                                                <td style="text-align:center;">{{ number_format((float) ($maintenance->charge ?? 0), 2) }}</td>
                                                <td style="text-align:center;">
                                                    <a href="{{ route('maintenance.show', encrypt($maintenance->id)) }}" class="text-primary me-2" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('maintenance.edit', encrypt($maintenance->id)) }}" class="text-info me-2" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('maintenance.destroy', encrypt($maintenance->id)) }}" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="javascript:;" onclick="if(confirm('Delete this maintenance record?')) { this.closest('form').submit(); }" title="Delete" class="text-danger">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($maintenances->isEmpty())
                                <div class="alert alert-light mt-3 mb-0" role="alert">
                                    No maintenance records found.
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="tab_maintenance_types" role="tabpanel">
                            <div class="table-responsive">
                                <table id="maintenance-types-table" class="table table-striped display nowrap datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th style="text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($maintenanceTypes as $type)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $type->type }}</td>
                                                <td>
                                                    @if($type->active)
                                                        <span class="badge rounded-pill bg-success">Active</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td style="text-align:center;">
                                                    <a href="javascript:;" class="text-info me-2"
                                                       onclick="openEditTypeModal({{ $type->id }}, @json($type->type), {{ $type->active ? 1 : 0 }})"
                                                       title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('maintenance-types.destroy', encrypt($type->id)) }}" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a href="javascript:;" class="text-danger"
                                                           onclick="if(confirm('Deactivate this maintenance type?')) { this.closest('form').submit(); }"
                                                           title="Deactivate">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($maintenanceTypes->isEmpty())
                                <div class="alert alert-light mt-3 mb-0" role="alert">
                                    No maintenance types found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Maintenance Type Modal --}}
    <div class="modal fade" id="maintenanceTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="maintenanceTypeModalTitle"><i class="fa fa-list-alt me-1"></i> Add Maintenance Type</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('maintenance-types.store') }}" id="maintenanceTypeForm">
                    @csrf
                    <input type="hidden" name="_method" value="POST" id="maintenanceTypeMethod">
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <input type="text" name="type" id="mt_type" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Status</label>
                                <select name="active" id="mt_active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        $(document).ready(function () {
            $('.datatable').DataTable({
                paging: true,
                ordering: true,
                searching: true,
                responsive: true,
                autoWidth: false
            });

            $('#btn-open-type-modal').on('click', function () {
                var tab = document.querySelector('a[href="#tab_maintenance_types"]');
                if (tab) tab.click();
                var modal = new bootstrap.Modal(document.getElementById('maintenanceTypeModal'));
                // reset to add mode
                document.getElementById('maintenanceTypeModalTitle').innerText = 'Add Maintenance Type';
                document.getElementById('maintenanceTypeForm').action = "{{ route('maintenance-types.store') }}";
                document.getElementById('maintenanceTypeMethod').value = 'POST';
                document.getElementById('mt_type').value = '';
                document.getElementById('mt_active').value = '1';
                modal.show();
            });

            // Handle deep link: /maintenance?tab=types&openTypeModal=1
            const params = new URLSearchParams(window.location.search);
            if (params.get('tab') === 'types') {
                const tab = document.querySelector('a[href="#tab_maintenance_types"]');
                if (tab) tab.click();
            }
            if (params.get('openTypeModal') === '1') {
                const tab = document.querySelector('a[href="#tab_maintenance_types"]');
                if (tab) tab.click();
                const modal = new bootstrap.Modal(document.getElementById('maintenanceTypeModal'));
                modal.show();
            }
        });

        function openEditTypeModal(id, type, active) {
            var tab = document.querySelector('a[href="#tab_maintenance_types"]');
            if (tab) tab.click();

            document.getElementById('maintenanceTypeModalTitle').innerText = 'Edit Maintenance Type';
            document.getElementById('mt_type').value = type || '';
            document.getElementById('mt_active').value = String(active ?? 1);
            document.getElementById('maintenanceTypeForm').action = '/maintenance-types/' + id;
            document.getElementById('maintenanceTypeMethod').value = 'PUT';

            var modal = new bootstrap.Modal(document.getElementById('maintenanceTypeModal'));
            modal.show();
        }
    </script>
@endsection
