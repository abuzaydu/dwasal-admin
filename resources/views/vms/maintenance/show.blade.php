@extends('layouts.vms')

@section('content')
    <div class="block-header pt-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-8 col-sm-12 mb-2">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('maintenance') }}">Maintenance</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 mb-2 text-end">
                <a href="{{ route('maintenance.edit', encrypt($maintenance->id)) }}" class="btn btn-info btn-sm me-1">
                    <i class="fa fa-edit me-1"></i> Edit
                </a>
                <a href="{{ url('maintenance') }}" class="btn btn-warning btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card radius-6">
                <div class="card-header pb-0 border-bottom">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-file-text-o me-2 text-success"></i> {{ $maintenance->maintenance_code }}
                    </h6>
                </div>

                <div class="card-body" style="padding: 0.75rem;">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Vehicle</div>
                            <div class="fw-semibold">{{ $maintenance->vehicle->plate_no ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Maintenance Type</div>
                            <div class="fw-semibold">{{ $maintenance->maintenanceType->type ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Employee</div>
                            <div class="fw-semibold">{{ $maintenance->employee->fname ?? '' }} {{ $maintenance->employee->lname ?? '' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Date</div>
                            <div class="fw-semibold">{{ $maintenance->date }}</div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Priority</div>
                            <div class="fw-semibold">{{ $maintenance->priority }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Status</div>
                            <div class="fw-semibold">{{ $maintenance->status }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Req Type</div>
                            <div class="fw-semibold">{{ $maintenance->req_type }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Charge</div>
                            <div class="fw-semibold">{{ number_format((float) ($maintenance->charge ?? 0), 2) }}</div>
                        </div>

                        <div class="col-md-12">
                            <div class="text-muted small mb-1">Service Title</div>
                            <div class="fw-semibold">{{ $maintenance->service_title }}</div>
                        </div>

                        @if(!empty($maintenance->remarks))
                            <div class="col-md-12">
                                <div class="text-muted small mb-1">Remarks</div>
                                <div>{{ $maintenance->remarks }}</div>
                            </div>
                        @endif
                    </div>

                    <hr class="my-3" />

                    <div class="row g-2">
                        <div class="col-md-12">
                            <h6 class="mb-1"><i class="fa fa-cubes me-1 text-primary"></i> Parts / Items</h6>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:60px;">#</th>
                                            <th>Part</th>
                                            <th style="width:130px; text-align:center;">Qty</th>
                                            <th style="width:170px; text-align:center;">Unit Price</th>
                                            <th style="width:160px; text-align:center;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($maintenance->items as $item)
                                            @php
                                                $partNo = $item->part->part_no ?? '';
                                                $partName = $item->part->part_name ?? '';
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $partNo }} {{ $partName }}</td>
                                                <td style="text-align:center;">{{ $item->qty }}</td>
                                                <td style="text-align:center;">{{ number_format((float) ($item->unit_price ?? 0), 2) }}</td>
                                                <td style="text-align:center;">{{ number_format((float) ($item->total_price ?? ($item->qty * $item->unit_price)), 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3" />

                    <div class="row g-2">
                        <div class="col-md-12">
                            <h6 class="mb-1"><i class="fa fa-camera me-1 text-primary"></i> Photos</h6>
                        </div>
                        <div class="col-md-12">
                            @if($maintenance->photos->isEmpty())
                                <div class="alert alert-light py-2 mb-0">No photos uploaded.</div>
                            @else
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($maintenance->photos as $photo)
                                        <div class="border rounded p-1" style="width: 120px;">
                                            <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="photo" style="width:100%; height:80px; object-fit:cover; border-radius:4px;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

