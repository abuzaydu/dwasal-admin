@extends('layouts.vms')

@section('content')
<div class="block-header pt-4">
    <div class="row align-items-center">
        <div class="col-md-7"><ul class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('field-operations.index') }}"><i class="fa fa-file-text-o"></i></a></li><li class="breadcrumb-item">Field Operations</li><li class="breadcrumb-item active">{{ $record->record_no }}</li></ul></div>
        <div class="col-md-5 d-flex flex-wrap justify-content-md-end gap-2">
            <a href="{{ route('field-operations.edit', $record) }}" class="btn btn-sm btn-outline-secondary" title="Edit record"><i class="fa fa-pencil me-1"></i> Edit</a>
            <a href="{{ route('field-operations.print', $record) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print record"><i class="fa fa-print me-1"></i> Print</a>
            <a href="{{ route('field-operations.pdf', $record) }}" class="btn btn-sm btn-primary" title="Download PDF"><i class="fa fa-file-pdf-o me-1"></i> PDF</a>
        </div>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="report-preview bg-white p-3 p-md-4 shadow-sm">
    @include('vms.field-operations.report', ['record' => $record, 'embedded' => true])
</div>
@endsection
