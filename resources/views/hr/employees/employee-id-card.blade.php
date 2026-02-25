@php
    /* ── Shared QR data — computed once, reused by all templates ── */
    $qr_empID     = $employee->emp_id      ?? 'EMPLOYEE_ID';
    $qr_companyID = $employee->company_id  ?? 'COMPANY_ID';
    $qr_empDbId   = $employee->id;

    $qr_payload   = \App\Helpers\QrCodeEncryption::encrypt(json_encode([
        'emp_id'     => $qr_empID,
        'company_id' => $qr_companyID,
        'id'         => $qr_empDbId,
    ]));

    /* 106 px ≈ 28 mm on CR80 — minimum reliable scan size */
    $qr_svg    = QrCode::size(106)->margin(1)->generate($qr_payload);
    $qr_base64 = 'data:image/svg+xml;base64,' . base64_encode($qr_svg);
@endphp

<style>
    /*FONTS*/
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap');

    /*WRAPPER */
    .idcard-multi-section {
        font-family: 'Montserrat', 'Segoe UI', system-ui, sans-serif;
        padding: 30px 15px;
    }

    .idcard-multi-section .section-heading {
        font-size: 1.1rem;
        font-weight: 700;
        color: #444;
        text-align: center;
        margin-bottom: 18px;
        letter-spacing: 0.5px;
    }

    /*TEMPLATE SWITCHER  —  pill buttons*/
    .idc-switcher {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }

    .idc-switcher-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 18px;
        border-radius: 50px;
        border: 2px solid #ddd;
        background: #fff;
        color: #666;
        font-size: 0.78rem;
        font-weight: 700;
        font-family: 'Montserrat', system-ui, sans-serif;
        letter-spacing: 0.4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .idc-switcher-btn:hover {
        border-color: #aaa;
        color: #333;
    }

    .idc-switcher-btn.active {
        border-color: transparent;
        color: #fff;
    }

    .idc-switcher-btn.active.btn-wave { background: linear-gradient(135deg, #1a3a6b, #4a7ab5); }
    .idc-switcher-btn.active.btn-gold { background: linear-gradient(135deg, #d4a017, #9a6e00); }

    /* TEMPLATE PANELS  (each hidden by default) */
    .idc-template-panel {
        display: none;
    }
    .idc-template-panel.active {
        display: block;
    }

    /* SHARED CARD ROW + LABELS*/
    .idc-cards-row {
        display: flex;
        flex-wrap: wrap;
        gap: 32px;
        justify-content: center;
        margin-bottom: 24px;
    }

    .idc-card-label {
        text-align: center;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #999;
        margin-bottom: 10px;
    }

    /* SHARED CARD SHELL  —  CR80 portrait
       54 × 85.6 mm  →  at 280 px wide: height = 444 px */
    .idc-shell {
        width: 280px;
        height: 444px;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 12px 48px rgba(0,0,0,0.18);
        flex-shrink: 0;
    }

    /* DOWNLOAD BUTTON  (shared, color set per template)*/
    .idc-download-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: 0.3px;
        font-family: 'Montserrat', system-ui, sans-serif;
        transition: opacity 0.2s, transform 0.15s;
    }
    .idc-download-btn:hover  { opacity: 0.9; transform: translateY(-1px); }
    .idc-download-btn:active { transform: translateY(0); }
    .idc-download-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    /* TEMPLATE 1 — WAVE */

    /* FRONT */
    #idc-wave-front {
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #idc-wave-front .wf-header {
        width: 100%;
        background: #ffffff;
        padding: 18px 16px 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-shrink: 0;
    }

    #idc-wave-front .wf-header .co-logo {
        width: 38px; height: 38px;
        object-fit: contain;
        border-radius: 6px;
        flex-shrink: 0;
    }

    #idc-wave-front .wf-header .co-logo-placeholder {
        width: 38px; height: 38px;
        background: #1a3a6b;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    #idc-wave-front .wf-header .co-logo-placeholder i { color: #fff; font-size: 1rem; }

    #idc-wave-front .wf-header .co-name {
        font-size: 0.82rem;
        font-weight: 800;
        color: #1a3a6b;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        line-height: 1.25;
        max-width: 160px;
    }

    #idc-wave-front .wf-photo-area { margin-top: 8px; flex-shrink: 0; }

    #idc-wave-front .wf-photo-ring {
        width: 96px; height: 96px;
        border-radius: 50%;
        border: 4px solid #1a3a6b;
        padding: 3px;
        box-shadow: 0 4px 18px rgba(26,58,107,0.22);
        background: #fff;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #idc-wave-front .wf-photo-ring img {
        width: 100%; height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    #idc-wave-front .wf-photo-ring .no-photo {
        width: 100%; height: 100%;
        border-radius: 50%;
        background: #e8eef6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a3a6b;
        font-size: 2.2rem;
    }

    #idc-wave-front .wf-name {
        margin-top: 14px;
        font-size: 1.1rem;
        font-weight: 900;
        color: #1a3a6b;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
        padding: 0 14px;
        line-height: 1.2;
    }

    #idc-wave-front .wf-position {
        margin-top: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #4a7ab5;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        text-align: center;
    }

    #idc-wave-front .wf-info-table {
        margin-top: 14px;
        width: 100%;
        padding: 0 20px;
        font-size: 0.68rem;
        color: #333;
        flex-shrink: 0;
    }

    #idc-wave-front .wf-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        border-bottom: 1px solid #e8eef6;
    }

    #idc-wave-front .wf-info-row:last-child { border-bottom: none; }

    #idc-wave-front .wf-info-label {
        font-weight: 700;
        color: #1a3a6b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #idc-wave-front .wf-info-value {
        font-weight: 600;
        color: #333;
        text-align: right;
        max-width: 60%;
    }

    #idc-wave-front .wf-wave-footer {
        margin-top: auto;
        width: 100%;
        position: relative;
        flex-shrink: 0;
        height: 150px;
    }

    #idc-wave-front .wf-wave-footer svg.wave-bg {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
    }

    #idc-wave-front .wf-wave-footer .wf-qr-wrap {
        position: absolute;
        bottom: 10px; right: 10px;
        background: #fff;
        padding: 6px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.18);
        line-height: 0;
        z-index: 2;
    }

    #idc-wave-front .wf-wave-footer .wf-qr-wrap img,
    #idc-wave-front .wf-wave-footer .wf-qr-wrap svg {
        width: 106px; height: 106px;
        display: block;
    }

    #idc-wave-front .wf-wave-footer .wf-valid-text {
        position: absolute;
        bottom: 14px; left: 14px;
        z-index: 2;
        color: rgba(255,255,255,0.9);
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.6;
    }

    /* BACK — wave */
    #idc-wave-back {
        background: #1a3a6b;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0;
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    #idc-wave-back .back-wave-top {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        pointer-events: none;
    }

    #idc-wave-back .back-wave-bottom {
        position: absolute;
        bottom: 0; left: 0;
        width: 100%;
        pointer-events: none;
        transform: rotate(180deg);
    }

    #idc-wave-back .back-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 20px;
        width: 100%;
    }

    #idc-wave-back .back-logo-img {
        width: 56px; height: 56px;
        object-fit: contain;
        border-radius: 10px;
        background: rgba(255,255,255,0.12);
        padding: 4px;
        margin-bottom: 10px;
    }

    #idc-wave-back .back-logo-placeholder {
        width: 56px; height: 56px;
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        font-size: 1.4rem;
    }

    #idc-wave-back .back-co-name {
        font-size: 0.9rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        line-height: 1.3;
        margin-bottom: 3px;
    }

    #idc-wave-back .back-industry {
        font-size: 0.65rem;
        opacity: 0.7;
        font-style: italic;
        margin-bottom: 14px;
    }

    #idc-wave-back .back-divider {
        width: 80%;
        height: 1px;
        background: rgba(255,255,255,0.25);
        margin-bottom: 14px;
    }

    #idc-wave-back .back-contact {
        width: 100%;
        text-align: left;
        font-size: 0.67rem;
        line-height: 2;
        padding: 0 4px;
    }

    #idc-wave-back .back-contact i { width: 16px; opacity: 0.8; margin-right: 4px; }

    #idc-wave-back .back-footer-text {
        margin-top: 14px;
        padding-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.2);
        width: 100%;
        font-size: 0.6rem;
        opacity: 0.7;
        line-height: 1.9;
        text-align: center;
    }

    /* TEMPLATE 2 — GOLD 
       Original gold gradient header design */

    /* FRONT */
    #idc-gold-front {
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #idc-gold-front .front-header {
        width: 100%;
        background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%);
        padding: 14px 16px 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-shrink: 0;
    }

    #idc-gold-front .front-header .co-logo {
        width: 36px; height: 36px;
        object-fit: contain;
        border-radius: 6px;
        background: rgba(255,255,255,0.2);
        padding: 2px;
    }

    #idc-gold-front .front-header .co-logo-placeholder {
        width: 36px; height: 36px;
        background: rgba(255,255,255,0.25);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #idc-gold-front .front-header .co-logo-placeholder i { color: #fff; font-size: 1rem; }

    #idc-gold-front .front-header .co-name {
        font-size: 0.72rem;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        line-height: 1.3;
        max-width: 160px;
    }

    #idc-gold-front .photo-area { margin-top: 18px; flex-shrink: 0; }

    #idc-gold-front .photo-ring {
        width: 100px; height: 100px;
        border-radius: 50%;
        border: 3px solid #d4a017;
        padding: 3px;
        box-shadow: 0 4px 16px rgba(212,160,23,0.35);
        background: #fff;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #idc-gold-front .photo-ring img {
        width: 100%; height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    #idc-gold-front .photo-ring .no-photo {
        width: 100%; height: 100%;
        border-radius: 50%;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #bbb;
        font-size: 2rem;
    }

    #idc-gold-front .emp-name {
        margin-top: 14px;
        font-size: 1rem;
        font-weight: 800;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        text-align: center;
        padding: 0 16px;
        line-height: 1.25;
    }

    #idc-gold-front .emp-position {
        margin-top: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #d4a017;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
    }

    #idc-gold-front .emp-id-badge {
        margin-top: 8px;
        background: #f7f7f7;
        border: 1px solid #ebebeb;
        border-radius: 20px;
        padding: 3px 14px;
        font-size: 0.7rem;
        color: #666;
        letter-spacing: 1px;
        font-weight: 600;
    }

    #idc-gold-front .front-footer {
        margin-top: auto;
        width: 100%;
        background: linear-gradient(180deg, #d4a017 0%, #9a6e00 100%);
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    #idc-gold-front .qr-box {
        background: #fff;
        padding: 6px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        line-height: 0;
    }

    #idc-gold-front .qr-box img,
    #idc-gold-front .qr-box svg {
        width: 106px; height: 106px;
        display: block;
    }

    /* BACK — gold */
    #idc-gold-back {
        background: linear-gradient(160deg, #c9950f 0%, #7a5500 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px 20px;
        color: #fff;
        text-align: center;
    }

    #idc-gold-back .back-logo {
        width: 60px; height: 60px;
        object-fit: contain;
        border-radius: 10px;
        background: rgba(255,255,255,0.15);
        padding: 4px;
        margin-bottom: 12px;
    }

    #idc-gold-back .back-logo-placeholder {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 1.5rem;
    }

    #idc-gold-back .back-company-name {
        font-size: 0.95rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.3;
        margin-bottom: 4px;
    }

    #idc-gold-back .back-industry {
        font-size: 0.72rem;
        opacity: 0.8;
        margin-bottom: 16px;
        font-style: italic;
    }

    #idc-gold-back .back-divider {
        width: 100%;
        height: 1px;
        background: rgba(255,255,255,0.35);
        margin-bottom: 16px;
    }

    #idc-gold-back .back-contact {
        width: 100%;
        text-align: left;
        font-size: 0.72rem;
        line-height: 1.9;
    }

    #idc-gold-back .back-contact i { width: 16px; opacity: 0.85; }

    #idc-gold-back .back-footer {
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px solid rgba(255,255,255,0.3);
        width: 100%;
        font-size: 0.65rem;
        opacity: 0.8;
        line-height: 1.8;
        text-align: center;
    }
