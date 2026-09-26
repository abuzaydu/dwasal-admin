@extends('layouts.vms')

@section('content')
<div class="block-header pt-4">
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('field-operations.index') }}"><i class="fa fa-file-text-o"></i></a></li>
        <li class="breadcrumb-item">Field Operations</li>
        <li class="breadcrumb-item active">{{ $page }}</li>
    </ul>
</div>

@if($errors->any())
    <div class="alert alert-danger"><strong>Please correct the following:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<form action="{{ $formAction }}" method="POST" data-draft-enabled="0" data-draft-key="field-operations-new-{{ $record->record_no }}" data-autosave-url="{{ $record->exists ? route('field-operations.autosave', $record) : route('field-operations.store') }}" data-autosave-create="{{ $record->exists ? '0' : '1' }}">
    @csrf
    @if($formMethod !== 'POST') @method($formMethod) @endif

    <div class="card mb-3 company-summary">
        <div class="card-body d-flex align-items-center gap-3">
            @if($company->logo_url)
                <img src="{{ asset('storage/clogos/' . $company->logo_url) }}" alt="{{ $company->name }} logo" class="company-logo">
            @endif
            <div>
                <h5 class="mb-1">{{ $company->name }}</h5>
                <div class="text-muted small">{{ $company->address }}{{ $company->postal_code ? ', ' . $company->postal_code : '' }}{{ $company->city ? ', ' . $company->city : '' }}</div>
                <div class="text-muted small">{{ $company->mobile }}{{ $company->email ? ' | ' . $company->email : '' }}{{ $company->tin ? ' | TIN: ' . $company->tin : '' }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h6 class="fw-bold mb-0"><i class="fa fa-info-circle text-primary me-2"></i> Document Details</h6></div>
        <div class="card-body">
            <div class="row g-3">
                @php
                    $fields = [
                        ['record_date', 'Date', 'date', true, ''],
                        ['name', 'Name', 'text', true, 'ANDERSON MSELE'],
                        ['title', 'Title', 'text', false, 'MNINI'],
                        ['contact', 'Contact', 'text', false, '0651791916'],
                        ['location', 'Location', 'text', false, 'MVERA'],
                        ['sign_in_time', 'Sign In Time', 'time', false, ''],
                        ['sign_out_time', 'Sign Out Time', 'time', false, ''],
                        ['fuel_in', 'Fuel In', 'number', false, ''],
                        ['fuel_out', 'Fuel Out', 'number', false, ''],
                        ['quantity_of_trips', 'Quantity of Trips', 'number', false, ''],
                    ];
                @endphp
                <div class="col-md-4">
                    <label class="form-label">No.</label>
                    <input value="{{ $record->record_no }}" class="form-control bg-light" readonly>
                    <div class="form-text">Generated automatically by the system.</div>
                </div>
                @foreach($fields as [$name, $label, $type, $required, $placeholder])
                    @php
                        $inputValue = old($name, $record->{$name});
                        if ($name === 'record_date' && $inputValue) {
                            $inputValue = \Carbon\Carbon::parse($inputValue)->format('Y-m-d');
                        } elseif (in_array($name, ['sign_in_time', 'sign_out_time']) && $inputValue) {
                            $inputValue = \Carbon\Carbon::parse($inputValue)->format('H:i');
                        }
                    @endphp
                    <div class="col-md-4">
                        <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
                        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ $inputValue }}" placeholder="{{ $placeholder }}" @if(in_array($name, ['fuel_in', 'fuel_out'])) step="0.01" min="0" @elseif($name === 'quantity_of_trips') min="0" @endif class="form-control" @required($required)>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @php
        $formRows = old('rows', $rows);
        $rowFields = ['truck_no', 'cubic', 'trip_count', 'amount_per_trip', 'expenditure_entries_json', 'cash_entries_json'];
    @endphp
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center"><h6 class="fw-bold mb-0"><i class="fa fa-table text-primary me-2"></i> Operations Rows</h6><button type="button" class="btn btn-primary btn-sm compact-action" data-bs-toggle="modal" data-bs-target="#operationRowModal"><i class="fa fa-plus me-1"></i> Add Row</button></div>
        <div class="card-body">
            <p class="text-muted small mb-3">Add one operation at a time. Expenditures and cash submissions can each contain multiple entries.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0" id="operationsSummary">
                    <thead class="table-light"><tr><th>No.</th><th>Truck No.</th><th>Cubic</th><th>Trips</th><th>Amount / Trip</th><th>Total Amount of Trips</th><th>Expense</th><th>Cash Submitted</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($formRows as $index => $row)
                            @if(collect($row)->contains(fn ($value) => is_array($value) ? count($value) > 0 : $value !== null && $value !== ''))
                                <tr data-row-index="{{ $index }}">
                                    <td>{{ $index + 1 }}</td>
                                    @php
                                        $expenditureEntries = $row['expenditure_entries'] ?? json_decode($row['expenditure_entries_json'] ?? '[]', true) ?? [];
                                        $cashEntries = $row['cash_entries'] ?? json_decode($row['cash_entries_json'] ?? '[]', true) ?? [];
                                        $amount = (float) ($row['amount_per_trip'] ?? 0);
                                        $trips = (int) ($row['trip_count'] ?? 0);
                                        $rowCashSubmitted = ($amount * $trips) - (float) collect($expenditureEntries)->sum('amount');
                                    @endphp
                                    <td class="summary-truck_no">{{ $row['truck_no'] ?? '' }}</td>
                                    <td class="summary-cubic">{{ number_format((float) ($row['cubic'] ?? 0), 0) }}</td>
                                    <td class="summary-trip_count">{{ $trips }}</td>
                                    <td class="summary-amount_per_trip">{{ number_format($amount, 0) }}</td>
                                    <td class="summary-total_amount">{{ number_format($amount * $trips, 0) }}</td>
                                    <td class="summary-expenditure_entries_json">{{ number_format((float) collect($expenditureEntries)->sum('amount'), 0) }}</td>
                                    <td class="summary-cash_entries_json">{{ number_format($rowCashSubmitted, 0) }}</td>
                                    <td class="text-nowrap"><button type="button" class="btn btn-sm btn-outline-secondary edit-row" title="Edit row"><i class="fa fa-pencil"></i></button> <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove row"><i class="fa fa-trash"></i></button>@foreach($rowFields as $field)<input type="hidden" name="rows[{{ $index }}][{{ $field }}]" value="{{ $field === 'expenditure_entries_json' ? json_encode($expenditureEntries) : ($field === 'cash_entries_json' ? json_encode($cashEntries) : ($row[$field] ?? '')) }}" data-field="{{ $field }}">@endforeach</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="emptyOperations" class="text-center text-muted py-4">No operation rows added yet.</div>
        </div>
    </div>

    <div class="modal fade" id="operationRowModal" tabindex="-1" aria-labelledby="operationRowModalLabel" aria-hidden="true">
        <div class="modal-dialog field-operation-modal modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="operationRowModalLabel">Add Operation Row</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <div class="modal-section-title">Trip information</div>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6"><label class="form-label" for="row_truck_no">Truck No.</label><input id="row_truck_no" class="form-control" placeholder="e.g. T 187 EFX"></div>
                    <div class="col-12 col-md-3"><label class="form-label" for="row_cubic">Cubic (m3)</label><input id="row_cubic" type="number" min="0" step="0.01" class="form-control" placeholder="0"></div>
                    <div class="col-12 col-md-3"><label class="form-label" for="row_trip_count">Trips</label><input id="row_trip_count" type="number" min="0" class="form-control" placeholder="0"></div>
                </div>
                <div class="modal-section-title">Amounts</div>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4"><label class="form-label" for="row_amount_per_trip">Amount Per Trip</label><input id="row_amount_per_trip" type="number" min="0" step="0.01" class="form-control" placeholder="0"></div>
                    <div class="col-12 col-md-8"><label class="form-label">Total Amount of Trips</label><input id="row_total_amount" class="form-control bg-light" readonly value="0"></div>
                </div>
                <div class="modal-section-title">Expense entries</div>
                <div class="entry-list" id="expenditureEntryList"></div>
                <div class="row g-2 align-items-end mb-4"><div class="col-12 col-md-4"><label class="form-label" for="new_expenditure_amount">Amount</label><input id="new_expenditure_amount" type="number" min="0" step="0.01" class="form-control" placeholder="0"></div><div class="col-12 col-md-6"><label class="form-label" for="new_expenditure_details">Details</label><input id="new_expenditure_details" class="form-control" placeholder="What was it used for?"></div><div class="col-12 col-md-2"><button type="button" class="btn btn-outline-primary btn-sm compact-action w-100" id="addExpenditureEntry"><i class="fa fa-plus"></i> Add</button></div></div>
                <div class="modal-section-title">Cash Submitted</div>
                <div class="alert alert-light border small mb-3 mb-0">Cash Submitted is auto-calculated as: Total Amount of Trips - Expense.</div>
                <div class="entry-list" id="cashEntryList" style="display:none;"></div>
                <div class="row g-2 align-items-end" style="display:none;"><div class="col-12 col-md-4"><label class="form-label" for="new_cash_amount">Amount</label><input id="new_cash_amount" type="number" min="0" step="0.01" class="form-control" placeholder="0"></div><div class="col-12 col-md-6"><label class="form-label" for="new_cash_details">Details</label><input id="new_cash_details" class="form-control" placeholder="Submission note"></div><div class="col-12 col-md-2"><button type="button" class="btn btn-outline-primary btn-sm compact-action w-100" id="addCashEntry"><i class="fa fa-plus"></i> Add</button></div></div>
            </div>
            <div class="modal-footer"><span id="rowSaveStatus" class="text-muted small me-auto"></span><button type="button" class="btn btn-light compact-action" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i> Close</button><button type="button" class="btn btn-primary compact-action" id="confirmOperationRow"><i class="fa fa-check me-1"></i> Confirm</button></div>
        </div></div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><h6 class="fw-bold mb-0"><i class="fa fa-pencil-square-o text-primary me-2"></i> Closing Details</h6></div>
        <div class="card-body row g-3">
            <div class="col-md-8"><label class="form-label" for="machine_condition">Machine Condition</label><textarea name="machine_condition" id="machine_condition" rows="3" class="form-control">{{ old('machine_condition', $record->machine_condition) }}</textarea></div>
            <div class="col-md-4"><label class="form-label" for="signature">Signature</label><input name="signature" id="signature" value="{{ old('signature', $record->signature) }}" class="form-control"></div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('field-operations.index') }}" class="btn btn-light">Cancel</a>
        <button type="submit" class="btn btn-primary compact-action"><i class="fa fa-save me-1"></i> Save</button>
    </div>
