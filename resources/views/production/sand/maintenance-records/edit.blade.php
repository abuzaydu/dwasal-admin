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
                <a href="{{ route('maintenance-records.show', encrypt($record->id)) }}" class="btn btn-light btn-sm me-1">
                    <i class="fa fa-eye me-1"></i> View
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
                    <h6 class="mb-0"><i class="fa fa-edit me-1"></i> {{ $page }}</h6>
                </div>

                <form method="POST" action="{{ route('maintenance-records.update', encrypt($record->id)) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small mb-1">Washing Equipment <span class="text-danger">*</span></label>
                                <select name="washing_equipment_id" class="form-select form-select-sm py-1" required>
                                    @foreach($equipments as $eq)
                                        <option value="{{ $eq->id }}" {{ $record->washing_equipment_id == $eq->id ? 'selected' : '' }}>
                                            {{ $eq->equipment_name }}{{ $eq->equipment_type ? ' - '.$eq->equipment_type : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small mb-1">Start Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control form-control-sm py-1" required
                                       value="{{ \Carbon\Carbon::parse($record->start_time)->format('Y-m-d\TH:i') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small mb-1">End Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_time" class="form-control form-control-sm py-1" required
                                       value="{{ \Carbon\Carbon::parse($record->end_time)->format('Y-m-d\TH:i') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small mb-1">Maintenance Type <span class="text-danger">*</span></label>
                                <input type="text" name="maintenance_type" class="form-control form-control-sm py-1" required value="{{ $record->maintenance_type }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small mb-1">Technician</label>
                                <input type="text" name="technician" class="form-control form-control-sm py-1" value="{{ $record->technician }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Description of WO</label>
                                <textarea name="description_of_wo" rows="2" class="form-control form-control-sm py-1">{{ $record->description_of_wo }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Inspection Findings</label>
                                <textarea name="inspection_findings" rows="2" class="form-control form-control-sm py-1">{{ $record->inspection_findings }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Parts Used</label>
                                <textarea name="parts_used" rows="2" class="form-control form-control-sm py-1">{{ $record->parts_used }}</textarea>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Cost</label>
                                <input type="number" name="cost" step="0.01" min="0" class="form-control form-control-sm py-1"
                                       value="{{ $record->cost ?? 0 }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm py-1" required>
                                    @foreach(['pending','in progress','completed'] as $st)
                                        <option value="{{ $st }}" {{ $record->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small mb-1">Notes</label>
                                <input type="text" name="notes" class="form-control form-control-sm py-1" value="{{ $record->notes }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Existing Photos</label>
                                @if($record->photos->isEmpty())
                                    <div class="alert alert-light py-2 mb-2">No photos uploaded.</div>
                                @else
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($record->photos as $photo)
                                            <div class="border rounded p-1" style="width:110px;">
                                                <img src="{{ asset('storage/' . $photo->photo_url) }}" alt="photo" style="width:100%; height:70px; object-fit:cover; border-radius:4px;">
                                                <div class="form-check mt-1">
                                                    <input class="form-check-input" type="checkbox" name="delete_photo_ids[]" value="{{ $photo->id }}" id="del-photo-{{ $photo->id }}">
                                                    <label class="form-check-label small" for="del-photo-{{ $photo->id }}">Delete</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Add more photos</label>
                                <input type="file" name="photos[]" class="form-control form-control-sm" accept="image/*" multiple>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer border-top text-end py-2">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

