@extends('layouts.vms')
@section('content')
    <script>

        function confirmDeleteFuelType(id){
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

        function editFuelType(id, name, description, active) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_active').value = active;

            document.getElementById('editFuelTypeForm').action = '/fuel-types/' + id ;

            var modal = new bootstrap.Modal(document.getElementById('editFuelTypeModal'));
            modal.show();
        }

        function editFuelStation(id, station_name, vendor_id, contact_person, contact_number, address, active) {
            document.getElementById('edit_station_name').value   = station_name;
            document.getElementById('edit_contact_person').value = contact_person;
            document.getElementById('edit_contact_number').value = contact_number;
            document.getElementById('edit_address').value        = address;
            document.getElementById('edit_active_station').value = active;

            document.getElementById('edit_vendor_id').value = vendor_id;

            document.getElementById('editFuelStationForm').action = '/fuel-stations/' + id;

            var modal = new bootstrap.Modal(document.getElementById('editFuelStationModal'));
            modal.show();
        }

        function confirmDeleteFuelStation(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-fs-form-' + id).submit();
                }
            });
        }

        // Edit Driver
        function editDriver(id, full_name, mobile, license_type_id, license_no,
            license_issue_date, nid, join_date, working_time_slot,
            date_of_birth, present_address, permanent_address, is_active, driver_photo) {

            document.getElementById('edit_full_name').value          = full_name;
            document.getElementById('edit_mobile').value             = mobile;
            document.getElementById('edit_license_type_id').value    = license_type_id;
            document.getElementById('edit_license_no').value         = license_no;
            document.getElementById('edit_license_issue_date').value = license_issue_date;
            document.getElementById('edit_nid').value                = nid;
            document.getElementById('edit_join_date').value          = join_date;
            document.getElementById('edit_working_time_slot').value  = working_time_slot;
            document.getElementById('edit_date_of_birth').value      = date_of_birth;
            document.getElementById('edit_present_address').value    = present_address;
            document.getElementById('edit_permanent_address').value  = permanent_address;
            document.getElementById('edit_is_active').value          = is_active;

            var photoPreview = document.getElementById('current_photo_preview');
            if (driver_photo && driver_photo !== '') {
                photoPreview.innerHTML = `<img src="/storage/${driver_photo}" 
                    style="width:80px; height:80px; object-fit:cover; border-radius:50%; border:1px solid #ddd;"
                    onerror="this.src='{{ asset('img/default.png') }}'">`;
            } else {
                photoPreview.innerHTML = `<img src="{{ asset('img/default.png') }}" 
                    style="width:80px; height:80px; object-fit:cover; border-radius:50%; border:1px solid #ddd;">`;
            }
            document.getElementById('editDriverForm').action = '/drivers/' + id;

            var modal = new bootstrap.Modal(document.getElementById('editDriverModal'));
            modal.show();
        }

        // Delete Driver
        function confirmDeleteDriver(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-driver-form-' + id).submit();
                }
            });
        }

        //edit license type
        function editLicenseType(id, name) {
            document.getElementById('edit_license_name').value = name;

            document.getElementById('editLicenseTypeForm').action = '/license-types/' + id;

            var modal = new bootstrap.Modal(document.getElementById('editLicenseTypeModal'));
            modal.show();
        }

        // Delete License Type
        function confirmDeleteLicenseType(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-license-form-' + id).submit();
                }
            });
        }
        // Auto calculate total cost
        document.addEventListener('DOMContentLoaded', function () {

            document.getElementById('fuel_qty').addEventListener('input', calculateTotal);
            document.getElementById('price').addEventListener('input', calculateTotal);

            document.getElementById('edit_fuel_qty').addEventListener('input', calculateEditTotal);
            document.getElementById('edit_price').addEventListener('input', calculateEditTotal);
        });

        function calculateTotal() {
            var qty   = parseFloat(document.getElementById('fuel_qty').value) || 0;
            var price = parseFloat(document.getElementById('price').value) || 0;
            document.getElementById('total_cost').value = (qty * price).toFixed(2);
        }

        function calculateEditTotal() {
            var qty   = parseFloat(document.getElementById('edit_fuel_qty').value) || 0;
            var price = parseFloat(document.getElementById('edit_price').value) || 0;
            document.getElementById('edit_total_cost').value = (qty * price).toFixed(2);
        }

        // Edit Refuel
        function editRefuel(id, vehicle_id, fuel_type_id, fuel_station_id, driver_id,
            odometer, fuel_qty, price, total_cost, date, time, note, doc_attachment) { 

            document.getElementById('edit_vehicle_id').value      = vehicle_id;
            document.getElementById('edit_fuel_type_id').value    = fuel_type_id;
            document.getElementById('edit_fuel_station_id').value = fuel_station_id;
            document.getElementById('edit_driver_id').value       = driver_id;
            document.getElementById('edit_odometer').value        = odometer;
            document.getElementById('edit_fuel_qty').value        = fuel_qty;
            document.getElementById('edit_price').value           = price;
            document.getElementById('edit_total_cost').value      = total_cost;
            document.getElementById('edit_date').value            = date;
            document.getElementById('edit_time').value            = time;
            document.getElementById('edit_note').value            = note;

            var previewDiv = document.getElementById('current_attachment_preview');
            if (doc_attachment && doc_attachment !== '') {
                var ext = doc_attachment.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png'].includes(ext)) {
                    previewDiv.innerHTML = `
                        <a href="/storage/${doc_attachment}" target="_blank">
                            <img src="/storage/${doc_attachment}" 
                                style="width:80px; height:80px; object-fit:cover; border:1px solid #ddd; border-radius:4px;">
                        </a>`;
                } else if (ext === 'pdf') {
                    previewDiv.innerHTML = `
                        <a href="/storage/${doc_attachment}" target="_blank" class="btn btn-sm btn-outline-danger">
                            <i class="fa fa-file-pdf-o"></i> View PDF
                        </a>`;
                }
            } else {
                previewDiv.innerHTML = '<span class="text-muted small">No attachment uploaded</span>';
            }

            document.getElementById('editRefuelForm').action = '/refueling/' + id;
            var modal = new bootstrap.Modal(document.getElementById('editRefuelModal'));
            modal.show();
        }

        // Delete Refuel
        function confirmDeleteRefuel(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-refuel-form-' + id).submit();
                }
            });
        }
    </script>
     <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>  
                    <li class="breadcrumb-item">Vehicles Managment</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <div class="d-flex justify-content-end align-items-center">
                    
                    <form class="dashform row g-1" action="{{ url('f-refueling') }}" method="POST" id="stockform">
                        @csrf
                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">

                        <div class="col-md-12">
                            <button type="button" class="btn btn-default pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                       </div>
                    </form>
                    <a href="{{ route('refueling.create')  }}" class="btn btn-primary btn-sm" >
                        <i class="fa fa-plus me-1"></i>  New Refuel
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item"><a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">Refuels</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_1">Fuel Types</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_2">Fuels Stations</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_3">License</a></li>
                         <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_4">Drivers</a></li>
                    </ul>
                    <div class="tab-content pt-2">
                       <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="table-responsive" id="vehicle-list">
                                <table id="refuels-table" class="table table-striped display nowrap datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Doc attchment</th>
                                            <th>Company</th>
                                            <th>User Name</th>
                                            <th>Vehicle</th>
                                            <th>Fuel Type</th>
                                            <th>Fuel Station</th>
                                            <th>Driver</th>
                                            <th>Ordo Meter</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total Cost</th>
                                            <th>Note</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($refuels as $refuel)
                                        <tr>
                                           <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if($refuel->doc_attachment)
                                                    <a href="{{ asset('storage/' . $refuel->doc_attachment) }}" target="_blank">
                                                        @php $ext = pathinfo($refuel->doc_attachment, PATHINFO_EXTENSION); @endphp
                                                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                            <img src="{{ asset('storage/' . $refuel->doc_attachment) }}" 
                                                                style="width:40px; height:40px; object-fit:cover;">
                                                        @elseif($ext == 'pdf')
                                                            <i class="fa fa-file-pdf-o" style="color:red; font-size:24px;"></i>
                                                        @endif
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary">No Attachment</span>
                                                @endif
                                            </td>
                                            <td>{{ $refuel->company->name }}</td>
                                            <td>{{ $refuel->user->first_name }}</td>
                                            <td>{{ $refuel->vehicle->plate_no }}</td>
                                            <td>{{ $refuel->fuelType->name }}</td>
                                            <td>{{ $refuel->fuelStation->station_name }}</td>
                                            <td>{{ $refuel->driver->full_name }}</td>
                                             <td>{{ $refuel->odometer }}</td>
                                            <td>{{ $refuel->fuel_qty }}</td>
                                            <td style="text-align: center;">{{ $refuel->price }}</td>
                                            <td>{{ $refuel->total_cost }}</td>
                                            <td>{{ $refuel->note }}</td>
                                            <td style="text-align: center;">
                                                <a href="javascript:;" onclick="editRefuel(
                                                    {{ $refuel->id }},
                                                    {{ $refuel->vehicle_id }},
                                                    {{ $refuel->fuel_type_id }},
                                                    {{ $refuel->fuel_station_id }},
                                                    {{ $refuel->driver_id }},
                                                    {{ $refuel->odometer }},
                                                    {{ $refuel->fuel_qty }},
                                                    {{ $refuel->price }},
                                                    {{ $refuel->total_cost }},
                                                    '{{ $refuel->date }}',
                                                    '{{ $refuel->time }}',
                                                    '{{ $refuel->note }}',
                                                    '{{ $refuel->doc_attachment }}' 
                                                    )">
                                                    <i class="fa fa-edit" style="color:blue;"></i>
                                                </a>
                                                <form method="POST" action="{{route('refueling.destroy' , encrypt($refuel->id))}}" id="delete-refuel-form-{{$refuel->id}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteRefuel({{$refuel->id}})">
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
                                <table id="fuel-types" class="table table-striped display nowrap datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($fuel_types as $fuel_type)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $fuel_type->name }}</td>
                                            <td>{{ $fuel_type->description }}</td>
                                            <td>
                                                @if($fuel_type->active)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                <span class="badge rounded-pill bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                <a href="javascript:;" onclick="editFuelType({{ $fuel_type->id }}, '{{ $fuel_type->name }}', '{{ $fuel_type->description }}', {{ $fuel_type->active ? 1 : 0 }})">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a>
                                                <form method="POST" action="{{route('fuel-types.destroy' , encrypt($fuel_type->id))}}" id="delete-vt-form-{{$fuel_type->id}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteFuelType({{$fuel_type->id}})">
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
                                <table id="fuel-stations-table" class="table table-striped display nowrap datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Station name</th>
                                            <th>Contact Person</th>
                                            <th>Contact Number</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($fuel_stations as $fuel_station)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><a href="">{{ $fuel_station->station_name }}</a></td>
                                            <td>{{ $fuel_station->contact_person }}</td>
                                            <td>{{ $fuel_station->contact_number }}</td>
                                            <td>{{ $fuel_station->address }}</td>
                                            <td>
                                                @if($fuel_station->active)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                <span class="badge rounded-pill bg-danger">In Active</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;"> 
                                                <a href="javascript:;" 
                                                    onclick="editFuelStation( {{ $fuel_station->id }},'{{ $fuel_station->station_name }}',{{ $fuel_station->vendor_id }}, '{{ $fuel_station->contact_person }}',
                                                    '{{ $fuel_station->contact_number }}','{{ $fuel_station->address }}', {{ $fuel_station->active ? 1 : 0 }} )">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                                </a>
                                                <form method="POST" action="{{route('fuel-stations.destroy' , encrypt($fuel_station->id))}}" id="delete-fs-form-{{$fuel_station->id}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteFuelStation({{$fuel_station->id}})">
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

                        <div class="tab-pane fade" id="tab_3">
                            <div class="table-responsive" id="license-types-table">
                                <table id="license-types-table" class="table table-striped display nowrap datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Company Name</th>
                                            <th>Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($license_types as $type)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $type->company->name }}</td>
                                            <td>{{ $type->name }}</td>
                                            <td style="text-align: center;">
                                               <a href="javascript:;" onclick="editLicenseType({{ $type->id }}, '{{ $type->name }}')">
                                                    <i class="fa fa-edit" style="color: blue;"></i>
                                               </a> 
                                                <form method="POST" action="{{route('license-types.destroy' , encrypt($type->id))}}" id="delete-license-form-{{$type->id}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteLicenseType({{$type->id}})">
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
                         <div class="tab-pane fade" id="tab_4">
                            <div class="table-responsive">
                                <table id="drivers-table" class="table table-striped display nowrap datatable" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Photo</th>
                                            <th>Full Name</th>
                                            <th>Mobile</th>
                                            <th>License Number</th>
                                            <th>License Type</th>
                                            <th>Nid</th>
                                            <th>License Issue Date</th>
                                            <th>Date Of Birth</th>
                                            <th>Pressent Address</th>
                                            <th>Permanent Address</th>
                                            <th>Status</th>
                                            <th style="text-align: center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($drivers as $driver)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ $driver->driver_photo ? asset('storage/' . $driver->driver_photo) : asset('img/default.png') }}" 
                                                    alt="{{ $driver->full_name }}"
                                                    style="width:45px; height:45px; object-fit:cover; border-radius:50%; border:2px solid #ddd;">
                                            </td>                                            <td>{{ $driver->full_name }}</td>
                                            <td>{{ $driver->mobile }}</td>
                                            <td>{{ $driver->license_no }}</td>
                                            <td>{{ $driver->licenseType->name ?? 'N/A'}}</td>
                                            <td>{{ $driver->nid }}</td>
                                            <td>{{ $driver->license_issue_date }}</td>
                                            <td><a href=""></a>{{ $driver->date_of_birth }}</td>
                                            <td>{{ $driver->present_address }}</td>
                                            <td>{{ $driver->permanent_address }}</td>
                                            <td>
                                                @if($driver->is_active)
                                                <span class="badge rounded-pill bg-success">Active</span>
                                                @else
                                                <span class="badge rounded-pill bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                <a href="javascript:;" onclick="editDriver(
                                                        {{ $driver->id }},
                                                        '{{ $driver->full_name }}',
                                                        '{{ $driver->mobile }}',
                                                        {{ $driver->license_type_id }},
                                                        '{{ $driver->license_no }}',
                                                        '{{ $driver->license_issue_date }}',
                                                        '{{ $driver->nid }}',
                                                        '{{ $driver->join_date }}',
                                                        '{{ $driver->working_time_slot }}',
                                                        '{{ $driver->date_of_birth }}',
                                                        '{{ $driver->present_address }}',
                                                        '{{ $driver->permanent_address }}',
                                                         {{ $driver->is_active ? 1 : 0 }},
                                                         '{{ $driver->driver_photo }}'
                                                    )">
                                                        <i class="fa fa-edit" style="color:blue;"></i>
                                                </a> 
                                                <form method="POST" action="{{route('drivers.destroy' , encrypt($driver->id))}}" id="delete-driver-form-{{$driver->id}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteDriver({{$driver->id}})">
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
    </div> <!-- end row -->

    <!--Modal for editing refuel -->
    <div class="modal fade" id="editRefuelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Refuel</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="editRefuelForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-4">
                                <label class="form-label">Vehicle <span style="color:red">*</span></label>
                                <select name="vehicle_id" id="edit_vehicle_id" required class="form-select form-select-sm select2">
                                    <option value="" disabled>-- Select Vehicle --</option>
                                     @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">
                                            {{ $vehicle->plate_no }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fuel Type <span style="color:red">*</span></label>
                                <select name="fuel_type_id" id="edit_fuel_type_id" required class="form-select form-select-sm select2">
                                    <option value="" disabled>-- Select Fuel Type --</option>
                                    @foreach($fuel_types as $fuel_type)
                                        <option value="{{ $fuel_type->id }}">{{ $fuel_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fuel Station <span style="color:red">*</span></label>
                                <select name="fuel_station_id" id="edit_fuel_station_id" required class="form-select form-select-sm select2">
                                    <option value="" disabled>-- Select Station --</option>
                                    @foreach($fuel_stations as $fuel_station)
                                        <option value="{{ $fuel_station->id }}">{{ $fuel_station->station_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Driver <span style="color:red">*</span></label>
                                <select name="driver_id" id="edit_driver_id" required class="form-select form-select-sm select2">
                                    <option value="" disabled>-- Select Driver --</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Odometer <span style="color:red">*</span></label>
                                <input type="number" step="0.01" name="odometer" id="edit_odometer" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fuel Quantity (L) <span style="color:red">*</span></label>
                                <input type="number" step="0.01" name="fuel_qty" id="edit_fuel_qty" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Price per Litre <span style="color:red">*</span></label>
                                <input type="number" step="0.01" name="price" id="edit_price" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Total Cost <span style="color:red">*</span></label>
                                <input type="number" step="0.01" name="total_cost" id="edit_total_cost" required
                                    class="form-control form-control-sm" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Date <span style="color:red">*</span></label>
                                <input type="date" name="date" id="edit_date" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Time <span style="color:red">*</span></label>
                                <input type="time" name="time" id="edit_time" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Note</label>
                                <input type="text" name="note" id="edit_note"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Document Attachment</label>
                                <input type="file" name="doc_attachment" accept=".pdf,.jpg,.jpeg,.png"
                                    class="form-control form-control-sm">
                                        <div id="current_attachment_preview" class="mb-2"></div>

                                <small class="text-muted">Leave empty to keep current attachment</small>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- modal for editing fuel type -->
    <div class="modal fade" id="editFuelTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Fuel Type</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editFuelTypeForm" action="">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <div class="row g-1 align-items-center">
                            <div class="col-md-12 pt-2">
                                <label class="form-label">Fuel Type <span style="color: red;">*</span></label>
                                <input type="text" name="name" id="edit_name" required 
                                    placeholder="Enter Fuel type name" 
                                    class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" id="edit_description" 
                                    class="form-control form-control-sm mb-1" 
                                    placeholder="Enter Description">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Status</label>
                                <select name="active" id="edit_active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12 pt-2">
                                <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                                <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for editing fuel station -->
    <div class="modal fade" id="editFuelStationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Fuel Station</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="editFuelStationForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-12">
                                <label class="form-label">Station Name <span style="color: red;">*</span></label>
                                <input type="text" name="station_name" id="edit_station_name" required
                                    placeholder="Enter station name"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Vendor <span style="color: red;">*</span></label>
                                <select name="vendor_id" id="edit_vendor_id" required class="form-select form-select-sm select2">
                                    <option value="" disabled>-- Select Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" id="edit_contact_person"
                                    placeholder="Enter contact person"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" id="edit_contact_number"
                                    placeholder="Enter contact number"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" id="edit_address"
                                    placeholder="Enter address"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Status</label>
                                <select name="active" id="edit_active_station" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for editing license type -->
    <div class="modal fade" id="editLicenseTypeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit License Type</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="editLicenseTypeForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-12">
                                <label class="form-label">License Type Name <span style="color:red">*</span></label>
                                <input type="text" name="name" id="edit_license_name" required
                                    class="form-control form-control-sm"
                                    placeholder="e.g. Class A, Class B, CDL">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

       <!-- Modal for editing  drivers -->
    <div class="modal fade" id="editDriverModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Driver</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="editDriverForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-6">
                                <label class="form-label">Full Name <span style="color:red">*</span></label>
                                <input type="text" name="full_name" id="edit_full_name" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mobile <span style="color:red">*</span></label>
                                <input type="text" name="mobile" id="edit_mobile" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">License Type <span style="color:red">*</span></label>
                                <select name="license_type_id" id="edit_license_type_id" required
                                    class="form-select form-select-sm select2">
                                    <option value="" disabled>-- Select License Type --</option>
                                    @foreach($license_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">License No <span style="color:red">*</span></label>
                                <input type="text" name="license_no" id="edit_license_no" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">License Issue Date <span style="color:red">*</span></label>
                                <input type="date" name="license_issue_date" id="edit_license_issue_date" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NID <span style="color:red">*</span></label>
                                <input type="text" name="nid" id="edit_nid" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Join Date <span style="color:red">*</span></label>
                                <input type="date" name="join_date" id="edit_join_date" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date of Birth <span style="color:red">*</span></label>
                                <input type="date" name="date_of_birth" id="edit_date_of_birth" required
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Working Time Slot <span style="color:red">*</span></label>
                                <select name="working_time_slot" id="edit_working_time_slot" required
                                    class="form-select form-select-sm">
                                    <option value="Morning">Morning</option>
                                    <option value="Afternoon">Afternoon</option>
                                    <option value="Night">Night</option>
                                    <option value="Full Day">Full Day</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="is_active" id="edit_is_active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Present Address</label>
                                <input type="text" name="present_address" id="edit_present_address"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Permanent Address</label>
                                <input type="text" name="permanent_address" id="edit_permanent_address"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Driver Photo</label>
                                <input type="file" name="driver_photo" accept="image/*"
                                    class="form-control form-control-sm">
                                <div id="current_photo_preview" class="mb-2"></div>
                                <small class="text-muted">Leave empty to keep current photo</small>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('page-scripts')
<script>
    $(document).ready(function(){
        $('.datatable').DataTable({
            paging: true,
            ordering: true,
            searching: true,
            responsive: true
        });
    });
</script>
@endsection
