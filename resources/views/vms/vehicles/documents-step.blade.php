@extends('layouts.vms')
@section('content')
<div class="block-header pt-2 pb-1">
    <div class="row align-items-center">
        <div class="col-lg-8 col-md-8 col-sm-12">
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ url('vehicles') }}">Vehicles</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles.create') }}">Register Vehicle</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 text-end">
            <a href="{{ route('vehicles.create') }}" class="btn btn-warning btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger py-1 mb-1">{{ session('error') }}</div>
@endif

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card radius-6">
            <div class="card-header py-2 border-bottom">
                <h6 class="card-title mb-0">
                    <i class="fa fa-file-pdf-o me-2 text-success"></i>
                    Upload Required Vehicle Documents
                </h6>
            </div>
            <form method="POST" action="{{ route('vehicles.documents.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body py-2">
                    <div class="alert alert-info py-1 mb-3" style="line-height:1.2;">
                        <strong>Vehicle:</strong> {{ $pendingVehicle['plate_no'] }}
                        <span class="mx-2">|</span>
                        <strong>Ownership:</strong> COMPANY ASSET
                    </div>

                    {{-- Small Cards Row --}}
                    <div class="row g-2">
                        @foreach([
                            ['key' => 'registration', 'name' => 'Vehicle Registration Card (TRA)', 'doc' => 'doc_registration', 'icon' => 'fa-address-card-o'],
                            ['key' => 'road_license', 'name' => 'Road License (TRA)', 'doc' => 'doc_road_license', 'icon' => 'fa-road'],
                            ['key' => 'inspection', 'name' => 'Vehicle Inspection Certificate (Police)', 'doc' => 'doc_inspection', 'icon' => 'fa-check-circle-o'],
                        ] as $item)
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0 radius-6">
                                <div class="card-header bg-light py-2 border-bottom-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fa {{ $item['icon'] }} text-success me-2 fa-lg"></i>
                                        <strong class="small text-dark">{{ $item['name'] }}</strong>
                                    </div>
                                </div>
                                <div class="card-body py-2 px-3">
                                    {{-- PDF File --}}
                                    <div class="mb-2">
                                        <label class="form-label small mb-0 fw-semibold text-dark">PDF File <span class="text-danger">*</span></label>
                                        <input type="file" name="{{ $item['doc'] }}" class="form-control form-control-sm py-0" accept=".pdf" required>
                                    </div>

                                    {{-- Issue & Expiry Row --}}
                                    <div class="row g-1 mb-2">
                                        <div class="col-6">
                                            <label class="form-label small mb-0 fw-semibold text-dark">Issue Date</label>
                                            <input type="date" name="doc_{{ $item['key'] }}_issue" class="form-control form-control-sm py-0" value="{{ old('doc_' . $item['key'] . '_issue') }}" required>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-0 fw-semibold text-dark">Expiry Date</label>
                                            <input type="date" name="doc_{{ $item['key'] }}_expiry" class="form-control form-control-sm py-0" value="{{ old('doc_' . $item['key'] . '_expiry') }}" required>
                                        </div>
                                    </div>

                                    {{-- Charge & Commission Row --}}
                                    <div class="row g-1">
                                        <div class="col-6">
                                            <label class="form-label small mb-0 fw-semibold text-dark">Charge Paid (TZS)</label>
                                            <input type="number" step="0.01" min="0" name="doc_{{ $item['key'] }}_charge_paid" class="form-control form-control-sm py-0" value="{{ old('doc_' . $item['key'] . '_charge_paid', '0') }}" placeholder="0.00">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small mb-0 fw-semibold text-dark">Commission (TZS)</label>
                                            <input type="number" step="0.01" min="0" name="doc_{{ $item['key'] }}_commission" class="form-control form-control-sm py-0" value="{{ old('doc_' . $item['key'] . '_commission', '0') }}" placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>{{-- end card-body --}}

                <div class="card-footer text-end border-top py-2">
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="fa fa-save me-1"></i> Save Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
