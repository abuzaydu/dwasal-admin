@php
    $qr_empID     = $employee->emp_id      ?? 'EMPLOYEE_ID';
    $qr_companyID = $employee->company_id  ?? 'COMPANY_ID';
    $qr_empDbId   = $employee->id;

    $brandColor = $employee->first()->company->brand_color ?? '#1E3A8A';

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

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap');

    :root {
        --id-primary:   {{ $brandColor }};
        --id-secondary: {{ $primaryDark }};

        --id-scale: 2.6;
        --id-w: calc(54   * var(--id-scale) * 1px);
        --id-h: calc(85.6 * var(--id-scale) * 1px);

        /* Proportional font sizes (mm × scale) */
        --f-xs: calc(1.8 * var(--id-scale) * 1px);
        --f-sm: calc(2.2 * var(--id-scale) * 1px);
        --f-md: calc(2.6 * var(--id-scale) * 1px);
        --f-lg: calc(3.0 * var(--id-scale) * 1px);
        --f-xl: calc(3.4 * var(--id-scale) * 1px);
    }

    /* ── Wrapper ── */
    .idc-wrap {
        font-family: 'Montserrat', 'Segoe UI', sans-serif;
        width: 100%;
        box-sizing: border-box;
        padding: 16px 0 8px;
    }
    .idc-heading {
        text-align: center;
        font-size: 0.95rem;
        font-weight: 700;
        color: #444;
        margin-bottom: 16px;
    }

    /* ── Switcher ── */
    .idc-switcher {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .idc-switcher-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 18px;
        border-radius: 50px;
        border: 2px solid #ddd;
        background: #fff;
        color: #666;
        font-size: 0.76rem;
        font-weight: 700;
        font-family: 'Montserrat', sans-serif;
        cursor: pointer;
        transition: all 0.2s;
    }
    .idc-switcher-btn:hover           { border-color: #bbb; color: #333; }
    .idc-switcher-btn.active          { border-color: transparent; color: #fff; }
    .idc-switcher-btn.active.btn-wave { background: var(--id-primary); }
    .idc-switcher-btn.active.btn-gold { background: var(--id-secondary); }

    /* ── Color picker ── */
    .color-picker {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding-bottom: 18px;
    }
    .color-picker span { font-size: 0.78rem; font-weight: 600; color: #555; }
    .color-option {
        width: 22px; height: 22px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #ccc;
        transition: transform 0.2s;
    }
    .color-option:hover { transform: scale(1.2); }

    /* ── Panels ── */
    .idc-panel        { display: none; }
    .idc-panel.active { display: block; }

    .idc-row {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .idc-col {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .idc-col-label {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #999;
        margin-bottom: 6px;
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
    }

    /* ── Download btn ── */
    .idc-dl-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #fff;
        border: none;
        padding: 9px 26px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Montserrat', sans-serif;
        transition: opacity 0.2s, transform 0.15s;
        margin-top: 6px;
    }
    .idc-dl-btn:hover    { opacity: 0.88; transform: translateY(-1px); }
    .idc-dl-btn:active   { transform: translateY(0); }
    .idc-dl-btn:disabled { opacity: 0.45; cursor: not-allowed; transform: none; }

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
        height: 75%; 
        width: auto; 
        max-width: 70%;
        object-fit: contain; 
        border-radius: 6px; 
    }

    .wave-front .wf-header .co-logo-placeholder {
        height: 70%; 
        width: 60%;
        background: var(--id-secondary);
        border-radius: 10px;
        display: flex; 
        align-items: center; 
        justify-content: center;
        color: #fff; 
        /* Boosted font size for the placeholder text/icon to remain proportional */
        font-size: calc(var(--f-lg) * 1.5); 
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
    }

    .wave-back .back-wave-top {
        width: 100%;
        height: 20%;
        flex-shrink: 0;
        display: block;
        pointer-events: none;
    }

    .wave-back .back-body {
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2% 6% 2%;
        width: 100%;
        box-sizing: border-box;
        text-align: center;
    }

    .wave-back .back-wave-bottom {
        width: 100%;
        height: 20%;
        flex-shrink: 0;
        display: block;
        pointer-events: none;
    }

     .wave-back .back-logo {
        width: 40%; 
        aspect-ratio: 1; 
        object-fit: contain;
        border-radius: 10px; 
        background: rgba(255, 255, 255, 0.12);
        padding: 4%; 
        margin-bottom: 3%; 
        flex-shrink: 0;
    }

    .wave-back .back-logo-placeholder {
        width: 35%; 
        aspect-ratio: 1;
        background: rgba(255, 255, 255, 0.15); 
        border-radius: 10px;
        display: flex; 
        align-items: center; 
        justify-content: center;
        margin-bottom: 3%; 
        /* Boosted the font size so the inner text/icon scales up beautifully with the larger box */
        font-size: calc(var(--f-lg) * 1.6); 
        flex-shrink: 0;
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
</style>


<div class="idc-wrap">

    <p class="idc-heading">
        <i class="fa fa-id-card me-2"></i> Employee Identity Card
    </p>

    <div class="idc-switcher">
        <button class="idc-switcher-btn btn-wave active" onclick="idcSwitch('wave', this)">
            <i class="fa fa-water"></i> Wave
        </button>
        <button class="idc-switcher-btn btn-gold" onclick="idcSwitch('gold', this)">
            <i class="fa fa-star"></i> Gold
        </button>
    </div>

    <div class="color-picker">
        <span>Choose Color:</span>
        <div class="color-option" data-color="#F7941D" style="background:#F7941D" title="Orange"></div>
        <div class="color-option" data-color="#1E3A8A" style="background:#1E3A8A" title="Navy"></div>
        <div class="color-option" data-color="#0F766E" style="background:#0F766E" title="Teal"></div>
        <div class="color-option" data-color="#DC2626" style="background:#DC2626" title="Red"></div>
        <div class="color-option" data-color="#111827" style="background:#111827" title="Dark"></div>
    </div>

    <div id="idc-panel-wave" class="idc-panel active">
        <div class="idc-row">

            <div class="idc-col">
                <div class="idc-col-label">Front Side</div>
                <div class="idc-card wave-front" id="idc-wave-front">

                    <svg class="front-wave-top" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="var(--id-primary)"/>
                    </svg>

                    <div class="wf-header">
                        @if($employee->company && $employee->company->logo_url)
                            <img class="co-logo" src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}" crossorigin="anonymous" alt="Logo">
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
                                <img src="{{ asset('storage/' . $user_photo) }}" crossorigin="anonymous" alt="{{ $employee->fname }}">
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

            <div class="idc-col">
                <div class="idc-col-label">Back Side</div>
                <div class="idc-card wave-back" id="idc-wave-back">

                    <svg class="back-wave-top" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="var(--id-primary)" opacity="0.9"/>
                    </svg>

                    <div class="back-body">
                        @if($employee->company && $employee->company->logo_url)
                            <img class="back-logo" src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}" crossorigin="anonymous" alt="Logo">
                        @else
                            <div class="back-logo-placeholder"><i class="fa fa-building"></i></div>
                        @endif
                        @if($showCompanyName)
                            <div class="back-company-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                        @else
                            <div class="back-industry">{{ $employee->company->industry ?? 'Industry' }}</div>
                        @endif
                        <div class="back-divider"></div>
                        <div class="back-contact">
                            <div><i class="fa fa-map-marker"></i> {{ $employee->company->address ?? 'Address Not Available' }}</div>
                            <div><i class="fa fa-phone"></i> {{ $employee->company->mobile ?? 'Phone Not Available' }}</div>
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

                    <svg class="back-wave-bottom" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,80 L280,80 L280,30 C200,0 100,60 0,20 Z" fill="var(--id-primary)" opacity="0.9"/>
                    </svg>

                </div>
            </div>
        </div>

        <div style="text-align:center;">
            <button class="idc-dl-btn" id="idc-wave-dl"
                    style="background:var(--id-primary);box-shadow:0 4px 14px rgba(0,0,0,0.2);"
                    onclick="idcDownload('wave')">
                <i class="fa fa-file-pdf"></i> Download PDF Card
            </button>
        </div>
    </div>


    <div id="idc-panel-gold" class="idc-panel">
        <div class="idc-row">

            <div class="idc-col">
                <div class="idc-col-label">Front Side</div>
                <div class="idc-card gold-front" id="idc-gold-front">

                    <div class="gh-header">
                        @if($employee->company && $employee->company->logo_url)
                            <img class="co-logo" src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}" crossorigin="anonymous" alt="Logo">
                        @else
                            <div class="co-logo-placeholder"><i class="fa fa-building"></i></div>
                        @endif
                        @if($showCompanyName)
                            <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                        @endif
                    </div>

                    <div class="gf-photo-wrap">
                        <div class="gf-photo-ring">
                            @if($user_photo)
                                <img src="{{ asset('storage/' . $user_photo) }}" crossorigin="anonymous" alt="{{ $employee->fname }}">
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

            <div class="idc-col">
                <div class="idc-col-label">Back Side</div>
                <div class="idc-card gold-back" id="idc-gold-back">
                    <div class="gb-body">
                    @if($employee->company && $employee->company->logo_url)
                        <img class="gb-logo" src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}" crossorigin="anonymous" alt="Logo">
                    @else
                        <div class="gb-logo-placeholder"><i class="fa fa-building"></i></div>
                    @endif
                    @if($showCompanyName)
                        <div class="gb-company-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                    @else
                        <div class="gb-industry">{{ $employee->company->industry ?? 'Industry' }}</div>
                    @endif
                    <div class="gb-divider"></div>
                    <div class="gb-contact">
                        <div><i class="fa fa-map-marker me-1"></i> {{ $employee->company->address ?? 'Address Not Available' }}</div>
                        <div><i class="fa fa-phone me-1"></i> {{ $employee->company->mobile ?? 'Phone Not Available' }}</div>
                        <div><i class="fa fa-envelope me-1"></i> {{ $employee->company->email ?? 'Email Not Available' }}</div>
                        @if($employee->company && $employee->company->website)
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

        <div style="text-align:center;">
            <button class="idc-dl-btn" id="idc-gold-dl"
                    style="background:var(--id-secondary);box-shadow:0 4px 14px rgba(0,0,0,0.2);"
                    onclick="idcDownload('gold')">
                <i class="fa fa-file-pdf"></i> Download PDF Card
            </button>
        </div>
    </div>

