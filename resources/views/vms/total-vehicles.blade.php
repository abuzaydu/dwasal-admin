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
                style="background: #f8f9fa; border-bottom: 2px solid #0d6efd;">
                <h6 style="margin: 0; font-weight: 600; color: #0d6efd;">
                    <i class="fa fa-car me-2"></i>{{ $page }}
                </h6>
                <span class="badge" style="background: #0d6efd; font-size: 0.85rem; padding: 6px 12px;">
                    {{ $vehicles->total() }} Total
                </span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-sm" style="font-size: 0.9rem;">
                    <thead style="background: #e9ecef;">
                        <tr>
                            <th style="padding: 10px;">#</th>
                            <th style="padding: 10px;">Vehicle Name</th>
                            <th style="padding: 10px;">Plate Number</th>
                            <th style="padding: 10px;">Type</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $i => $vehicle)
                        <tr style="transition: background 0.15s;"
                            onmouseover="this.style.background='#f0f4ff'"
                            onmouseout="this.style.background=''">
                            <td style="padding: 10px;">{{ $vehicles->firstItem() + $i }}</td>
                            <td style="padding: 10px; font-weight: 500;">{{ $vehicle->vehicle_name }}</td>
                            <td style="padding: 10px;">{{ $vehicle->plate_no ?? '-' }}</td>
                            <td style="padding: 10px;">{{ $vehicle->vehicleType->name ?? '-' }}</td>
                            <td style="padding: 10px;">
                                <span style="padding: 4px 10px; border-radius: 12px; font-size: 0.78rem; font-weight: 600;
                                    background: {{ $vehicle->status === 'Active' ? '#d1fae5' : '#e9ecef' }};
                                    color: {{ $vehicle->status === 'Active' ? '#065f46' : '#6c757d' }};">
                                    {{ $vehicle->status ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #adb5bd;">
                                <i class="fa fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                                No vehicles found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 15px;">{{ $vehicles->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection