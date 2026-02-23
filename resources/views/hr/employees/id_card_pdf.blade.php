{{--
    Employee ID Card — Wave Template
    Include as: @include('hr.employees.employee-id-card-wave', ['employee' => $employee, 'position' => $position, 'user_photo' => $user_photo])
--}}

<style>
    /* ── Google Font ── */
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap');

    .idcard-wave-section {
        font-family: 'Montserrat', 'Segoe UI', system-ui, sans-serif;
        padding: 30px 15px;
    }

    .idcard-wave-section .section-heading {
        font-size: 1.1rem;
        font-weight: 700;
        color: #444;
        text-align: center;
        margin-bottom: 28px;
        letter-spacing: 0.5px;
    }

    .idcard-wave-section .cards-row {
        display: flex;
        flex-wrap: wrap;
        gap: 32px;
        justify-content: center;
        margin-bottom: 28px;
    }

    .idcard-wave-section .card-label {
        text-align: center;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #999;
        margin-bottom: 10px;
    }

    /* ══════════════════════════════
       SHARED SHELL  —  CR80 portrait
       85.6 × 54 mm → scale ×4 for screen preview
       Actual proportions: 54:85.6  ≈  width:height = 1:1.585
       At 280px wide → height = 444px
    ══════════════════════════════ */
    .id-card-wave-shell {
        width: 280px;
        height: 444px;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 12px 48px rgba(0,0,0,0.18);
        flex-shrink: 0;
    }

    /* ══════════════════════════════
       FRONT  —  White + Navy Wave
    ══════════════════════════════ */
    #idcw-front {
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* ── Top header bar ── */
    #idcw-front .wf-header {
        width: 100%;
        background: #ffffff;
        padding: 18px 16px 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-shrink: 0;
    }

    #idcw-front .wf-header .co-logo {
        width: 38px;
        height: 38px;
        object-fit: contain;
        border-radius: 6px;
        flex-shrink: 0;
    }

    #idcw-front .wf-header .co-logo-placeholder {
        width: 38px;
        height: 38px;
        background: #1a3a6b;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    #idcw-front .wf-header .co-logo-placeholder i {
        color: #fff;
        font-size: 1rem;
    }

    #idcw-front .wf-header .co-name {
        font-size: 0.82rem;
        font-weight: 800;
        color: #1a3a6b;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        line-height: 1.25;
        max-width: 160px;
    }

    /* ── Photo ring ── */
    #idcw-front .wf-photo-area {
        margin-top: 8px;
        flex-shrink: 0;
    }

    #idcw-front .wf-photo-ring {
        width: 96px;
        height: 96px;
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

    #idcw-front .wf-photo-ring img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    #idcw-front .wf-photo-ring .no-photo {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #e8eef6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a3a6b;
        font-size: 2.2rem;
    }

    /* ── Name & position ── */
    #idcw-front .wf-name {
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

    #idcw-front .wf-position {
        margin-top: 4px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #4a7ab5;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        text-align: center;
    }

    /* ── Info rows ── */
    #idcw-front .wf-info-table {
        margin-top: 14px;
        width: 100%;
        padding: 0 20px;
        font-size: 0.68rem;
        color: #333;
        flex-shrink: 0;
    }

    #idcw-front .wf-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        border-bottom: 1px solid #e8eef6;
    }

    #idcw-front .wf-info-row:last-child {
        border-bottom: none;
    }

    #idcw-front .wf-info-label {
        font-weight: 700;
        color: #1a3a6b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #idcw-front .wf-info-value {
        font-weight: 600;
        color: #333;
        text-align: right;
        max-width: 60%;
    }

    /* ── Wave footer with QR ── */
    #idcw-front .wf-wave-footer {
        margin-top: auto;
        width: 100%;
        position: relative;
        flex-shrink: 0;
        height: 150px;   /* taller so the larger scannable QR fits comfortably */
    }

    #idcw-front .wf-wave-footer svg.wave-bg {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
    }

    #idcw-front .wf-wave-footer .wf-qr-wrap {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: #fff;
        padding: 6px;      /* slightly more padding for better scanner contrast margin */
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.18);
        line-height: 0;
        z-index: 2;
    }

    #idcw-front .wf-wave-footer .wf-qr-wrap img,
    #idcw-front .wf-wave-footer .wf-qr-wrap svg {
        width: 106px;   /* ≈ 28mm on the printed CR80 card — minimum reliable scan size */
        height: 106px;
        display: block;
    }

    #idcw-front .wf-wave-footer .wf-valid-text {
        position: absolute;
        bottom: 14px;
        left: 14px;
        z-index: 2;
        color: rgba(255,255,255,0.9);
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.6;
    }

    /* ══════════════════════════════
       BACK  —  Full Navy + Wave accent
    ══════════════════════════════ */
    #idcw-back {
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

    /* decorative wave top */
    #idcw-back .back-wave-top {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        pointer-events: none;
    }

    /* decorative wave bottom */
    #idcw-back .back-wave-bottom {
        position: absolute;
        bottom: 0; left: 0;
        width: 100%;
        pointer-events: none;
        transform: rotate(180deg);
    }

    #idcw-back .back-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px 20px;
        width: 100%;
    }

    #idcw-back .back-logo-img {
        width: 56px;
        height: 56px;
        object-fit: contain;
        border-radius: 10px;
        background: rgba(255,255,255,0.12);
        padding: 4px;
        margin-bottom: 10px;
    }

    #idcw-back .back-logo-placeholder {
        width: 56px;
        height: 56px;
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        font-size: 1.4rem;
    }

    #idcw-back .back-co-name {
        font-size: 0.9rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
        line-height: 1.3;
        margin-bottom: 3px;
    }

    #idcw-back .back-industry {
        font-size: 0.65rem;
        opacity: 0.7;
        font-style: italic;
        margin-bottom: 14px;
    }

    #idcw-back .back-divider {
        width: 80%;
        height: 1px;
        background: rgba(255,255,255,0.25);
        margin-bottom: 14px;
    }

    #idcw-back .back-contact {
        width: 100%;
        text-align: left;
        font-size: 0.67rem;
        line-height: 2;
        padding: 0 4px;
    }

    #idcw-back .back-contact i {
        width: 16px;
        opacity: 0.8;
        margin-right: 4px;
    }

    #idcw-back .back-footer-text {
        margin-top: 14px;
        padding-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.2);
        width: 100%;
        font-size: 0.6rem;
        opacity: 0.7;
        line-height: 1.9;
        text-align: center;
    }

    /* ── Download Button ── */
    .idcard-wave-download-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #1a3a6b, #4a7ab5);
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
        box-shadow: 0 4px 14px rgba(26,58,107,0.35);
    }

    .idcard-wave-download-btn:hover  { opacity: 0.9; transform: translateY(-1px); }
    .idcard-wave-download-btn:active { transform: translateY(0); }
    .idcard-wave-download-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
