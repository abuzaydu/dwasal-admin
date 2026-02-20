{{--
    Employee ID Card Partial
    Include as: @include('hr.employees.employee-id-card', ['employee' => $employee, 'position' => $position, 'user_photo' => $user_photo])
--}}

<style>
    .idcard-section {
        font-family: 'Segoe UI', system-ui, sans-serif;
        padding: 30px 15px;
    }

    .idcard-section .section-heading {
        font-size: 1.1rem;
        font-weight: 700;
        color: #444;
        text-align: center;
        margin-bottom: 28px;
        letter-spacing: 0.5px;
    }

    .idcard-section .cards-row {
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        justify-content: center;
        margin-bottom: 28px;
    }

    .idcard-section .card-label {
        text-align: center;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #999;
        margin-bottom: 10px;
    }

    /* ── Shared Card Shell ── */
    .id-card-shell {
        width: 280px;
        height: 440px;
        border-radius: 18px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        flex-shrink: 0;
    }

    /* ══════════════════════════════
       FRONT CARD
    ══════════════════════════════ */
    #idc-front {
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Gold top stripe */
    #idc-front .front-header {
        width: 100%;
        background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%);
        padding: 14px 16px 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-shrink: 0;
    }

    #idc-front .front-header .co-logo {
        width: 36px;
        height: 36px;
        object-fit: contain;
        border-radius: 6px;
        background: rgba(255,255,255,0.2);
        padding: 2px;
    }

    #idc-front .front-header .co-name {
        font-size: 0.72rem;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        line-height: 1.3;
        max-width: 160px;
    }

    /* Photo area */
    #idc-front .photo-area {
        margin-top: 18px;
        flex-shrink: 0;
    }

    #idc-front .photo-ring {
        width: 100px;
        height: 100px;
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

    #idc-front .photo-ring img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    #idc-front .photo-ring .no-photo {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #bbb;
        font-size: 2rem;
    }

    /* Name & position */
    #idc-front .emp-name {
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

    #idc-front .emp-position {
        margin-top: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #d4a017;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
    }

    #idc-front .emp-id-badge {
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

    /* QR footer */
    #idc-front .front-footer {
        margin-top: auto;
        width: 100%;
        background: linear-gradient(180deg, #d4a017 0%, #9a6e00 100%);
        height: 138px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    #idc-front .qr-box {
        background: #fff;
        padding: 6px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        line-height: 0;
    }

    #idc-front .qr-box img,
    #idc-front .qr-box svg {
        width: 110px;
        height: 110px;
        display: block;
    }

    /* ══════════════════════════════
       BACK CARD
    ══════════════════════════════ */
    #idc-back {
        background: linear-gradient(160deg, #c9950f 0%, #7a5500 100%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 24px 20px;
        color: #fff;
        text-align: center;
    }

    #idc-back .back-logo {
        width: 60px;
        height: 60px;
        object-fit: contain;
        border-radius: 10px;
        background: rgba(255,255,255,0.15);
        padding: 4px;
        margin-bottom: 12px;
    }

    #idc-back .back-logo-placeholder {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 1.5rem;
    }

    #idc-back .back-company-name {
        font-size: 0.95rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.3;
        margin-bottom: 4px;
    }

    #idc-back .back-industry {
        font-size: 0.72rem;
        opacity: 0.8;
        margin-bottom: 16px;
        font-style: italic;
    }

    #idc-back .back-divider {
        width: 100%;
        height: 1px;
        background: rgba(255,255,255,0.35);
        margin-bottom: 16px;
    }

    #idc-back .back-contact {
        width: 100%;
        text-align: left;
        font-size: 0.72rem;
        line-height: 1.9;
    }

    #idc-back .back-contact i {
        width: 16px;
        opacity: 0.85;
    }

    #idc-back .back-footer {
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px solid rgba(255,255,255,0.3);
        width: 100%;
        font-size: 0.65rem;
        opacity: 0.8;
        line-height: 1.8;
        text-align: center;
    }

    /* ── Download Button ── */
    .idcard-download-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #d4a017, #9a6e00);
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        letter-spacing: 0.3px;
        transition: opacity 0.2s, transform 0.15s;
        box-shadow: 0 4px 14px rgba(212,160,23,0.4);
    }

    .idcard-download-btn:hover  { opacity: 0.92; transform: translateY(-1px); }
    .idcard-download-btn:active { transform: translateY(0); }
    .idcard-download-btn:disabled { opacity: 0.55; cursor: not-allowed; transform: none; }
</style>

