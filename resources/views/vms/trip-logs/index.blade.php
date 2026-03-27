@extends('layouts.vms')

@section('content')

<!-- Breadcrumb -->
<div class="block-header pt-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
            <i class="fa fa-list-alt text-primary me-2"></i> Requisition Trip Logs
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tripLogsTable" class="table table-bordered table-striped table-hover w-100">
                <thead >
                    <tr>
                        <th>#</th>
                        <th>Trip No</th>
                        <th>Employee</th>
                        <th>Vehicle</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Start Odometer</th>
                        <th>End Odometer</th>
                        <th>Distance (km)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tripLogs as $index => $log)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $log->trip_no }}</td>
                        <td>{{ $log->vehicleRequisition->employee->fname ?? 'N/A' }}</td>
                        <td>{{ $log->vehicleRequisition->vehicle->plate_no ?? 'N/A' }}</td>
                        <td>{{ $log->start_time ?? '-' }}</td>
                        <td>{{ $log->end_time ?? '-' }}</td>
                        <td>{{ $log->start_odometer ? number_format($log->start_odometer, 2) . ' km' : '-' }}</td>
                        <td>{{ $log->end_odometer ? number_format($log->end_odometer, 2) . ' km' : '-' }}</td>
                        <td>
                            @if($log->start_odometer && $log->end_odometer)
                                {{ number_format($log->end_odometer - $log->start_odometer, 2) }} km
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($log->status == 'Pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($log->status == 'On Trip')
                                <span class="badge bg-info">On Trip</span>
                            @elseif($log->status == 'Completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($log->status == 'Cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">{{ $log->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('requisition-trip-logs.show', $log->vehicle_requisition_id) }}" title="View">
                                <i class="fa fa-eye" style="color: blue"></i>
                            </a>
                            @if ($log->status !== 'Completed')
                                <a href="{{ route('requisition-trip-logs.create', $log->vehicle_expense_id) }}" title="add expense">
                                    <i class="fa fa-plus" style="color: green"></i>Add Expense
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    @section('page-scripts')
        <script>
            $(document).ready(function () {
                $('#tripLogsTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[0, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [10] } 
                    ],
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ trip logs",
                        emptyTable: "No trip logs found",
                        zeroRecords: "No matching trip logs found"
                    }
                });
            });
        </script>
    @endsection
@endsection