</style>

<div class="idcard-wave-section">

    <p class="section-heading">
        <i class="fa fa-id-card me-2"></i> Employee Identity Card
    </p>

    <div class="cards-row">

        {{-- ══════════════════════
             FRONT
        ══════════════════════ --}}
        <div>
            <p class="card-label">Front Side</p>
            <div class="id-card-wave-shell" id="idcw-front">

                {{-- Header: Logo + Company Name --}}
                <div class="wf-header">
                    @if($employee->company && $employee->company->logo_url)
                        <img class="co-logo"
                             src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                             crossorigin="anonymous"
                             alt="Logo">
                    @else
                        <div class="co-logo-placeholder">
                            <i class="fa fa-building"></i>
                        </div>
                    @endif
                    <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                </div>

                {{-- Photo --}}
                <div class="wf-photo-area">
                    <div class="wf-photo-ring">
                        @if($user_photo)
                            <img src="{{ asset('storage/' . $user_photo) }}"
                                 crossorigin="anonymous"
                                 alt="{{ $employee->fname }}">
                        @else
                            <div class="no-photo">
                                <i class="fa fa-user"></i>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Name --}}
                <div class="wf-name">{{ $employee->fname }} {{ $employee->lname }}</div>

                {{-- Position --}}
                <div class="wf-position">{{ $position ? $position->name : 'Designation' }}</div>

                {{-- Info rows: ID + Issue Date --}}
                <div class="wf-info-table">
                    <div class="wf-info-row">
                        <span class="wf-info-label">ID Number</span>
                        <span class="wf-info-value">{{ $employee->emp_id ?? 'N/A' }}</span>
                    </div>
                    <div class="wf-info-row">
                        <span class="wf-info-label">Valid Through</span>
                        <span class="wf-info-value">{{ date('d/m/Y', strtotime($employee->end_date)) }}</span>
                    </div>
                </div>

                {{-- Wave Footer with QR --}}
                <div class="wf-wave-footer">
                    {{-- SVG wave background --}}
                    <svg class="wave-bg" viewBox="0 0 280 150" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0,65 C60,15 140,100 200,50 C240,18 265,38 280,28 L280,150 L0,150 Z" fill="#1a3a6b"/>
                        <path d="M0,82 C50,38 120,110 190,65 C230,38 260,54 280,44 L280,150 L0,150 Z" fill="#2a5298" opacity="0.6"/>
                    </svg>

                    {{-- Valid text (left side of wave) --}}
                    <div class="wf-valid-text">
                        <div>Authorized</div>
                        <div>by Company</div>
                    </div>

                    {{-- QR Code --}}
                    @php
                        $empID_w      = $employee->emp_id      ?? 'EMPLOYEE_ID';
                        $companyID_w  = $employee->company_id  ?? 'COMPANY_ID';
                        $employeeId_w = $employee->id;

                        $qrData_w    = json_encode([
                            'emp_id'     => $empID_w,
                            'company_id' => $companyID_w,
                            'id'         => $employeeId_w,
                        ]);
                        $qrContent_w  = \App\Helpers\QrCodeEncryption::encrypt($qrData_w);
                        // {{-- QR size: 106px screen = ≈28mm on CR80 print — reliably scannable --}}
                        $qrSvg_w      = QrCode::size(106)->margin(1)->generate($qrContent_w);
                        $qrBase64_w   = 'data:image/svg+xml;base64,' . base64_encode($qrSvg_w);
                    @endphp
                    <div class="wf-qr-wrap">
                        <img src="{{ $qrBase64_w }}" width="106" height="106" alt="QR Code">
                    </div>
                </div>

            </div>
        </div>

        {{-- ══════════════════════
             BACK
        ══════════════════════ --}}
        <div>
            <p class="card-label">Back Side</p>
            <div class="id-card-wave-shell" id="idcw-back">

                {{-- Decorative wave top --}}
                <svg class="back-wave-top" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="#2a5298" opacity="0.5"/>
                </svg>

                {{-- Decorative wave bottom --}}
                <svg class="back-wave-bottom" viewBox="0 0 280 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0,0 L280,0 L280,50 C200,80 100,20 0,60 Z" fill="#2a5298" opacity="0.5"/>
                </svg>

                <div class="back-content">
                    {{-- Logo --}}
                    @if($employee->company && $employee->company->logo_url)
                        <img class="back-logo-img"
                             src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                             crossorigin="anonymous"
                             alt="Logo">
                    @else
                        <div class="back-logo-placeholder">
                            <i class="fa fa-building"></i>
                        </div>
                    @endif

                    {{-- Company Name --}}
                    <div class="back-co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                    <div class="back-industry">{{ $employee->company->industry ?? 'Industry' }}</div>

                    <div class="back-divider"></div>

                    {{-- Contact Details --}}
                    <div class="back-contact">
                        <div><i class="fa fa-map-marker"></i> {{ $employee->company->address ?? 'Address Not Available' }}</div>
                        <div><i class="fa fa-phone"></i> {{ $employee->company->mobile ?? 'Phone Not Available' }}</div>
                        <div><i class="fa fa-envelope"></i> {{ $employee->company->email ?? 'Email Not Available' }}</div>
                        @if($employee->company && $employee->company->website)
                            <div><i class="fa fa-globe"></i> {{ $employee->company->website }}</div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="back-footer-text">
                        <div>Issue Date: {{ date('d/m/Y', strtotime($employee->created_at)) }}</div>
                        <div>Authorized by Company</div>
                    </div>
                </div>

            </div>
        </div>

    </div>{{-- .cards-row --}}

    {{-- Download --}}
    <div style="text-align:center;">
        <button class="idcard-wave-download-btn" id="idcwDownloadBtn" onclick="downloadWaveIDCard()">
            <i class="fa fa-file-pdf"></i> Download PDF Card
        </button>
    </div>

