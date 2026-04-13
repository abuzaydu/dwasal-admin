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
                style="background: #f8f9fa; border-bottom: 2px solid #fd7e14;">
                <h6 style="margin: 0; font-weight: 600; color: #fd7e14;">
                    <i class="fa fa-clock-o me-2"></i>{{ $page }}
                </h6>
                <span style="background: #fd7e14; color: #fff; padding: 6px 14px;
                             border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                    {{ $requests->total() }} Pending
                </span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-sm" style="font-size: 0.9rem;">
                    <thead style="background: #e9ecef;">
                        <tr>
                            <th style="padding: 10px;">#</th>
                            <th style="padding: 10px;">Requisition No</th>
                            <th style="padding: 10px;">Vehicle</th>
                            <th style="padding: 10px;">Driver</th>
                            <th style="padding: 10px;">Requested On</th>
                            <th style="padding: 10px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $i => $req)
                        <tr style="transition: background 0.15s;"
                            onmouseover="this.style.background='#fff8f0'"
                            onmouseout="this.style.background=''">
                            <td style="padding: 10px;">{{ $requests->firstItem() + $i }}</td>
                            <td style="padding: 10px; font-weight: 500;">{{ $req->requisition_no ?? '-' }}</td>
                            <td style="padding: 10px;">{{ $req->vehicle->vehicle_name ?? '-' }}</td>
                            <td style="padding: 10px;">{{ $req->driver->full_name ?? '-' }}</td>
                            <td style="padding: 10px; color: #6c757d;">{{ $req->created_at->format('Y-m-d') }}</td>
                            <td style="padding: 10px;">
                                <span style="padding: 4px 10px; border-radius: 12px; font-size: 0.78rem; font-weight: 600;
                                    background: #ffe5d0; color: #7b3500;">
                                    Awaiting Approval
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #adb5bd;">
                                <i class="fa fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 8px;"></i>
                                No pending requests found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 15px;">{{ $requests->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection