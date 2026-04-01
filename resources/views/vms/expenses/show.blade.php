@extends('layouts.vms')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a href="{{ url('vms-expenses') }}">VMS Expenses</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                @if($expense->status === 'Awaiting For Approval')
                    <div class="d-flex justify-content-end gap-2" style="max-width: 300px; margin-left:auto;"> 
                        <form method="POST" action="{{ route('approve-vms-expense', encrypt($expense->id)) }}" class="flex-fill m-0">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="fa fa-check me-1"></i> Approve
                            </button>
                        </form>

                        <button type="button"
                            class="btn btn-danger btn-sm flex-fill"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal">
                            <i class="fa fa-times me-1"></i> Reject
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

        <div class="row clearfix">
            <div class="col-xl-12">
                <div class="card radius-6 mb-3">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-1 fw-bold">
                                    <i class="fa fa-car me-2 text-primary"></i>
                                    Trip: {{ $expense->trip_no }}
                                </h5>
                                <span class="text-muted" style="font-size:13px;">
                                    Recorded by {{ $expense->first_name ?? '' }} {{ $expense->last_name ?? '' }}
                                    &nbsp;|&nbsp;
                                    {{ \Carbon\Carbon::parse($expense->created_at)->format('d M Y, h:i A') }}
                                </span>
                            </div>
                            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                @if($expense->status === 'Pending')
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">Pending</span>
                                @elseif($expense->status === 'Awaiting For Approval')
                                    <span class="badge bg-info fs-6 px-3 py-2">Awaiting Approval</span>
                                @elseif($expense->status === 'In Progress')
                                    <span class="badge bg-primary fs-6 px-3 py-2">In Progress</span>
                                @elseif($expense->status === 'Approved')
                                    <span class="badge bg-success fs-6 px-3 py-2">Approved</span>
                                @elseif($expense->status === 'Rejected')
                                    <span class="badge bg-danger fs-6 px-3 py-2">Rejected</span>
                                @elseif($expense->status === 'Closed')
                                    <span class="badge bg-secondary fs-6 px-3 py-2">Closed</span>
                                @else
                                    <span class="badge bg-light text-dark fs-6 px-3 py-2">{{ $expense->status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card radius-6 mb-3">
                            <div class="card-header border-bottom pb-2">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fa fa-info-circle me-2 text-primary"></i> Trip Details
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-sm align-middle mb-0">
                                        <tbody>
                                            <tr>
                                                @if ($expense->employee_id && $expense->vehicle_id)
                                                    <th style="width: 25%">Vehicle</th>
                                                    <td>
                                                        {{ $expense->plate_no ?? 'N/A' }}
                                                        @if($expense->vehicle_name) — {{ $expense->vehicle_name }} @endif
                                                    </td>

                                                    <th style="width: 25%">Employee </th>
                                                    <td>
                                                        {{ trim(($expense->fname ?? '') . ' ' . ($expense->lname ?? '')) ?: 'N/A' }}
                                                    </td>
                                                @endif
                                            </tr>

                                            <tr>
                                                <th>Vendor</th>
                                                <td>{{ $expense->vendor_name ?? 'N/A' }}</td>

                                                <th>Expense Date</th>
                                                <td>{{ $expense->date }}</td>
                                            </tr>

                                           @if ($expense->trip_type_id)
                                                <tr>
                                                    <th>Driver</th>
                                                    <td>{{ trim(($expense->fname ?? '') . ' ' . ($expense->lname ?? '')) ?: 'N/A' }}</td>
                                                    
                                                    <th>Trip Type</th>
                                                    <td>{{ $expense->trip_type ?? 'N/A' }}</td>       
                                                </tr>
                                           @endif

                                            @if($expense->remarks)
                                            <tr>
                                                <th>Expense Group</th>
                                                <td>{{ $expense->exp_group ?? '-' }}</td>

                                                <th>Remarks</th>
                                                <td colspan="3">{{ $expense->remarks }}</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card radius-6 mb-3">
                            <div class="card-header border-bottom pb-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fa fa-list me-2 text-success"></i> Expense Items
                                </h6>
                                <span class="badge bg-success">{{ $expenseItems->count() }} item(s)</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width:5%;">#</th>
                                                <th>Expense Type</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Unit Price</th>
                                                <th class="text-center">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($expenseItems as $index => $item)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $item->expense_type }}</td>
                                                <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                                                <td class="text-center">{{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-center fw-semibold">{{ number_format($item->total_price, 2) }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No expense items found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th></th>
                                                <th class="text-end fw-bold">TOTAL</th>
                                                <th class="text-center fw-bold">{{ number_format($expenseItems->sum('quantity'), 2) }}</th>
                                                <th></th>
                                                <th class="text-center fw-bold text-success" style="font-size:15px;">
                                                    {{ number_format($expenseItems->sum('total_price'), 2) }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card radius-6 mb-3">
                            <div class="card-header border-bottom pb-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fa fa-paperclip me-2 text-warning"></i> Attached Documents
                                </h6>
                                <span class="badge bg-warning text-dark">{{ $attachments->count() }} file(s)</span>
                            </div>
                            <div class="card-body">
                                @if($attachments->count() > 0)
                                    <div class="row g-3">
                                        @foreach($attachments as $attachment)
                                            @php
                                                $ext = strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION));
                                                $url = asset('storage/' . $attachment->file_path);
                                            @endphp

                                            <div class="col-12">
                                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                    <a href="{{ $url }}" target="_blank">
                                                        <img src="{{ $url }}"
                                                            alt="Attachment"
                                                            class="img-fluid rounded border shadow-sm w-100"
                                                            style="max-height:300px; object-fit:cover; cursor:pointer;">
                                                    </a>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <small class="text-muted">{{ strtoupper($ext) }} Image</small>
                                                        <a href="{{ $url }}" download class="btn btn-outline-success btn-sm">
                                                            <i class="fa fa-download me-1"></i> Download
                                                        </a>
                                                    </div>
                                                @elseif($ext === 'pdf')
                                                    <div class="border rounded p-2 mb-2 text-center" style="background:#f8f9fa;">
                                                        <i class="fa fa-file-pdf-o text-danger" style="font-size:36px;"></i>
                                                        <p class="text-muted mt-1 mb-0" style="font-size:12px;">PDF Document</p>
                                                    </div>
                                                    <iframe src="{{ $url }}"
                                                            width="100%"
                                                            height="280px"
                                                            class="border rounded mb-2">
                                                    </iframe>
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                            <i class="fa fa-external-link me-1"></i> Open
                                                        </a>
                                                        <a href="{{ $url }}" download class="btn btn-outline-success btn-sm">
                                                            <i class="fa fa-download me-1"></i> Download
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="border rounded p-3 text-center" style="background:#f8f9fa;">
                                                        <i class="fa fa-file text-secondary" style="font-size:36px;"></i>
                                                        <p class="text-muted mt-1 mb-2" style="font-size:12px;">
                                                            {{ strtoupper($ext) }} Document
                                                        </p>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                                <i class="fa fa-external-link me-1"></i> Open
                                                            </a>
                                                            <a href="{{ $url }}" download class="btn btn-outline-success btn-sm">
                                                                <i class="fa fa-download me-1"></i> Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(!$loop->last)
                                                    <hr class="my-3">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="py-4 text-center">
                                        <i class="fa fa-file-o text-muted" style="font-size:48px;"></i>
                                        <p class="text-muted mt-2 mb-0">No documents attached.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card radius-6">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">                      
                        <small class="text-muted">
                            Last updated: {{ \Carbon\Carbon::parse($expense->updated_at)->format('d M Y, h:i A') }}
                        </small>

                        <div class="d-flex gap-2" style="min-width: 200px;">
                            
                            @if(in_array($expense->status, ['Pending', 'Awaiting For Approval']))
                                <a href="{{ route('vms-expenses.edit', encrypt($expense->id)) }}"
                                class="btn btn-primary btn-sm flex-fill text-center">
                                    <i class="fa fa-edit me-1"></i> Edit
                                </a>
                            @endif

                            <a href="{{ url('vms-expenses') }}" class="btn btn-secondary btn-sm flex-fill text-center">
                                <i class="fa fa-arrow-left me-1"></i> Back
                            </a>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger"><i class="fa fa-times me-1"></i> Reject Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('reject-vms-expense') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $expense->id }}">
                        <div class="modal-body">
                            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="remarks" rows="3" required
                                class="form-control form-control-sm"
                                placeholder="Enter reason for rejecting this expense..."></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger btn-sm">Submit Rejection</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

@endsection