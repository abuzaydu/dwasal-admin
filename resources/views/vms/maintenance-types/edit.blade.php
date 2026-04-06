@extends('layouts.vms')

@section('content')
    <div class="block-header pt-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-8 col-sm-12 mb-2">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('maintenance-types') }}">Maintenance Types</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 mb-2 text-end">
                <a href="{{ route('maintenance-types.index') }}" class="btn btn-warning btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card radius-6">
                <div class="card-header pb-0 border-bottom">
                    <h6 class="card-title mb-0">
                        <i class="fa fa-edit me-2 text-success"></i> {{ $page }}
                    </h6>
                </div>

                <form method="POST" action="{{ route('maintenance-types.update', encrypt($maintenanceType->id)) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body" style="padding: 0.75rem;">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Type <span class="text-danger">*</span></label>
                                <input type="text" name="type" class="form-control form-control-sm py-1" value="{{ old('type', $maintenanceType->type) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Active</label>
                                <select name="active" class="form-select form-select-sm py-1">
                                    <option value="1" {{ old('active', (int)$maintenanceType->active) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('active', (int)$maintenanceType->active) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fa fa-save me-1"></i> Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

