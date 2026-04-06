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

                <form action="{{ route('insurance.update', encrypt($insurance->id)) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Vehicle <span class="text-danger">*</span></label>
                            <select name="vehicle_id" id="insurance_vehicle_id" class="form-select form-select-sm select2" required>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ (int)$insurance->vehicle_id === (int)$v->id ? 'selected' : '' }}>
                                        {{ $v->plate_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Insurance Company <span class="text-danger">*</span></label>
                            <select name="insurance_company_id" class="form-select form-select-sm" required>
                                @foreach($insuranceCompanies as $c)
                                    <option value="{{ $c->id }}" {{ (int)$insurance->insurance_company_id === (int)$c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">IR Period <span class="text-danger">*</span></label>
                            <select name="ir_period_id" class="form-select form-select-sm" required>
                                @foreach($irPeriods as $p)
                                    <option value="{{ $p->id }}" {{ (int)$insurance->ir_period_id === (int)$p->id ? 'selected' : '' }}>
                                        {{ $p->period }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Policy Number <span class="text-danger">*</span></label>
                            <input type="text" name="policy_number" class="form-control form-control-sm" value="{{ $insurance->policy_number }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Charge Payable <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="charge_payable" class="form-control form-control-sm" value="{{ $insurance->charge_payable }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Deductible <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="deductible" class="form-control form-control-sm" value="{{ $insurance->deductible }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Recurring Date</label>
                            <input type="date" name="recurring_date" class="form-control form-control-sm" value="{{ optional($insurance->recurring_date)->format('Y-m-d') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ optional($insurance->start_date)->format('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ optional($insurance->end_date)->format('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Policy Attachment (PDF)</label>
                            <div class="mb-2">
                                @if(!empty($insurance->policy_attachment))
                                    <a href="{{ route('insurance.download', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-primary">
                                        <i class="fa fa-download"></i> Download current
                                    </a>
                                @endif
                            </div>
                            <input type="file" name="policy_attachment" class="form-control form-control-sm" accept=".pdf">
                            <small class="text-muted">Leave empty to keep the current PDF.</small>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch pt-3">
                                <input type="checkbox" name="add_reminder" value="1" class="form-check-input" id="add_reminder" {{ $insurance->add_reminder ? 'checked' : '' }}>
                                <label class="form-check-label" for="add_reminder">Add Reminder</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch pt-3">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ $insurance->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Is Active</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3">{{ $insurance->description }}</textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
            width: '100%'
        });

        $('#insurance_vehicle_id').on('select2:open', function () {
            $('.select2-search__field').attr('placeholder', 'Search vehicle...');
        });
    });
</script>
@endsection