</style>


<div class="idcard-multi-section">

    <p class="section-heading">
        <i class="fa fa-id-card me-2"></i> Employee Identity Card
    </p>

    {{--TEMPLATE SWITCHER BUTTONS--}}
    <div class="idc-switcher">
        <button class="idc-switcher-btn btn-wave active"
                onclick="idcSwitchTemplate('wave', this)">
            <i class="fa fa-water"></i> Wave
        </button>
        <button class="idc-switcher-btn btn-gold"
                onclick="idcSwitchTemplate('gold', this)">
            <i class="fa fa-star"></i> Gold
        </button>
        {{--
            To add more templates in the future:
            1. Add a button here:   <button class="idc-switcher-btn btn-YOURKEY" onclick="idcSwitchTemplate('YOURKEY', this)">...</button>
            2. Add a panel below:   <div id="idc-panel-YOURKEY" class="idc-template-panel">...</div>
            3. Style your card using #idc-YOURKEY-front and #idc-YOURKEY-back selectors
        --}}
    </div>


    {{-- TEMPLATE PANEL 1  ·  WAVE --}}
    <div id="idc-panel-wave" class="idc-template-panel active">

        <div class="idc-cards-row">

            {{-- Front --}}
            <div>
                <p class="idc-card-label">Front Side</p>
                <div class="idc-shell" id="idc-wave-front">

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
                            <span class="wf-info-label">ID Number</span>
                            <span class="wf-info-value">{{ $employee->emp_id ?? 'N/A' }}</span>
                        </div>
                        <div class="wf-info-row">
                            <span class="wf-info-label">Valid Through</span>
                            <span class="wf-info-value">{{ date('d/m/Y', strtotime($employee->created_at)) }}</span>
                        </div>
                    </div>

                    <div class="wf-wave-footer">
                        <svg class="wave-bg" viewBox="0 0 280 150" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0,65 C60,15 140,100 200,50 C240,18 265,38 280,28 L280,150 L0,150 Z" fill="#1a3a6b"/>
                            <path d="M0,82 C50,38 120,110 190,65 C230,38 260,54 280,44 L280,150 L0,150 Z" fill="#2a5298" opacity="0.6"/>
                        </svg>
                        <div class="wf-valid-text">
                            <div>Authorized</div>
                            <div>by Company</div>
                        </div>
                        <div class="wf-qr-wrap">
                            <img src="{{ $qr_base64 }}" width="106" height="106" alt="QR Code">
                        </div>
                    </div>

                </div>
            </div>

            {{-- Back --}}
            <div>
                <p class="idc-card-label">Back Side</p>
                <div class="idc-shell" id="idc-wave-back">
                    <svg class="back-wave-top" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="#2a5298" opacity="0.5"/>
                    </svg>
                    <svg class="back-wave-bottom" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="#2a5298" opacity="0.5"/>
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
                            <div><i class="fa fa-map-marker"></i> {{ $employee->company->address ?? 'Address Not Available' }}</div>
                            <div><i class="fa fa-phone"></i> {{ $employee->company->mobile ?? 'Phone Not Available' }}</div>
                            <div><i class="fa fa-envelope"></i> {{ $employee->company->email ?? 'Email Not Available' }}</div>
                            @if($employee->company && $employee->company->website)
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

        <div style="text-align:center;">
            <button class="idc-download-btn"
                    id="idc-wave-download-btn"
                    style="background:linear-gradient(135deg,#1a3a6b,#4a7ab5);box-shadow:0 4px 14px rgba(26,58,107,0.35);"
                    onclick="idcDownload('wave')">
                <i class="fa fa-file-pdf"></i> Download PDF Card
            </button>
        </div>

    </div>{{-- /panel wave --}}


    {{-- TEMPLATE PANEL 2  ·  GOLD --}}
    <div id="idc-panel-gold" class="idc-template-panel">

        <div class="idc-cards-row">

            {{-- Front --}}
            <div>
                <p class="idc-card-label">Front Side</p>
                <div class="idc-shell" id="idc-gold-front">

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
                            <img src="{{ $qr_base64 }}" width="106" height="106" alt="QR Code">
                        </div>
                    </div>

                </div>
            </div>

            {{-- Back --}}
            <div>
                <p class="idc-card-label">Back Side</p>
                <div class="idc-shell" id="idc-gold-back">

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
                        <div><i class="fa fa-map-marker me-2"></i> {{ $employee->company->address ?? 'Address Not Available' }}</div>
                        <div><i class="fa fa-phone me-2"></i> {{ $employee->company->mobile ?? 'Phone Not Available' }}</div>
                        <div><i class="fa fa-envelope"></i> {{ $employee->company->email ?? 'Email Not Available' }}</div>
                        @if($employee->company && $employee->company->website)
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

        <div style="text-align:center;">
            <button class="idc-download-btn"
                    id="idc-gold-download-btn"
                    style="background:linear-gradient(135deg,#d4a017,#9a6e00);box-shadow:0 4px 14px rgba(212,160,23,0.4);"
                    onclick="idcDownload('gold')">
                <i class="fa fa-file-pdf"></i> Download PDF Card
            </button>
        </div>

    </div>{{-- /panel gold --}}

