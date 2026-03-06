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

        /*  Layout */
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

        /* Card size (CR80 standard) */
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

        /* FRONT */
        .badge-card.front {
            background: #ffffff;
        }

        .badge-top {
            height: 7mm;
            background: linear-gradient(135deg, #F7941D, #e07b10);
            flex-shrink: 0;
        }

        .badge-body {
            flex: 1;
            background: #ffffff;
            display: flex;
            flex-direction: row;
            align-items: center;
            padding: 2mm 3mm;
            gap: 2mm;
            overflow: hidden;
        }

        /* Left column */
        .badge-body-left {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.5mm;
        }

        .badge-logo {
            width: 12mm;
            height: 12mm;
            object-fit: contain;
            flex-shrink: 0;
        }

        .badge-logo-placeholder {
            width: 12mm;
            height: 12mm;
            background: #f5f5f5;
            border-radius: 2mm;
            border: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .badge-company {
            font-size: 7pt;
            font-weight: 700;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
            line-height: 1.25;
            word-break: break-word;
        }

        .badge-divider {
            width: 55%;
            height: 1px;
            background: #F7941D;
            opacity: 0.6;
        }

        .badge-number {
            font-size: 6.5pt;
            font-weight: 600;
            color: #555;
            letter-spacing: 0.8px;
            text-align: center;
        }

        /* Right column — QR */
        .badge-body-right {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1mm;
        }

        .badge-qr-img {
            width: 27mm;
            height: 27mm;
            object-fit: contain;
            display: block;
            border: 0.5mm solid #ececec;
            border-radius: 1mm;
            padding: 1mm;
            background: #fff;
        }

        .badge-qr-label {
            font-size: 4.5pt;
            color: #aaa;
            letter-spacing: 0.4px;
            text-align: center;
            text-transform: uppercase;
        }

        .badge-bottom {
            height: 6mm;
            background: linear-gradient(135deg, #F7941D, #e07b10);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-bottom span {
            color: white;
            font-size: 5pt;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* BACK  */
        .badge-card.back {
            background: linear-gradient(160deg, #F7941D 0%, #e07b10 60%, #c96a0a 100%);
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 4mm;
        }

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

        /* Print */
        @media print {
            body * { visibility: hidden; }
            #printableBadge,
            #printableBadge * { visibility: visible; }
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

    <!-- BADGE PREVIEW — FRONT and BACK -->
    <div class="badge-preview-container" id="printableBadge">

        <!-- FRONT -->
        <div class="badge-wrapper">
            <div class="badge-label">▸ Front Side</div>
            <div class="badge-card front">

                <div class="badge-top"></div>

                <div class="badge-body">

                    <div class="badge-body-left">

                        @if($badges->company && $badges->company->logo_url)
                            <img class="badge-logo"
                                src="{{ asset('storage/clogos/' . $badges->company->logo_url) }}"
                                alt="Logo">
                        @else
                            <div class="badge-logo-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="#bbb">
                                    <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
                                </svg>
                            </div>
                        @endif

                        <div class="badge-company">
                            {{ $badges->company->name ?? 'COMPANY NAME' }}
                        </div>

                        <div class="badge-divider"></div>

                        <div class="badge-number">
                            Badge No: {{ $badges->badge_number }}
                        </div>

                    </div>

                    <div class="badge-body-right">
                        <?php $badgeId = $badges->id; $badgeNo = $badges->badge_number; ?>
                        <img
                            class="badge-qr-img"
                            src="data:image/png;base64,{{ DNS2D::getBarcodePNG(encrypt($badgeId.'&'.$badgeNo), 'QRCODE', 3, 3) }}"
                            alt="QR Code"
                        />
                        <div class="badge-qr-label">Scan to verify</div>
                    </div>

                </div>

                <div class="badge-bottom">
                    <span>Authorized Badge</span>
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