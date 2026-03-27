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
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Document</th><td>{{ $document->documentType?->dt_name ?? '—' }}</td></tr>
                    <tr><th>Vehicle</th><td>{{ $document->vehicle?->plate_no ?? '—' }}</td></tr>
                    <tr><th>Issue</th><td>{{ optional($document->last_issue_date)->format('d/m/Y') }}</td></tr>
                    <tr><th>Expiry</th><td>{{ optional($document->expire_date)->format('d/m/Y') }}</td></tr>
                    <tr><th>Charge Paid</th><td>{{ $document->charge_paid }}</td></tr>
                    <tr><th>Commission</th><td>{{ $document->commission }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            @if($document->status === 'EXPIRED')
                                <span class="badge bg-danger">Expired</span>
                            @elseif($document->status === 'EXPIRING_SOON')
                                <span class="badge bg-warning">Expiring Soon</span>
                            @else
                                <span class="badge bg-success">Valid</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <a href="{{ route('legal-documents.download', encrypt($document->id)) }}" class="btn btn-primary"><i class="fa fa-download"></i> Download</a>
                <a href="{{ route('legal-documents.edit', encrypt($document->id)) }}" class="btn btn-secondary">Edit</a>
            </div>
        </div>
    </div>
</div>
@endsection
