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
                @php
                    $currentDocName = $document->documentType?->dt_name;
                @endphp
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('legal-documents.update', encrypt($document->id)) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Document Type <span class="text-danger">*</span></label>
                            <select name="document_type_id" class="form-select" required>
                                @foreach($documentTypes as $dt)
                                    <option value="{{ $dt->id }}" {{ (string)old('document_type_id', $document->document_type_id) === (string)$dt->id ? 'selected' : '' }}>
                                        {{ $dt->dt_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vehicle</label>
                            <select name="vehicle_id" class="form-select">
                                <option value="">-- Select Vehicle --</option>
                                @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ (string)old('vehicle_id', $document->vehicle_id) === (string)$v->id ? 'selected' : '' }}>{{ $v->plate_no }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', optional($document->last_issue_date)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', optional($document->expire_date)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Charge Paid</label>
                            <input type="number" step="0.01" min="0" name="charge_paid" class="form-control" value="{{ old('charge_paid', $document->charge_paid ?? '0') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission</label>
                            <input type="number" step="0.01" min="0" name="commission" class="form-control" value="{{ old('commission', $document->commission ?? '0') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Replace PDF (optional)</label>
                            <input type="file" name="doc_attachment" class="form-control" accept=".pdf">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('legal-documents.index', array_filter(['doc_name' => $currentDocName, 'vehicle_id' => $document->vehicle_id])) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
