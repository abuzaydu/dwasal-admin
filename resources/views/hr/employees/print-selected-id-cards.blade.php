@php
    $brandColor = $employees->first()->company->brand_color;

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

            :root {
                --id-primary: {{ $brandColor }};
                --id-secondary: {{ $primaryDark }};
            }
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap');

            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                font-family: 'Montserrat', 'Arial', sans-serif;
                background: #f0f2f5;
            }

            /* TOOLBAR */
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
                cursor: pointer; box-shadow: 0 3px 10px rgba(247,148,29,0.35); transition: opacity 0.2s;
            }
            .btn-print:hover { opacity: 0.88; }
            .btn-back {
                display: inline-flex; align-items: center; gap: 5px;
                font-size: 0.85rem; color: #555; text-decoration: none; font-weight: 600;
            }

            /* TEMPLATE SWITCHER */
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

            /* TEMPLATE PANELS */
            .idc-template-panel        { display: none; }
            .idc-template-panel.active { display: block; }

            /* CARD GRID */
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

            /* CR80 SHELL*/
            .idc-shell {
                width: 54mm;
                height: 85.6mm;
                border-radius: 3.5mm;
                overflow: hidden;
                position: relative;
                display: flex;
                flex-direction: column;
                box-shadow: 0 4px 18px rgba(0,0,0,0.13);
                border: 1px solid #ddd;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }


            /* WAVE — FRONT */
            .idc-wave-front {
                background: #ffffff;
                align-items: center;
            }

            .idc-wave-front .wf-header {
                width: 100%;
                background: #ffffff;
                padding: 2.5mm 3mm 1.5mm;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 1mm;
                flex-shrink: 0;
            }
            .idc-wave-front .co-logo {
                width: 20mm; height: 20mm;
                object-fit: contain; border-radius: 1.5mm; flex-shrink: 0;
            }
            .idc-wave-front .co-logo-placeholder {
                width: 10mm; height: 10mm;
                background: var(--id-primary); border-radius: 1.5mm;
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-wave-front .co-logo-placeholder i { color: #fff; font-size: 6pt; }
            .idc-wave-front .co-name {
                font-size: 5pt; font-weight: 800; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.3px;
                line-height: 1.25; text-align: center; max-width: 46mm;
            }

            /* Orange-ring photo */
            .idc-wave-front .wf-photo-area { margin-top: 1.5mm; flex-shrink: 0; }
            .idc-wave-front .wf-photo-ring {
                width: 18mm; height: 18mm; border-radius: 50%;
                border: 0.8mm solid var(--id-primary); padding: 0.5mm;
                box-shadow: 0 1mm 3.5mm rgba(26,58,107,0.22);
                background: #fff; overflow: hidden;
                display: flex; align-items: center; justify-content: center;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-wave-front .wf-photo-ring img {
                width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;
            }
            .idc-wave-front .wf-photo-ring .no-photo {
                width: 100%; height: 100%; border-radius: 50%; background: #e8eef6;
                display: flex; align-items: center; justify-content: center;
                color: var(--id-primary); font-size: 9pt;
            }

            /* Orange name + position */
            .idc-wave-front .wf-name {
                margin-top: 2mm; font-size: 6.5pt; font-weight: 900; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.5px;
                text-align: center; padding: 0 2mm; line-height: 1.2;
            }
            .idc-wave-front .wf-position {
                margin-top: 0.5mm; font-size: 5pt; font-weight: 700; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.8px; text-align: center;
            }

            /* Full-width info table */
            .idc-wave-front .wf-info-table {
                margin-top: 2mm; width: 100%;
                padding: 0 4mm; font-size: 4.8pt; color: #333; flex-shrink: 0;
            }
            .idc-wave-front .wf-info-row {
                display: flex; justify-content: space-between; align-items: center;
                padding: 0.8mm 0; border-bottom: 0.2mm solid #e8eef6;
            }
            .idc-wave-front .wf-info-row:last-child { border-bottom: none; }
            .idc-wave-front .wf-info-label { font-weight: 700; color: var(--id-primary); text-transform: uppercase; letter-spacing: 0.3px; }
            .idc-wave-front .wf-info-value { font-weight: 600; color: #333; text-align: right; max-width: 55%; }

            /* Dual-wave footer */
            .idc-wave-front .wf-wave-footer {
                margin-top: auto; width: 100%;
                position: relative; flex-shrink: 0; height: 22mm;
            }
            .idc-wave-front .wf-wave-footer svg.wave-bg {
                position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            }
            .idc-wave-front .wf-qr-wrap {
                position: absolute; bottom: 1.5mm; right: 1.5mm;
                background: #fff; padding: 1mm; border-radius: 1.5mm;
                box-shadow: 0 0.5mm 2mm rgba(0,0,0,0.18); line-height: 0; z-index: 2;
            }
            .idc-wave-front .wf-qr-wrap img { width: 16mm; height: 16mm; display: block; }
            .idc-wave-front .wf-valid-text {
                position: absolute; bottom: 2.5mm; left: 2.5mm; z-index: 2;
                color: rgba(255,255,255,0.9); font-size: 4.5pt; font-weight: 700;
                text-transform: uppercase; letter-spacing: 0.3px; line-height: 1.6;
            }


            /*  WAVE — BACK */
            .idc-wave-back {
                background: var(--id-secondary);
                align-items: center; justify-content: center;
                color: #fff; text-align: center;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-wave-back .back-wave-top {
                position: absolute; top: 0; left: 0; width: 100%; pointer-events: none;
            }
            .idc-wave-back .back-wave-bottom {
                position: absolute; bottom: 0; left: 0; width: 100%;
                pointer-events: none; transform: rotate(180deg);
            }
            .idc-wave-back .back-content {
                position: relative; z-index: 2;
                display: flex; flex-direction: column; align-items: center;
                padding: 3mm 3mm; width: 100%;
            }
            .idc-wave-back .back-logo-img {
                width: 20mm; height: 20mm; object-fit: contain; border-radius: 2mm;
                background: rgba(255,255,255,0.12); padding: 0.5mm; margin-bottom: 1.5mm;
            }
            .idc-wave-back .back-logo-placeholder {
                width: 11mm; height: 11mm; background: rgba(255,255,255,0.15); border-radius: 2mm;
                display: flex; align-items: center; justify-content: center;
                margin-bottom: 1.5mm; font-size: 8pt;
            }
            .idc-wave-back .back-co-name {
                font-size: 6.5pt; font-weight: 900; text-transform: uppercase;
                letter-spacing: 0.5px; line-height: 1.3; margin-bottom: 0.5mm;
            }
            .idc-wave-back .back-industry { font-size: 4.5pt; opacity: 0.7; font-style: italic; margin-bottom: 2mm; }
            .idc-wave-back .back-divider  { width: 80%; height: 0.2mm; background: rgba(255,255,255,0.25); margin-bottom: 2mm; }
            .idc-wave-back .back-contact  { width: 100%; text-align: left; font-size: 4.8pt; line-height: 2; padding: 0 1mm; }
            .idc-wave-back .back-contact i { width: 3mm; opacity: 0.8; margin-right: 0.8mm; font-size: 4.8pt; }
            .idc-wave-back .back-footer-text {
                margin-top: 2mm; padding-top: 2mm;
                border-top: 0.2mm solid rgba(255,255,255,0.2);
                width: 100%; font-size: 4.5pt; opacity: 0.7; line-height: 1.9; text-align: center;
            }

            /* GOLD — FRONT*/
            .idc-gold-front { background: #ffffff; align-items: center; }
            .idc-gold-front .front-header {
                width: 100%; background: linear-gradient(135deg, var(--id-primary), var(--id-secondary));
                padding: 2.5mm 3mm; display: flex; flex-direction: column;
                align-items: center; justify-content: center;
                gap: 1mm; flex-shrink: 0;
            }
            .idc-gold-front .co-logo {
                width: 10mm; height: 10mm; object-fit: contain;
                border-radius: 1.5mm; background: rgba(255,255,255,0.2); padding: 0.3mm;
            }
            .idc-gold-front .co-logo-placeholder {
                width: 10mm; height: 10mm; background: rgba(255,255,255,0.25);
                border-radius: 1.5mm; display: flex; align-items: center; justify-content: center;
            }
            .idc-gold-front .co-logo-placeholder i { color: #fff; font-size: 6pt; }
            .idc-gold-front .co-name {
                font-size: 5pt; font-weight: 700; color: #fff;
                text-transform: uppercase; letter-spacing: 0.3px; line-height: 1.3;
                text-align: center; max-width: 46mm;
            }
            .idc-gold-front .photo-area { margin-top: 3.5mm; flex-shrink: 0; }
            .idc-gold-front .photo-ring {
                width: 18mm; height: 18mm; border-radius: 50%;
                border: 0.8mm solid var(--id-primary); padding: 0.5mm;
                box-shadow: 0 1mm 3mm rgba(136,99,4,0.35); background: var(--id-secondary);
                overflow: hidden; display: flex; align-items: center; justify-content: center;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-gold-front .photo-ring img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block; }
            .idc-gold-front .photo-ring .no-photo {
                width: 100%; height: 100%; border-radius: 50%; background: #f0f0f0;
                display: flex; align-items: center; justify-content: center; color: var(--id-primary); font-size: 9pt;
            }
            .idc-gold-front .emp-name {
                margin-top: 2.5mm; font-size: 6.5pt; font-weight: 800; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.5px;
                text-align: center; padding: 0 2mm; line-height: 1.25;
            }
            .idc-gold-front .emp-position {
                margin-top: 0.8mm; font-size: 5pt; font-weight: 600; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.8px; text-align: center;
            }
            .idc-gold-front .emp-id-badge {
                margin-top: 1.5mm; background: #f7f7f7; border: 0.2mm solid var(--id-primary);
                border-radius: 4mm; padding: 0.5mm 2.5mm;
                font-size: 4.8pt; color: var(--id-primary); letter-spacing: 0.8px; font-weight: 600;
            }
            .idc-gold-front .front-footer {
                margin-top: auto; width: 100%;
                background: linear-gradient(180deg, var(--id-primary), var(--id-secondary));
                height: 22mm; display: flex; align-items: center; justify-content: center;
                flex-shrink: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-gold-front .qr-box {
                background: #fff; padding: 1mm; border-radius: 2mm;
                box-shadow: 0 0.5mm 2mm rgba(0,0,0,0.2); line-height: 0;
            }
            .idc-gold-front .qr-box img { width: 16mm; height: 16mm; display: block; }

            /* GOLD — BACK */
            .idc-gold-back {
                background: linear-gradient(160deg, var(--id-primary) 0%, var(--id-secondary) 100%);
                display: flex; flex-direction: column;
                align-items: center; justify-content: center;
                padding: 4mm 3.5mm; color: #fff; text-align: center;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-gold-back .back-logo {
                width: 11mm; height: 11mm; object-fit: contain;
                border-radius: 2mm; background: rgba(255,255,255,0.15);
                padding: 0.5mm; margin-bottom: 2mm;
            }
            .idc-gold-back .back-logo-placeholder {
                width: 11mm; height: 11mm; background: rgba(255,255,255,0.2);
                border-radius: 2mm; display: flex; align-items: center; justify-content: center;
                margin-bottom: 2mm; font-size: 8pt;
            }
            .idc-gold-back .back-company-name {
                font-size: 6.5pt; font-weight: 800; text-transform: uppercase;
                letter-spacing: 0.4px; line-height: 1.3; margin-bottom: 0.8mm;
            }
            .idc-gold-back .back-industry { font-size: 4.5pt; opacity: 0.8; margin-bottom: 2.5mm; font-style: italic; }
            .idc-gold-back .back-divider  { width: 100%; height: 0.2mm; background: rgba(255,255,255,0.35); margin-bottom: 2.5mm; }
            .idc-gold-back .back-contact  { width: 100%; text-align: left; font-size: 4.8pt; line-height: 1.9; }
            .idc-gold-back .back-contact i { width: 3mm; opacity: 0.85; margin-right: 0.8mm; }
            .idc-gold-back .back-footer {
                margin-top: 2.5mm; padding-top: 2mm;
                border-top: 0.2mm solid rgba(255,255,255,0.3);
                width: 100%; font-size: 4.5pt; opacity: 0.8; line-height: 1.8; text-align: center;
            }
            /*color picker*/
            .color-picker {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding-bottom: 20px;
            }

            .color-picker span {
                font-size: 0.8rem;
                font-weight: 600;
                color: #555;
            }

            .color-option {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                cursor: pointer;
                border: 2px solid #fff;
                box-shadow: 0 0 0 1px #ccc;
                transition: transform 0.2s ease;
            }

            .color-option:hover {
                transform: scale(1.15);
            }

            /* PRINT */
            @media print {
                body              { background: #fff; }
                .toolbar          { display: none; }
                .idc-switcher     { display: none; }
                .card-side-label  { display: none; }
                .employee-divider { display: none; }
                .card-grid        { padding: 0; gap: 8mm; }
                .idc-shell        { box-shadow: none; }
                .color-picker     { display: none; }
            }
        
        </style>
    </head>
    <body>

        <div class="toolbar">
            <i class="fa fa-id-card" style="font-size:1.2rem;color:var(--id-primary);"></i>
            <h6>Print Employee ID Cards &mdash; {{ count($employees) }} record(s)</h6>
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

        {{--TEMPLATE PANEL wave--}}
        <div id="idc-panel-wave" class="idc-template-panel active">
            <div class="card-grid">

                @foreach($employees as $employee)
                    @php
                        $qr_empID     = $employee->emp_id      ?? 'EMPLOYEE_ID';
                        $qr_companyID = $employee->company_id  ?? 'COMPANY_ID';
                        $qr_empDbId   = $employee->id;

                        $qr_payload   = \App\Helpers\QrCodeEncryption::encrypt(json_encode([
                            'emp_id'     => $qr_empID,
                            'company_id' => $qr_companyID,
                            'id'         => $qr_empDbId,
                        ]));
                        $qr_svg    = QrCode::size(64)->margin(0)->generate($qr_payload);
                        $qr_base64 = 'data:image/svg+xml;base64,' . base64_encode($qr_svg);

                        $passport   = \App\Models\EmployeeDoc::where('employee_id', $qr_empDbId)
                                        ->where('type', 'Passport')->first();
                        $user_photo = $passport->link ?? null;
                        $position   = $employee->position ?? null;
                    @endphp

                    <div class="card-row">

                        
                        <div class="card-wrapper">
                            <div class="card-side-label">Front</div>
                            <div class="idc-shell idc-wave-front">

                                <div class="wf-header">
                                    @if($employee->company && $employee->company->logo_url)
                                        <img class="co-logo"
                                            src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                            crossorigin="anonymous" alt="Logo">
                                    @else
                                        <div class="co-logo-placeholder"><i class="fa fa-building"></i></div>
                                    @endif
                                    <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                </div>

                                <div class="wf-photo-area">
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

                                <div class="wf-info-table">
                                    <div class="wf-info-row">
                                        <span class="wf-info-label">ID No:</span>
                                        <span class="wf-info-value">{{ $employee->emp_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="wf-info-row">
                                        <span class="wf-info-label">Valid:</span>
                                        <span class="wf-info-value">{{ date('d/m/Y', strtotime($employee->created_at)) }}</span>
                                    </div>
                                </div>

                                <div class="wf-wave-footer">
                                    <svg class="wave-bg" viewBox="0 0 54 22" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0,10 C10,2 25,18 38,9 C45,4 51,7 54,5 L54,22 L0,22 Z" fill="var(--id-secondary)"/>
                                        <path d="M0,13 C8,5 22,19 36,11 C44,6 50,9 54,7 L54,22 L0,22 Z" fill="var(--id-primary)" opacity="0.6"/>
                                    </svg>
                                    <div class="wf-valid-text">
                                        <div>Authorized</div>
                                        <div>by Company</div>
                                    </div>
                                    <div class="wf-qr-wrap">
                                        <img src="data:image/png;base64,{{DNS2D::getBarcodePNG(encrypt($qr_empDbId.'&'.$qr_empID), 'QRCODE', 10, 10)}}" alt="QR Code" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- BACK --}}
                        <div class="card-wrapper">
                            <div class="card-side-label">Back</div>
                            <div class="idc-shell idc-wave-back">

                                <svg class="back-wave-top" viewBox="0 0 54 14" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0,0 L54,0 L54,9 C38,14 18,4 0,11 Z" fill="var(--id-primary)" opacity="0.6"/>
                                </svg>
                                <svg class="back-wave-bottom" viewBox="0 0 54 14" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0,0 L54,0 L54,9 C38,14 18,4 0,11 Z" fill="var(--id-primary)" opacity="0.6"/>
                                </svg>

                                <div class="back-content">
                                    @if($employee->company && $employee->company->logo_url)
                                        <img class="back-logo-img"
                                            src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                            crossorigin="anonymous" alt="Logo">
                                    @else
                                        <div class="back-logo-placeholder"><i class="fa fa-building"></i></div>
                                    @endif
                                    <div class="back-co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                    <div class="back-industry">{{ $employee->company->industry ?? 'Industry' }}</div>
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
                                    <div class="back-footer-text">
                                        <div>Issue Date: {{ date('d/m/Y', strtotime($employee->created_at)) }}</div>
                                        <div>Authorized by Company</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    {{-- /.card-row --}}

                    @if(!$loop->last)
                        <hr class="employee-divider">
                    @endif

                @endforeach

            </div>
        </div>

        {{--TEMPLATE PANEL — GOLD--}}
        <div id="idc-panel-gold" class="idc-template-panel">
            <div class="card-grid">

                @foreach($employees as $employee)
                    @php
                        $qr_empID     = $employee->emp_id      ?? 'EMPLOYEE_ID';
                        $qr_companyID = $employee->company_id  ?? 'COMPANY_ID';
                        $qr_empDbId   = $employee->id;

                        $qr_payload   = \App\Helpers\QrCodeEncryption::encrypt(json_encode([
                            'emp_id'     => $qr_empID,
                            'company_id' => $qr_companyID,
                            'id'         => $qr_empDbId,
                        ]));
                        $qr_svg    = QrCode::size(64)->margin(0)->generate($qr_payload);
                        $qr_base64 = 'data:image/svg+xml;base64,' . base64_encode($qr_svg);

                        $passport   = \App\Models\EmployeeDoc::where('employee_id', $qr_empDbId)
                                        ->where('type', 'Passport')->first();
                        $user_photo = $passport->link ?? null;
                        $position   = $employee->position ?? null;
                    @endphp

                    <div class="card-row">

                        {{-- FRONT --}}
                        <div class="card-wrapper">
                            <div class="card-side-label">Front</div>
                            <div class="idc-shell idc-gold-front">

                                <div class="front-header">
                                    @if($employee->company && $employee->company->logo_url)
                                        <img class="co-logo"
                                            src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                            crossorigin="anonymous" alt="Logo">
                                    @else
                                        <div class="co-logo-placeholder"><i class="fa fa-building"></i></div>
                                    @endif
                                    <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                </div>

                                <div class="photo-area">
                                    <div class="photo-ring">
                                        @if($user_photo)
                                            <img src="{{ asset('storage/' . $user_photo) }}"
                                                crossorigin="anonymous" alt="{{ $employee->fname }}">
                                        @else
                                            <div class="no-photo"><i class="fa fa-user"></i></div>
                                        @endif
                                    </div>
                                </div>

                                <div class="emp-name">{{ $employee->fname }} {{ $employee->lname }}</div>
                                <div class="emp-position">{{ $position ? $position->name : 'Designation' }}</div>
                                <div class="emp-id-badge">EMPLOYEE ID: {{ $employee->emp_id ?? 'N/A' }}</div>

                                <div class="front-footer">
                                    <div class="qr-box">
                                        <img src="data:image/png;base64,{{DNS2D::getBarcodePNG(encrypt($qr_empDbId.'&'.$qr_empID), 'QRCODE', 10, 10)}}" alt="QR Code" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- BACK --}}
                        <div class="card-wrapper">
                            <div class="card-side-label">Back</div>
                            <div class="idc-shell idc-gold-back">

                                @if($employee->company && $employee->company->logo_url)
                                    <img class="back-logo"
                                        src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                        crossorigin="anonymous" alt="Logo">
                                @else
                                    <div class="back-logo-placeholder"><i class="fa fa-building"></i></div>
                                @endif
                                <div class="back-company-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                <div class="back-industry">{{ $employee->company->industry ?? 'Industry' }}</div>
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
                        </div>

                    </div>{{-- /.card-row --}}

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

            /*js for changing id color on select such color*/
            document.querySelectorAll('.color-option').forEach(option => {
                option.addEventListener('click', function () {

                    const selectedColor = this.getAttribute('data-color');

                    document.documentElement.style.setProperty('--id-primary', selectedColor);

                    const darker = shadeColor(selectedColor, -20);
                    document.documentElement.style.setProperty('--id-secondary', darker);

                    });
            });

            function shadeColor(color, percent) {
                let R = parseInt(color.substring(1,3),16);
                let G = parseInt(color.substring(3,5),16);
                let B = parseInt(color.substring(5,7),16);

                R = parseInt(R * (100 + percent) / 100);
                G = parseInt(G * (100 + percent) / 100);
                B = parseInt(B * (100 + percent) / 100);

                R = (R<255)?R:255;  
                G = (G<255)?G:255;  
                B = (B<255)?B:255;  

                const RR = ((R.toString(16).length==1)?"0"+R.toString(16):R.toString(16));
                const GG = ((G.toString(16).length==1)?"0"+G.toString(16):G.toString(16));
                const BB = ((B.toString(16).length==1)?"0"+B.toString(16):B.toString(16));

                return "#"+RR+GG+BB;
            }
        </script>

    </body>
</html>