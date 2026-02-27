@extends('layouts.vml')

@section('content')

<!-- Breadcrumb -->
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <ul class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="javascript:history.back();" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth">
                    <i class="fa fa-arrow-left"></i>
                </a>
            </li>

                <li class="breadcrumb-item">
                    <a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('badges.index') }}">Badges Management</a>
                </li>
                <li class="breadcrumb-item active">View Badge</li>
            </ul>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 text-end">
            <a href="{{ route('badges.auto.print-one-badge') }}?ids[]={{ urlencode(encrypt($badges->id)) }}"
               target="_blank"
               class="btn btn-primary btn-sm">
                <i class="fa fa-print"></i> Print Badge
            </a>
        </div>
    </div>
</div>

<style>

/* ── Layout ── */
body {
    background: #f0f2f5;
}

.badge-preview-container {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: flex-start;
    gap: 40px;
    margin-top: 40px;
    flex-wrap: wrap;
}

.badge-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.badge-label {
    font-size: 11pt;
    font-weight: 600;
    color: #555;
    text-align: center;
}

/*  Card size (CR80 standard)  */
.badge-card {
    width: 85.6mm;
    height: 54mm;
    border-radius: 3.5mm;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

/* FRONT SIDE */
.badge-card.front {
    background: #ffffff;
}

.badge-top {
    height: 8mm;
    background: linear-gradient(135deg, #F7941D, #e07b10);
}

.badge-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2mm;
    padding: 2mm;
}

.badge-logo {
    width: 14mm;
    height: 14mm;
    object-fit: contain;
}

.badge-company {
    font-size: 9pt;
    font-weight: bold;
    text-align: center;
    color: #222;
}

.badge-divider {
    width: 60%;
    height: 1px;
    background: #F7941D;
}

.badge-number {
    font-size: 8pt;
    font-weight: bold;
    color: #333;
}

.badge-bottom {
    height: 6mm;
    background: linear-gradient(135deg, #F7941D, #e07b10);
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge-bottom span {
    color: white;
    font-size: 6pt;
    font-weight: bold;
    letter-spacing: 1px;
}

/* BACK SIDE */
.badge-card.back {
    background: linear-gradient(160deg, #F7941D 0%, #e07b10 60%, #c96a0a 100%);
    justify-content: center;
    align-items: center;
    position: relative;
    padding: 4mm;
}

/* subtle watermark circle */
.badge-card.back::before {
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

/* PRINT */
@media print {
    body * {
        visibility: hidden;
    }

    #printableBadge,
    #printableBadge * {
        visibility: visible;
    }

    #printableBadge {
        position: absolute;
        left: 0;
        top: 0;
        display: flex;
        flex-direction: row;
        gap: 5mm;
    }
}

</style>

<!-- BADGE PREVIEW — FRONT & BACK -->
<div class="badge-preview-container" id="printableBadge">

    <!-- FRONT -->
    <div class="badge-wrapper">
        <div class="badge-label">▸ Front Side</div>
        <div class="badge-card front">

            <div class="badge-top"></div>

            <div class="badge-body">

                @if($badges->company && $badges->company->logo_url)
                    <img class="badge-logo"
                         src="{{ asset('storage/clogos/'.$badges->company->logo_url) }}"
                         alt="Logo">
                @endif

                <div class="badge-company">
                    {{ $badges->company->name ?? 'COMPANY NAME' }}
                </div>

                <div class="badge-divider"></div>

                <div class="badge-number">
                    Badge No: {{ $badges->badge_number }}
                </div>

            </div>

            <div class="badge-bottom">
                <span>AUTHORIZED BADGE</span>
            </div>

        </div>
    </div>

    <!-- BACK -->
    <div class="badge-wrapper">
        <div class="badge-label">▸ Back Side</div>
        <div class="badge-card back">

            <div class="back-inner">

                @if($badges->company && $badges->company->logo_url)
                    <img class="back-logo"
                         src="{{ asset('storage/clogos/'.$badges->company->logo_url) }}"
                         alt="Logo">
                @endif

                <div class="back-company-name">
                    {{ $badges->company->name ?? 'COMPANY NAME' }}
                </div>

                <div class="back-divider"></div>

                <div class="back-info">

                    @if(!empty($badges->company->address))
                    <div class="back-info-item">
                        <i class="fa fa-map-marker"></i>
                        <span>{{ $badges->company->address }}</span>
                    </div>
                    @endif

                    @if(!empty($badges->company->postal_address))
                    <div class="back-info-item">
                        <i class="fa fa-envelope-o"></i>
                        <span>{{ $badges->company->postal_address }}</span>
                    </div>
                    @endif

                    @if(!empty($badges->company->email))
                    <div class="back-info-item">
                        <i class="fa fa-at"></i>
                        <span>{{ $badges->company->email }}</span>
                    </div>
                    @endif

                    @if(!empty($badges->company->location))
                    <div class="back-info-item">
                        <i class="fa fa-globe"></i>
                        <span>{{ $badges->company->location }}</span>
                    </div>
                    @endif

                </div>

            </div>

            <div class="back-footer">Visitor Pass</div>

        </div>
    </div>

</div>

<script>
function printBadge() {
    window.print();
}
</script>

@endsection