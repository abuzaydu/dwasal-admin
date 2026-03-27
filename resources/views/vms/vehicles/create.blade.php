@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row align-items-center">
        <div class="col-lg-6 col-md-6 col-sm-12 mb-2">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ url('vehicles') }}">Vehicles</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 mb-2"></div>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card radius-6">
            <div class="card-header pb-1 border-bottom">
                <h6 class="card-title mb-0">
                    <i class="fa fa-car me-2 text-success"></i>
                    Register New Vehicle
                </h6>
            </div>
            <form method="POST" id="vehicle-create-form" action="{{ route('vehicles.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                    <p class="text-muted fw-semibold mb-2" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">
                        <i class="fa fa-car me-1"></i> Vehicle Details
                    </p>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" name="plate_no" class="form-control form-control-sm py-1" value="{{ old('plate_no') }}" required placeholder="e.g. T 123 ABC">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Vehicle Name</label>
                            <input type="text" name="vehicle_name" class="form-control form-control-sm py-1" value="{{ old('vehicle_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Chassis Number</label>
                            <input type="text" name="chassis_no" class="form-control form-control-sm py-1" value="{{ old('chassis_no') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Vehicle Type <span class="text-danger">*</span></label>
                            <select name="vehicle_type_id" class="form-select form-select-sm py-1" required>
                                <option value="">-- Select --</option>
                                @foreach($vehtypes as $t)
                                <option value="{{ $t->id }}" {{ old('vehicle_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Ownership <span class="text-danger">*</span></label>
                            <select name="ownership_id" class="form-select form-select-sm py-1" required>
                                <option value="">-- Select --</option>
                                @foreach($ownerships as $o)
                                <option value="{{ $o->id }}" {{ old('ownership_id') == $o->id ? 'selected' : '' }}>{{ $o->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Capacity <span class="text-danger">*</span></label>
                            <input type="text" name="capacity" class="form-control form-control-sm py-1" value="{{ old('capacity') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Unit <span class="text-danger">*</span></label>
                            <select name="uom" class="form-select form-select-sm py-1" required>
                                <option value="">--</option>
                                @foreach($units as $u)
                                <option value="{{ $u->unit_name }}" {{ old('uom') == $u->unit_name ? 'selected' : '' }}>{{ $u->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Department</label>
                            <select name="department_id" class="form-select form-select-sm py-1">
                                <option value="">-- Select --</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Registration Date</label>
                            <input type="date" name="reg_date" class="form-control form-control-sm py-1" value="{{ old('reg_date') }}">
                        </div>
                    </div>
                </div>{{-- end card-body --}}
                    <div class="card-footer text-end border-top pt-3">
                        <a href="{{ url('vehicles') }}" class="btn btn-warning btn-sm me-1">
                            <i class="fa fa-times me-1"></i> Cancel
                        </a>
                        <button type="button" id="nextBtn" class="btn btn-primary btn-sm d-none">
                            <i class="fa fa-forward me-1"></i> Next: Upload Documents
                        </button>
                        <button type="button" id="saveBtn" class="btn btn-success btn-sm">
                            <i class="fa fa-save me-1"></i> Save Vehicle
                        </button>
                    </div>
                </form>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('vehicle-create-form');
        const ownershipSelect = form?.querySelector('select[name="ownership_id"]');
        const nextBtn = document.getElementById('nextBtn');
        const saveBtn = document.getElementById('saveBtn');

        function toggleButtons() {
            const ownershipId = ownershipSelect ? ownershipSelect.value : '';
            const needsDocsNow = String(ownershipId) === '1';

            if (needsDocsNow) {
                nextBtn?.classList.remove('d-none');
                saveBtn?.classList.add('d-none');
            } else {
                nextBtn?.classList.add('d-none');
                saveBtn?.classList.remove('d-none');
            }
        }

        toggleButtons();
        ownershipSelect?.addEventListener('change', toggleButtons);

        saveBtn?.addEventListener('click', function () {
            Swal.fire({
                title: 'Save Vehicle?',
                text: 'This ownership type does not require legal documents immediately. You can add them later if needed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        nextBtn?.addEventListener('click', function () {
            form.action = "{{ route('vehicles.prepare-documents') }}";
            form.submit();
        });
    });
</script>
@endsection
