@extends('layouts.vms')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
        <!--breadcrumb-->
        <div class="block-header pt-4">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Vehicle Management</li>
                        <li class="breadcrumb-item active">{{$page}}</li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                    <a href="{{ route('vms-expenses.create')  }}" class="btn btn-primary btn-sm" >
                        <i class="fa fa-plus me-1"></i> Create New Expense
                        </a> 
                </div>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="row clearfix">
            <div class="col-md-12 mx-auto">
                <div class="card radius-10">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-new2" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#expensesTab" role="tab">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list font-18 me-1'></i></div>
                                        <div class="tab-title">VMS Expenses With No Trip Logs</div>
                                        <span class="badge bg-secondary ms-1">{{ $expenses1->count() }}</span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#expensesTab1" role="tab">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-list font-18 me-1'></i></div>
                                        <div class="tab-title">VMS Expenses With Trip Logs</div>
                                        <span class="badge bg-secondary ms-1">{{ $expenses->count() }}</span>
                                    </div>
                                </a>
                            </li>
                        
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#expenseTypesTab" role="tab">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-tags font-18 me-1'></i></div>
                                        <div class="tab-title">Expense Types</div>
                                        <span class="badge bg-secondary ms-1">{{ $expenseTypes->count() }}</span>
                                    </div>
                                </a>
                            </li>

                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#tripTypesTab" role="tab">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='fa fa-road font-18 me-1'></i></div>
                                        <div class="tab-title">Trip Types</div>
                                        <span class="badge bg-secondary ms-1">{{ $tripTypes->count() }}</span>
                                    </div>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content py-3">
                            <div class="tab-pane fade show active table-responsive" id="expensesTab" role="tabpanel">
                                <table class="table table-striped table-bordered datatable" id="expensesTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Trip No</th>
                                            <th>Vendor</th>
                                            <th>Exp Group</th>
                                            <th>Date</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenses1 as $expense)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $expense->trip_no }}</td>
                                            <td>{{ $expense->vendor->vendor_name  ?? ''}}</td>
                                            <td>{{ $expense->exp_group ?? '-' }}</td>
                                            <td>{{ $expense->date }}</td>
                                            <td>{{ number_format($expense->items->sum('total_price'), 2) }}</td>
                                            <td>
                                                @if($expense->status === 'Pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($expense->status === 'Awaiting For Approval')
                                                    <span class="badge bg-info">Awaiting Approval</span>
                                                @elseif($expense->status === 'In Progress')
                                                    <span class="badge bg-primary">In Progress</span>
                                                @elseif($expense->status === 'Approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($expense->status === 'Rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @elseif($expense->status === 'Closed')
                                                    <span class="badge bg-secondary">Closed</span>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $expense->status }}</span>
                                                @endif
                                            </td>
                                        <td>
                                                <a href="{{ route('vms-expenses.show', encrypt($expense->id)) }}" class="text-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                @if(in_array($expense->status, ['Awaiting For Approval', 'Rejected']))
                                                    <a href="{{ route('vms-expenses.edit', encrypt($expense->id)) }}" class="text-primary ms-2">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif

                                                @if($expense->status == 'Awaiting For Approval')
                                                    <a href="javascript:;" onclick="confirmDeleteExpense('{{ $expense->id }}')" class="ms-2">
                                                        <i class="fa fa-trash text-danger"></i>
                                                    </a>

                                                    <form id="deleteExpenseForm_{{ $expense->id }}" method="POST"
                                                        action="{{ route('vms-expenses.destroy', encrypt($expense->id)) }}"
                                                        style="display:none;">
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

                            <div class="tab-pane fade table-responsive" id="expensesTab1" role="tabpanel">
                                <table class="table table-striped table-bordered datatable" id="expensesTable1">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Vendor</th>
                                            <th>Driver</th>
                                            <th>Vehicle</th>
                                            <th>Trip Type</th>
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
                                            <td>{{ $expense->vendor->vendor_name  ?? ''}}</td>
                                            <td>{{ $expense->tripLog->vehicleRequisition->driver->full_name ?? ''}}</td>
                                            <td>{{ $expense->vehicle->plate_no ?? 'N/A' }} {{ $expense->vehicle_name ? '- ' . $expense->vehicle_name : '' }}</td>
                                            <td>{{ $expense->tripType->trip_type ?? 'N/A' }}</td>
                                            <td>{{ $expense->date }}</td>
                                            <td>{{ number_format($expense->items->sum('total_price'), 2) }}</td>
                                            <td>
                                                @if($expense->status === 'Pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($expense->status === 'Awaiting For Approval')
                                                    <span class="badge bg-info">Awaiting Approval</span>
                                                @elseif($expense->status === 'In Progress')
                                                    <span class="badge bg-primary">In Progress</span>
                                                @elseif($expense->status === 'Approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($expense->status === 'Rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @elseif($expense->status === 'Closed')
                                                    <span class="badge bg-secondary">Closed</span>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $expense->status }}</span>
                                                @endif
                                            </td>
                                        <td>
                                                <a href="{{ route('vms-expenses.show', encrypt($expense->id)) }}" class="text-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                @if(in_array($expense->status, ['Awaiting For Approval', 'Rejected']))
                                                    <a href="{{ route('vms-expenses.edit', encrypt($expense->id)) }}" class="text-primary ms-2">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif

                                                @if($expense->status == 'Awaiting For Approval')
                                                    <a href="javascript:;" onclick="confirmDeleteExpense('{{ $expense->id }}')" class="ms-2">
                                                        <i class="fa fa-trash text-danger"></i>
                                                    </a>

                                                    <form id="deleteExpenseForm_{{ $expense->id }}" method="POST"
                                                        action="{{ route('vms-expenses.destroy', encrypt($expense->id)) }}"
                                                        style="display:none;">
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

                            <div class="tab-pane fade table-responsive" id="expenseTypesTab" role="tabpanel">
                                <table class="table table-striped table-bordered datatable" id="expenseTypesTable">
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
                                                    '{{ addslashes($expenseType->type) }}',
                                                    '{{ $expenseType->active }}')">
                                                    <i class="fa fa-edit" style="color:blue"></i> Edit
                                                </a>
                                                |
                                                <a href="javascript:;" onclick="confirmDeleteExpenseType('{{ $expenseType->id }}')">
                                                    <i class="fa fa-trash" style="color:red"></i> Delete
                                                </a>
                                                <form id="deleteExpenseTypeForm_{{ $expenseType->id }}" method="POST"
                                                    action="{{ route('expense-type.destroy', $expenseType->id) }}"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade table-responsive" id="tripTypesTab" role="tabpanel">
                                <table class="table table-striped table-bordered datatable" id="tripTypesTable">
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
                                                    '{{ addslashes($tripType->trip_type) }}',
                                                    '{{ $tripType->active }}')">
                                                    <i class="fa fa-edit" style="color:blue"></i> Edit
                                                </a>
                                                |
                                                <a href="javascript:;" onclick="confirmDeleteTripType('{{ $tripType->id }}')">
                                                    <i class="fa fa-trash" style="color:red"></i> Delete
                                                </a>
                                                <form id="deleteTripTypeForm_{{ $tripType->id }}" method="POST"
                                                    action="{{ route('trip-types.destroy', $tripType->id) }}"
                                                    style="display:none;">
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
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label">Expense Type <span class="text-danger">*</span></label>
                                    <input type="text" name="type" id="edit_expense_type"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="active" id="edit_expense_active" class="form-select form-select-sm">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
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
                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label class="form-label">Trip Type <span class="text-danger">*</span></label>
                                    <input type="text" name="trip_type" id="edit_trip_type_name"
                                        class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="active" id="edit_trip_type_active" class="form-select form-select-sm">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
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
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(function () {
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = userlang === 'en'
                ? "{{ asset('assets/vendor/libs/English.json') }}"
                : "{{ asset('assets/vendor/libs/Swahili.json') }}";

            $('#expensesTable').DataTable({ scrollX: true, language: { url: languageUrl } });
            $('#expenseTypesTable').DataTable({ scrollX: true, language: { url: languageUrl } });
            $('#tripTypesTable').DataTable({ scrollX: true, language: { url: languageUrl } });
        });

        function confirmDeleteExpense(id) {
            Swal.fire({
                title: "{{trans('navmenu.are_you_sure_delete')}}",
                text: "{{trans('navmenu.no_revert')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
                if (result.value) {
                    document.getElementById('deleteExpenseForm_' + id).submit();
                }
            });
        }

        function openEditExpenseTypeModal(id, type, active) {
            document.getElementById('edit_expense_type').value   = type;
            document.getElementById('edit_expense_active').value = active;
            document.getElementById('editExpenseTypeForm').action = '/expense-type/' + id;
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
            document.getElementById('editTripTypeForm').action     = '/trip-types/' + id;
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
    </script>
@endsection