</form>

@section('page-styles')
<style>
    .company-logo { width: 58px; height: 58px; object-fit: contain; }
    #operationsSummary { font-size: .86rem; }
    #operationsSummary input[type="hidden"] { display: none; }
    #operationsSummary th { white-space: nowrap; }
    #operationsSummary .summary-expenditure_entries_json,
    #operationsSummary .summary-cash_entries_json { white-space: nowrap; }
    .modal-section-title { margin: 0 0 .75rem; padding-bottom: .4rem; border-bottom: 1px solid #e6e9ef; color: #495057; font-size: .78rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    .modal .form-label { margin-bottom: .35rem; font-size: .86rem; font-weight: 600; }
    .modal .form-control { min-height: 42px; }
    .modal textarea.form-control { min-height: 78px; }
    .field-operation-modal { max-width: 640px; }
    .compact-action { display: inline-flex !important; flex: 0 0 auto !important; width: auto !important; min-width: 0 !important; padding: .35rem .75rem !important; white-space: nowrap; }
    .entry-list:empty::before { content: 'No entries added'; display: block; padding: .5rem .75rem; margin-bottom: .75rem; border: 1px dashed #ced4da; color: #6c757d; font-size: .82rem; }
    .entry-item { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .45rem .65rem; margin-bottom: .5rem; border: 1px solid #e6e9ef; border-radius: .25rem; background: #f8f9fa; font-size: .84rem; }
    .entry-item-details { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .entry-item-amount { font-weight: 700; white-space: nowrap; }
    .field-operation-modal .col-md-3 .form-control { max-width: 180px; }
    .field-operation-modal .col-md-4 .form-control { max-width: 240px; }
    .field-operation-modal .col-md-8 .form-control { max-width: 420px; }
    .note-editor.note-frame { border-color: #ced4da; }
    .note-editor .note-editable { min-height: 120px; }
    @media (max-width: 575.98px) {
        .field-operation-modal { width: calc(100% - 1rem); margin: .5rem auto; }
        .field-operation-modal .modal-body { padding: 1rem; }
    }
</style>
@endsection
@section('page-scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    $(function () {
        const modal = $('#operationRowModal');
        const form = $('form[data-draft-key]');
        const draftEnabled = false;
        const draftKey = form.data('draft-key');
        let autosaveUrl = form.data('autosave-url');
        let isNewRecord = form.data('autosave-create') === 1 || form.data('autosave-create') === '1';
        let editingIndex = null;
        let editingEntryKind = null;
        let editingEntryIndex = null;
        let draftTimer = null;
        const fields = ['truck_no', 'cubic', 'trip_count', 'amount_per_trip', 'expenditure_entries_json', 'cash_entries_json'];
        const displayFields = ['truck_no', 'cubic', 'trip_count', 'amount_per_trip', 'total_amount', 'expenditure_entries_json', 'cash_entries_json'];
        let expenditureEntries = [];
        let cashEntries = [];

        function formatValue(field, value, values) {
            if (field === 'total_amount') return ((Number(values.amount_per_trip) || 0) * (Number(values.trip_count) || 0)).toLocaleString('en-US', { maximumFractionDigits: 2 });
            if (field === 'expenditure_entries_json') return expenditureTotal(values).toLocaleString('en-US', { maximumFractionDigits: 2 });
            if (field === 'cash_entries_json') return (((Number(values.amount_per_trip) || 0) * (Number(values.trip_count) || 0)) - expenditureTotal(values)).toLocaleString('en-US', { maximumFractionDigits: 2 });
            if (['cubic', 'amount_per_trip'].includes(field) && value !== '') return Number(value).toLocaleString('en-US', { maximumFractionDigits: 2 });
            return value || '';
        }

        function entriesFrom(value) {
            try { return value ? JSON.parse(value) : []; } catch (error) { return []; }
        }

        function expenditureTotal(values) { return entriesFrom(values.expenditure_entries_json).reduce((sum, entry) => sum + (Number(entry.amount) || 0), 0); }
        function cashTotal(values) {
            const totalAmount = (Number(values.amount_per_trip) || 0) * (Number(values.trip_count) || 0);
            return totalAmount - expenditureTotal(values);
        }

        function refreshEmptyState() {
            $('#emptyOperations').toggle($('#operationsSummary tbody tr').length === 0);
        }

        function clearModal() {
            $('#row_truck_no, #row_cubic, #row_trip_count, #row_amount_per_trip').val('');
            $('#row_total_amount').val('0');
            $('#new_expenditure_amount, #new_expenditure_details, #new_cash_amount, #new_cash_details').val('');
            expenditureEntries = [];
            cashEntries = [];
            editingEntryKind = null;
            editingEntryIndex = null;
            renderEntries();
            editingIndex = null;
            $('#operationRowModalLabel').text('Add Operation Row');
        }

        function clearModalAndDraftState() {
            clearModal();
        }

        function rowInput(index, field, value) {
            return $('<input>', { type: 'hidden', name: 'rows[' + index + '][' + field + ']', value: value, 'data-field': field });
        }

        function renderEntries() {
            function render(list, target, kind) {
                const container = $(target).empty();
                list.forEach((entry, entryIndex) => container.append('<div class="entry-item"><span class="entry-item-details">' + $('<div>').text(entry.details || 'No details').html() + '</span><span class="entry-item-amount">' + Number(entry.amount || 0).toLocaleString('en-US') + '</span><button type="button" class="btn btn-sm btn-outline-secondary edit-entry" data-kind="' + kind + '" data-entry-index="' + entryIndex + '" title="Edit entry"><i class="fa fa-pencil"></i></button><button type="button" class="btn btn-sm btn-outline-danger remove-entry" data-kind="' + kind + '" data-entry-index="' + entryIndex + '" title="Remove entry"><i class="fa fa-trash"></i></button></div>'));
            }
            render(expenditureEntries, '#expenditureEntryList', 'expenditure');
            render(cashEntries, '#cashEntryList', 'cash');
        }

        function addSummaryRow(index, values) {
            const cells = '<td>' + (index + 1) + '</td>' + displayFields.map(field => '<td class="summary-' + field + '">' + $('<div>').text(formatValue(field, values[field], values)).html() + '</td>').join('');
            const row = $('<tr>', { 'data-row-index': index }).html(cells + '<td class="text-nowrap"><button type="button" class="btn btn-sm btn-outline-secondary edit-row" title="Edit row"><i class="fa fa-pencil"></i></button> <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove row"><i class="fa fa-trash"></i></button></td>');
            fields.forEach(field => row.find('td:last').append(rowInput(index, field, values[field] || '')));
            $('#operationsSummary tbody').append(row);
            refreshEmptyState();
        }

        function updateTotalAmount() {
            $('#row_total_amount').val(((Number($('#row_amount_per_trip').val()) || 0) * (Number($('#row_trip_count').val()) || 0)).toLocaleString('en-US'));
        }

        function scheduleDraftSave() {
            return;
        }

        function saveDraft() {
            return;
        }

        function autosaveRecord() {
            if (isNewRecord) {
                $('#rowSaveStatus').removeClass('text-success text-danger').addClass('text-muted').text('Draft disabled');
                return Promise.resolve();
            }

            if ($('#machine_condition').next('.note-editor').length) {
                $('#machine_condition').val($('#machine_condition').summernote('code'));
            }

            $('#rowSaveStatus').removeClass('text-success text-danger').addClass('text-muted').text('Saving...');
            const payload = new FormData(form[0]);

            return fetch(autosaveUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: payload
            }).then(response => {
                if (!response.ok) throw new Error('Save failed');
                return response.json();
            }).then(data => {
                if (isNewRecord && data.update_url) {
                    autosaveUrl = data.update_url;
                    isNewRecord = false;
                    form.attr('action', data.update_url);
                    let methodField = form.find('input[name="_method"]');
                    if (!methodField.length) methodField = $('<input>', { type: 'hidden', name: '_method' }).appendTo(form);
                    methodField.val('PUT');
                    localStorage.removeItem(draftKey);
                }
                $('#rowSaveStatus').removeClass('text-muted text-danger').addClass('text-success').text('Saved');
            }).catch(() => {
                $('#rowSaveStatus').removeClass('text-muted text-success').addClass('text-danger').text('Save failed. Use Save Record below.');
            });
        }

        function loadDraft() {
            return;
        }

        $('#row_amount_per_trip, #row_trip_count').on('input', updateTotalAmount);
        $('#addExpenditureEntry').on('click', function () {
            const amount = Number($('#new_expenditure_amount').val()) || 0;
            const details = $('#new_expenditure_details').val().trim();
            if (!amount && !details) return;
            if (editingEntryKind === 'expenditure' && editingEntryIndex !== null) expenditureEntries.splice(editingEntryIndex, 1, { amount, details });
            else expenditureEntries.push({ amount, details });
            $('#new_expenditure_amount, #new_expenditure_details').val('');
            editingEntryKind = null; editingEntryIndex = null;
            renderEntries();
            scheduleDraftSave();
        });
        $('#addCashEntry').on('click', function () {
            const amount = Number($('#new_cash_amount').val()) || 0;
            const details = $('#new_cash_details').val().trim();
            if (!amount && !details) return;
            if (editingEntryKind === 'cash' && editingEntryIndex !== null) cashEntries.splice(editingEntryIndex, 1, { amount, details });
            else cashEntries.push({ amount, details });
            $('#new_cash_amount, #new_cash_details').val('');
            editingEntryKind = null; editingEntryIndex = null;
            renderEntries();
            scheduleDraftSave();
        });
        $(document).on('click', '.edit-entry', function () {
            const kind = $(this).data('kind');
            const index = Number($(this).data('entry-index'));
            const entry = (kind === 'expenditure' ? expenditureEntries : cashEntries)[index];
            editingEntryKind = kind;
            editingEntryIndex = index;
            $('#' + (kind === 'expenditure' ? 'new_expenditure_amount' : 'new_cash_amount')).val(entry.amount);
            $('#' + (kind === 'expenditure' ? 'new_expenditure_details' : 'new_cash_details')).val(entry.details);
        });
        $(document).on('click', '.remove-entry', function () {
            const list = $(this).data('kind') === 'expenditure' ? expenditureEntries : cashEntries;
            list.splice(Number($(this).data('entry-index')), 1);
            renderEntries();
            scheduleDraftSave();
        });

        $('#confirmOperationRow').on('click', function () {
            const values = {
                truck_no: $('#row_truck_no').val(),
                cubic: $('#row_cubic').val(),
                trip_count: $('#row_trip_count').val(),
                amount_per_trip: $('#row_amount_per_trip').val(),
                expenditure_entries_json: JSON.stringify(expenditureEntries),
                cash_entries_json: JSON.stringify(cashEntries)
            };
            if (!values.truck_no && !values.amount_per_trip && !values.cubic && !values.trip_count && !expenditureEntries.length && !cashEntries.length) {
                alert('Enter at least one operation value.');
                return;
            }
            const index = editingIndex === null ? [...Array(24).keys()].find(number => !$('[data-row-index="' + number + '"]').length) : editingIndex;
            if (index === undefined) { alert('The report supports a maximum of 24 rows.'); return; }
            if (editingIndex !== null) $('[data-row-index="' + editingIndex + '"]').remove();
            addSummaryRow(index, values);
            clearModal();
            bootstrap.Modal.getOrCreateInstance(modal[0]).hide();
        });

        $(document).on('click', '.edit-row', function () {
            const row = $(this).closest('tr');
            editingIndex = Number(row.data('row-index'));
            $('#row_truck_no').val(row.find('[data-field="truck_no"]').val());
            $('#row_cubic').val(row.find('[data-field="cubic"]').val());
            $('#row_trip_count').val(row.find('[data-field="trip_count"]').val());
            $('#row_amount_per_trip').val(row.find('[data-field="amount_per_trip"]').val());
            expenditureEntries = entriesFrom(row.find('[data-field="expenditure_entries_json"]').val());
            cashEntries = entriesFrom(row.find('[data-field="cash_entries_json"]').val());
            updateTotalAmount();
            renderEntries();
            $('#operationRowModalLabel').text('Edit Operation Row');
            bootstrap.Modal.getOrCreateInstance(modal[0]).show();
        });

        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
            expenditureEntries = [];
            cashEntries = [];
            editingIndex = null;
            editingEntryKind = null;
            editingEntryIndex = null;
            clearModal();
            refreshEmptyState();
        });
        form.on('submit', function () {
            localStorage.removeItem(draftKey);
        });
        form.on('input change', 'input, textarea, select', scheduleDraftSave);
        $('#machine_condition').on('summernote.change', scheduleDraftSave);
        $(document).on('click', '[data-bs-target="#operationRowModal"]', function () {
            editingIndex = null;
            expenditureEntries = [];
            cashEntries = [];
            clearModal();
        });
        if ($.fn.summernote) {
            $('#machine_condition').summernote({
                height: 120,
                toolbar: [['style', ['bold', 'italic', 'underline']], ['para', ['ul', 'ol', 'paragraph']], ['view', ['fullscreen', 'codeview']]]
            });
        }
        modal.on('hidden.bs.modal', clearModal);
        refreshEmptyState();
        loadDraft();
    });
</script>
@endsection
@endsection
