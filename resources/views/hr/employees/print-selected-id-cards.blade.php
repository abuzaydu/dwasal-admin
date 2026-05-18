@php
    $brandColor = $employees->first()->company->brand_color;
    $showCompanyName = $employees->first()->company->show_name_on_id_card;
    $hex = ltrim($brandColor, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $primaryDark = sprintf('#%02x%02x%02x',
        max(0, (int)round($r * 0.80)),
        max(0, (int)round($g * 0.80)),
        max(0, (int)round($b * 0.80))
    );
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print Employee ID Cards</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

       <style>
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap');

            :root {
                --id-primary:   {{ $brandColor }};
                --id-secondary: {{ $primaryDark }};

                /* Same scale as the single-card template */
                --id-scale: 2.6;
                --id-w: calc(54   * var(--id-scale) * 1px);
                --id-h: calc(85.6 * var(--id-scale) * 1px);

                /* Proportional font sizes — identical to single-card blade */
                --f-xs: calc(1.8 * var(--id-scale) * 1px);
                --f-sm: calc(2.2 * var(--id-scale) * 1px);
                --f-md: calc(2.6 * var(--id-scale) * 1px);
                --f-lg: calc(3.0 * var(--id-scale) * 1px);
                --f-xl: calc(3.4 * var(--id-scale) * 1px);
            }

            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                font-family: 'Montserrat', 'Arial', sans-serif;
                background: #f0f2f5;
            }

            /* ── TOOLBAR ── */
            .toolbar {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 14px 20px;
                background: #fff;
                border-bottom: 1px solid #e0e0e0;
                margin-bottom: 20px;
            }
            .toolbar h6 { font-size: 0.95rem; font-weight: 700; color: #333; margin: 0; flex: 1; }
            .btn-print {
                display: inline-flex; align-items: center; gap: 7px;
                background: linear-gradient(135deg, var(--id-primary), var(--id-secondary));
                color: #fff; border: none; padding: 8px 22px; border-radius: 8px;
                font-size: 0.85rem; font-weight: 600; font-family: 'Montserrat', sans-serif;
                cursor: pointer; box-shadow: 0 3px 10px rgba(0,0,0,0.18); transition: opacity 0.2s;
            }
            .btn-print:hover { opacity: 0.88; }
            .btn-back {
                display: inline-flex; align-items: center; gap: 5px;
                font-size: 0.85rem; color: #555; text-decoration: none; font-weight: 600;
            }

            /* ── TEMPLATE SWITCHER ── */
            .idc-switcher {
                display: flex; justify-content: center; gap: 8px;
                flex-wrap: wrap; padding: 0 20px 16px;
            }
            .idc-switcher-btn {
                display: inline-flex; align-items: center; gap: 6px;
                padding: 7px 18px; border-radius: 50px; border: 2px solid #ddd;
                background: #fff; color: #666; font-size: 0.78rem; font-weight: 700;
                font-family: 'Montserrat', sans-serif; letter-spacing: 0.4px;
                cursor: pointer; transition: all 0.2s ease;
            }
            .idc-switcher-btn:hover  { border-color: #aaa; color: #333; }
            .idc-switcher-btn.active { border-color: transparent; color: #fff; background: var(--id-primary); }

            /* ── COLOR PICKER ── */
            .color-picker {
                display: flex; align-items: center; justify-content: center;
                gap: 10px; padding-bottom: 20px;
            }
            .color-picker span { font-size: 0.8rem; font-weight: 600; color: #555; }
            .color-option {
                width: 24px; height: 24px; border-radius: 50%; cursor: pointer;
                border: 2px solid #fff; box-shadow: 0 0 0 1px #ccc;
                transition: transform 0.2s ease;
            }
            .color-option:hover { transform: scale(1.15); }

            .idc-template-panel        { display: none; }
            .idc-template-panel.active { display: block; }

            .card-grid {
                display: flex; flex-direction: column;
                align-items: center; gap: 10mm; padding: 5mm;
            }
            .card-row {
                display: flex; flex-direction: row;
                align-items: flex-start; gap: 8mm;
                page-break-inside: avoid; break-inside: avoid;
            }
            .card-wrapper { display: flex; flex-direction: column; align-items: center; gap: 3px; }
            .card-side-label {
                font-size: 7.5pt; font-weight: 600; color: #777;
                text-align: center; margin-bottom: 2px;
                text-transform: uppercase; letter-spacing: 1px;
            }
            .employee-divider {
                border: none; border-top: 2px dashed #ddd; margin: 6mm auto; width: 80%;
            }

            .idc-card {
                width:  var(--id-w);
                height: var(--id-h);
                border-radius: 10px;
                overflow: hidden;
                position: relative;
                box-shadow: 0 6px 24px rgba(0,0,0,0.16);
                display: flex;
                flex-direction: column;
                align-items: center;
                box-sizing: border-box;
                flex-shrink: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .idc-card.wave-front { background: #fff; }

            .wave-front .front-wave-top {
                position: absolute; top: 0; left: 0;
                width: 100%; height: 22%;
                pointer-events: none;
            }

            .wave-front .wf-header {
                position: relative; z-index: 1;
                width: 100%;
                height: 22%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: flex-end;
                padding-bottom: 3%;
                gap: 3%;
                flex-shrink: 0;
                box-sizing: border-box;
            }
            .wave-front .wf-header .co-logo {
                height: 38%; width: auto; max-width: 42%;
                object-fit: contain; border-radius: 4px;
            }
            .wave-front .wf-header .co-logo-placeholder {
                height: 36%; width: 36%;
                background: var(--id-secondary);
                border-radius: 5px;
                display: flex; align-items: center; justify-content: center;
                color: #fff; font-size: var(--f-lg);
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .wave-front .wf-header .co-name {
                font-size: var(--f-sm);
                font-weight: 800;
                color: var(--id-primary);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                text-align: center;
                line-height: 1.2;
                padding: 0 6%;
                overflow: hidden;
                max-height: 2.5em;
            }

            .wave-front .wf-photo-wrap {
                width: 100%; height: 16%;
                display: flex; justify-content: center; align-items: center;
                flex-shrink: 0;
            }
            .wave-front .wf-photo-ring {
                height: 88%; aspect-ratio: 1;
                border-radius: 50%;
                border: 2px solid var(--id-secondary);
                background: #e8eef6;
                overflow: hidden;
                display: grid; place-items: center;
                box-shadow: 0 2px 8px rgba(26,58,107,0.18);
                flex-shrink: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .wave-front .wf-photo-ring img { width:100%; height:100%; object-fit:cover; display:block; }
            .wave-front .wf-photo-ring .no-photo { color: var(--id-primary); font-size: var(--f-xl); }

            .wave-front .wf-name {
                width: 100%; flex-shrink: 0;
                font-size: var(--f-lg); font-weight: 900;
                color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.04em;
                text-align: center; line-height: 1.2;
                padding: 1% 5% 0;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            }
            .wave-front .wf-position {
                width: 100%; flex-shrink: 0;
                font-size: var(--f-md); font-weight: 700;
                color: var(--id-secondary);
                text-transform: uppercase; letter-spacing: 0.08em;
                text-align: center; padding: 0.5% 5%;
                white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            }

            .wave-front .wf-info {
                width: 100%; padding: 0 5%;
                flex-shrink: 0; box-sizing: border-box; margin-top: 1%;
            }
            .wave-front .wf-info-row {
                display: flex; justify-content: space-between;
                padding: 1.5% 0;
                border-bottom: 1px solid #e8eef6;
                font-size: var(--f-sm); line-height: 1.3;
            }
            .wave-front .wf-info-row:last-child { border-bottom: none; }
            .wave-front .wf-info-label { font-weight: 700; color: var(--id-primary); text-transform: uppercase; }
            .wave-front .wf-info-value { font-weight: 600; color: #333; text-align: right; }

            .wave-front .wf-footer {
                margin-top: auto; width: 100%;
                position: relative; flex-shrink: 0;
                height: 35%;
            }
            .wave-front .wf-footer svg.wave-svg {
                position: absolute; top:0; left:0; width:100%; height:100%;
            }
            .wave-front .wf-footer .wf-qr {
                position: absolute; bottom: 6%; right: 4%;
                background: #fff; padding: 2.5%;
                border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.16);
                z-index: 2; width: 32%; aspect-ratio: 1;
                display: flex; align-items: center; justify-content: center;
            }
            .wave-front .wf-footer .wf-qr img { width:100%; height:100%; display:block; object-fit:contain; }
            .wave-front .wf-footer .wf-auth {
                position: absolute; bottom: 10%; left: 5%; z-index: 2;
                color: rgba(255,255,255,0.9);
                font-size: var(--f-sm); font-weight: 700;
                text-transform: uppercase; letter-spacing: 0.04em; line-height: 1.6;
            }

            .idc-card.wave-back {
                background: var(--id-secondary);
                color: #fff;
                justify-content: flex-start;
                padding: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }

            .wave-back .back-wave-top {
                width: 100%; height: 20%;
                flex-shrink: 0; display: block; pointer-events: none;
            }
            .wave-back .back-body {
                flex: 1 1 auto;
                display: flex; flex-direction: column;
                align-items: center; justify-content: center;
                padding: 2% 6% 2%;
                width: 100%; box-sizing: border-box; text-align: center;
            }
            .wave-back .back-wave-bottom {
                width: 100%; height: 20%;
                flex-shrink: 0; display: block; pointer-events: none;
            }

            .wave-back .back-logo {
                width:26%; aspect-ratio:1; object-fit:contain;
                border-radius:6px; background:rgba(255,255,255,0.12);
                padding:3%; margin-bottom:4%; flex-shrink:0;
            }
            .wave-back .back-logo-placeholder {
                width:20%; aspect-ratio:1;
                background:rgba(255,255,255,0.15); border-radius:6px;
                display:flex; align-items:center; justify-content:center;
                margin-bottom:4%; font-size:var(--f-lg); flex-shrink:0;
            }
            .wave-back .back-company-name {
                font-size:var(--f-md); font-weight:900;
                text-transform:uppercase; letter-spacing:0.06em;
                line-height:1.2; margin-bottom:1%; flex-shrink:0;
            }
            .wave-back .back-industry {
                font-size:var(--f-sm); opacity:0.75;
                font-style:italic; margin-bottom:3%; flex-shrink:0;
            }
            .wave-back .back-divider {
                width:80%; height:1px;
                background:rgba(255,255,255,0.25);
                margin-bottom:3%; flex-shrink:0;
            }
            .wave-back .back-contact {
                width:100%; text-align:left;
                font-size:var(--f-sm); line-height:2.0; flex-shrink:0;
            }
            .wave-back .back-contact i { opacity:0.85; margin-right:3px; }
            .wave-back .back-footer {
                margin-top:3%; padding-top:3%;
                border-top:1px solid rgba(255,255,255,0.2);
                width:100%; font-size:var(--f-xs);
                opacity:0.75; line-height:1.8; text-align:center; flex-shrink:0;
            }

            .idc-card.gold-front { background:#fff; }

            .gold-front .gh-header {
                position: relative; z-index: 1;
                width:100%; height:22%;
                background: var(--id-primary);
                display:flex; flex-direction:column;
                align-items:center; justify-content:flex-end;
                padding-bottom:3%; gap:3%; flex-shrink:0;
                box-sizing:border-box;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .gold-front .gh-header .co-logo {
                height:38%; width:auto; max-width:42%;
                object-fit:contain; border-radius:4px;
                background:rgba(255,255,255,0.2); padding:2px;
            }
            .gold-front .gh-header .co-logo-placeholder {
                height:36%; width:36%;
                background:rgba(255,255,255,0.25); border-radius:5px;
                display:flex; align-items:center; justify-content:center;
                color:#fff; font-size:var(--f-lg);
            }
            .gold-front .gh-header .co-name {
                font-size:var(--f-sm); font-weight:800;
                color:#fff; text-transform:uppercase;
                letter-spacing:0.05em; text-align:center;
                line-height:1.2; padding:0 6%;
                overflow:hidden; max-height:2.5em;
            }

            .gold-front .gf-photo-wrap {
                width:100%; height:16%;
                display:flex; justify-content:center; align-items:center;
                flex-shrink:0;
            }
            .gold-front .gf-photo-ring {
                height:88%; aspect-ratio:1;
                border-radius:50%;
                border:2px solid var(--id-secondary);
                background:#e8eef6; overflow:hidden;
                display:grid; place-items:center;
                box-shadow:0 2px 8px rgba(26,58,107,0.18);
                flex-shrink:0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .gold-front .gf-photo-ring img { width:100%; height:100%; object-fit:cover; display:block; }
            .gold-front .gf-photo-ring .no-photo { color:var(--id-primary); font-size:var(--f-xl); }

            .gold-front .gf-name {
                width:100%; flex-shrink:0;
                font-size:var(--f-lg); font-weight:900;
                color:var(--id-primary); text-transform:uppercase;
                letter-spacing:0.04em; text-align:center;
                padding:1% 5% 0; line-height:1.2;
                white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
            }
            .gold-front .gf-position {
                width:100%; flex-shrink:0;
                font-size:var(--f-md); font-weight:700;
                color:var(--id-secondary); text-transform:uppercase;
                letter-spacing:0.08em; text-align:center;
                padding:0.5% 5%;
                white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
            }
            .gold-front .gf-id-badge {
                flex-shrink:0;
                background:#f7f7f7; border:1px solid #e8eef6;
                border-radius:20px; padding:1.5% 6%;
                font-size:var(--f-sm); color:#666;
                letter-spacing:0.06em; font-weight:600; margin-top:1%;
            }

            .gold-front .gf-footer {
                margin-top:auto; width:100%;
                background:var(--id-primary); height:34%;
                display:flex; align-items:center; justify-content:center;
                flex-shrink:0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .gold-front .gf-qr-box {
                background:#fff; padding:3%; border-radius:6px;
                box-shadow:0 2px 8px rgba(0,0,0,0.18);
                width:40%; aspect-ratio:1;
                display:flex; align-items:center; justify-content:center;
            }
            .gold-front .gf-qr-box img { width:100%; height:100%; display:block; object-fit:contain; }

            .idc-card.gold-back {
                background:var(--id-primary); color:#fff;
                justify-content:center; padding:0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }

            .gold-back .gb-body {
                display:flex; flex-direction:column; align-items:center;
                padding:8% 6% 8%;
                width:100%; box-sizing:border-box;
                text-align:center; height:100%; justify-content:center;
            }
            .gold-back .gb-logo {
                width:26%; aspect-ratio:1; object-fit:contain;
                border-radius:6px; background:rgba(255,255,255,0.12);
                padding:3%; margin-bottom:4%; flex-shrink:0;
            }
            .gold-back .gb-logo-placeholder {
                width:20%; aspect-ratio:1;
                background:rgba(255,255,255,0.15); border-radius:6px;
                display:flex; align-items:center; justify-content:center;
                margin-bottom:4%; font-size:var(--f-lg); flex-shrink:0;
            }
            .gold-back .gb-company-name {
                font-size:var(--f-md); font-weight:900;
                text-transform:uppercase; letter-spacing:0.06em;
                line-height:1.2; margin-bottom:1%; flex-shrink:0;
            }
            .gold-back .gb-industry {
                font-size:var(--f-sm); opacity:0.75;
                font-style:italic; margin-bottom:3%; flex-shrink:0;
            }
            .gold-back .gb-divider {
                width:80%; height:1px;
                background:rgba(255,255,255,0.25);
                margin-bottom:3%; flex-shrink:0;
            }
            .gold-back .gb-contact {
                width:100%; text-align:left;
                font-size:var(--f-sm); line-height:2.0; flex-shrink:0;
            }
            .gold-back .gb-contact i { opacity:0.85; margin-right:3px; }
            .gold-back .gb-footer {
                margin-top:3%; padding-top:3%;
                border-top:1px solid rgba(255,255,255,0.2);
                width:100%; font-size:var(--f-xs);
                opacity:0.75; line-height:1.8; text-align:center; flex-shrink:0;
            }

            @media print {
                :root {
                    --id-scale: 3.78;
                }

                body              { background: #fff; }
                .toolbar          { display: none; }
                .idc-switcher     { display: none; }
                .card-side-label  { display: none; }
                .employee-divider { display: none; }
                .color-picker     { display: none; }
                .card-grid        { padding: 0; gap: 8mm; }

                .idc-card {
                    box-shadow: none;
                    border: 0.3mm solid #ddd;
                }
            }

        </style>
    </head>
    <body>

        <div class="toolbar">
            <i class="fa fa-id-card" style="font-size:1.2rem;color:var(--id-primary);"></i>
            <h6>Printed Employee ID Cards &mdash; {{ count($employees) }} record(s)</h6>
            <button class="btn-print" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
            <a href="#" onclick="goBackAndRefresh()" class="btn-back">
                <i class="fa fa-arrow-left"></i> Go Back
            </a>
        </div>

        <div class="idc-switcher">
            <button class="idc-switcher-btn active" onclick="idcSwitchTemplate('wave', this)">
                <i class="fa fa-water"></i> Wave
            </button>
            <button class="idc-switcher-btn" onclick="idcSwitchTemplate('gold', this)">
                <i class="fa fa-star"></i> Gold
            </button>
        </div>

        <div class="color-picker">
            <span>Choose Color:</span>
            <div class="color-option" data-color="#F7941D" style="background:#F7941D"></div>
            <div class="color-option" data-color="#1E3A8A" style="background:#1E3A8A"></div>
            <div class="color-option" data-color="#0F766E" style="background:#0F766E"></div>
            <div class="color-option" data-color="#DC2626" style="background:#DC2626"></div>
            <div class="color-option" data-color="#111827" style="background:#111827"></div>
        </div>

        <div id="idc-panel-wave" class="idc-template-panel active">
            <div class="card-grid">

                @foreach($employees as $employee)
                    @php
                        $qr_empID     = $employee->emp_id      ?? 'EMPLOYEE_ID';
                        $qr_companyID = $employee->company_id  ?? 'COMPANY_ID';
                        $qr_empDbId   = $employee->id;

                        $passport   = \App\Models\EmployeeDoc::where('employee_id', $qr_empDbId)
                                        ->where('type', 'Passport')->first();
                        $user_photo = $passport->link ?? null;
                        $position   = $employee->position ?? null;
                    @endphp

                    <div class="card-row">

                        <div class="card-wrapper">
                            <div class="card-side-label">Front</div>
                            <div class="idc-card wave-front">

                                <svg class="front-wave-top" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="var(--id-primary)"/>
                                </svg>

                                <div class="wf-header">
                                    @if($employee->company && $employee->company->logo_url)
                                        <img class="co-logo"
                                             src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                             crossorigin="anonymous" alt="Logo">
                                    @else
                                        <div class="co-logo-placeholder"><i class="fa fa-building"></i></div>
                                    @endif
                                    @if($showCompanyName)
                                    <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                    @endif
                                </div>

                                <div class="wf-photo-wrap">
                                    <div class="wf-photo-ring">
                                        @if($user_photo)
                                            <img src="{{ asset('storage/' . $user_photo) }}"
                                                 crossorigin="anonymous" alt="{{ $employee->fname }}">
                                        @else
                                            <div class="no-photo"><i class="fa fa-user"></i></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="wf-name">{{ $employee->fname }} {{ $employee->lname }}</div>
                                <div class="wf-position">{{ $position ? $position->name : 'Designation' }}</div>

                                <div class="wf-info">
                                    <div class="wf-info-row">
                                        <span class="wf-info-label">ID No:</span>
                                        <span class="wf-info-value">{{ $employee->emp_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="wf-info-row">
                                        <span class="wf-info-label">Issue Date:</span>
                                        <span class="wf-info-value">{{ date('d/m/Y', strtotime($employee->created_at)) }}</span>
                                    </div>
                                </div>

                                <div class="wf-footer">
                                    <svg class="wave-svg" viewBox="0 0 280 150" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0,65 C60,15 140,100 200,50 C240,18 265,38 280,28 L280,150 L0,150 Z" fill="var(--id-secondary)"/>
                                        <path d="M0,82 C50,38 120,110 190,65 C230,38 260,54 280,44 L280,150 L0,150 Z" fill="var(--id-primary)" opacity="0.6"/>
                                    </svg>
                                    <div class="wf-auth">
                                        <div>Authorized</div>
                                        <div>by Company</div>
                                    </div>
                                    <div class="wf-qr">
                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(encrypt($qr_empDbId.'&'.$qr_empID), 'QRCODE', 10, 10) }}" alt="QR"/>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-wrapper">
                            <div class="card-side-label">Back</div>
                            <div class="idc-card wave-back">

                                <svg class="back-wave-top" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="var(--id-primary)" opacity="0.9"/>
                                </svg>

                                <div class="back-body">
                                    @if($employee->company && $employee->company->logo_url)
                                        <img class="back-logo"
                                             src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                             crossorigin="anonymous" alt="Logo">
                                    @else
                                        <div class="back-logo-placeholder"><i class="fa fa-building"></i></div>
                                    @endif
                                    @if ($showCompanyName)                                        
                                        <div class="back-company-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                    @else
                                      <div class="back-industry">{{ $employee->company->industry ?? 'Industry' }}</div>                                  
                                    @endif
                                    <div class="back-divider"></div>
                                    <div class="back-contact">
                                        @if(!empty($employee->company->address))
                                            <div><i class="fa fa-map-marker"></i> {{ $employee->company->address }}</div>
                                        @endif
                                        @if(!empty($employee->company->mobile))
                                            <div><i class="fa fa-phone"></i> {{ $employee->company->mobile }}</div>
                                        @endif
                                        @if(!empty($employee->company->email))
                                            <div><i class="fa fa-envelope"></i> {{ $employee->company->email }}</div>
                                        @endif
                                        @if(!empty($employee->company->website))
                                            <div><i class="fa fa-globe"></i> {{ $employee->company->website }}</div>
                                        @endif
                                    </div>
                                    <div class="back-footer">
                                        <div>Issue Date: {{ date('d/m/Y', strtotime($employee->created_at)) }}</div>
                                        <div>Authorized by Company</div>
                                    </div>
                                </div>

                                <svg class="back-wave-bottom" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0,80 L280,80 L280,30 C200,0 100,60 0,20 Z" fill="var(--id-primary)" opacity="0.9"/>
                                </svg>

                            </div>
                        </div>

                    </div>

                    @if(!$loop->last)
                        <hr class="employee-divider">
                    @endif

                @endforeach

            </div>
        </div>

        <div id="idc-panel-gold" class="idc-template-panel">
            <div class="card-grid">

                @foreach($employees as $employee)
                    @php
                        $qr_empID     = $employee->emp_id      ?? 'EMPLOYEE_ID';
                        $qr_companyID = $employee->company_id  ?? 'COMPANY_ID';
                        $qr_empDbId   = $employee->id;

                        $passport   = \App\Models\EmployeeDoc::where('employee_id', $qr_empDbId)
                                        ->where('type', 'Passport')->first();
                        $user_photo = $passport->link ?? null;
                        $position   = $employee->position ?? null;
                    @endphp

                    <div class="card-row">

                        <div class="card-wrapper">
                            <div class="card-side-label">Front</div>
                            <div class="idc-card gold-front">

                                <div class="gh-header">
                                    @if($employee->company && $employee->company->logo_url)
                                        <img class="co-logo"
                                             src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                             crossorigin="anonymous" alt="Logo">
                                    @else
                                        <div class="co-logo-placeholder"><i class="fa fa-building"></i></div>
                                    @endif
                                    @if ($showCompanyName)
                                       <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                    @endif
                                </div>

                                <div class="gf-photo-wrap">
                                    <div class="gf-photo-ring">
                                        @if($user_photo)
                                            <img src="{{ asset('storage/' . $user_photo) }}"
                                                 crossorigin="anonymous" alt="{{ $employee->fname }}">
                                        @else
                                            <div class="no-photo"><i class="fa fa-user"></i></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="gf-name">{{ $employee->fname }} {{ $employee->lname }}</div>
                                <div class="gf-position">{{ $position ? $position->name : 'Designation' }}</div>
                                <div class="gf-id-badge">ID: {{ $employee->emp_id ?? 'N/A' }}</div>

                                <div class="gf-footer">
                                    <div class="gf-qr-box">
                                        <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG(encrypt($qr_empDbId.'&'.$qr_empID), 'QRCODE', 10, 10) }}" alt="QR"/>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-wrapper">
                            <div class="card-side-label">Back</div>
                            <div class="idc-card gold-back">
                                <div class="gb-body">
                                    @if($employee->company && $employee->company->logo_url)
                                        <img class="gb-logo"
                                             src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                             crossorigin="anonymous" alt="Logo">
                                    @else
                                        <div class="gb-logo-placeholder"><i class="fa fa-building"></i></div>
                                    @endif
                                    @if ($showCompanyName)
                                        <div class="gb-company-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                    @else
                                    <div class="gb-industry">{{ $employee->company->industry ?? 'Industry' }}</div>
                                    @endif
                                    <div class="gb-divider"></div>
                                    <div class="gb-contact">
                                        @if(!empty($employee->company->address))
                                            <div><i class="fa fa-map-marker me-1"></i> {{ $employee->company->address }}</div>
                                        @endif
                                        @if(!empty($employee->company->mobile))
                                            <div><i class="fa fa-phone me-1"></i> {{ $employee->company->mobile }}</div>
                                        @endif
                                        @if(!empty($employee->company->email))
                                            <div><i class="fa fa-envelope me-1"></i> {{ $employee->company->email }}</div>
                                        @endif
                                        @if(!empty($employee->company->website))
                                            <div><i class="fa fa-globe me-1"></i> {{ $employee->company->website }}</div>
                                        @endif
                                    </div>
                                    <div class="gb-footer">
                                        <div>Issue Date: {{ date('d/m/Y', strtotime($employee->created_at)) }}</div>
                                        <div>Authorized by Company</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    @if(!$loop->last)
                        <hr class="employee-divider">
                    @endif

                @endforeach

            </div>
        </div>

        <script>
            function idcSwitchTemplate(key, btn) {
                document.querySelectorAll('.idc-template-panel').forEach(p => p.classList.remove('active'));
                document.querySelectorAll('.idc-switcher-btn').forEach(b => b.classList.remove('active'));
                document.getElementById('idc-panel-' + key).classList.add('active');
                btn.classList.add('active');
            }

            function goBackAndRefresh() {
                if (window.opener) { window.opener.location.reload(); }
                window.close();
                return false;
            }

            document.querySelectorAll('.color-option').forEach(option => {
                option.addEventListener('click', function () {
                    const selectedColor = this.getAttribute('data-color');
                    document.documentElement.style.setProperty('--id-primary', selectedColor);
                    document.documentElement.style.setProperty('--id-secondary', shadeColor(selectedColor, -20));
                });
            });

            function shadeColor(color, percent) {
                let R = parseInt(color.substring(1,3), 16);
                let G = parseInt(color.substring(3,5), 16);
                let B = parseInt(color.substring(5,7), 16);
                R = Math.min(255, parseInt(R * (100 + percent) / 100));
                G = Math.min(255, parseInt(G * (100 + percent) / 100));
                B = Math.min(255, parseInt(B * (100 + percent) / 100));
                return '#'
                    + R.toString(16).padStart(2, '0')
                    + G.toString(16).padStart(2, '0')
                    + B.toString(16).padStart(2, '0');
            }
        </script>

    </body>
</html>