@extends('layouts.vms')
@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row align-items-center">
            <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Vehicles Management</li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-8 col-md-6 col-sm-12 mb-2">
                <div class="d-flex flex-wrap gap-1 justify-content-md-end justify-content-start">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#fuelTypeModal">
                        <i class="fa fa-plus me-1"></i> Add Fuel Type
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#fuelStations">
                        <i class="fa fa-plus me-1"></i> Add Fuel Station
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addLicenseTypeModal">
                        <i class="fa fa-plus me-1"></i> Add Licence Type
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#drivers">
                        <i class="fa fa-plus me-1"></i> Add Drivers
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card radius-6">
                <div class="card-header pb-1 border-bottom">
                    <h6 class="card-title mb-0"><i class="fa fa-tint me-2 text-success"></i>New Refuel Record</h6>
                </div>
                <form method="POST" action="{{ route('refueling.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">

                        {{-- ── Section 1: Vehicle & Trip Info ── --}}
                        <p class="text-muted fw-semibold mb-2" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">
                            <i class="fa fa-car me-1"></i> Vehicle & Trip Info
                        </p>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Vehicle <span class="text-danger">*</span></label>
                                <select name="vehicle_id" required class="form-select form-select-sm">
                                    <option value="" disabled selected>-- Select Vehicle --</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->plate_no }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Driver <span class="text-danger">*</span></label>
                                <select name="driver_id" required class="form-select form-select-sm">
                                    <option value="" disabled selected>-- Select Driver --</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Odometer Reading <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="odometer" required
                                        class="form-control form-control-sm"
                                        placeholder="e.g. 12500.00">
                                    <span class="input-group-text">km</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- ── Section 2: Fuel Details ── --}}
                        <p class="text-muted fw-semibold mb-2" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">
                            <i class="fa fa-fire me-1"></i> Fuel Details
                        </p>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Fuel Type <span class="text-danger">*</span></label>
                                <select name="fuel_type_id" required class="form-select form-select-sm">
                                    <option value="" disabled selected>-- Select Fuel Type --</option>
                                    @foreach($fuel_types as $fuel_type)
                                        <option value="{{ $fuel_type->id }}">{{ $fuel_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Fuel Quantity (L) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="fuel_qty" id="fuel_qty" required
                                        class="form-control form-control-sm"
                                        placeholder="e.g. 50.00"
                                        oninput="calculateTotalCost()">
                                    <span class="input-group-text">L</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Price per Litre <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="price" id="price" required
                                        class="form-control form-control-sm"
                                        placeholder="e.g. 2500.00"
                                        oninput="calculateTotalCost()">
                                    <span class="input-group-text">TZS</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Total Cost</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" name="total_cost" id="total_cost"
                                        class="form-control form-control-sm" readonly
                                        style="background:#f8f9fa; font-weight:600;">
                                    <span class="input-group-text">TZS</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- ── Section 3: Fuel Station ── --}}
                        <p class="text-muted fw-semibold mb-2" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">
                            <i class="fa fa-map-marker me-1"></i> Fuel Station
                        </p>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                {{-- Toggle --}}
                                <div class="d-flex gap-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="station_mode"
                                            id="mode_existing" value="existing" checked onchange="toggleStationMode()">
                                        <label class="form-check-label" for="mode_existing">
                                            <i class="fa fa-list me-1"></i> Select Existing Station
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="station_mode"
                                            id="mode_new" value="new" onchange="toggleStationMode()">
                                        <label class="form-check-label" for="mode_new">
                                            <i class="fa fa-plus me-1"></i> Add New Station
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Existing station --}}
                           <div class="col-md-6" id="existing_station_block">
                                <select name="fuel_station_id" id="fuel_station_id" class="form-select form-select-sm">
                                    <option value="">-- Select Station --</option>
                                    @foreach($fuel_stations as $fuel_station)
                                        <option value="{{ $fuel_station->id }}">{{ $fuel_station->station_name }}</option>
                                    @endforeach
                                </select>
                           </div>

                            <div class="col-md-12" id="existing_station_hint">
                                <small class="text-muted">
                                    <i class="fa fa-info-circle me-1"></i>
                                    Can't find your station? Switch to <strong>Add New Station</strong> above.
                                </small>
                            </div>

                            {{-- New station inline --}}
                            <div class="col-md-12" id="new_station_block" style="display:none;">
                                <div class="border rounded p-3" style="background:#f8fffe;">
                                    <p class="text-success fw-semibold mb-2" style="font-size:12px;">
                                        <i class="fa fa-plus-circle me-1"></i> New Station Details
                                    </p>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label">Station Name <span class="text-danger">*</span></label>
                                            <input type="text" name="new_station_name" id="new_station_name"
                                                class="form-control form-control-sm"
                                                placeholder="e.g. Total Mikocheni">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Vendor <span class="text-danger">*</span></label>
                                            <select name="new_station_vendor_id" class="form-select form-select-sm">
                                                <option value="">-- Select Vendor --</option>
                                                @foreach($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Address</label>
                                            <input type="text" name="new_station_address"
                                                class="form-control form-control-sm"
                                                placeholder="Enter address">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Contact Person</label>
                                            <input type="text" name="new_station_contact_person"
                                                class="form-control form-control-sm"
                                                placeholder="Enter contact person">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Contact Number</label>
                                            <input type="text" name="new_station_contact_number"
                                                class="form-control form-control-sm"
                                                placeholder="Enter contact number">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- ── Section 4: Date, Time & Attachment ── --}}
                        <p class="text-muted fw-semibold mb-2" style="font-size:12px; text-transform:uppercase; letter-spacing:.5px;">
                            <i class="fa fa-calendar me-1"></i> Date, Time & Attachment
                        </p>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" required class="form-control form-control-sm">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" name="time" required class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Note</label>
                                <input type="text" name="note" class="form-control form-control-sm"
                                    placeholder="Enter note (optional)">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Document Attachment <span class="text-danger">*</span></label>
                                <input type="file" name="doc_attachment" accept=".pdf,.jpg,.jpeg,.png"
                                    class="form-control form-control-sm" required>
                                <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG</small>
                            </div>
                        </div>

                    </div>{{-- end card-body --}}

                    <div class="card-footer text-end border-top pt-3">
                        <a href="{{ url()->previous() }}" class="btn btn-warning btn-sm me-1">
                            <i class="fa fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-save me-1"></i> Save Refuel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Modal: Add Fuel Type ── --}}
        <div class="modal fade" id="fuelTypeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">New Fuel Type</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('fuel-types.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label">Fuel Type Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required placeholder="e.g. Diesel, Petrol"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-control form-control-sm"
                                        placeholder="Enter description">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="active" class="form-select form-select-sm">
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

        {{-- ── Modal: Add Fuel Station ── --}}
        <div class="modal fade" id="fuelStations" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Add Fuel Station</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('fuel-stations.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label">Station Name <span class="text-danger">*</span></label>
                                    <input type="text" name="station_name" required
                                        placeholder="Enter station name" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Vendor <span class="text-danger">*</span></label>
                                    <select name="vendor_id" required class="form-select form-select-sm">
                                        <option value="" disabled selected>-- Select Vendor --</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Person</label>
                                    <input type="text" name="contact_person" placeholder="Contact person"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" name="contact_number" placeholder="Contact number"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" name="address" placeholder="Enter address"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="active" class="form-select form-select-sm">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-sm">Save Station</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Modal: Add License Type ── --}}
        <div class="modal fade" id="addLicenseTypeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Add License Type</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('license-types.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label">License Type Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required class="form-control form-control-sm"
                                        placeholder="e.g. Class A, Class B, CDL">
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

        {{-- ── Modal: Add Driver ── --}}
        <div class="modal fade" id="drivers" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Add Driver</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('drivers.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" required class="form-control form-control-sm"
                                        placeholder="Enter full name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                    <input type="text" name="mobile" required class="form-control form-control-sm"
                                        placeholder="Enter mobile number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">License Type <span class="text-danger">*</span></label>
                                    <select name="license_type_id" required class="form-select form-select-sm">
                                        <option value="" disabled selected>-- Select License Type --</option>
                                        @foreach($license_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">License No <span class="text-danger">*</span></label>
                                    <input type="text" name="license_no" required class="form-control form-control-sm"
                                        placeholder="Enter license number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">License Issue Date <span class="text-danger">*</span></label>
                                    <input type="date" name="license_issue_date" required class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NID <span class="text-danger">*</span></label>
                                    <input type="text" name="nid" required class="form-control form-control-sm"
                                        placeholder="Enter national ID">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Join Date <span class="text-danger">*</span></label>
                                    <input type="date" name="join_date" required class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_birth" required class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Working Time Slot <span class="text-danger">*</span></label>
                                    <select name="working_time_slot" required class="form-select form-select-sm">
                                        <option value="" disabled selected>-- Select Slot --</option>
                                        <option value="Morning">Morning</option>
                                        <option value="Afternoon">Afternoon</option>
                                        <option value="Night">Night</option>
                                        <option value="Full Day">Full Day</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="is_active" class="form-select form-select-sm">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Present Address</label>
                                    <input type="text" name="present_address" class="form-control form-control-sm"
                                        placeholder="Enter present address">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Permanent Address</label>
                                    <input type="text" name="permanent_address" class="form-control form-control-sm"
                                        placeholder="Enter permanent address">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Driver Photo</label>
                                    <input type="file" name="driver_photo" accept="image/*"
                                        class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-sm">Save Driver</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- end row clearfix --}}

@endsection

@section('page-scripts')
<script>
    function calculateTotalCost() {
        var qty   = parseFloat(document.getElementById('fuel_qty').value)  || 0;
        var price = parseFloat(document.getElementById('price').value) || 0;
        document.getElementById('total_cost').value = (qty * price).toFixed(2);
    }

    function toggleStationMode() {
        var mode           = document.querySelector('input[name="station_mode"]:checked').value;
        var existingBlock  = document.getElementById('existing_station_block');
        var newBlock       = document.getElementById('new_station_block');
        var stationSelect  = document.getElementById('fuel_station_id');
        var newStationName = document.getElementById('new_station_name');

        if (mode === 'new') {
            existingBlock.style.display = 'none';
            newBlock.style.display      = 'block';
            stationSelect.required      = false;
            stationSelect.value         = '';
            newStationName.required     = true;
        } else {
            existingBlock.style.display = 'block';
            newBlock.style.display      = 'none';
            stationSelect.required      = true;
            newStationName.required     = false;
            newStationName.value        = '';
        }
    }
</script>
@endsection