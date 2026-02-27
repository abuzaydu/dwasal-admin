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

        /* Toolbar  */
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
            background: linear-gradient(135deg, #F7941D, #e07b10);
            color: #fff;
            border: none;
            padding: 8px 22px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(247,148,29,0.35);
            transition: opacity 0.2s;
        }
        .btn-print:hover { opacity: 0.88; }

        /*  Badge Row: front + back side by side */
        .badge-grid {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10mm;
            padding: 5mm;
        }

        /* Each row = one badge (front + back) */
        .badge-row {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 8mm;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .badge-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .badge-label {
            font-size: 7.5pt;
            font-weight: 600;
            color: #777;
            text-align: center;
            margin-bottom: 2px;
        }

        /*  Badge Shell (CR80) */
        .badge {
            width: 85.6mm;
            height: 54mm;
            border-radius: 3.5mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 18px rgba(0,0,0,0.13);
            border: 1px solid #ddd;
        }

        /*  FRONT SIDE */
        .badge.front {
            background: #ffffff;
        }

        .badge-top {
            width: 100%;
            height: 8mm;
            background: linear-gradient(135deg, #F7941D, #e07b10);
            flex-shrink: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

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

        .badge-company {
            font-size: 8pt;
            font-weight: 700;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            line-height: 1.3;
        }

        .badge-divider {
            width: 50%;
            height: 1px;
            background: #F7941D;
            opacity: 0.6;
        }

        .badge-number {
            font-size: 7.5pt;
            font-weight: 600;
            color: #555;
            letter-spacing: 1px;
            text-align: center;
        }

        .badge-bottom {
            width: 100%;
            height: 6mm;
            background: linear-gradient(135deg, #F7941D, #e07b10);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .badge-bottom span {
            font-size: 5pt;
            color: rgba(255,255,255,0.9);
            letter-spacing: 1px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* BACK SIDE*/
        .badge.back {
            background: linear-gradient(160deg, #F7941D 0%, #e07b10 60%, #c96a0a 100%);
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 4mm;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* watermark circle */
        .badge.back::before {
            content: '';
            position: absolute;
            width: 60mm;
            height: 60mm;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .back-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2mm;
            z-index: 1;
            width: 100%;
        }

        .back-logo {
            width: 12mm;
            height: 12mm;
            object-fit: contain;
            opacity: 0.95;
        }

        .back-company-name {
            font-size: 8.5pt;
            font-weight: bold;
            color: #ffffff;
            text-align: center;
            letter-spacing: 0.5px;
        }

        .back-divider {
            width: 55%;
            height: 1px;
            background: rgba(255, 255, 255, 0.5);
        }

        .back-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.2mm;
            width: 100%;
            padding: 0 3mm;
        }

        .back-info-item {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 1.5mm;
            width: 100%;
        }

        .back-info-item i {
            color: rgba(255, 255, 255, 0.9);
            font-size: 6.5pt;
            width: 3.5mm;
            min-width: 3.5mm;
            text-align: center;
            margin-top: 0.3mm;
        }

        .back-info-item span {
            color: rgba(255, 255, 255, 0.92);
            font-size: 5.8pt;
            line-height: 1.4;
            text-align: left;
            word-break: break-word;
        }

        .back-footer {
            position: absolute;
            bottom: 2mm;
            font-size: 5pt;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* PRINT*/
        @media print {
            body        { background: #fff; }
            .toolbar    { display: none; }
            .badge-grid { padding: 0; gap: 8mm; }
            .badge      { box-shadow: none; }
            .badge-label { display: none; }
        }

    </style>

    {{-- Font Awesome for icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

{{-- Toolbar --}}
<div class="toolbar">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#F7941D">
        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM8 9c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm4 8H4v-1c0-1.33 2.67-2 4-2s4 .67 4 2v1zm8-3h-6v-1.5h6V14zm0-2.5h-6v-1.5h6v1.5zm0-2.5h-6V7.5h6V9z"/>
    </svg>
    <h6>Print Badges &mdash; {{ count($badges) }} record(s)</h6>
    <button class="btn-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="white">
            <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
        </svg>
        Print
    </button>
    <a href="#" onclick="goBackAndRefresh()" style="display:inline-flex;align-items:center;gap:5px;font-size:0.85rem;color:#555;text-decoration:none;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
        </svg>
        Go Back
    </a>
    
</div>

{{-- Badges --}}
<div class="badge-grid">
    @foreach($badges as $badge)
    <div class="badge-row">

        {{-- FRONT --}}
        <div class="badge-wrapper">
            <div class="badge front">

                <div class="badge-top"></div>

                <div class="badge-body">

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

                    <div class="badge-company">
                        {{ $badge->company->name ?? 'COMPANY NAME' }}
                    </div>

                    <div class="badge-divider"></div>

                    <div class="badge-number">
                        Badge No: {{ $badge->badge_number }}
                    </div>

                </div>

                <div class="badge-bottom">
                    <span>Authorized Badge</span>
                </div>

            </div>
        </div>

        {{-- BACK --}}
        <div class="badge-wrapper">
            <div class="badge back">

                <div class="back-inner">

                    @if($badge->company && $badge->company->logo_url)
                        <img class="back-logo"
                             src="{{ asset('storage/clogos/' . $badge->company->logo_url) }}"
                             alt="Logo">
                    @endif

                    <div class="back-company-name">
                        {{ $badge->company->name ?? 'COMPANY NAME' }}
                    </div>

                    <div class="back-divider"></div>

                    <div class="back-info">

                        @if(!empty($badge->company->address))
                        <div class="back-info-item">
                            <i class="fa fa-map-marker"></i>
                            <span>{{ $badge->company->address }}</span>
                        </div>
                        @endif

                        @if(!empty($badge->company->postal_address))
                        <div class="back-info-item">
                            <i class="fa fa-envelope-o"></i>
                            <span>{{ $badge->company->postal_address }}</span>
                        </div>
                        @endif

                        @if(!empty($badge->company->email))
                        <div class="back-info-item">
                            <i class="fa fa-at"></i>
                            <span>{{ $badge->company->email }}</span>
                        </div>
                        @endif

                        @if(!empty($badge->company->location))
                        <div class="back-info-item">
                            <i class="fa fa-globe"></i>
                            <span>{{ $badge->company->location }}</span>
                        </div>
                        @endif

                    </div>

                </div>

                <div class="back-footer">Visitor Pass</div>

            </div>
        </div>

    </div>
    @endforeach
</div>

<script>
    window.onload = function () { window.print(); };

     function goBackAndRefresh() {
        if (window.opener) {
            window.opener.location.reload();
        }

        window.close();

        return false;
    }
</script>

</body>
</html>