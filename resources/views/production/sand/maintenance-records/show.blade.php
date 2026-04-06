@extends('layouts.vms')

@section('content')
    <div class="pt-3">
        <ul class="nav nav-pills nav-pills-sm flex-wrap gap-1">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('sand-prod-dash') ? 'active' : '' }}" href="{{ url('sand-prod-dash') }}">
                    <i class="fa fa-home me-1"></i> Dashboard
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->is('sand-productions*') || request()->is('quality-tests*') || request()->is('rm-sourcings*') || request()->is('raw-material-sources*') ? 'active' : '' }}"
                   href="javascript:;" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-list me-1"></i> Washed Sand Productions
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('sand-productions.create') }}">New Production Run</a></li>
                    <li><a class="dropdown-item" href="{{ url('sand-productions') }}">Production Records</a></li>
                    <li><a class="dropdown-item" href="{{ url('quality-tests') }}">Quality Tests</a></li>
                    <li><a class="dropdown-item" href="{{ url('rm-sourcings') }}">Raw Material Sourcings</a></li>
                    <li><a class="dropdown-item" href="{{ url('raw-material-sources') }}">Raw Material Sources</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->is('washing-plants*') || request()->is('washing-equipments*') ? 'active' : '' }}"
                   href="javascript:;" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-th-large me-1"></i> Washing Plants & Equipments
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ url('washing-plants') }}">Washing Plants</a></li>
                    <li><a class="dropdown-item" href="{{ url('washing-equipments') }}">Washing Equipments</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('storage-locations*') ? 'active' : '' }}" href="{{ url('storage-locations') }}">
                    <i class="fa fa-map-marker me-1"></i> Storage Locations
                </a>
            </li>
        </ul>
    </div>

    <div class="block-header pt-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-8 col-sm-12 mb-2">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 mb-2 text-end">
                <a href="{{ route('maintenance-records.edit', encrypt($record->id)) }}" class="btn btn-info btn-sm me-1">
                    <i class="fa fa-edit me-1"></i> Edit
                </a>
                <a href="{{ url('maintenance-records') }}" class="btn btn-warning btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h6 class="mb-0"><i class="fa fa-file-text-o me-1"></i> Maintenance Record #{{ $record->id }}</h6>
                </div>

                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Equipment</div>
                            <div class="fw-semibold">
                                {{ $record->washingEquipment->equipment_name ?? '-' }}
                                @if(!empty($record->washingEquipment?->equipment_type))
                                    <span class="text-muted">({{ $record->washingEquipment->equipment_type }})</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Type</div>
                            <div class="fw-semibold">{{ $record->maintenance_type }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Status</div>
                            <div class="fw-semibold">{{ $record->status }}</div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Start Time</div>
                            <div class="fw-semibold">{{ $record->start_time }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">End Time</div>
                            <div class="fw-semibold">{{ $record->end_time }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Technician</div>
                            <div class="fw-semibold">{{ $record->technician ?? '-' }}</div>
                        </div>

                        @if(!empty($record->description_of_wo))
                            <div class="col-md-12">
                                <div class="text-muted small mb-1">Description of WO</div>
                                <div>{{ $record->description_of_wo }}</div>
                            </div>
                        @endif

                        @if(!empty($record->inspection_findings))
                            <div class="col-md-12">
                                <div class="text-muted small mb-1">Inspection Findings</div>
                                <div>{{ $record->inspection_findings }}</div>
                            </div>
                        @endif

                        @if(!empty($record->parts_used))
                            <div class="col-md-12">
                                <div class="text-muted small mb-1">Parts Used</div>
                                <div>{{ $record->parts_used }}</div>
                            </div>
                        @endif

                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Cost</div>
                            <div class="fw-semibold">{{ number_format((float) ($record->cost ?? 0), 2) }}</div>
                        </div>

                        @if(!empty($record->notes))
                            <div class="col-md-12">
                                <div class="text-muted small mb-1">Notes</div>
                                <div>{{ $record->notes }}</div>
                            </div>
                        @endif
                    </div>

                    <hr class="my-3" />

                    <div class="row g-2">
                        <div class="col-md-12">
                            <h6 class="mb-1"><i class="fa fa-camera me-1 text-primary"></i> Photos</h6>
                        </div>
                        <div class="col-md-12">
                            @if($record->photos->isEmpty())
                                <div class="alert alert-light py-2 mb-0">No photos uploaded.</div>
                            @else
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($record->photos as $photo)
                                        <div class="border rounded p-1" style="width: 120px;">
                                            <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="photo" style="width:100%; height:80px; object-fit:cover; border-radius:4px;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

