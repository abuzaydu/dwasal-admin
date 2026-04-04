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
        <div class="col-lg-6 d-flex justify-content-end align-items-center">
            <form class="dashform" action="{{ url('f-legal-documents') }}" method="POST" id="stockform">
                @csrf
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <button type="button" class="btn btn-default btn-sm w-auto" id="reportrange" style="white-space: nowrap;">
                    <i class="fa fa-calendar"></i>
                    <span id="reportrange-label" class="mx-1"></span>
                    <i class="fa fa-caret-down"></i>
                </button>
            </form>
        </div>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs mb-3 flex-nowrap overflow-auto" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ empty($docName) ? 'active' : '' }}"
                           data-bs-toggle="tab" href="#legal-docs-tab-all" role="tab">
                            All Documents
                        </a>
                    </li>

                    @foreach($tabNames as $name)
                        @php $slug = \Illuminate\Support\Str::slug($name); @endphp
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $docName === $name ? 'active' : '' }}"
                               data-bs-toggle="tab" href="#legal-docs-tab-{{ $slug }}" role="tab">
                                {{ $name }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    {{-- ALL DOCUMENTS --}}
                    <div class="tab-pane fade {{ empty($docName) ? 'show active' : '' }}" id="legal-docs-tab-all" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm legal-docs-table" id="legal-documents-table-all">
                                <thead>
                                    <tr>
                                        <th>Document</th>
                                        <th>Vehicle</th>
                                        <th>Issue</th>
                                        <th>Expiry</th>
                                        <th>Status</th>
                                        <th>Actions</th>
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
                                            <td>
                                                @if($doc->vehicle_id)
                                                    <a href="{{ route('legal-documents.status', ['vehicle_id' => $doc->vehicle_id]) }}" class="btn btn-xs btn-outline-info" title="View Vehicle Status">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('legal-documents.download', encrypt($doc->id)) }}" class="btn btn-xs btn-outline-primary"><i class="fa fa-download"></i></a>
                                                <a href="{{ route('legal-documents.edit', encrypt($doc->id)) }}" class="btn btn-xs btn-outline-secondary"><i class="fa fa-edit"></i></a>
                                                <form action="{{ route('legal-documents.destroy', encrypt($doc->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(($allDocuments ?? collect())->isEmpty())
                                <div class="text-center text-muted py-2">No documents.</div>
                            @endif
                        </div>
                    </div>

                    {{-- BY DOCUMENT TYPE --}}
                    @foreach($tabNames as $name)
                        @php $slug = \Illuminate\Support\Str::slug($name); @endphp
                        @php $docs = $documentsByTab[$name] ?? collect(); @endphp
                        <div class="tab-pane fade {{ $docName === $name ? 'show active' : '' }}" id="legal-docs-tab-{{ $slug }}" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm legal-docs-table" id="legal-documents-table-{{ $slug }}">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Vehicle</th>
                                            <th>Issue</th>
                                            <th>Expiry</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($docs as $doc)
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
                                                <td>
                                                    @if($doc->vehicle_id)
                                                        <a href="{{ route('legal-documents.status', ['vehicle_id' => $doc->vehicle_id]) }}" class="btn btn-xs btn-outline-info" title="View Vehicle Status">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('legal-documents.download', encrypt($doc->id)) }}" class="btn btn-xs btn-outline-primary"><i class="fa fa-download"></i></a>
                                                    <a href="{{ route('legal-documents.edit', encrypt($doc->id)) }}" class="btn btn-xs btn-outline-secondary"><i class="fa fa-edit"></i></a>
                                                    <form action="{{ route('legal-documents.destroy', encrypt($doc->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-outline-danger"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if(($docs ?? collect())->isEmpty())
                                    <div class="text-center text-muted py-2">No documents.</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
    $(document).ready(function(){
        $('.legal-docs-table').DataTable({
            paging: true,
            ordering: true,
            searching: true,
            info: false,
            lengthChange: true,
            pageLength: {{ $perPage }},
            responsive: true
        });
    });
</script>
@endsection
