@extends('layouts.vms')
@section('content')
<div class="block-header pt-2 pb-1">
    <div class="row align-items-center">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ url('vehicles') }}">Vehicles</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 text-end">
            <a href="{{ url('vehicles') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show py-1 mb-2" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close py-0" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card radius-6 shadow-sm">
            <div class="card-header py-2 bg-light border-bottom">
                <h6 class="card-title mb-0">
                    <i class="fa fa-car me-2 text-primary"></i>
                    Register New Vehicle
                </h6>
            </div>
            <form method="POST" id="vehicle-create-form" action="{{ route('vehicles.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body py-3">
                    {{-- Identification Section --}}
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="bg-light px-2 py-1 rounded mb-2">
                                <small class="text-primary fw-semibold"><i class="fa fa-tag me-1"></i> Identification</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Plate Number <span class="text-danger">*</span></label>
                            <input type="text" name="plate_no" class="form-control form-control-sm" value="{{ old('plate_no') }}" required placeholder="T 123 ABC">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Vehicle Name</label>
                            <input type="text" name="vehicle_name" class="form-control form-control-sm" value="{{ old('vehicle_name') }}" placeholder="e.g. Toyota Hilux">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Chassis Number</label>
                            <input type="text" name="chassis_no" class="form-control form-control-sm" value="{{ old('chassis_no') }}" placeholder="Chassis/VIN">
                        </div>
                    </div>

                    {{-- Classification Section --}}
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="bg-light px-2 py-1 rounded mb-2">
                                <small class="text-primary fw-semibold"><i class="fa fa-gears me-1"></i> Classification</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Vehicle Type <span class="text-danger">*</span></label>
                            <select name="vehicle_type_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Type --</option>
                                @foreach($vehtypes as $t)
                                <option value="{{ $t->id }}" {{ old('vehicle_type_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Ownership <span class="text-danger">*</span></label>
                            <select name="ownership_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Ownership --</option>
                                @foreach($ownerships as $o)
                                <option value="{{ $o->id }}" {{ old('ownership_id') == $o->id ? 'selected' : '' }}>{{ $o->type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Capacity <span class="text-danger">*</span></label>
                            <input type="text" name="capacity" class="form-control form-control-sm" value="{{ old('capacity') }}" required placeholder="e.g. 2000">
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Unit <span class="text-danger">*</span></label>
                            <select name="uom" class="form-select form-select-sm" required>
                                <option value="">-- Unit --</option>
                                @foreach($units as $u)
                                <option value="{{ $u->unit_name }}" {{ old('uom') == $u->unit_name ? 'selected' : '' }}>{{ $u->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Department</label>
                            <select name="department_id" class="form-select form-select-sm">
                                <option value="">-- Select Dept --</option>
                                @foreach($departments as $d)
                                <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Registration & Media Section --}}
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="bg-light px-2 py-1 rounded mb-2">
                                <small class="text-primary fw-semibold"><i class="fa fa-calendar me-1"></i> Registration & Media</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Registration Date</label>
                            <input type="date" name="reg_date" class="form-control form-control-sm" value="{{ old('reg_date') }}">
                        </div>
                        <div class="col-md-5 mb-2">
                            <label class="form-label fw-semibold mb-0 small">Vehicle Image</label>
                            <input type="file" name="vehicle_picture" class="form-control form-control-sm" accept="image/*">
                            <div class="form-text small text-muted mt-0">Optional. JPEG, PNG, WebP. Max 5 MB</div>
                        </div>
                    </div>
                </div>{{-- end card-body --}}

                <div class="card-footer text-end border-top py-2 bg-light">
                    <a href="{{ url('vehicles') }}" class="btn btn-sm btn-secondary me-1">
                        <i class="fa fa-times me-1"></i> Cancel
                    </a>
                    <button type="button" id="nextBtn" class="btn btn-sm btn-primary d-none">
                        <i class="fa fa-forward me-1"></i> Next: Upload Documents
                    </button>
                    <button type="button" id="saveBtn" class="btn btn-sm btn-success">
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
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        nextBtn?.addEventListener('click', function () {
            Swal.fire({
                title: 'Continue?',
                text: 'You will be redirected to upload required documents.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Continue',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.action = "{{ route('vehicles.prepare-documents') }}";
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