</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    function idcSwitch(key, btn) {
        document.querySelectorAll('.idc-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.idc-switcher-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('idc-panel-' + key).classList.add('active');
        btn.classList.add('active');
    }

    document.querySelectorAll('.color-option').forEach(opt => {
        opt.addEventListener('click', function () {
            const color  = this.dataset.color;
            const darker = shadeColor(color, -20);
            document.documentElement.style.setProperty('--id-primary',   color);
            document.documentElement.style.setProperty('--id-secondary', darker);
        });
    });

    function shadeColor(color, pct) {
        let R = parseInt(color.slice(1,3), 16);
        let G = parseInt(color.slice(3,5), 16);
        let B = parseInt(color.slice(5,7), 16);
        R = Math.min(255, Math.round(R * (100 + pct) / 100));
        G = Math.min(255, Math.round(G * (100 + pct) / 100));
        B = Math.min(255, Math.round(B * (100 + pct) / 100));
        return '#' + [R,G,B].map(v => v.toString(16).padStart(2,'0')).join('');
    }

    async function idcDownload(key) {
        const btn = document.getElementById('idc-' + key + '-dl');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating...';
        try {
            const { jsPDF } = window.jspdf;
            const W = 54, H = 85.6;
            const opts = { scale: 4, useCORS: true, allowTaint: true, logging: false, backgroundColor: '#ffffff' };
            const frontCanvas = await html2canvas(document.getElementById('idc-' + key + '-front'), opts);
            const backCanvas  = await html2canvas(document.getElementById('idc-' + key + '-back'),  opts);
            const pdf = new jsPDF({ unit: 'mm', format: [W, H], orientation: 'portrait' });
            pdf.addImage(frontCanvas.toDataURL('image/jpeg', 1), 'JPEG', 0, 0, W, H);
            pdf.addPage([W, H], 'portrait');
            pdf.addImage(backCanvas.toDataURL('image/jpeg', 1),  'JPEG', 0, 0, W, H);
            pdf.save('{{ $employee->fname }}_{{ $employee->lname }}_ID_Card.pdf');
        } catch (err) {
            console.error(err);
            alert('PDF generation failed. Check the browser console for details.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-file-pdf"></i> Download PDF Card';
        }
    }
</script>