</div>{{-- .idcard-wave-section --}}


{{-- ── Scripts ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
async function downloadWaveIDCard() {
    const btn = document.getElementById('idcwDownloadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating PDF...';

    try {
        const { jsPDF } = window.jspdf;

        // ── CR80 standard ID card size ──
        // Portrait: 54mm wide × 85.6mm tall
        const cardW_mm = 54;
        const cardH_mm = 85.6;

        const captureOptions = {
            scale:           4,       // High res for sharp print output
            useCORS:         true,
            allowTaint:      true,
            logging:         false,
            backgroundColor: '#ffffff',
        };

        // ── Page 1: Front ──
        const frontEl     = document.getElementById('idcw-front');
        const frontCanvas = await html2canvas(frontEl, captureOptions);
        const frontImg    = frontCanvas.toDataURL('image/jpeg', 1.0);

        const pdf = new jsPDF({
            unit:        'mm',
            format:      [cardW_mm, cardH_mm],   // exact CR80 portrait
            orientation: 'portrait',
        });

        pdf.addImage(frontImg, 'JPEG', 0, 0, cardW_mm, cardH_mm);

        // ── Page 2: Back ──
        pdf.addPage([cardW_mm, cardH_mm], 'portrait');

        const backEl     = document.getElementById('idcw-back');
        const backCanvas = await html2canvas(backEl, captureOptions);
        const backImg    = backCanvas.toDataURL('image/jpeg', 1.0);

        pdf.addImage(backImg, 'JPEG', 0, 0, cardW_mm, cardH_mm);

        pdf.save('{{ $employee->fname }}_{{ $employee->lname }}_ID_Card.pdf');

    } catch (err) {
        console.error('Wave ID Card PDF error:', err);
        alert('Could not generate PDF. Check console for details.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-file-pdf"></i> Download PDF Card';
    }
}
</script>