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

    <div class="card mb-3">
        <div class="card-header">
            <h6 class="fw-bold mb-0">
                <i class="fa fa-info-circle text-primary me-2"></i> Requisition Trip Details
            </h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <tbody>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Employee</th>
                        <td>{{ $trip->vehicleRequisition->employee->fname ?? '-' }}</td>
                        <th class="text-muted fw-semibold bg-light" style="width: 20%;">Trip No</th>
                        <td style="width: 30%;">{{ $trip->trip_no ?? 'N/A' }}</td>
                       
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light" style="width: 20%;">start Time</th>
                        <td>{{ $trip->start_time ?? 'N/A' }}</td>
                        <th class="text-muted fw-semibold bg-light">End Time</th>
                        <td>{{ $trip->end_time ?? 'N/A' }}</td>
                        
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Start Odometer</th>
                        <td>{{ $trip->start_odometer ?? '-' }}</td>
                        <th class="text-muted fw-semibold bg-light">End odometer</th>
                        <td>{{ $trip->end_odometer ?? '-'}}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Remark</th>
                        <td>{{ $trip->remarks ?? '-'}}</td>
                        <th class="text-muted fw-semibold bg-light">Vehicle</th>
                        <td>{{ $trip->vehicleRequisition->vehicle->vehicle_name ?? ''}}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Assigned Driver</th>
                        <td>{{ $trip->vehicleRequisition->driver->full_name ?? 'N/A' }}</td>
                        <th class="text-muted fw-semibold bg-light">Vehicle plate number</th>
                        <td>{{ $trip->vehicleRequisition->vehicle->plate_no ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Status</th>
                        <td>
                            @if($trip->status == 'In Progress')
                                <span class="badge bg-warning">In Progress</span>
                            @elseif($trip->status == 'Completed')
                                <span class="badge bg-success">Completed</span>
                            @else
                                <span class="badge bg-secondary">{{ $trip->status }}</span>
                            @endif
                        </td>
                    </tr>

                    @if($trip->status == 'Rejected' && $trip->rejection_reason)
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Rejection Reason</th>
                        <td colspan="3" class="text-danger">{{ $trip->rejection_reason ?? '' }}</td>
                    </tr>
                    @endif

                    @if($trip->details)
                    <tr>
                        <th class="text-muted fw-semibold bg-light">Details</th>
                        <td colspan="3">{{ $trip->details ?? ''}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Last updated: {{ \Carbon\Carbon::parse($trip->updated_at)->format('d M Y, h:i A') }}
            </small>
            <div class="d-flex gap-2">
                @php
                    $tripLog = \App\Models\RequisitionTripLog::where('vehicle_requisition_id', $trip->id)->first();
                @endphp
                @if($trip->status == 'In Progress' && (!$tripLog || !$tripLog->end_time))
                    <a class="btn btn-primary btn-sm" style="font-size:0.75rem;padding:0.25rem 0.6rem;"
                    data-bs-toggle="modal" data-bs-target="#closeTripModal{{ $trip->id }}">
                        <i class="fa-solid fa-circle-xmark"></i> close Trip
                    </a>
                @elseif($trip->status == 'Rejected')
                    <a class="btn btn-warning btn-sm" style="font-size:0.75rem;padding:0.25rem 0.6rem;"
                    href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editVehicleRequisitionModal{{ $trip->id }}">
                        <i class="fa fa-edit me-1"></i> Resubmit
                    </a>
                
                @elseif($trip->status == 'Approved' && (!$tripLog || !$tripLog->start_time))
                    <a class="btn btn-primary btn-sm" style="font-size:0.75rem;padding:0.25rem 0.6rem;"
                    href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#startTripModal{{ $trip->id }}">
                        <i class="fa fa-play me-1"></i> Start Trip
                    </a>
                @endif
            </div>
        </div>
    </div>
    <!-- Modal to close trip -->
    <div class="modal fade" id="closeTripModal{{ $trip->id }}" tabindex="-1" aria-labelledby="closeTripModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closeTripModalLabel">Close Trip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('trip.end', $trip->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="end_odometer" class="form-label">End Odometer <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('end_odometer') is-invalid @enderror" 
                                    id="end_odometer" name="end_odometer" required 
                                    min="{{ $trip->start_odometer ?? 0 }}" placeholder="Enter end odometer reading">
                            
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                        id="remarks" name="remarks" rows="3" placeholder="Enter any remarks"></textarea>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Close Trip</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

@endsection