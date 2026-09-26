@php
    $sample = $sample ?? false;
    $company = $record->company;
    $rows = $sample ? collect(range(1, 24))->map(function () { return null; }) : $record->rows->filter(function ($row) {
        return filled($row->truck_no)
            || $row->cubic !== null
            || $row->trip_count !== null
            || $row->amount_per_trip !== null
            || count($row->expenditure_entries ?? []) > 0
            || count($row->cash_entries ?? []) > 0;
    })->values();
    $logo = ($company->logo_url && is_file(public_path('storage/clogos/'.$company->logo_url)))
        ? asset('storage/clogos/'.$company->logo_url)
        : asset('assets/img/logo-2.png');
    $address = collect([$company->address, $company->postal_code, $company->city])->filter()->implode(', ');
    $contacts = collect([$company->mobile, $company->email])->filter()->implode(' | ');
    $cell = 'border: 1px solid #000 !important; box-shadow: inset 0 0 0 1px #000; padding: 4px 5px; font-size: 11px; vertical-align: top;';
    $label = $cell.' width: 38%; font-weight: bold; background: #f0f0f0;';
@endphp
<style>
    #print-fo.row {
        display: block !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding: 2px;
    }
    #print-fo > [class*="col-"] {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        flex: none !important;
    }
    #print-fo {
        border: 2px solid #000;
        padding: 6px;
        background: #fff;
        box-sizing: border-box;
    }
    #print-fo table {
        width: 100%;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        table-layout: fixed;
    }
    #print-fo td, #print-fo th {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    #print-fo .sig-box {
        height: 42px;
        position: relative;
        padding: 3px 5px;
        vertical-align: top;
    }
    #print-fo .sig-value {
        font-size: 11px;
        line-height: 1.15;
        max-height: 15px;
        overflow: hidden;
        margin-top: 1px;
    }
    #print-fo .sig-line {
        position: absolute;
        left: 5px;
        right: 5px;
        bottom: 3px;
        border-top: 1px solid #000;
    }
    #print-fo .doc-no-box {
        text-align: center;
        vertical-align: middle;
        background: #f0f0f0;
        padding: 6px 5px;
    }
    #print-fo .doc-no-label {
        font-size: 9px;
        font-weight: bold;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #333;
        margin-bottom: 3px;
    }
    #print-fo .doc-no-value {
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 1px;
        color: #000;
        line-height: 1.2;
    }
