@extends('layouts.vms')
@section('content')

        <!--breadcrumb-->
        <div class="block-header pt-4">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Vehicle Management</li>
                        <li class="breadcrumb-item"><a href="{{ route('vms-expenses.index') }}">VMS Expenses</a></li>
                        <li class="breadcrumb-item active">{{ $page }}</li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 text-right">

                    @if($expense->status === 'Open' || $expense->status === 'In Progress' || $expense->status === 'Rejected')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="fa fa-plus"></i> Add Expense Item
                        </button>
                    @endif

                    @if($expense->status === 'In Progress')
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#closeTripModal">
                            <i class="fa fa-flag-checkered"></i> Close Trip
                        </button>
                    @endif

                    @if($expense->status === 'Pending')
                        <button type="button" class="btn btn-success btn-sm" onclick="confirmApprove('{{ $expense->id }}')">
                            <i class="fa fa-check"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fa fa-times"></i> Reject
                        </button>
                    @endif

                    @if($expense->status === 'Rejected')
                        <button type="button" class="btn btn-warning btn-sm" 
                            onclick="confirmResubmit('{{ $expense->id }}')">
                            <i class="fa fa-refresh"></i> Resubmit for Approval
                        </button>

                        <form id="resubmitForm" method="POST" 
                            action="{{ route('vms-expenses.close-trip', $expense->id) }}" 
                            style="display:none;">
                            @csrf
                            <input type="hidden" name="odometer_mileage" value="{{ $expense->odometer_mileage }}">
                            <input type="hidden" name="vehicle_rent" value="{{ $expense->vehicle_rent }}">
                            <input type="hidden" name="return_date" value="{{ now()->format('Y-m-d') }}">
                            <input type="hidden" name="remarks" value="{{ $expense->remarks }}">
                        </form>
                    @endif

                </div>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="row clearfix">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <div class="mb-3">
                            <span class="fw-bold">Trip Status: </span>
                            @if($expense->status === 'Open')
                                <span class="badge bg-info">Open</span>
                            @elseif($expense->status === 'In Progress')
                                <span class="badge bg-primary">In Progress</span>
                            @elseif($expense->status === 'Pending')
                                <span class="badge bg-warning text-dark">Pending Approval</span>
                            @elseif($expense->status === 'Approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($expense->status === 'Rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </div>

                        <ul class="nav nav-tabs" id="expenseTabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tripInfoTab">
                                    <i class="fa fa-info-circle"></i> Trip Info
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#itemsTab">
                                    <i class="fa fa-list"></i> Expense Items
                                    <span class="badge bg-primary">{{ $expense->items->count() }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#timelineTab">
                                    <i class="fa fa-clock-o"></i> Timeline
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content pt-3">

                            <div class="tab-pane fade show active" id="tripInfoTab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%">Trip No</th>
                                                <td>{{ $expense->trip_no }}</td>
                                            </tr>
                                            <tr>
                                                <th>Expense Group</th>
                                                <td>{{ $expense->exp_group }}</td>
                                            </tr>
                                            <tr>
                                                <th>Trip Type</th>
                                                <td>{{ $expense->tripType->trip_type ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Date</th>
                                                <td>{{ $expense->date }}</td>
                                            </tr>
                                            <tr>
                                                <th>Remarks</th>
                                                <td>{{ $expense->remarks ?? 'N/A' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <th width="40%">Driver</th>
                                                <td>{{ $expense->employee->fname ?? '' }} {{ $expense->employee->lname ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Employee ID</th>
                                                <td>{{ $expense->employee->emp_id ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Vehicle</th>
                                                <td>{{ $expense->vehicle->plate_no ?? 'N/A' }} - {{ $expense->vehicle->vehicle_name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Odometer</th>
                                                <td>{{ $expense->odometer_mileage > 0 ? number_format($expense->odometer_mileage, 2) : 'Not yet filled' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Vehicle Rent</th>
                                                <td>{{ $expense->vehicle_rent > 0 ? number_format($expense->vehicle_rent, 2) : 'Not yet filled' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="itemsTab">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Expense Type</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Total Price</th>
                                                @if($expense->status !== 'Approved')
                                                <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expense->items as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->expenseType->type ?? 'N/A' }}</td>
                                                <td>{{ number_format($item->quantity, 2) }}</td>
                                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                                <td>{{ number_format($item->total_price, 2) }}</td>
                                                @if($expense->status !== 'Approved')
                                                <td>
                                                    <a href="javascript:;" onclick="confirmDeleteItem('{{ $item->id }}')">
                                                        <i class="fa fa-trash" style="color:red"></i> Delete
                                                    </a>

                                                    <form id="deleteItemForm_{{ $item->id }}" method="POST"
                                                        action="{{ route('vms-expense-items.destroy', $item->id) }}" style="display:none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                                @endif
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="{{ $expense->status !== 'Approved' ? 4 : 3 }}" class="text-end fw-bold">Grand Total:</td>
                                                <td class="fw-bold">{{ number_format($expense->items->sum('total_price'), 2) }}</td>
                                                @if($expense->status !== 'Approved')<td></td>@endif
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                @if($expense->items->count() === 0)
                                    <div class="alert alert-info">No expense items added yet.</div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="timelineTab">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
                                        <span class="badge bg-info me-2">1</span>
                                        <strong>Trip Created</strong> —
                                        {{ $expense->created_at->format('Y-m-d H:i') }}
                                        <small class="text-muted">Status set to Open</small>
                                    </li>
                                    @if($expense->status !== 'Open')
                                    <li class="list-group-item">
                                        <span class="badge bg-primary me-2">2</span>
                                        <strong>Expense Items Added</strong> —
                                        <small class="text-muted">Status set to In Progress</small>
                                    </li>
                                    @endif
                                    @if(in_array($expense->status, ['Pending', 'Approved', 'Rejected']))
                                    <li class="list-group-item">
                                        <span class="badge bg-warning text-dark me-2">3</span>
                                        <strong>Trip Closed</strong> —
                                        <small class="text-muted">Submitted for approval. Status set to Pending</small>
                                    </li>
                                    @endif
                                    @if($expense->status === 'Approved')
                                    <li class="list-group-item">
                                        <span class="badge bg-success me-2">4</span>
                                        <strong>Approved</strong> —
                                        {{ $expense->updated_at->format('Y-m-d H:i') }}
                                        <small class="text-muted">Expense locked</small>
                                    </li>
                                    @endif
                                    @if($expense->status === 'Rejected')
                                    <li class="list-group-item">
                                        <span class="badge bg-danger me-2">4</span>
                                        <strong>Rejected</strong> —
                                        {{ $expense->updated_at->format('Y-m-d H:i') }}
                                        <br><small class="text-muted">Reason: {{ $expense->remarks }}</small>
                                    </li>
                                    @endif
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Expense Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('vms-expenses.store-item', $expense->id) }}">
                        @csrf
                        <div class="modal-body row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Expense Type <span style="color:red">*</span></label>
                                <select name="expense_type_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Expense Type --</option>
                                    @foreach($expenseTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Quantity <span style="color:red">*</span></label>
                                <input type="number" name="quantity" id="add_quantity" step="any" min="0"
                                    class="form-control form-control-sm" value="1" onchange="calculateAddTotal()" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Unit Price <span style="color:red">*</span></label>
                                <input type="number" name="unit_price" id="add_unit_price" step="any" min="0"
                                    class="form-control form-control-sm" value="0" onchange="calculateAddTotal()" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Total Price</label>
                                <input type="number" name="total_price" id="add_total_price" step="any"
                                    class="form-control form-control-sm" value="0" readonly>
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

        <div class="modal fade" id="closeTripModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Close Trip</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('vms-expenses.close-trip', $expense->id) }}">
                        @csrf
                        <div class="modal-body row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Odometer Mileage <span style="color:red">*</span></label>
                                <input type="number" name="odometer_mileage" step="any" min="0"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Vehicle Rent <span style="color:red">*</span></label>
                                <input type="number" name="vehicle_rent" step="any" min="0"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Return Date <span style="color:red">*</span></label>
                                <input type="date" name="return_date" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Final Remarks</label>
                                <textarea name="remarks" rows="2" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning btn-sm">Close Trip & Submit for Approval</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('vms-expenses.reject', $expense->id) }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-2">
                                <label class="form-label">Rejection Reason <span style="color:red">*</span></label>
                                <textarea name="rejection_reason" rows="3"
                                    class="form-control form-control-sm" required
                                    placeholder="Enter reason for rejection"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection
@section('page-scripts')
    <script>

        function calculateAddTotal() {
            var qty         = parseFloat(document.getElementById('add_quantity').value) || 0;
            var unitPrice   = parseFloat(document.getElementById('add_unit_price').value) || 0;
            document.getElementById('add_total_price').value = (qty * unitPrice).toFixed(2);
        }

        function confirmDeleteItem(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This expense item will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.value) {
                    document.getElementById('deleteItemForm_' + id).submit();
                }
            });
        }

        function confirmApprove(id) {
            Swal.fire({
                title: 'Approve this expense?',
                text: 'This action will lock the expense and cannot be undone!',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    document.getElementById('approveForm').submit();
                }
            });
        }
        function confirmResubmit(id) {
            Swal.fire({
                title: 'Resubmit for Approval?',
                text: 'Make sure you have corrected all issues before resubmitting!',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f6a821',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Resubmit!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    document.getElementById('resubmitForm').submit();
                }
            });
        }

    </script>

    <form id="approveForm" method="POST" action="{{ route('vms-expenses.approve', $expense->id) }}" style="display:none;">
        @csrf
    </form>

@endsection