<div class="idcard-section">

    <p class="section-heading">
        <i class="fa fa-id-card me-2"></i> Employee Identity Card
    </p>

    <div class="cards-row">

        {{-- ══════════════════════
             FRONT
        ══════════════════════ --}}
        <div>
            <p class="card-label">Front Side</p>
            <div class="id-card-shell" id="idc-front">

                {{-- Header --}}
                <div class="front-header">
                    @if($employee->company && $employee->company->logo_url)
                        <img class="co-logo"
                             src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                             crossorigin="anonymous"
                             alt="Logo">
                    @else
                        <div style="width:36px;height:36px;background:rgba(255,255,255,0.25);border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-building" style="color:#fff;font-size:1rem;"></i>
                        </div>
                    @endif
                    <div class="co-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                </div>

                {{-- Photo --}}
                <div class="photo-area">
                    <div class="photo-ring">
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
                <div class="emp-name">{{ $employee->fname }} {{ $employee->lname }}</div>

                {{-- Position --}}
                <div class="emp-position">{{ $position ? $position->name : 'Designation' }}</div>

                {{-- ID Badge --}}
                <div class="emp-id-badge">EMPLOYEE ID: {{ $employee->emp_id ?? 'N/A' }}</div>

                {{-- QR Footer --}}
                <div class="front-footer">
                    @php
                        $empID      = $employee->emp_id      ?? 'EMPLOYEE_ID';
                        $companyID  = $employee->company_id  ?? 'COMPANY_ID';
                        $employeeId = $employee->id;

                        $qrData    = json_encode([
                            'emp_id'     => $empID,
                            'company_id' => $companyID,
                            'id'         => $employeeId,
                        ]);
                        $qrContent  = \App\Helpers\QrCodeEncryption::encrypt($qrData);
                        $qrSvg      = QrCode::size(110)->margin(0)->generate($qrContent);
                        $qrBase64   = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
                    @endphp
                    <div class="qr-box">
                        <img src="{{ $qrBase64 }}" width="110" height="110" alt="QR Code">
                    </div>
                </div>

            </div>
        </div>

        {{-- ══════════════════════
             BACK
        ══════════════════════ --}}
        <div>
            <p class="card-label">Back Side</p>
            <div class="id-card-shell" id="idc-back">

                {{-- Logo --}}
                @if($employee->company && $employee->company->logo_url)
                    <img class="back-logo"
                         src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}"
                         crossorigin="anonymous"
                         alt="Logo">
                @else
                    <div class="back-logo-placeholder">
                        <i class="fa fa-building"></i>
                    </div>
                @endif

                {{-- Company Name --}}
                <div class="back-company-name">{{ $employee->company->name ?? 'Company Name' }}</div>
                <div class="back-industry">{{ $employee->company->industry ?? 'Industry' }}</div>

                <div class="back-divider"></div>

                {{-- Contact --}}
                <div class="back-contact">
                    <div><i class="fa fa-map-marker me-2"></i> {{ $employee->company->address ?? 'Address Not Available' }}</div>
                    <div><i class="fa fa-phone me-2"></i> {{ $employee->company->mobile ?? 'Phone Not Available' }}</div>
                    <div><i class="fa fa-envelope"></i> {{ $employee->company->email ?? 'Email Not Available' }}</div>
                    @if($employee->company && $employee->company->website)
                        <div><i class="fa fa-globe"></i> {{ $employee->company->website }}</div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="back-footer">
                    <div>Issue Date: {{ date('d/m/Y', strtotime($employee->created_at)) }}</div>
                    <div>Authorized by Company</div>
                </div>

            </div>
        </div>

    </div>{{-- .cards-row --}}

    {{-- Download --}}
    <div style="text-align:center;">
        <button class="idcard-download-btn" id="idcDownloadBtn" onclick="downloadEmployeeIDCard()">
            <i class="fa fa-file-pdf"></i> Download PDF Card
        </button>
    </div>

</div>{{-- .idcard-section --}}


{{-- ── Scripts ── --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
async function downloadEmployeeIDCard() {
    const btn = document.getElementById('idcDownloadBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Generating PDF...';

    try {
        const { jsPDF } = window.jspdf;

        // Landscape A4 so both cards sit side by side comfortably
        const pdf = new jsPDF({ unit: 'mm', format: 'a4', orientation: 'landscape' });

        const pageW  = pdf.internal.pageSize.getWidth();   // 297mm
        const pageH  = pdf.internal.pageSize.getHeight();  // 210mm

        const margin  = 15;
        const gap     = 10; // space between the two cards
        const cardW   = (pageW - (margin * 2) - gap) / 2;  // each card gets equal half

        const captureOptions = {
            scale:           3,
            useCORS:         true,
            allowTaint:      true,
            logging:         false,
            backgroundColor: '#ffffff',
        };

        // ── Capture Front ──
        const frontEl     = document.getElementById('idc-front');
        const frontCanvas = await html2canvas(frontEl, captureOptions);
        const frontImg    = frontCanvas.toDataURL('image/jpeg', 0.98);
        const frontH      = (frontCanvas.height / frontCanvas.width) * cardW;
        const frontTop    = (pageH - frontH) / 2; // center vertically

        // ── Capture Back ──
        const backEl     = document.getElementById('idc-back');
        const backCanvas = await html2canvas(backEl, captureOptions);
        const backImg    = backCanvas.toDataURL('image/jpeg', 0.98);
        const backH      = (backCanvas.height / backCanvas.width) * cardW;
        const backTop    = (pageH - backH) / 2;

        // ── Place both on ONE page ──
        // Front on the left
        pdf.addImage(frontImg, 'JPEG', margin, frontTop, cardW, frontH);

        // Back on the right
        pdf.addImage(backImg,  'JPEG', margin + cardW + gap, backTop, cardW, backH);

        // Optional: add small labels above each card
        pdf.setFontSize(8);
        pdf.setTextColor(150, 150, 150);
        pdf.text('FRONT', margin + (cardW / 2), frontTop - 3, { align: 'center' });
        pdf.text('BACK',  margin + cardW + gap + (cardW / 2), backTop - 3, { align: 'center' });

        pdf.save('{{ $employee->fname }}_{{ $employee->lname }}_ID_Card.pdf');

    } catch (err) {
        console.error('ID Card PDF error:', err);
        alert('Could not generate PDF. Check console for details.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-file-pdf"></i> Download PDF Card';
    }
}
</script>