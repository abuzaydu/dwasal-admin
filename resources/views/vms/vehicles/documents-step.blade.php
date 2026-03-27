@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row align-items-center">
        <div class="col-lg-8 col-md-8 col-sm-12 mb-2">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ url('vehicles') }}">Vehicles</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vehicles.create') }}">Register Vehicle</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12 mb-2 text-end">
            <a href="{{ route('vehicles.create') }}" class="btn btn-warning btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row clearfix">
    <div class="col-md-12">
        <div class="card radius-6">
            <div class="card-header pb-0 border-bottom">
                <h6 class="card-title mb-0">
                    <i class="fa fa-file-pdf-o me-2 text-success"></i>
                    Upload Required Vehicle Documents
                </h6>
            </div>
            <form method="POST" action="{{ route('vehicles.documents.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body" style="padding: 0.6rem;">
                    <div class="alert alert-info py-1 mb-2" style="line-height:1.1;">
                        <strong>Vehicle:</strong> {{ $pendingVehicle['plate_no'] }}
                        <span class="mx-2">|</span>
                        <strong>Ownership:</strong> COMPANY ASSET
                    </div>

                    <div class="row g-1">
                        @foreach([
                            ['key' => 'registration', 'name' => 'Vehicle Registration Card (TRA)', 'doc' => 'doc_registration'],
                            ['key' => 'road_license', 'name' => 'Road License (TRA)', 'doc' => 'doc_road_license'],
                            ['key' => 'inspection', 'name' => 'Vehicle Inspection Certificate (Police)', 'doc' => 'doc_inspection'],
                        ] as $item)
                        <div class="col-md-12 border rounded p-1">
                            <div class="d-flex align-items-start justify-content-between gap-2">
                                <strong class="mb-0">{{ $item['name'] }}</strong>
                            </div>

                            {{-- Row 1: File + Issue/Expiry (keeps columns within 12) --}}
                            <div class="row g-1 mt-1">
                                <div class="col-md-4">
                                    <label class="form-label small mb-1">PDF File <span class="text-danger">*</span></label>
                                    <input type="file" name="{{ $item['doc'] }}" class="form-control form-control-sm py-1" accept=".pdf" required style="max-width: 240px;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1">Issue</label>
                                    <input type="date" name="doc_{{ $item['key'] }}_issue" class="form-control form-control-sm py-1" value="{{ old('doc_' . $item['key'] . '_issue') }}" required style="max-width: 180px;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small mb-1">Expiry</label>
                                    <input type="date" name="doc_{{ $item['key'] }}_expiry" class="form-control form-control-sm py-1" value="{{ old('doc_' . $item['key'] . '_expiry') }}" required style="max-width: 180px;">
                                </div>
                            </div>

                            {{-- Row 2: Charge + Commission --}}
                            <div class="row g-1 mt-1">
                                <div class="col-md-6">
                                    <label class="form-label small mb-1">Charge Paid</label>
                                    <input type="number" step="0.01" min="0" name="doc_{{ $item['key'] }}_charge_paid" class="form-control form-control-sm py-1" value="{{ old('doc_' . $item['key'] . '_charge_paid', '0') }}" style="max-width: 220px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small mb-1">Commission</label>
                                    <input type="number" step="0.01" min="0" name="doc_{{ $item['key'] }}_commission" class="form-control form-control-sm py-1" value="{{ old('doc_' . $item['key'] . '_commission', '0') }}" style="max-width: 220px;">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>{{-- end card-body --}}

                <div class="card-footer text-end border-top pt-1">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-save me-1"></i> Save Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
