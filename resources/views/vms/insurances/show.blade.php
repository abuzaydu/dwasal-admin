@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-6">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item"><a href="{{ url('insurance') }}">Insurance</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
        <div class="col-lg-6 text-end">
            <a href="{{ route('insurance.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card radius-6">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Vehicle:</strong> {{ $insurance->vehicle?->plate_no ?? 'N/A' }}</div>
                    <div class="col-md-6"><strong>Insurance Company:</strong> {{ $insurance->insuranceCompany?->name ?? 'N/A' }}</div>
                    <div class="col-md-6"><strong>Policy Number:</strong> {{ $insurance->policy_number }}</div>
                    <div class="col-md-6"><strong>IR Period:</strong> {{ $insurance->irPeriod?->period ?? 'N/A' }}</div>
                    <div class="col-md-6"><strong>Charge Payable:</strong> {{ number_format((float)$insurance->charge_payable, 2) }}</div>
                    <div class="col-md-6"><strong>Deductible:</strong> {{ number_format((float)$insurance->deductible, 2) }}</div>
                    <div class="col-md-6"><strong>Start Date:</strong> {{ optional($insurance->start_date)->format('d/m/Y') }}</div>
                    <div class="col-md-6"><strong>End Date:</strong> {{ optional($insurance->end_date)->format('d/m/Y') }}</div>
                    <div class="col-md-6"><strong>Recurring Date:</strong> {{ optional($insurance->recurring_date)->format('d/m/Y') }}</div>
                    <div class="col-md-6"><strong>Status:</strong> 
                        @if($insurance->status === 'EXPIRED')
                            <span class="badge bg-danger">Expired</span>
                        @elseif($insurance->status === 'EXPIRING_SOON')
                            <span class="badge bg-warning">Expiring Soon</span>
                        @else
                            <span class="badge bg-success">Valid</span>
                        @endif
                    </div>
                </div>

                <hr class="my-3">

                <div class="d-flex gap-2">
                    <a href="{{ route('insurance.download', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-primary">
                        <i class="fa fa-download"></i> Download PDF
                    </a>
                    <a href="{{ route('insurance.edit', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-secondary">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

