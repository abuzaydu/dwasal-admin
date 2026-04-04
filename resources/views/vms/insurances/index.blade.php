@extends('layouts.vms')
@section('content')
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('vehicles-dash') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>            
        <div class="col-lg-6 col-md-4 col-sm-12 d-flex align-items-center justify-content-end gap-2 flex-nowrap">
            <div class="d-flex flex-wrap justify-content-start justify-content-md-end align-items-center gap-2">

                <form class="dashform" action="{{ url('f-insurance') }}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <button type="button" class="btn btn-default btn-sm w-auto" id="reportrange"
                        style="white-space: nowrap;">
                        <i class="fa fa-calendar"></i>
                        <span id="reportrange-label" class="mx-1"></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </form>

                <a href="{{ route('insurance-companies.index') }}" class="btn btn-secondary btn-sm text-nowrap">
                    <i class="fa fa-building"></i> Insurance Companies
                </a>
                <a href="{{ route('ir-periods.index') }}" class="btn btn-secondary btn-sm text-nowrap">
                    <i class="fa fa-clock-o"></i> IR Periods
                </a>
                <a href="{{ route('insurance.create') }}" class="btn btn-success btn-sm text-nowrap">
                    <i class="fa fa-plus"></i> Add Insurance
                </a>
            </div>
        </div>
    </div>
