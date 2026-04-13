@extends('layouts.vms')
@section('content')
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('vehicles') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Vehicles</li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-12 text-end">
                <a href="{{ url('vehicles') }}" class="btn btn-warning btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Back to Vehicles
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-2" method="POST" action="{{ route('ownerships.update', encrypt($ownership->id)) }}">
                            @csrf
                            {{ method_field('PATCH') }}

                            @if($ownership->is_system)
                                <div class="col-md-12">
                                    <label class="form-label">Ownership type</label>
                                    <input type="text" class="form-control form-control-sm" value="{{ $ownership->type }}" readonly>
                                    <div class="form-text">Core type name cannot be changed.</div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="3" class="form-control form-control-sm" placeholder="Shown to staff when registering vehicles">{{ old('description', $ownership->description) }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="active" class="form-select form-select-sm" style="max-width: 220px;">
                                        <option value="1" {{ old('active', $ownership->active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('active', $ownership->active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <div class="form-text">Inactive types are hidden when registering new vehicles.</div>
                                </div>
                            @else
                                <div class="col-md-5 pt-2">
                                    <label class="form-label">Ownership Type <span class="text-danger">*</span></label>
                                    <input type="text" name="type" value="{{ old('type', $ownership->type) }}" required class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="2" class="form-control form-control-sm mb-1">{{ old('description', $ownership->description) }}</textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="active" class="form-select form-select-sm" style="max-width: 220px;">
                                        <option value="1" {{ old('active', $ownership->active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('active', $ownership->active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-12 pt-2">
                                <button type="submit" class="btn btn-success btn-sm">{{ trans('navmenu.btn_save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
