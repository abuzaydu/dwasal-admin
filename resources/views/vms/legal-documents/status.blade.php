@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-6">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
        <div class="col-lg-6 text-end"></div>
    </div>
</div>

<div class="row clearfix mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(empty($vehicleId))
                    @php
                        $activeAll = empty($status);
                        $activeValid = $status === 'valid';
                        $activeExpiring = $status === 'expiring';
                        $activeExpired = $status === 'expired';
                        $activeMissing = $status === 'missing';
                    @endphp

                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeAll ? 'active' : '' }}" data-bs-toggle="tab" href="#legal-docs-status-all" role="tab">All</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeValid ? 'active' : '' }}" data-bs-toggle="tab" href="#legal-docs-status-valid" role="tab">Valid</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeExpiring ? 'active' : '' }}" data-bs-toggle="tab" href="#legal-docs-status-expiring" role="tab">Expiring Soon</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeExpired ? 'active' : '' }}" data-bs-toggle="tab" href="#legal-docs-status-expired" role="tab">Expired</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeMissing ? 'active' : '' }}" data-bs-toggle="tab" href="#legal-docs-status-missing" role="tab">Missing</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade {{ $activeAll ? 'show active' : '' }}" id="legal-docs-status-all" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm datatable vehicle-documents-status-table" id="vehicle-documents-status-table-all">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Vehicle</th>
                                            <th>Issue</th>
                                            <th>Expiry</th>
                                            <th>Status</th>
                                            <th style="text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allDocuments as $doc)
                                            <tr>
                                                <td>{{ $doc->documentType?->dt_name ?? '—' }}</td>
                                                <td>{{ $doc->vehicle?->plate_no ?? '—' }}</td>
                                                <td>{{ optional($doc->last_issue_date)->format('d/m/Y') }}</td>
                                                <td>{{ optional($doc->expire_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    @if($doc->status === 'EXPIRED')
                                                        <span class="badge bg-danger">Expired</span>
                                                    @elseif($doc->status === 'EXPIRING_SOON')
                                                        <span class="badge bg-warning">Expiring Soon</span>
                                                    @else
                                                        <span class="badge bg-success">Valid</span>
                                                    @endif
                                                </td>
                                                <td style="text-align:center;">
                                                    <a href="{{ route('legal-documents.status', ['vehicle_id' => $doc->vehicle_id]) }}" class="btn btn-xs btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($allDocuments->isEmpty())
                                <div class="text-center text-muted py-2">No documents found for this status.</div>
                            @endif
                        </div>

                        <div class="tab-pane fade {{ $activeValid ? 'show active' : '' }}" id="legal-docs-status-valid" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm datatable vehicle-documents-status-table" id="vehicle-documents-status-table-valid">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Vehicle</th>
                                            <th>Issue</th>
                                            <th>Expiry</th>
                                            <th>Status</th>
                                            <th style="text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($validDocuments as $doc)
                                            <tr>
                                                <td>{{ $doc->documentType?->dt_name ?? '—' }}</td>
                                                <td>{{ $doc->vehicle?->plate_no ?? '—' }}</td>
                                                <td>{{ optional($doc->last_issue_date)->format('d/m/Y') }}</td>
                                                <td>{{ optional($doc->expire_date)->format('d/m/Y') }}</td>
                                                <td><span class="badge bg-success">Valid</span></td>
                                                <td style="text-align:center;">
                                                    <a href="{{ route('legal-documents.status', ['vehicle_id' => $doc->vehicle_id]) }}" class="btn btn-xs btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($validDocuments->isEmpty())
                                <div class="text-center text-muted py-2">No documents found for this status.</div>
                            @endif
                        </div>

                        <div class="tab-pane fade {{ $activeExpiring ? 'show active' : '' }}" id="legal-docs-status-expiring" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm datatable vehicle-documents-status-table" id="vehicle-documents-status-table-expiring">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Vehicle</th>
                                            <th>Issue</th>
                                            <th>Expiry</th>
                                            <th>Status</th>
                                            <th style="text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expiringSoonDocuments as $doc)
                                            <tr>
                                                <td>{{ $doc->documentType?->dt_name ?? '—' }}</td>
                                                <td>{{ $doc->vehicle?->plate_no ?? '—' }}</td>
                                                <td>{{ optional($doc->last_issue_date)->format('d/m/Y') }}</td>
                                                <td>{{ optional($doc->expire_date)->format('d/m/Y') }}</td>
                                                <td><span class="badge bg-warning">Expiring Soon</span></td>
                                                <td style="text-align:center;">
                                                    <a href="{{ route('legal-documents.status', ['vehicle_id' => $doc->vehicle_id]) }}" class="btn btn-xs btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($expiringSoonDocuments->isEmpty())
                                <div class="text-center text-muted py-2">No documents found for this status.</div>
                            @endif
                        </div>

                        <div class="tab-pane fade {{ $activeExpired ? 'show active' : '' }}" id="legal-docs-status-expired" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm datatable vehicle-documents-status-table" id="vehicle-documents-status-table-expired">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Vehicle</th>
                                            <th>Issue</th>
                                            <th>Expiry</th>
                                            <th>Status</th>
                                            <th style="text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expiredDocuments as $doc)
                                            <tr>
                                                <td>{{ $doc->documentType?->dt_name ?? '—' }}</td>
                                                <td>{{ $doc->vehicle?->plate_no ?? '—' }}</td>
                                                <td>{{ optional($doc->last_issue_date)->format('d/m/Y') }}</td>
                                                <td>{{ optional($doc->expire_date)->format('d/m/Y') }}</td>
                                                <td><span class="badge bg-danger">Expired</span></td>
                                                <td style="text-align:center;">
                                                    <a href="{{ route('legal-documents.status', ['vehicle_id' => $doc->vehicle_id]) }}" class="btn btn-xs btn-outline-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($expiredDocuments->isEmpty())
                                <div class="text-center text-muted py-2">No documents found for this status.</div>
                            @endif
                        </div>

                        <div class="tab-pane fade {{ $activeMissing ? 'show active' : '' }}" id="legal-docs-status-missing" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm datatable vehicle-documents-status-table" id="vehicle-documents-status-table-missing">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Vehicle</th>
                                            <th>Issue</th>
                                            <th>Expiry</th>
                                            <th>Status</th>
                                            <th style="text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($missingRows as $row)
                                            <tr>
                                                <td>{{ $row['documentTypeName'] }}</td>
                                                <td>{{ $row['vehicle']->plate_no }}</td>
                                                <td>—</td>
                                                <td>—</td>
                                                <td><span class="badge bg-secondary">Missing</span></td>
                                                <td style="text-align:center;">
                                                    <a href="{{ route('legal-documents.status', ['vehicle_id' => $row['vehicle']->id]) }}" class="btn btn-xs btn-outline-primary">
                                                        View
                                                    </a>
                                                    @php
                                                        $addQuery = array_filter(['doc_name' => $row['documentTypeName'], 'vehicle_id' => $row['vehicle']->id]);
                                                    @endphp
                                                    <a href="{{ route('legal-documents.create') }}?{{ http_build_query($addQuery) }}" class="btn btn-xs btn-primary">
                                                        <i class="fa fa-plus"></i> Add
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($missingRows->isEmpty())
                                <div class="text-center text-muted py-2">No documents found for this status.</div>
                            @endif
                        </div>
                    </div>
                @else
                    <hr class="my-3">
                    <h6 class="mb-3">
                        Vehicle Checklist
                        @if(!empty($selectedVehicle))
                            - <span class="text-primary">{{ $selectedVehicle->plate_no }}</span>
                        @endif
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm datatable" id="vehicle-documents-checklist-table">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Issue Date</th>
                                            <th>Expiry Date</th>
                                            <th>Status</th>
                                            <th>Charge Paid</th>
                                            <th>Commission</th>
                                            <th style="text-align:center;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requiredDocs as $docTypeName)
                                            @php
                                                $doc = $docsByTypeName->get($docTypeName);
                                            @endphp
                                            <tr>
                                                <td><strong>{{ $docTypeName }}</strong></td>

                                                @if($doc)
                                                    <td>{{ optional($doc->last_issue_date)->format('d/m/Y') }}</td>
                                                    <td>{{ optional($doc->expire_date)->format('d/m/Y') }}</td>
                                                    <td>
                                                        @if($doc->status === 'EXPIRED')
                                                            <span class="badge bg-danger">Expired</span>
                                                        @elseif($doc->status === 'EXPIRING_SOON')
                                                            <span class="badge bg-warning">Expiring Soon</span>
                                                        @else
                                                            <span class="badge bg-success">Valid</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format((float)($doc->charge_paid ?? 0), 2) }}</td>
                                                    <td>{{ number_format((float)($doc->commission ?? 0), 2) }}</td>
                                                    <td style="text-align:center;">
                                                        <a href="{{ route('legal-documents.download', encrypt($doc->id)) }}" class="btn btn-xs btn-outline-primary">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <a href="{{ route('legal-documents.edit', encrypt($doc->id)) }}" class="btn btn-xs btn-outline-secondary">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    </td>
                                                @else
                                                    <td>—</td>
                                                    <td>—</td>
                                                    <td><span class="badge bg-secondary">Missing</span></td>
                                                    <td>—</td>
                                                    <td>—</td>
                                                    <td style="text-align:center;">
                                                        @php
                                                            $addQuery = array_filter(['doc_name' => $docTypeName, 'vehicle_id' => $vehicleId]);
                                                        @endphp
                                                        <a href="{{ route('legal-documents.create') }}?{{ http_build_query($addQuery) }}" class="btn btn-xs btn-primary">
                                                            <i class="fa fa-plus"></i> Add
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
    $(document).ready(function(){
        $('.vehicle-documents-status-table').each(function () {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    paging: true,
                    ordering: true,
                    searching: true,
                    responsive: true
                });
            }
        });

        if ($('#vehicle-documents-checklist-table').length) {
            $('#vehicle-documents-checklist-table').DataTable({
                paging: true,
                ordering: true,
                searching: true,
                responsive: true
            });
        }
    });
</script>
@endsection