</div>

    <div class="row clearfix">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @php
                    $activeAll = empty($status) || $status === 'all';
                    $activeValid = $status === 'valid';
                    $activeExpiring = $status === 'expiring';
                    $activeExpired = $status === 'expired';
                    $activeMissing = $status === 'missing';
                @endphp

                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeAll ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_ins_all" role="tab">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeValid ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_ins_valid" role="tab">Valid</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeExpiring ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_ins_expiring" role="tab">Expiring Soon</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeExpired ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_ins_expired" role="tab">Expired</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeMissing ? 'active' : '' }}" data-bs-toggle="tab" href="#tab_ins_missing" role="tab">Missing</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade {{ $activeAll ? 'show active' : '' }}" id="tab_ins_all" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm datatable" id="insurances-tab-all">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Insurance Company</th>
                                        <th>Policy No.</th>
                                        <th>Period</th>
                                        <th>Charge Payable</th>
                                        <th>Deductible</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($insurancesAll ?? [] as $insurance)
                                        <tr>
                                            <td>{{ $insurance->vehicle?->plate_no ?? 'N/A' }}</td>
                                            <td>{{ $insurance->insuranceCompany?->name ?? 'N/A' }}</td>
                                            <td>{{ $insurance->policy_number }}</td>
                                            <td>{{ $insurance->irPeriod?->period ?? 'N/A' }}</td>
                                            <td>{{ number_format((float)$insurance->charge_payable, 2) }}</td>
                                            <td>{{ number_format((float)$insurance->deductible, 2) }}</td>
                                            <td>{{ optional($insurance->start_date)->format('d/m/Y') }}</td>
                                            <td>{{ optional($insurance->end_date)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($insurance->status === 'EXPIRED')
                                                    <span class="badge bg-danger">Expired</span>
                                                @elseif($insurance->status === 'EXPIRING_SOON')
                                                    <span class="badge bg-warning">Expiring Soon</span>
                                                @else
                                                    <span class="badge bg-success">Valid</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('insurance.download', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-primary">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <a href="{{ route('insurance.edit', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-secondary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('insurance.destroy', encrypt($insurance->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this insurance?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(($insurancesAll ?? collect())->isEmpty())
                                <div class="text-center text-muted py-2">No insurance records found.</div>
                            @endif
                        </div>
                    </div>

                    <div class="tab-pane fade {{ $activeValid ? 'show active' : '' }}" id="tab_ins_valid" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm datatable" id="insurances-tab-valid">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Insurance Company</th>
                                        <th>Policy No.</th>
                                        <th>Period</th>
                                        <th>Charge Payable</th>
                                        <th>Deductible</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($validInsurances ?? [] as $insurance)
                                        <tr>
                                            <td>{{ $insurance->vehicle?->plate_no ?? 'N/A' }}</td>
                                            <td>{{ $insurance->insuranceCompany?->name ?? 'N/A' }}</td>
                                            <td>{{ $insurance->policy_number }}</td>
                                            <td>{{ $insurance->irPeriod?->period ?? 'N/A' }}</td>
                                            <td>{{ number_format((float)$insurance->charge_payable, 2) }}</td>
                                            <td>{{ number_format((float)$insurance->deductible, 2) }}</td>
                                            <td>{{ optional($insurance->start_date)->format('d/m/Y') }}</td>
                                            <td>{{ optional($insurance->end_date)->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-success">Valid</span></td>
                                            <td>
                                                <a href="{{ route('insurance.download', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-primary">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <a href="{{ route('insurance.edit', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-secondary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('insurance.destroy', encrypt($insurance->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this insurance?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(($validInsurances ?? collect())->isEmpty())
                                <div class="text-center text-muted py-2">No valid insurance records.</div>
                            @endif
                        </div>
                    </div>

                    <div class="tab-pane fade {{ $activeExpiring ? 'show active' : '' }}" id="tab_ins_expiring" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm datatable" id="insurances-tab-expiring">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Insurance Company</th>
                                        <th>Policy No.</th>
                                        <th>Period</th>
                                        <th>Charge Payable</th>
                                        <th>Deductible</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiringSoonInsurances ?? [] as $insurance)
                                        <tr>
                                            <td>{{ $insurance->vehicle?->plate_no ?? 'N/A' }}</td>
                                            <td>{{ $insurance->insuranceCompany?->name ?? 'N/A' }}</td>
                                            <td>{{ $insurance->policy_number }}</td>
                                            <td>{{ $insurance->irPeriod?->period ?? 'N/A' }}</td>
                                            <td>{{ number_format((float)$insurance->charge_payable, 2) }}</td>
                                            <td>{{ number_format((float)$insurance->deductible, 2) }}</td>
                                            <td>{{ optional($insurance->start_date)->format('d/m/Y') }}</td>
                                            <td>{{ optional($insurance->end_date)->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-warning">Expiring Soon</span></td>
                                            <td>
                                                <a href="{{ route('insurance.download', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-primary">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <a href="{{ route('insurance.edit', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-secondary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('insurance.destroy', encrypt($insurance->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this insurance?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(($expiringSoonInsurances ?? collect())->isEmpty())
                                <div class="text-center text-muted py-2">No expiring soon records.</div>
                            @endif
                        </div>
                    </div>

                    <div class="tab-pane fade {{ $activeExpired ? 'show active' : '' }}" id="tab_ins_expired" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm datatable" id="insurances-tab-expired">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Insurance Company</th>
                                        <th>Policy No.</th>
                                        <th>Period</th>
                                        <th>Charge Payable</th>
                                        <th>Deductible</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiredInsurances ?? [] as $insurance)
                                        <tr>
                                            <td>{{ $insurance->vehicle?->plate_no ?? 'N/A' }}</td>
                                            <td>{{ $insurance->insuranceCompany?->name ?? 'N/A' }}</td>
                                            <td>{{ $insurance->policy_number }}</td>
                                            <td>{{ $insurance->irPeriod?->period ?? 'N/A' }}</td>
                                            <td>{{ number_format((float)$insurance->charge_payable, 2) }}</td>
                                            <td>{{ number_format((float)$insurance->deductible, 2) }}</td>
                                            <td>{{ optional($insurance->start_date)->format('d/m/Y') }}</td>
                                            <td>{{ optional($insurance->end_date)->format('d/m/Y') }}</td>
                                            <td><span class="badge bg-danger">Expired</span></td>
                                            <td>
                                                <a href="{{ route('insurance.download', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-primary">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <a href="{{ route('insurance.edit', encrypt($insurance->id)) }}" class="btn btn-xs btn-outline-secondary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <form action="{{ route('insurance.destroy', encrypt($insurance->id)) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this insurance?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(($expiredInsurances ?? collect())->isEmpty())
                                <div class="text-center text-muted py-2">No expired records.</div>
                            @endif
                        </div>
                    </div>

                    <div class="tab-pane fade {{ $activeMissing ? 'show active' : '' }}" id="tab_ins_missing" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm datatable" id="insurances-tab-missing">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Insurance Company</th>
                                        <th>Policy No.</th>
                                        <th>Period</th>
                                        <th>Charge Payable</th>
                                        <th>Deductible</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($missingVehicles ?? [] as $vehicle)
                                        <tr>
                                            <td>{{ $vehicle->plate_no ?? 'N/A' }}</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td>—</td>
                                            <td><span class="badge bg-secondary">Missing</span></td>
                                            <td style="text-align:center;">
                                                <a href="{{ route('insurance.create', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-xs btn-primary">
                                                    <i class="fa fa-plus"></i> Add
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if(($missingVehicles ?? collect())->isEmpty())
                                <div class="text-center text-muted py-2">No missing vehicles found.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-scripts')
<script>
    $(document).ready(function(){
        $('.datatable').DataTable({
            paging: true,
            ordering: true,
            searching: true,
            responsive: true
        });
    });
</script>
@endsection