</div>{{-- .idcard-multi-section --}}


{{-- ── Scripts ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
/* TEMPLATE SWITCHER
   — shows the chosen panel, hides the rest
   — updates active state on switcher buttons */
function idcSwitchTemplate(key, clickedBtn) {
    // Hide all panels
    document.querySelectorAll('.idc-template-panel').forEach(p => p.classList.remove('active'));
    // Deactivate all switcher buttons
    document.querySelectorAll('.idc-switcher-btn').forEach(b => b.classList.remove('active'));

    // Show the selected panel + activate its button
    document.getElementById('idc-panel-' + key).classList.add('active');
    clickedBtn.classList.add('active');
}


/* UNIFIED PDF DOWNLOAD
   — reads front/back IDs for the active template
   — CR80 portrait: 54 × 85.6 mm, one side per page */
async function idcDownload(templateKey) {
    const btn = document.getElementById('idc-' + templateKey + '-download-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating PDF...';

    try {
        const { jsPDF } = window.jspdf;

        // CR80 standard card dimensions (portrait)
        const cardW_mm = 54;
        const cardH_mm = 85.6;

        const captureOpts = {
            scale:           4,       // ~300 DPI equivalent for sharp print output
            useCORS:         true,
            allowTaint:      true,
            logging:         false,
            backgroundColor: '#ffffff',
        };

        // ── Capture Front ──
        const frontEl     = document.getElementById('idc-' + templateKey + '-front');
        const frontCanvas = await html2canvas(frontEl, captureOpts);
        const frontImg    = frontCanvas.toDataURL('image/jpeg', 1.0);

        // Create PDF at exact card size
        const pdf = new jsPDF({
            unit:        'mm',
            format:      [cardW_mm, cardH_mm],
            orientation: 'portrait',
        });

        pdf.addImage(frontImg, 'JPEG', 0, 0, cardW_mm, cardH_mm);

        // ── Page 2: Back ──
        pdf.addPage([cardW_mm, cardH_mm], 'portrait');

        const backEl     = document.getElementById('idc-' + templateKey + '-back');
        const backCanvas = await html2canvas(backEl, captureOpts);
        const backImg    = backCanvas.toDataURL('image/jpeg', 1.0);

        pdf.addImage(backImg, 'JPEG', 0, 0, cardW_mm, cardH_mm);

        pdf.save('{{ $employee->fname }}_{{ $employee->lname }}_ID_Card.pdf');

    } catch (err) {
        console.error('ID Card PDF error:', err);
        alert('Could not generate PDF. Please check the browser console for details.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-file-pdf"></i> Download PDF Card';
    }
}
</script>