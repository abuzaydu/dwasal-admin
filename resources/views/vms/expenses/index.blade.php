@extends('layouts.vms')
@section('content')

        <!--breadcrumb-->
        <div class="block-header pt-4">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Vehicle Management</li>
                        <li class="breadcrumb-item active">VMS Expenses</li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 text-right" id="tab-buttons">

                    <button type="button" class="btn btn-primary btn-sm trip-btn" data-bs-toggle="modal" data-bs-target="#addVmsExpenseModal">
                        <i class="fa fa-plus"></i> Create Trip
                    </button>
                    <button type="button" class="btn btn-success btn-sm expense-type-btn d-none" data-bs-toggle="modal" data-bs-target="#addExpenseTypeModal">
                        <i class="fa fa-plus"></i> Add Expense Type
                    </button>
                    <button type="button" class="btn btn-success btn-sm trip-type-btn d-none" data-bs-toggle="modal" data-bs-target="#addTripTypeModal">
                        <i class="fa fa-plus"></i> Add Trip Type
                    </button>
                </div>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="row mb-3" id="summary-cards">
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Open</div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ $expenses->where('status', 'Open')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-folder-open fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">In Progress</div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ $expenses->where('status', 'In Progress')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-spinner fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approval</div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ $expenses->where('status', 'Pending')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-clock-o fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-2">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                                <div class="h5 mb-0 font-weight-bold">
                                    {{ $expenses->where('status', 'Approved')->count() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--  Card with Tabs -->
        <div class="row clearfix">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <ul class="nav nav-tabs" id="mainTabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tripsTab" id="tab-trips">
                                    <i class="fa fa-car"></i> Trips
                                    <span class="badge bg-primary">{{ $expenses->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#expenseTypesTab" id="tab-expense-types">
                                    <i class="fa fa-tags"></i> Expense Types
                                    <span class="badge bg-secondary">{{ $expenseTypes->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tripTypesTab" id="tab-trip-types">
                                    <i class="fa fa-road"></i> Trip Types
                                    <span class="badge bg-secondary">{{ $tripTypes->count() }}</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content pt-3">

                            <div class="tab-pane fade show active" id="tripsTab">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Trip No</th>
                                                <th>Driver</th>
                                                <th>Vehicle</th>
                                                <th>Trip Type</th>
                                                <th>Exp Group</th>
                                                <th>Date</th>
                                                <th>Total Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expenses as $expense)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $expense->trip_no }}</td>
                                                <td>
                                                    {{ $expense->employee->fname ?? '' }}
                                                    {{ $expense->employee->lname ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $expense->vehicle->plate_no ?? 'N/A' }}
                                                    - {{ $expense->vehicle->vehicle_name ?? '' }}
                                                </td>
                                                <td>{{ $expense->tripType->trip_type ?? 'N/A' }}</td>
                                                <td>{{ $expense->exp_group }}</td>
                                                <td>{{ $expense->date }}</td>
                                                <td>{{ number_format($expense->items->sum('total_price'), 2) }}</td>
                                                <td>
                                                    @if($expense->status === 'Open')
                                                        <span class="badge bg-info">Open</span>
                                                    @elseif($expense->status === 'In Progress')
                                                        <span class="badge bg-primary">In Progress</span>
                                                    @elseif($expense->status === 'Pending')
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @elseif($expense->status === 'Approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($expense->status === 'Rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('vms-expenses.show', $expense->id) }}" class="text-info">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                    @if($expense->status === 'Open' || $expense->status === 'In Progress')
                                                        |
                                                        <a href="javascript:;" onclick="confirmDeleteExpense('{{ $expense->id }}')">
                                                            <i class="fa fa-trash" style="color:red"></i> Delete
                                                        </a>
                                                        <form id="deleteExpenseForm_{{ $expense->id }}" method="POST"
                                                            action="{{ route('vms-expenses.destroy', $expense->id) }}" style="display:none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="expenseTypesTab">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Expense Type</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expenseTypes as $expenseType)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $expenseType->type }}</td>
                                                <td>
                                                    @if($expenseType->active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $expenseType->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    <a href="javascript:;" onclick="openEditExpenseTypeModal(
                                                        '{{ $expenseType->id }}',
                                                        '{{ $expenseType->type }}',
                                                        '{{ $expenseType->active }}')">
                                                        <i class="fa fa-edit" style="color:blue"></i> Edit
                                                    </a>
                                                    |
                                                    <a href="javascript:;" onclick="confirmDeleteExpenseType('{{ $expenseType->id }}')">
                                                        <i class="fa fa-trash" style="color:red"></i> Delete
                                                    </a>
                                                    <form id="deleteExpenseTypeForm_{{ $expenseType->id }}" method="POST"
                                                        action="{{ route('expense-type.destroy', $expenseType->id) }}" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tripTypesTab">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered datatable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Trip Type</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tripTypes as $tripType)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $tripType->trip_type }}</td>
                                                <td>
                                                    @if($tripType->active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $tripType->created_at->format('Y-m-d') }}</td>
                                                <td>
                                                    <a href="javascript:;" onclick="openEditTripTypeModal(
                                                        '{{ $tripType->id }}',
                                                        '{{ $tripType->trip_type }}',
                                                        '{{ $tripType->active }}')">
                                                        <i class="fa fa-edit" style="color:blue"></i> Edit
                                                    </a>
                                                    |
                                                    <a href="javascript:;" onclick="confirmDeleteTripType('{{ $tripType->id }}')">
                                                        <i class="fa fa-trash" style="color:red"></i> Delete
                                                    </a>
                                                    <form id="deleteTripTypeForm_{{ $tripType->id }}" method="POST"
                                                        action="{{ route('trip-types.destroy', $tripType->id) }}" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
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


        <div class="modal fade" id="addVmsExpenseModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-plus"></i> Create Trip</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('vms-expenses.store') }}">
                        @csrf
                        <div class="modal-body row">

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Driver / Employee <span style="color:red">*</span></label>
                                <select name="employee_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">
                                            {{ $employee->fname }} {{ $employee->lname }}
                                            ({{ $employee->emp_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Vendor <span style="color:red">*</span></label>
                                <select name="vendor_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->vendor_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Vehicle <span style="color:red">*</span></label>
                                <select name="vehicle_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Vehicle --</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">
                                            {{ $vehicle->plate_no }} - {{ $vehicle->vehicle_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Trip Type <span style="color:red">*</span></label>
                                <select name="trip_type_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Trip Type --</option>
                                    @foreach($tripTypes as $tripType)
                                        @if($tripType->active)
                                            <option value="{{ $tripType->id }}">{{ $tripType->trip_type }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Expense Group <span style="color:red">*</span></label>
                                <input type="text" name="exp_group" class="form-control form-control-sm"
                                    placeholder="e.g. Dodoma Run" required>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Trip No <span style="color:red">*</span></label>
                                <input type="text" name="trip_no" class="form-control form-control-sm"
                                    placeholder="Auto genarated" readonly>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Departure Date <span style="color:red">*</span></label>
                                <input type="date" name="date" class="form-control form-control-sm" required>
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" rows="2" class="form-control form-control-sm"
                                    placeholder="Optional notes"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-sm">Create Trip</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addExpenseTypeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-tags"></i> Add Expense Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('expense-type.store') }}">
                        @csrf
                        <div class="modal-body row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Expense Type <span style="color:red">*</span></label>
                                <input type="text" name="type" class="form-control form-control-sm"
                                    placeholder="Enter expense type name" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Status</label>
                                <select name="active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
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

        <div class="modal fade" id="editExpenseTypeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Expense Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" id="editExpenseTypeForm" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-body row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Expense Type <span style="color:red">*</span></label>
                                <input type="text" name="type" id="edit_expense_type"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Status</label>
                                <select name="active" id="edit_expense_active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-sm">Update</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addTripTypeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-road"></i> Add Trip Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('trip-types.store') }}">
                        @csrf
                        <div class="modal-body row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Trip Type <span style="color:red">*</span></label>
                                <input type="text" name="trip_type" class="form-control form-control-sm"
                                    placeholder="Enter trip type name" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Status</label>
                                <select name="active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
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

        <div class="modal fade" id="editTripTypeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Trip Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" id="editTripTypeForm" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-body row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Trip Type <span style="color:red">*</span></label>
                                <input type="text" name="trip_type" id="edit_trip_type_name"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Status</label>
                                <select name="active" id="edit_trip_type_active" class="form-select form-select-sm">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-sm">Update</button>
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

    document.querySelectorAll('#mainTabs .nav-link').forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function(e) {
            var target = e.target.getAttribute('href');

            document.querySelectorAll('.trip-btn, .expense-type-btn, .trip-type-btn')
                .forEach(function(btn) { btn.classList.add('d-none'); });

            document.getElementById('summary-cards').classList.add('d-none');

            if (target === '#tripsTab') {
                document.querySelector('.trip-btn').classList.remove('d-none');
                document.getElementById('summary-cards').classList.remove('d-none');
            } else if (target === '#expenseTypesTab') {
                document.querySelector('.expense-type-btn').classList.remove('d-none');
            } else if (target === '#tripTypesTab') {
                document.querySelector('.trip-type-btn').classList.remove('d-none');
            }
        });
    });

    function openEditExpenseTypeModal(id, type, active) {
        document.getElementById('edit_expense_type').value   = type;
        document.getElementById('edit_expense_active').value = active;
        document.getElementById('editExpenseTypeForm').action = `/expense-type/${id}`;
        new bootstrap.Modal(document.getElementById('editExpenseTypeModal')).show();
    }

    function confirmDeleteExpenseType(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This expense type will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
                document.getElementById('deleteExpenseTypeForm_' + id).submit();
            }
        });
    }

    function openEditTripTypeModal(id, tripType, active) {
        document.getElementById('edit_trip_type_name').value   = tripType;
        document.getElementById('edit_trip_type_active').value = active;
        document.getElementById('editTripTypeForm').action     = `/trip-types/${id}`;
        new bootstrap.Modal(document.getElementById('editTripTypeModal')).show();
    }

    function confirmDeleteTripType(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This trip type will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
                document.getElementById('deleteTripTypeForm_' + id).submit();
            }
        });
    }

    function confirmDeleteExpense(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This trip and all its expense items will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.value) {
                document.getElementById('deleteExpenseForm_' + id).submit();
            }
        });
    }

</script>
@endsection