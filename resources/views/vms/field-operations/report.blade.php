@php
    $sample = $sample ?? false;
    $company = $record->company;
    $rows = $sample ? collect(range(1, 24))->map(fn () => null) : $record->rows->filter(function ($row) {
        return filled($row->truck_no)
            || $row->cubic !== null
            || $row->trip_count !== null
            || $row->amount_per_trip !== null
            || count($row->expenditure_entries ?? []) > 0
            || count($row->cash_entries ?? []) > 0;
    })->values();
    $money = fn ($value) => $sample && (float) $value === 0 ? '' : ($value === null || $value === '' ? '' : number_format((float) $value, 0));
    $logoPath = $company->logo_url ? public_path('storage/clogos/' . $company->logo_url) : public_path('assets/img/logo-2.png');
    $logo = asset('assets/img/logo-2.png');
    if (is_file($logoPath)) {
        $logoMime = function_exists('mime_content_type') ? mime_content_type($logoPath) : 'image/png';
        $logo = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
    $address = collect([$company->address, $company->postal_code, $company->city])->filter()->implode(', ');
    $contacts = collect([$company->mobile, $company->email])->filter()->implode(' | ');
@endphp
<style>
        @page { size: A4 portrait; margin: 8mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, sans-serif; font-size: 9px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .report { width: 100%; }
        .report, .report-header, .metadata, .operations-table, .footer { page-break-inside: avoid; }
        .report-header { display: flex; border: 1px solid #111; min-height: 86px; }
        .logo-cell { width: 25%; display: flex; align-items: center; justify-content: center; border-right: 1px solid #111; padding: 8px; }
        .logo-cell img { max-width: 100%; max-height: 62px; }
        .company-info { width: 75%; text-align: center; padding: 8px; }
        .company-info h1 { margin: 0 0 5px; font-size: 16px; }
        .company-info p { margin: 2px 0; font-size: 8px; }
        .title { margin-top: 5px; border: 1px solid #111; padding: 6px; text-align: center; font-size: 13px; font-weight: bold; }
        .metadata { display: grid; grid-template-columns: 1fr 1fr; gap: 0 14px; margin: 5px 0; }
        .metadata-col { border-left: 1px solid #111; border-right: 1px solid #111; }
        .metadata-row { display: flex; min-height: 20px; border-bottom: 1px solid #111; }
        .metadata-row:first-child { border-top: 1px solid #111; }
        .metadata-row strong { width: 38%; padding: 4px; background: #f0f0f0; }
        .metadata-row span { flex: 1; padding: 4px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #111; padding: 3px; vertical-align: top; word-wrap: break-word; }
        th { background: #e9e9e9; text-align: center; font-size: 8px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .operations-table { min-height: 0; }
        .operations-table th:nth-child(1) { width: 4%; }
        .operations-table th:nth-child(2) { width: 12%; }
        .operations-table th:nth-child(3) { width: 9%; }
        .operations-table th:nth-child(4) { width: 7%; }
        .operations-table th:nth-child(5) { width: 11%; }
        .operations-table th:nth-child(6) { width: 14%; }
        .operations-table th:nth-child(7) { width: 13%; }
        .operations-table th:nth-child(8) { width: 17%; }
        .operations-table th:nth-child(9) { width: 13%; }
        .operations-table tbody tr { height: 19px; }
        .operations-table tfoot td { font-weight: bold; height: 25px; }
        .footer { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 7px; }
        .footer-box { border: 1px solid #111; min-height: 74px; padding: 5px; }
        .footer-box strong { display: block; margin-bottom: 8px; }
        .signature-line { margin-top: 36px; border-top: 1px solid #111; width: 80%; }
        .screen-actions { margin-bottom: 12px; }
        @media print {
            .screen-actions { display: none; }
            body { font-size: 8px; }
        }
</style>
<div class="report">
    <div class="screen-actions"><button onclick="window.print()">Print</button>@if($sample) <a href="{{ route('field-operations.sample-pdf') }}">Download Sample PDF</a>@endif</div>
    <div class="report-header">
        <div class="logo-cell"><img src="{{ $logo }}" alt="{{ $company->name }} logo"></div>
        <div class="company-info">
            <h1>{{ $company->name }}</h1>
            @if($address)<p>{{ $address }}</p>@endif
            <p>FIELD OPERATIONS RECORD &amp; REPORT</p>
            @if($contacts)<p>{{ $contacts }}</p>@endif
            <p>{{ $company->website ? 'Website: ' . $company->website : '' }}{{ $company->website && $company->tin ? ' | ' : '' }}{{ $company->tin ? 'TIN: ' . $company->tin : '' }}</p>
        </div>
    </div>
    <div class="title">FIELD OPERATIONS RECORD &amp; REPORT</div>

    <div class="metadata">
        <div class="metadata-col">
            <div class="metadata-row"><strong>Name</strong><span>{{ $record->name }}</span></div>
            <div class="metadata-row"><strong>Title</strong><span>{{ $record->title }}</span></div>
            <div class="metadata-row"><strong>Contact</strong><span>{{ $record->contact }}</span></div>
            <div class="metadata-row"><strong>Location</strong><span>{{ $record->location }}</span></div>
        </div>
        <div class="metadata-col">
            <div class="metadata-row"><strong>No.</strong><span>{{ $record->record_no }}</span></div>
            <div class="metadata-row"><strong>Date</strong><span>{{ $record->record_date?->format('d/m/Y') }}</span></div>
            <div class="metadata-row"><strong>Sign In / Out</strong><span>{{ $record->sign_in_time }} / {{ $record->sign_out_time }}</span></div>
            <div class="metadata-row"><strong>Fuel In / Out</strong><span>{{ $record->fuel_in }} / {{ $record->fuel_out }}</span></div>
            <div class="metadata-row"><strong>Quantity of Trips</strong><span>{{ $record->quantity_of_trips }}</span></div>
        </div>
    </div>

    <table class="operations-table">
        <thead><tr><th>No.</th><th>TRUCK NO</th><th>CUBIC (m3)</th><th>TRIPS</th><th style="text-align: right;">AMOUNT PER TRIP</th><th style="text-align: right;">TOTAL AMOUNT OF TRIPS</th><th>EXPENSE DETAILS</th><th style="text-align: right;">EXPENSE AAMOUNT</th><th style="text-align: right;">CASH SUBMITTED</th></tr></thead>
        <tbody>
            @forelse($rows as $number => $row)
                <tr>
                    <td class="text-center">{{ $number + 1 }}</td>
                    <td>{{ $row?->truck_no }}</td>
                    <td style="text-align: center;">{{ $money($row?->cubic) }}</td>
                    <td style="text-align: center;">{{ $row?->trip_count }}</td>
                    <td style="text-align: right;">{{ $money($row?->amount_per_trip) }}</td>
                    <td style="text-align: right;">{{ $money($row?->total_amount) }}</td>
                    <td>@foreach($row?->expenditure_entries ?? [] as $entry)<div>{{ $entry['details'] ?? '' }}</div>@endforeach</td>
                    <td style="text-align: right;">@foreach($row?->expenditure_entries ?? [] as $entry)<div>{{ $money($entry['amount'] ?? 0) }}</div>@endforeach</td>
                    <td style="text-align: right;">{{ $money(max(0, ($row?->total_amount ?? 0) - ($row?->expenditure_total ?? 0))) }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;">No operation rows recorded.</td></tr>
            @endforelse
        </tbody>
        <tfoot><tr><td colspan="2" style="text-align: right;">TOTAL: </td><td style="text-align: center;">{{ $money($record->total('cubic')) }}</td><td></td><td style="text-align: right;"> {{ $money($record->total('amount_per_trip')) }}</td><td style="text-align: right;"> {{ $money($record->total('total_amount')) }}</td><td></td><td style="text-align: right;"> {{ $money($record->total('expenditure')) }}</td><td style="text-align: right;"> {{ $money($record->total('cash_submitted')) }}</td></tr></tfoot>
    </table>

    <div class="footer">
        <div class="footer-box"><strong>Machine Condition</strong>{{ strip_tags($record->machine_condition ?? '') }}</div>
        <div class="footer-box"><strong>Signature</strong>{{ $record->signature }}<div class="signature-line"></div></div>
    </div>
</div>
