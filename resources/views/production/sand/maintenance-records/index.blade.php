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
                <a href="{{ route('maintenance-records.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus me-1"></i> New Record
                </a>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if($records->isEmpty())
                        <div class="alert alert-light mb-0 py-2">No maintenance records found.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Equipment</th>
                                        <th>Type</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Technician</th>
                                        <th style="text-align:center;">Cost</th>
                                        <th>Status</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($records as $rec)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $rec->washingEquipment->equipment_name ?? '-' }}
                                            @if(!empty($rec->washingEquipment?->equipment_type))
                                                <span class="text-muted">({{ $rec->washingEquipment->equipment_type }})</span>
                                            @endif
                                        </td>
                                        <td>{{ $rec->maintenance_type }}</td>
                                        <td>{{ $rec->start_time }}</td>
                                        <td>{{ $rec->end_time }}</td>
                                        <td>{{ $rec->technician ?? '-' }}</td>
                                        <td style="text-align:center;">{{ number_format((float) ($rec->cost ?? 0), 2) }}</td>
                                        <td>{{ $rec->status }}</td>
                                        <td style="text-align:center;">
                                            <a href="{{ route('maintenance-records.show', encrypt($rec->id)) }}" class="text-primary me-2" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('maintenance-records.edit', encrypt($rec->id)) }}" class="text-info me-2" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('maintenance-records.destroy', encrypt($rec->id)) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" onclick="if(confirm('Delete this record?')) { this.closest('form').submit(); }" class="text-danger" title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </form>
                                        </td>
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

