@extends('layouts.vms')
@section('content')
<div class="block-header pt-2 pb-1">
    <div class="row">
        <div class="col-lg-6">
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item"><a href="{{ url('insurance') }}">Insurance</a></li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-12">
        <div class="card radius-6">
            <div class="card-body py-2">
                @if($errors->any())
                    <div class="alert alert-danger py-1 mb-2">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('insurance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-1">
                        <div class="col-md-6 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Vehicle <span class="text-danger">*</span></label>
                            <select name="vehicle_id" id="insurance_vehicle_id" class="form-select form-select-sm py-0" required>
                                <option value="">-- Select Vehicle --</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ (int)old('vehicle_id', $vehicleId ?? 0) === (int)$v->id ? 'selected' : '' }}>{{ $v->plate_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Insurance Company <span class="text-danger">*</span></label>
                            <select name="insurance_company_id" class="form-select form-select-sm py-0" required>
                                <option value="">-- Select Insurer --</option>
                                @foreach($insuranceCompanies as $c)
                                    <option value="{{ $c->id }}" {{ old('insurance_company_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-1">
                            <label class="form-label small mb-0 fw-semibold">IR Period <span class="text-danger">*</span></label>
                            <select name="ir_period_id" class="form-select form-select-sm py-0" required>
                                <option value="">-- Select Period --</option>
                                @foreach($irPeriods as $p)
                                    <option value="{{ $p->id }}" {{ old('ir_period_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->period }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Policy Number <span class="text-danger">*</span></label>
                            <input type="text" name="policy_number" class="form-control form-control-sm py-0" value="{{ old('policy_number') }}" required>
                        </div>

                        <div class="col-md-2 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Charge Payable <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="charge_payable" class="form-control form-control-sm py-0" value="{{ old('charge_payable') }}" required>
                        </div>

                        <div class="col-md-2 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Deductible <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="deductible" class="form-control form-control-sm py-0" value="{{ old('deductible') }}" required>
                        </div>

                        <div class="col-md-3 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control form-control-sm py-0" value="{{ old('start_date') }}" required>
                        </div>

                        <div class="col-md-3 mb-1">
                            <label class="form-label small mb-0 fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control form-control-sm py-0" value="{{ old('end_date') }}" required>
                        </div>

                        <div class="col-md-3 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Recurring Date</label>
                            <input type="date" name="recurring_date" class="form-control form-control-sm py-0" value="{{ old('recurring_date') }}">
                        </div>

                        <div class="col-md-3 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Policy Attachment <span class="text-danger">*</span></label>
                            <input type="file" name="policy_attachment" class="form-control form-control-sm py-0" accept=".pdf" required>
                        </div>

                        <div class="col-md-3 mb-1">
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" name="add_reminder" value="1" class="form-check-input" id="add_reminder" {{ old('add_reminder', 1) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="add_reminder">Add Reminder</label>
                            </div>
                        </div>

                        <div class="col-md-3 mb-1">
                            <div class="form-check form-switch mt-2">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="is_active">Is Active</label>
                            </div>
                        </div>

                        <div class="col-12 mb-1">
                            <label class="form-label small mb-0 fw-semibold">Description</label>
                            <textarea name="description" class="form-control form-control-sm py-0" rows="2">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-2 pt-1 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa fa-save me-1"></i> Save Insurance
                        </button>
                        <a href="{{ route('insurance.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-times me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
    $(document).ready(function(){
        $('#insurance_vehicle_id').select2({
            width: '100%',
            placeholder: '-- Select Vehicle --',
            dropdownAutoWidth: true
        });

        $('#insurance_vehicle_id').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search vehicle...');
        });

        // Adjust select2 height to match compact form
        $('.select2-selection').css('height', '31px');
        $('.select2-selection__rendered').css('line-height', '29px');
    });
</script>
<style>
    .select2-container .select2-selection--single {
        height: 31px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 29px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 29px !important;
    }
</style>
@endsection
