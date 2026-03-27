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
                <a href="{{ route('maintenance.show', encrypt($maintenance->id)) }}" class="btn btn-light btn-sm">
                    <i class="fa fa-eye me-1"></i> View
                </a>
                <a href="{{ url('maintenance') }}" class="btn btn-warning btn-sm ms-1">
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

                <form method="POST" action="{{ route('maintenance.update', encrypt($maintenance->id)) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body" style="padding: 0.75rem;">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Vehicle <span class="text-danger">*</span></label>
                                <select name="vehicle_id" class="form-select form-select-sm py-1" style="max-width: 260px;" required>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ $maintenance->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->plate_no }} {{ $vehicle->vehicle_name ? '- '.$vehicle->vehicle_name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select form-select-sm py-1" style="max-width: 260px;" required>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $maintenance->employee_id == $employee->id ? 'selected' : '' }}>
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
                                    @foreach($maintenanceTypes as $type)
                                        <option value="{{ $type->id }}" {{ $maintenance->maintenance_type_id == $type->id ? 'selected' : '' }}>
                                            {{ $type->type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control form-control-sm py-1" style="max-width: 190px;" value="{{ $maintenance->date }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Maintenance Code <span class="text-danger">*</span></label>
                                <input type="text" name="maintenance_code" class="form-control form-control-sm py-1" style="max-width: 220px;" value="{{ $maintenance->maintenance_code }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Req Type <span class="text-danger">*</span></label>
                                <input type="text" name="req_type" class="form-control form-control-sm py-1" style="max-width: 220px;" value="{{ $maintenance->req_type }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Priority <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select form-select-sm py-1" style="max-width: 220px;" required>
                                    @foreach(['Low','Normal','High','Urgent'] as $p)
                                        <option value="{{ $p }}" {{ $maintenance->priority === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select form-select-sm py-1" style="max-width: 220px;" required>
                                    @foreach(['Pending','In Progress','Completed'] as $st)
                                        <option value="{{ $st }}" {{ $maintenance->status === $st ? 'selected' : '' }}>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small mb-1">Service Title <span class="text-danger">*</span></label>
                                <input type="text" name="service_title" class="form-control form-control-sm py-1" value="{{ $maintenance->service_title }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Charge Bear By</label>
                                <input type="text" name="charge_bear_by" class="form-control form-control-sm py-1" style="max-width: 220px;" value="{{ $maintenance->charge_bear_by }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small mb-1">Charge</label>
                                <input type="number" name="charge" step="0.01" min="0" class="form-control form-control-sm py-1" style="max-width: 220px;" value="{{ $maintenance->charge ?? 0 }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Remarks</label>
                                <textarea name="remarks" rows="2" class="form-control form-control-sm py-1" style="max-width: 780px;">{{ $maintenance->remarks }}</textarea>
                            </div>
                        </div>

                        <hr class="my-2" />

                        <div class="row g-2">
                            <div class="col-md-12">
                                <h6 class="mb-1"><i class="fa fa-cubes me-1 text-primary"></i> Items (Parts)</h6>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Search Part (click to add)</label>
                                <input type="text" id="search_part_key" class="form-control form-control-sm" placeholder="Search by part code or name..." autocomplete="off">
                                <ul id="searchResultParts" class="list-group mt-1" style="max-height: 180px; overflow:auto;"></ul>
                            </div>

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
                                            @foreach($maintenance->items as $item)
                                                @php
                                                    $idx = $loop->index;
                                                    $qty = (float) ($item->qty ?? 0);
                                                    $unit = (float) ($item->unit_price ?? 0);
                                                    $total = $qty * $unit;
                                                    $partNo = $item->part->part_no ?? '';
                                                    $partName = $item->part->part_name ?? '';
                                                @endphp
                                                <tr id="item-row-{{ $idx }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="text-muted">{{ $partNo }} {{ $partName }}</td>
                                                    <td style="text-align:center;">
                                                        <input type="number" step="any" min="0" id="qty-{{ $idx }}" class="form-control form-control-sm text-center"
                                                            style="max-width: 110px; margin: 0 auto;" name="items[{{ $idx }}][qty]" value="{{ $qty }}" oninput="recalcTotal({{ $idx }})">
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <input type="number" step="0.01" min="0" id="unit-{{ $idx }}" class="form-control form-control-sm text-center"
                                                            style="max-width: 140px; margin: 0 auto;" name="items[{{ $idx }}][unit_price]" value="{{ $unit }}" oninput="recalcTotal({{ $idx }})">
                                                    </td>
                                                    <td style="text-align:center;">
                                                        <span id="total-{{ $idx }}">{{ number_format($total, 2) }}</span>
                                                        <input type="hidden" name="items[{{ $idx }}][part_id]" value="{{ $item->part_id }}">
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

                        <hr class="my-2" />

                        <div class="row g-2">
                            <div class="col-md-12">
                                <h6 class="mb-1"><i class="fa fa-camera me-1 text-primary"></i> Photos</h6>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small mb-1">Existing photos</label>
                                @if($maintenance->photos->isEmpty())
                                    <div class="alert alert-light py-2 mb-2">No photos uploaded.</div>
                                @else
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($maintenance->photos as $photo)
                                            <div class="border rounded p-1" style="width: 110px;">
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

                    <div class="card-footer text-end border-top pt-2">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fa fa-save me-1"></i> Save Changes
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
        let itemIndex = {{ $maintenance->items->count() }};

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
            renumberItems();
            recalcTotal(idx);
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

