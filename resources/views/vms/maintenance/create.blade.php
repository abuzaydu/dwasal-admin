@extends('layouts.vms')

@section('content')
    <div class="block-header pt-4">
        <div class="row align-items-center">
            <div class="col-lg-8 col-md-8 col-sm-12 mb-2">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('maintenance') }}">Maintenance</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 mb-2 text-end">
                <a href="{{ url('maintenance') }}" class="btn btn-warning btn-sm">
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
                        <i class="fa fa-wrench me-2 text-success"></i> {{ $page }}
                    </h6>
                </div>

                <form method="POST" action="{{ route('maintenance.store') }}" enctype="multipart/form-data" id="maintenance-form">
                    @csrf
                    <div class="card-body" style="padding: 0.75rem;">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Vehicle <span class="text-danger">*</span></label>
                                <select name="vehicle_id" class="form-select form-select-sm py-1" style="max-width: 260px;" required>
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->plate_no }} {{ $vehicle->vehicle_name ? '- '.$vehicle->vehicle_name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select form-select-sm py-1" style="max-width: 260px;" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->fname }} {{ $employee->lname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Maintenance Type <span class="text-danger">*</span></label>
                                @if($maintenanceTypes->isEmpty())
                                    <div class="alert alert-warning py-1 mb-2" style="line-height:1.1; width: max-content; max-width: 280px;">
                                        No maintenance types found. Please add one from <b>Maintenance Types</b>.
                                    </div>
                                @endif
                                <select name="maintenance_type_id" class="form-select form-select-sm py-1" style="max-width: 260px;" required>
                                    <option value="">Select Type</option>
                                    @foreach($maintenanceTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('maintenance_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control form-control-sm py-1" style="max-width: 190px;" value="{{ old('date') }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Req Type </label>
                                <input type="text" name="req_type" class="form-control form-control-sm py-1" style="max-width: 220px;">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Priority </label>
                                <select name="priority" class="form-select form-select-sm py-1" style="max-width: 220px;" required>
                                    @foreach(['Low','Normal','High','Urgent'] as $p)
                                        <option value="{{ $p }}" {{ old('priority', 'Normal') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Service Title <span class="text-danger">*</span></label>
                                <input type="text" name="service_title" class="form-control form-control-sm py-1" >
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Charge Bear By</label>
                                <select name="charge_bear_by" class="form-select form-select-sm py-2">
                                    <option value="Company">Company</option>
                                    <option value="Employee">Employee</option>
                                    <option value="Vendor">Vendor</option>
                                    <option value="Insurance">Insurance</option>
                                </select>                            
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Remarks</label>
                                <textarea name="remarks" rows="2" class="form-control form-control-sm py-1" style="max-width: 780px;">{{ old('remarks') }}</textarea>
                            </div>
                        </div>

                        <hr class="my-2" />

                        <div class="row g-2">
                            <div class="col-md-12">
                                <h6 class="mb-1"><i class="fa fa-cubes me-1 text-primary"></i> Items (Parts)</h6>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Search Part (click to add) <span class="text-danger">*</span></label>
                                <input type="text" id="search_part_key" class="form-control form-control-sm" placeholder="Search by part code or name..." autocomplete="off">
                                <ul id="searchResultParts" class="list-group mt-1" style="max-height: 180px; overflow:auto;"></ul>
                            </div>

                            @php
                                $oldItems = old('items', []);
                                $oldKeys = array_keys($oldItems);
                                $maxKey = -1;
                                foreach ($oldKeys as $k) {
                                    if (is_numeric($k)) {
                                        $maxKey = max($maxKey, (int)$k);
                                    }
                                }
                                $initialItemIndex = $maxKey + 1;
                            @endphp

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:60px;">#</th>
                                                <th>Part</th>
                                                <th style="width:130px; text-align:center;">Qty</th>
                                                <th style="width:170px; text-align:center;">Unit Price</th>
                                                <th style="width:160px; text-align:center;">Total</th>
                                                <th style="width:60px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-body">
                                            @foreach($oldItems as $idx => $it)
                                                @php
                                                    $qty = (float) ($it['qty'] ?? 0);
                                                    $unit = (float) ($it['unit_price'] ?? 0);
                                                    $total = $qty * $unit;
                                                @endphp
                                                <tr id="item-row-{{ $idx }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="text-muted">Part ID: {{ $it['part_id'] ?? '-' }}</td>
                                                    <td style="text-align:center;">
                                                        <input type="number" step="any" min="0" id="qty-{{ $idx }}" class="form-control form-control-sm text-center" style="max-width: 110px; margin: 0 auto;" name="items[{{ $idx }}][qty]" value="{{ $qty }}" oninput="recalcTotal({{ $idx }})">
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <input type="number" step="0.01" min="0" id="unit-{{ $idx }}" class="form-control form-control-sm text-center" style="max-width: 140px; margin: 0 auto;" name="items[{{ $idx }}][unit_price]" value="{{ $unit }}" oninput="recalcTotal({{ $idx }})">
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <span id="total-{{ $idx }}">{{ number_format($total, 2) }}</span>
                                                        <input type="hidden" name="items[{{ $idx }}][part_id]" value="{{ $it['part_id'] ?? '' }}">
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <a href="javascript:;" class="text-danger" onclick="removeItemRow({{ $idx }});"><i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-end border-top pt-2">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-save me-1"></i> Save Maintenance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        let itemIndex = {{ $initialItemIndex }};

        function recalcTotal(idx) {
            const qty = parseFloat(document.getElementById('qty-' + idx)?.value || 0);
            const unit = parseFloat(document.getElementById('unit-' + idx)?.value || 0);
            const total = qty * unit;
            const el = document.getElementById('total-' + idx);
            if (el) el.innerText = total.toFixed(2);
        }

        function removeItemRow(idx) {
            const row = document.getElementById('item-row-' + idx);
            if (row) row.remove();
            renumberItems();
        }

        function addItemRow(part) {
            const idx = itemIndex++;

            const row = document.createElement('tr');
            row.setAttribute('id', 'item-row-' + idx);

            row.innerHTML = `
                <td></td>
                <td class="text-muted">${part.part_no} ${part.part_name}</td>
                <td style="text-align:center;">
                    <input type="number" step="any" min="0" id="qty-${idx}" class="form-control form-control-sm text-center"
                        style="max-width: 110px; margin: 0 auto;" name="items[${idx}][qty]" value="1" oninput="recalcTotal(${idx})">
                </td>
                <td style="text-align:center;">
                    <input type="number" step="0.01" min="0" id="unit-${idx}" class="form-control form-control-sm text-center"
                        style="max-width: 140px; margin: 0 auto;" name="items[${idx}][unit_price]" value="0" oninput="recalcTotal(${idx})">
                </td>
                <td style="text-align:center;">
                    <span id="total-${idx}">0.00</span>
                    <input type="hidden" name="items[${idx}][part_id]" value="${part.id}">
                </td>
                <td style="text-align:center;">
                    <a href="javascript:;" class="text-danger" onclick="removeItemRow(${idx});"><i class="fa fa-trash"></i></a>
                </td>
            `;

            document.getElementById('items-body').appendChild(row);
            recalcTotal(idx);
            renumberItems();
        }

        function renumberItems() {
            const rows = document.querySelectorAll('#items-body tr');
            rows.forEach((tr, i) => {
                const td = tr.querySelector('td');
                if (td) td.innerText = i + 1;
            });
        }

        $(document).ready(function () {
            $('#search_part_key').on('keyup', function () {
                const query = $(this).val();
                if (!query || query.length < 2) {
                    $('#searchResultParts').empty();
                    return;
                }

                $.ajax({
                    url: "{{ url('search-part') }}",
                    type: 'GET',
                    data: { search_key: query },
                    success: function (response) {
                        $('#searchResultParts').empty();
                        if (!response || response.length === 0) return;

                        response.forEach(function (p) {
                            $('#searchResultParts').append(`
                                <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center part-pick"
                                    style="cursor:pointer;"
                                    data-id="${p.id}"
                                    data-part_no="${String(p.part_no ?? '').replace(/"/g, '&quot;')}"
                                    data-part_name="${String(p.part_name ?? '').replace(/"/g, '&quot;')}">
                                    <span>${p.part_no} ${p.part_name}</span>
                                    <span class="badge bg-success rounded-pill"><i class="fa fa-arrow-right"></i></span>
                                </li>
                            `);
                        });
                    }
                });
            });

            $(document).on('click', '.part-pick', function () {
                const part = {
                    id: parseInt($(this).data('id')),
                    part_no: $(this).data('part_no'),
                    part_name: $(this).data('part_name')
                };
                addItemRow(part);
                $('#search_part_key').val('');
                $('#searchResultParts').empty();
            });
        });
    </script>
@endsection

