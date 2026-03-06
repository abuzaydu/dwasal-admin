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
                --id-primary:   {{ $brandColor }};
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
                cursor: pointer; box-shadow: 0 3px 10px rgba(0,0,0,0.18); transition: opacity 0.2s;
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
            .idc-switcher-btn:hover              { border-color: #aaa; color: #333; }
            .idc-switcher-btn.active             { border-color: transparent; color: #fff; background: var(--id-primary); }

            /* COLOR PICKER */
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

            /* TEMPLATE PANELS */
            .idc-template-panel        { display: none; }
            .idc-template-panel.active { display: block; }

            /* CARD GRID (print layout) */
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

            /* CR80 SHELL  */
            .idc-shell {
                width: 54mm;
                aspect-ratio: 54 / 85.6;
                border-radius: 12px;
                overflow: hidden;
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                box-shadow: 0 4px 18px rgba(0,0,0,0.13);
                border: 1px solid #ddd;
                container-type: inline-size;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* WAVE FRONT */
            .idc-wave-front { background: #fff; }

            .idc-wave-front .front-wave-top {
                position: absolute; top: 0; left: 0;
                width: 100%; pointer-events: none;
            }

            .idc-wave-front .wf-header {
                width: 100%;
                background: #fff;
                padding: 20% 8% 4%;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 4%;
                flex-shrink: 0;
            }

            .idc-wave-front .co-logo {
                width: 24%; aspect-ratio: 1;
                object-fit: contain; border-radius: 6px;
            }
            .idc-wave-front .co-logo-placeholder {
                width: 22%; aspect-ratio: 1;
                background: var(--id-primary); border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.5rem; color: #fff;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-wave-front .co-name {
                font-size: 2.8cqw; font-weight: 800; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.05em;
                text-align: center; line-height: 1.2;
            }

            .idc-wave-front .wf-photo-wrap {
                margin-top: 2%; flex-shrink: 0;
                width: 100%; display: flex; justify-content: center;
            }
            .idc-wave-front .wf-photo-ring {
                width: 30%; aspect-ratio: 1 / 1;
                border-radius: 50%;
                border: 3px solid var(--id-secondary);
                background: #e8eef6;
                overflow: hidden;
                display: grid; place-items: center;
                box-shadow: 0 3px 10px rgba(26,58,107,0.18);
                flex-shrink: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-wave-front .wf-photo-ring img {
                width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;
            }
            .idc-wave-front .wf-photo-ring .no-photo {
                display: flex; align-items: center; justify-content: center;
                color: var(--id-primary); font-size: 9cqw;
                width: 100%; height: 100%;
            }

            .idc-wave-front .wf-name {
                margin-top: 3%; font-size: 3.4cqw; font-weight: 900; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.04em;
                text-align: center; padding: 0 6%; line-height: 1.15;
            }
            .idc-wave-front .wf-position {
                margin-top: 1%; font-size: 2.2cqw; font-weight: 700; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.1em; text-align: center;
            }

            .idc-wave-front .wf-info {
                margin-top: 3%; width: 100%;
                padding: 0 7%; font-size: 2cqw;
                flex-shrink: 0; box-sizing: border-box;
            }
            .idc-wave-front .wf-info-row {
                display: flex; justify-content: space-between;
                padding: 2% 0; border-bottom: 1px solid #e8eef6;
            }
            .idc-wave-front .wf-info-row:last-child { border-bottom: none; }
            .idc-wave-front .wf-info-label { font-weight: 700; color: var(--id-primary); text-transform: uppercase; }
            .idc-wave-front .wf-info-value { font-weight: 600; color: #333; text-align: right; }

            .idc-wave-front .wf-footer {
                margin-top: auto; width: 100%;
                position: relative; flex-shrink: 0; height: 34%;
            }
            .idc-wave-front .wf-footer svg.wave-svg {
                position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            }
            .idc-wave-front .wf-footer .wf-qr {
                position: absolute; bottom: 5%; right: 4%;
                background: #fff; padding: 3%; border-radius: 6px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.16); z-index: 2;
                line-height: 0; width: 34%; aspect-ratio: 1;
                display: flex; align-items: center; justify-content: center;
            }
            .idc-wave-front .wf-footer .wf-qr img { width: 100%; height: 100%; display: block; object-fit: contain; }
            .idc-wave-front .wf-footer .wf-auth {
                position: absolute; bottom: 8%; left: 5%; z-index: 2;
                color: rgba(255,255,255,0.9); font-size: 1.8cqw; font-weight: 700;
                text-transform: uppercase; letter-spacing: 0.04em; line-height: 1.6;
            }

            /*  WAVE BACK */
            .idc-wave-back {
                background: var(--id-secondary);
                color: #fff; position: relative; overflow: hidden;
                justify-content: center; padding: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-wave-back .back-wave-top {
                position: absolute; top: 0; left: 0; width: 100%; pointer-events: none;
            }
            .idc-wave-back .back-wave-bottom {
                position: absolute; bottom: 0; left: 0; width: 100%;
                pointer-events: none; transform: rotate(180deg);
            }
            .idc-wave-back .back-body {
                position: relative; z-index: 2;
                display: flex; flex-direction: column; align-items: center;
                padding: 5% 7%; width: 100%; box-sizing: border-box; text-align: center;
            }
            .idc-wave-back .back-logo {
                width: 30%; aspect-ratio: 1; object-fit: contain;
                border-radius: 8px; background: rgba(255,255,255,0.12);
                padding: 4%; margin-bottom: 5%;
            }
            .idc-wave-back .back-logo-placeholder {
                width: 20%; aspect-ratio: 1;
                background: rgba(255,255,255,0.15); border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                margin-bottom: 3%; font-size: 1.2rem;
            }
            .idc-wave-back .back-company-name {
                font-size: 2.8cqw; font-weight: 900; text-transform: uppercase;
                letter-spacing: 0.06em; line-height: 1.2; margin-bottom: 1%;
            }
            .idc-wave-back .back-industry { 
                font-size: 2.8cqw;
                opacity: 0.7; 
                font-style: italic; 
                margin-bottom: 4%; }
            .idc-wave-back .back-divider  { width: 75%; height: 1px; background: rgba(255,255,255,0.25); margin-bottom: 4%; }
            .idc-wave-back .back-contact  { width: 100%; text-align: left; font-size: 2.5cqw; line-height: 2.5; }
            .idc-wave-back .back-contact i { width: 14px; opacity: 0.85; margin-right: 3px; }
            .idc-wave-back .back-footer {
                margin-top: 4%; padding-top: 3%;
                border-top: 1px solid rgba(255,255,255,0.2);
                width: 100%; font-size: 2.8cqw; opacity: 0.75; line-height: 1.8; text-align: center;
            }

            /* GOLD FRONT*/
            .idc-gold-front { background: #fff; }

            .idc-gold-front .gh-header {
                width: 100%;
                background: var(--id-primary);
                padding: 6% 8% 5%;
                display: flex; flex-direction: column; align-items: center; gap: 4%; flex-shrink: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-gold-front .co-logo {
                width: 25%; aspect-ratio: 1; object-fit: contain;
                border-radius: 6px; background: rgba(255,255,255,0.2); padding: 3px;
            }
            .idc-gold-front .co-logo-placeholder {
                width: 25%; aspect-ratio: 1;
                background: rgba(255,255,255,0.25); border-radius: 6px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1rem; color: #fff;
            }
            .idc-gold-front .co-name {
                font-size: 3.0cqw; font-weight: 700; color: #fff;
                text-transform: uppercase; letter-spacing: 0.04em;
                text-align: center; line-height: 1.2;
            }

            .idc-gold-front .gf-photo-wrap {
                margin-top: 4%; flex-shrink: 0;
                width: 100%; display: flex; justify-content: center;
            }
            .idc-gold-front .gf-photo-ring {
                width: 32%; aspect-ratio: 1 / 1; border-radius: 50%;
                border: 3px solid var(--id-secondary); background: #f0f0f0;
                overflow: hidden; display: grid; place-items: center;
                box-shadow: 0 3px 12px rgba(136,99,4,0.3); flex-shrink: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-gold-front .gf-photo-ring img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .idc-gold-front .gf-photo-ring .no-photo {
                display: flex; align-items: center; justify-content: center;
                color: #bbb; font-size: 9cqw; width: 100%; height: 100%;
            }

            .idc-gold-front .gf-name {
                margin-top: 4%; font-size: 3.2cqw; font-weight: 800; color: var(--id-primary);
                text-transform: uppercase; letter-spacing: 0.04em;
                text-align: center; padding: 0 6%; line-height: 1.2;
            }
            .idc-gold-front .gf-position {
                margin-top: 1%; font-size: 2.2cqw; font-weight: 600; color: var(--id-secondary);
                text-transform: uppercase; letter-spacing: 0.08em; text-align: center;
            }
            .idc-gold-front .gf-id-badge {
                margin-top: 2%; background: #f7f7f7;
                border: 1px solid #ebebeb; border-radius: 20px;
                padding: 1.5% 6%; font-size: 2cqw; color: #666;
                letter-spacing: 0.06em; font-weight: 600;
            }
            .idc-gold-front .gf-footer {
                margin-top: auto; width: 100%;
                background: var(--id-primary);
                height: 34%;
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-gold-front .gf-qr-box {
                background: #fff; padding: 3%; border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.18); line-height: 0;
                width: 40%; aspect-ratio: 1;
                display: flex; align-items: center; justify-content: center;
            }
            .idc-gold-front .gf-qr-box img { width: 100%; height: 100%; display: block; object-fit: contain; }

            /* GOLD BACK*/
            .idc-gold-back {
                background: var(--id-primary);
                color: #fff;
                justify-content: center;
                padding: 5% 7%;
                box-sizing: border-box;
                text-align: center;
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
            .idc-gold-back .gb-logo {
                width: 35%; aspect-ratio: 1; object-fit: contain;
                border-radius: 8px; background: rgba(255,255,255,0.15);
                padding: 7%; margin-bottom: 6%;
            }
            .idc-gold-back .gb-logo-placeholder {
                width: 25%; aspect-ratio: 1;
                background: rgba(255,255,255,0.2); border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                margin-bottom: 8%; font-size: 1.4rem;
            }
            .idc-gold-back .gb-company-name {
                font-size: 4.0cqw; font-weight: 800; text-transform: uppercase;
                letter-spacing: 0.04em; line-height: 1.2; margin-bottom: 1%;
            }
            .idc-gold-back .gb-industry { font-size: 4.0cqw; opacity: 0.8; font-style: italic; margin-bottom: 4%; }
            .idc-gold-back .gb-divider  { width: 100%; height: 1px; background: rgba(255,255,255,0.35); margin-bottom: 4%; }
            .idc-gold-back .gb-contact  { width: 100%; text-align: left; font-size: 3.8cqw; line-height: 3.5; }
            .idc-gold-back .gb-contact i { width: 14px; opacity: 0.85; margin-right: 3px; }
            .idc-gold-back .gb-footer {
                margin-top: 4%; padding-top: 3%;
                border-top: 1px solid rgba(255,255,255,0.3);
                width: 100%; font-size: 3.5cqw; opacity: 0.8; line-height: 1.8; text-align: center;
            }

            /* PRINT */
            @media print {
                body              { background: #fff; }
                .toolbar          { display: none; }
                .idc-switcher     { display: none; }
                .card-side-label  { display: none; }
                .employee-divider { display: none; }
                .color-picker     { display: none; }
                .card-grid        { padding: 0; gap: 8mm; }
                .idc-shell        { box-shadow: none; }
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

        {{-- TEMPLATE PANEL WAVE --}}
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

                        $passport   = \App\Models\EmployeeDoc::where('employee_id', $qr_empDbId)
                                        ->where('type', 'Passport')->first();
                        $user_photo = $passport->link ?? null;
                        $position   = $employee->position ?? null;
                    @endphp

                    <div class="card-row">

                        {{-- FRONT --}}
                        <div class="card-wrapper">
                            <div class="card-side-label">Front</div>
                            <div class="idc-shell idc-wave-front">

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
                                    <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
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
                                        <span class="wf-info-label">Valid:</span>
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
                                        <img src="data:image/png;base64,{{DNS2D::getBarcodePNG(encrypt($qr_empDbId.'&'.$qr_empID), 'QRCODE', 10, 10)}}" alt="QR Code" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- BACK --}}
                        <div class="card-wrapper">
                            <div class="card-side-label">Back</div>
                            <div class="idc-shell idc-wave-back">

                                <svg class="back-wave-top" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="var(--id-primary)" opacity="0.9"/>
                                </svg>
                                <svg class="back-wave-bottom" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
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
                        </div>

                    </div>{{-- card-row --}}

                    @if(!$loop->last)
                        <hr class="employee-divider">
                    @endif

                @endforeach

            </div>
        </div>

        {{-- TEMPLATE PANEL GOLD --}}
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

                                <div class="gh-header">
                                    @if($employee->company && $employee->company->logo_url)
                                        <img class="co-logo"
                                             src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                             crossorigin="anonymous" alt="Logo">
                                    @else
                                        <div class="co-logo-placeholder"><i class="fa fa-building"></i></div>
                                    @endif
                                    <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
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
                                    <img class="gb-logo"
                                         src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                                         crossorigin="anonymous" alt="Logo">
                                @else
                                    <div class="gb-logo-placeholder"><i class="fa fa-building"></i></div>
                                @endif
                                <div class="gb-company-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                                <div class="gb-industry">{{ $employee->company->industry ?? 'Industry' }}</div>
                                <div class="gb-divider"></div>
                                <div class="gb-contact">
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
                                <div class="gb-footer">
                                    <div>Issue Date: {{ date('d/m/Y', strtotime($employee->created_at)) }}</div>
                                    <div>Authorized by Company</div>
                                </div>

                            </div>
                        </div>

                    </div>{{-- card-row --}}

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
                let R = parseInt(color.substring(1,3),16);
                let G = parseInt(color.substring(3,5),16);
                let B = parseInt(color.substring(5,7),16);
                R = Math.min(255, parseInt(R * (100 + percent) / 100));
                G = Math.min(255, parseInt(G * (100 + percent) / 100));
                B = Math.min(255, parseInt(B * (100 + percent) / 100));
                return '#'
                    + R.toString(16).padStart(2,'0')
                    + G.toString(16).padStart(2,'0')
                    + B.toString(16).padStart(2,'0');
            }
        </script>

    </body>
</html>