</style>
<div class="row g-1 print_invoice" id="print-fo">
    <div class="col-md-12">
        <table>
            <colgroup>
                <col style="width:18%">
                <col style="width:82%">
            </colgroup>
            <tr>
                <td style="{{ $cell }} text-align: center; vertical-align: middle;">
                    <img class="invoice-logo" src="{{ $logo }}" alt="" style="max-width: 100%; max-height: 70px;">
                </td>
                <td style="{{ $cell }} text-align: center;">
                    <strong style="font-size: 16px;">{{ $company->name }}</strong><br>
                    @if($address)<small>{{ $address }}</small><br>@endif
                    @if($contacts)<small>{{ $contacts }}</small><br>@endif
                    <small>
                        @if($company->website)Website: {{ $company->website }}@endif
                        @if($company->website && $company->tin) | @endif
                        @if($company->tin)TIN: {{ $company->tin }}@endif
                    </small>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="{{ $cell }} text-align: center; font-size: 13px; font-weight: bold; background: #e9e9e9;">FIELD OPERATIONS RECORD &amp; REPORT</td>
            </tr>
        </table>
    </div>
    <div class="col-md-12" style="padding-top: 6px; padding-bottom: 6px;">
        <table>
            <colgroup>
                <col style="width:35%">
                <col style="width:30%">
                <col style="width:35%">
            </colgroup>
            <tr>
                <td style="padding-right: 6px; vertical-align: top; border: none !important;">
                    <table>
                        <tr>
                            <td style="{{ $label }}">Name</td>
                            <td style="{{ $cell }}">{{ $record->name }}</td>
                        </tr>
                        <tr>
                            <td style="{{ $label }}">Title</td>
                            <td style="{{ $cell }}">{{ $record->title }}</td>
                        </tr>
                        <tr>
                            <td style="{{ $label }}">Contact</td>
                            <td style="{{ $cell }}">{{ $record->contact }}</td>
                        </tr>
                        <tr>
                            <td style="{{ $label }}">Location</td>
                            <td style="{{ $cell }}">{{ $record->location }}</td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left: 6px; padding-right: 6px; vertical-align: middle; border: none !important;">
                    <table>
                        <tr>
                            <td style="{{ $cell }} doc-no-box">
                                <div class="doc-no-label">Document No.</div>
                                <div class="doc-no-value">{{ $record->record_no }}</div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left: 6px; vertical-align: top; border: none !important;">
                    <table>
                        <tr>
                            <td style="{{ $label }}">Date</td>
                            <td style="{{ $cell }}">{{ $record->record_date ? $record->record_date->format('d/m/Y') : '' }}</td>
                        </tr>
                        <tr>
                            <td style="{{ $label }}">Sign In / Out</td>
                            <td style="{{ $cell }}">{{ $record->sign_in_time }} / {{ $record->sign_out_time }}</td>
                        </tr>
                        <tr>
                            <td style="{{ $label }}">Fuel In / Out</td>
                            <td style="{{ $cell }}">{{ $record->fuel_in }} / {{ $record->fuel_out }}</td>
                        </tr>
                        <tr>
                            <td style="{{ $label }}">Quantity of Trips</td>
                            <td style="{{ $cell }}">{{ $record->quantity_of_trips }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-12">
        <table class="items mt-0" style="margin-top: 0;">
            <colgroup>
                <col style="width:4%">
                <col style="width:11%">
                <col style="width:8%">
                <col style="width:6%">
                <col style="width:10%">
                <col style="width:12%">
                <col style="width:20%">
                <col style="width:13%">
                <col style="width:16%">
            </colgroup>
            <thead>
                <tr>
                    <th style="{{ $cell }} text-align: center; background: #e9e9e9; font-size: 8px;">No.</th>
                    <th style="{{ $cell }} text-align: center; background: #e9e9e9; font-size: 8px;">TRUCK NO</th>
                    <th style="{{ $cell }} text-align: center; background: #e9e9e9; font-size: 8px;">CUBIC (m3)</th>
                    <th style="{{ $cell }} text-align: center; background: #e9e9e9; font-size: 8px;">TRIPS</th>
                    <th style="{{ $cell }} text-align: right; background: #e9e9e9; font-size: 8px;">AMOUNT PER TRIP</th>
                    <th style="{{ $cell }} text-align: right; background: #e9e9e9; font-size: 8px;">TOTAL AMOUNT OF TRIPS</th>
                    <th style="{{ $cell }} text-align: center; background: #e9e9e9; font-size: 8px;">EXPENSE DETAILS</th>
                    <th style="{{ $cell }} text-align: right; background: #e9e9e9; font-size: 8px;">EXPENSE AMOUNT</th>
                    <th style="{{ $cell }} text-align: right; background: #e9e9e9; font-size: 8px;">CASH SUBMITTED</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $number => $row)
                    @php
                        $cubicVal = $row->cubic ?? null;
                        $amountVal = $row->amount_per_trip ?? null;
                        $totalVal = $row->total_amount ?? null;
                        $cubic = ($sample && (float) $cubicVal === 0.0) || $cubicVal === null || $cubicVal === '' ? '' : number_format((float) $cubicVal, 0);
                        $amountPerTrip = ($sample && (float) $amountVal === 0.0) || $amountVal === null || $amountVal === '' ? '' : number_format((float) $amountVal, 0);
                        $totalAmount = ($sample && (float) $totalVal === 0.0) || $totalVal === null || $totalVal === '' ? '' : number_format((float) $totalVal, 0);
                        $cashSubmitted = max(0, ($totalVal ?? 0) - ($row->expenditure_total ?? 0));
                        $cash = ($sample && (float) $cashSubmitted === 0.0) ? '' : number_format((float) $cashSubmitted, 0);
                    @endphp
                    <tr>
                        <td style="{{ $cell }} text-align: center;">{{ $number + 1 }}</td>
                        <td style="{{ $cell }}">{{ $row->truck_no ?? '' }}</td>
                        <td style="{{ $cell }} text-align: center;">{{ $cubic }}</td>
                        <td style="{{ $cell }} text-align: center;">{{ $row->trip_count ?? '' }}</td>
                        <td style="{{ $cell }} text-align: right;">{{ $amountPerTrip }}</td>
                        <td style="{{ $cell }} text-align: right;">{{ $totalAmount }}</td>
                        <td style="{{ $cell }}">
                            @foreach(($row->expenditure_entries ?? []) as $entry)
                                <div>{{ $entry['details'] ?? '' }}</div>
                            @endforeach
                        </td>
                        <td style="{{ $cell }} text-align: right;">
                            @foreach(($row->expenditure_entries ?? []) as $entry)
                                <div>{{ (($sample && (float) ($entry['amount'] ?? 0) === 0.0) || ($entry['amount'] ?? '') === '') ? '' : number_format((float) ($entry['amount'] ?? 0), 0) }}</div>
                            @endforeach
                        </td>
                        <td style="{{ $cell }} text-align: right;">{{ $cash }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="{{ $cell }} text-align: center; padding: 12px;">No operation rows recorded.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                @php
                    $totalCubic = $record->total('cubic');
                    $totalAmountPerTrip = $record->total('amount_per_trip');
                    $totalTripAmount = $record->total('total_amount');
                    $totalExpenditure = $record->total('expenditure');
                    $totalCash = $record->total('cash_submitted');
                @endphp
                <tr>
                    <td colspan="2" style="{{ $cell }} text-align: right; font-weight: bold; background: #f0f0f0;">TOTAL:</td>
                    <td style="{{ $cell }} text-align: center; font-weight: bold; background: #f0f0f0;">{{ ($sample && (float) $totalCubic === 0.0) ? '' : number_format((float) $totalCubic, 0) }}</td>
                    <td style="{{ $cell }} background: #f0f0f0;"></td>
                    <td style="{{ $cell }} text-align: right; font-weight: bold; background: #f0f0f0;">{{ ($sample && (float) $totalAmountPerTrip === 0.0) ? '' : number_format((float) $totalAmountPerTrip, 0) }}</td>
                    <td style="{{ $cell }} text-align: right; font-weight: bold; background: #f0f0f0;">{{ ($sample && (float) $totalTripAmount === 0.0) ? '' : number_format((float) $totalTripAmount, 0) }}</td>
                    <td style="{{ $cell }} background: #f0f0f0;"></td>
                    <td style="{{ $cell }} text-align: right; font-weight: bold; background: #f0f0f0;">{{ ($sample && (float) $totalExpenditure === 0.0) ? '' : number_format((float) $totalExpenditure, 0) }}</td>
                    <td style="{{ $cell }} text-align: right; font-weight: bold; background: #f0f0f0;">{{ ($sample && (float) $totalCash === 0.0) ? '' : number_format((float) $totalCash, 0) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-md-12" style="padding-top: 8px;">
        <table>
            <colgroup>
                <col style="width:50%">
                <col style="width:50%">
            </colgroup>
            <tr>
                <td style="padding-right: 4px; vertical-align: top; border: none !important;">
                    <table>
                        <tr>
                            <td style="{{ $cell }} height: 42px;">
                                <strong>Machine Condition</strong><br>
                                {{ strip_tags($record->machine_condition ?? '') }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="padding-left: 4px; vertical-align: top; border: none !important;">
                    <table>
                        <tr>
                            <td style="{{ $cell }} sig-box">
                                <strong>Signature</strong>
                                <div class="sig-value">{{ $record->signature }}</div>
                                <div class="sig-line"></div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>