@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
        <li class="breadcrumb-item"><a href="{{ route('legal-documents.index') }}">Legal Documents</a></li>
        <li class="breadcrumb-item active">{{ $page }}</li>
    </ul>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('legal-documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @php
                        $fixedVehicle = null;
                        if (!empty($vehicleId)) {
                            $fixedVehicle = $vehicles->firstWhere('id', (int) old('vehicle_id', $vehicleId));
                        }
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Document Type <span class="text-danger">*</span></label>
                            <select name="document_type_id" class="form-select" required>
                                <option value="">-- Select Tanzania Document --</option>
                                @foreach($documentTypes as $dt)
                                    <option value="{{ $dt->id }}" {{ (string)old('document_type_id', $selectedDocumentTypeId) === (string)$dt->id ? 'selected' : '' }}>
                                        {{ $dt->dt_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vehicle <span class="text-danger">*</span></label>
                            @if(!empty($vehicleId) && $fixedVehicle)
                                <input type="text" class="form-control" value="{{ $fixedVehicle->plate_no }}" readonly>
                                <input type="hidden" name="vehicle_id" value="{{ $fixedVehicle->id }}">
                            @else
                                <select name="vehicle_id" class="form-select" required>
                                    <option value="">-- Select Vehicle --</option>
                                    @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ (string)old('vehicle_id', $vehicleId) === (string)$v->id ? 'selected' : '' }}>{{ $v->plate_no }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Charge Paid</label>
                            <input type="number" step="0.01" min="0" name="charge_paid" class="form-control" value="{{ old('charge_paid', '0') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission</label>
                            <input type="number" step="0.01" min="0" name="commission" class="form-control" value="{{ old('commission', '0') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">PDF File <span class="text-danger">*</span></label>
                            <input type="file" name="doc_attachment" class="form-control" accept=".pdf" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('legal-documents.index', array_filter(['doc_name' => $docName, 'vehicle_id' => $vehicleId])) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
