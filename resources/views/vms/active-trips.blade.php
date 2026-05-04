@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row">
        <div class="col-sm-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ url('f-vehicles-dash') }}">VMS Dashboard</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background: #f8f9fa; border-bottom: 2px solid #ffc107;">
                <h6 style="margin: 0; font-weight: 600; color: #856404;">
                    <i class="fa fa-road me-2"></i>{{ $page }}
                </h6>
                <span class="badge" style="background: #ffc107; color: #212529; font-size: 0.85rem; padding: 6px 12px;">
                    {{ $trips->total() }} Ongoing
                </span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-sm" style="font-size: 0.9rem;">
                    <thead style="background: #e9ecef;">
                        <tr>
                            <th style="padding: 10px;">#</th>
                            <th style="padding: 10px;">Trip No</th>
                            <th style="padding: 10px;">Vehicle</th>
                            <th style="padding: 10px;">Driver</th>
                            <th style="padding: 10px;">Start Time</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trips as $i => $trip)
                        <tr style="transition: background 0.15s;"
                            onmouseover="this.style.background='#fffbea'"
                            onmouseout="this.style.background=''">
                            <td style="padding: 10px;">{{ $trips->firstItem() + $i }}</td>
                            <td style="padding: 10px; font-weight: 500;">{{ $trip->trip_no }}</td>
                            <td style="padding: 10px;">{{ $trip->vehicleRequisition->vehicle->vehicle_name ?? '-' }}</td>
                            <td style="padding: 10px;">{{ $trip->vehicleRequisition->driver->full_name ?? '-' }}</td>
                            <td style="padding: 10px;">
                                {{ $trip->start_time ? \Carbon\Carbon::parse($trip->start_time)->format('Y-m-d H:i') : '-' }}
                            </td>
                            <td style="padding: 10px;">
                                <span style="padding: 4px 10px; border-radius: 12px; font-size: 0.78rem; font-weight: 600;
                                    background: #fff3cd; color: #856404;">
                                    Ongoing
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #adb5bd;">
                                <i class="fa fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                                No active trips found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 15px;">{{ $trips->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection