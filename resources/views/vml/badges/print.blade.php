<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Badges</title>
    <style>

        @page {
            size: A4;
            margin: 10mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Arial', sans-serif;
            background: #f0f2f5;
        }

        /* ── Screen toolbar ── */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 24px;
        }

        .toolbar h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: linear-gradient(135deg, #d4a017, #9a6e00);
            color: #fff;
            border: none;
            padding: 8px 22px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(212,160,23,0.35);
            transition: opacity 0.2s;
        }
        .btn-print:hover { opacity: 0.88; }

        /* ── Badge Grid: 2 per row ── */
        .badge-grid {
            display: grid;
            grid-template-columns: repeat(2, 85.6mm);
            gap: 8mm;
            padding: 5mm;
            justify-content: center;
        }

        /* ── Badge Shell (PVC CR80) ── */
        .badge {
            width: 85.6mm;
            height: 54mm;
            border-radius: 3.5mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
            break-inside: avoid;
            box-shadow: 0 4px 18px rgba(0,0,0,0.13);
            border: 1px solid #ddd;
        }

        /* ── Top gold band ── */
        .badge-top {
            width: 100%;
            height: 8mm;
            background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%);
            flex-shrink: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Center content ── */
        .badge-body {
            flex: 1;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2mm 4mm;
            gap: 2mm;
        }

        /* Company logo */
        .badge-logo {
            width: 14mm;
            height: 14mm;
            object-fit: contain;
            display: block;
        }

        .badge-logo-placeholder {
            width: 14mm;
            height: 14mm;
            background: #f5f5f5;
            border-radius: 2mm;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Company name */
        .badge-company {
            font-size: 8pt;
            font-weight: 700;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            line-height: 1.3;
        }

        /* Divider */
        .badge-divider {
            width: 50%;
            height: 1px;
            background: #d4a017;
            opacity: 0.5;
        }

        /* Badge number */
        .badge-number {
            font-size: 7.5pt;
            font-weight: 600;
            color: #555;
            letter-spacing: 1px;
            text-align: center;
        }

        /* ── Bottom gold band ── */
        .badge-bottom {
            width: 100%;
            height: 6mm;
            background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .badge-bottom span {
            font-size: 5pt;
            color: rgba(255,255,255,0.85);
            letter-spacing: 1px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* ── Print styles ── */
        @media print {
            body        { background: #fff; }
            .toolbar    { display: none; }
            .badge-grid { padding: 0; gap: 6mm; }
            .badge      { box-shadow: none; }
        }

    </style>
</head>
<body>

{{-- Toolbar --}}
<div class="toolbar">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#d4a017">
        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM8 9c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm4 8H4v-1c0-1.33 2.67-2 4-2s4 .67 4 2v1zm8-3h-6v-1.5h6V14zm0-2.5h-6v-1.5h6v1.5zm0-2.5h-6V7.5h6V9z"/>
    </svg>
    <h6>Print Badges &mdash; {{ count($badges) }} record(s)</h6>
    <button class="btn-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="white">
            <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
        </svg>
        Print
    </button>
</div>

{{-- Badges --}}
<div class="badge-grid">
    @foreach($badges as $badge)
    <div class="badge">

        {{-- Top gold strip --}}
        <div class="badge-top"></div>

        {{-- Center: logo + company + badge number --}}
        <div class="badge-body">

            {{-- Company Logo --}}
            @if($badge->company && $badge->company->logo_url)
                <img class="badge-logo"
                     src="{{ asset('storage/clogos/' . $badge->company->logo_url) }}"
                     alt="Logo">
            @else
                <div class="badge-logo-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#bbb">
                        <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                    </svg>
                </div>
            @endif

            {{-- Company Name --}}
            <div class="badge-company">
                {{ $badge->company->name ?? 'COMPANY NAME' }}
            </div>

            {{-- Divider --}}
            <div class="badge-divider"></div>

            {{-- Badge Number --}}
            <div class="badge-number">
                Badge No: {{ $badge->badge_number }}
            </div>

        </div>

        {{-- Bottom gold strip --}}
        <div class="badge-bottom">
            <span>Authorized Badge</span>
        </div>

    </div>
    @endforeach
</div>

<script>
    window.onload = function () { window.print(); };
</script>

</body>
</html>