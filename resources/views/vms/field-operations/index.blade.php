@extends('layouts.vms')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="block-header pt-4">
    <div class="row align-items-center">
        <div class="col-md-7">
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
        <div class="col-md-5 text-end">
            <a href="{{ route('field-operations.create') }}" class="btn btn-primary btn-sm" title="Create new record"><i class="fa fa-plus me-1"></i> New</a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form class="dashform d-flex flex-wrap justify-content-end gap-2" action="{{ route('field-operations.index') }}" method="GET" id="fieldOperationsFilter">
            <input type="hidden" name="start_date" id="start_input" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" id="end_input" value="{{ request('end_date') }}">
            <button type="button" class="btn btn-default btn-sm" id="reportrange" style="white-space: nowrap;">
                <i class="fa fa-calendar"></i>
                <span id="reportrange-label" class="mx-1">Select date range</span>
                <i class="fa fa-caret-down"></i>
            </button>
            <a href="{{ route('field-operations.index') }}" class="btn btn-light btn-sm">Clear</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="fa fa-file-text-o text-primary me-2"></i> Field Operations Records</h6>
        <span class="text-muted">{{ $records->count() }} record(s)</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
                <table id="fieldOperationsTable" class="table table-striped table-bordered datatable nowrap align-middle mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Trucks</th>
                        <th>Trips</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td>{{ $record->record_no }}</td>
                            <td data-order="{{ $record->record_date?->format('Y-m-d') }}">{{ $record->record_date?->format('d/m/Y') }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->location ?: '-' }}</td>
                            <td>{{ $record->rows_count }}</td>
                            <td>{{ $record->quantity_of_trips ?? '-' }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('field-operations.show', $record) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('field-operations.edit', $record) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="fa fa-pencil"></i></a>
                                <a href="{{ route('field-operations.print', $record) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print"><i class="fa fa-print"></i></a>
                                <form action="{{ route('field-operations.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this field operations record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No field operations records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
