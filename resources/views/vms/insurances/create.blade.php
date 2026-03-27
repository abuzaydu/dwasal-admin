@extends('layouts.vms')
@section('content')
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-6">
            <ul class="breadcrumb">
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
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('insurance.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Vehicle <span class="text-danger">*</span></label>
                            <select name="vehicle_id" id="insurance_vehicle_id" class="form-select form-select-sm select2" required>
                                <option value="">-- Select Vehicle --</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ (int)old('vehicle_id', $vehicleId ?? 0) === (int)$v->id ? 'selected' : '' }}>{{ $v->plate_no }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Insurance Company <span class="text-danger">*</span></label>
                            <select name="insurance_company_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Insurer --</option>
                                @foreach($insuranceCompanies as $c)
                                    <option value="{{ $c->id }}" {{ old('insurance_company_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">IR Period <span class="text-danger">*</span></label>
                            <select name="ir_period_id" class="form-select form-select-sm" required>
                                <option value="">-- Select Period --</option>
                                @foreach($irPeriods as $p)
                                    <option value="{{ $p->id }}" {{ old('ir_period_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->period }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Policy Number <span class="text-danger">*</span></label>
                            <input type="text" name="policy_number" class="form-control form-control-sm" value="{{ old('policy_number') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Charge Payable <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="charge_payable" class="form-control form-control-sm" value="{{ old('charge_payable') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Deductible <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="deductible" class="form-control form-control-sm" value="{{ old('deductible') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Recurring Date</label>
                            <input type="date" name="recurring_date" class="form-control form-control-sm" value="{{ old('recurring_date') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ old('start_date') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ old('end_date') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Policy Attachment (PDF) <span class="text-danger">*</span></label>
                            <input type="file" name="policy_attachment" class="form-control form-control-sm" accept=".pdf" required>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch pt-3">
                                <input type="checkbox" name="add_reminder" value="1" class="form-check-input" id="add_reminder" {{ old('add_reminder', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="add_reminder">Add Reminder</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch pt-3">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Is Active</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Insurance</button>
                        <a href="{{ route('insurance.index') }}" class="btn btn-secondary">Cancel</a>
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
            placeholder: '-- Select Vehicle --'
        });

        $('#insurance_vehicle_id').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search vehicle...');
        });
    });
</script>
@endsection

