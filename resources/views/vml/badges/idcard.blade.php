@extends('layouts.vml')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee ID Card</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 20px;
        }

        .id-card {
            width: 100%;
            max-width: 350px;
            height: 500px;
            border-radius: 15px;
            overflow: hidden;
            margin: 0 auto 20px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background: #fff;
        }

        .id-card .main-content {
            padding-bottom: 170px;
            height: 100%;
        }

        .company-logo img {
            max-width: 65px;
            max-height: 65px;
            object-fit: contain;
        }

        .placeholder-logo {
            width: 65px;
            height: 65px;
            background: #f5f5f5;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .employee-photo {
            width: 115px;
            height: 115px;
            border-radius: 50%;
            object-fit: cover;
        }

        .photo-wrapper {
            display: inline-block;
            padding: 3px;
            border-radius: 50%;
            border: 3px solid #d4a017;
            box-shadow: 0 3px 10px rgba(212,160,23,0.3);
        }

        .footer-qr {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 170px;
            background: linear-gradient(180deg, #d4a017 0%, #b8860b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-qr .qr-box {
            background: #fff;
            padding: 8px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .back-card {
            color: white;
            text-align: center;
            background: linear-gradient(135deg, #d4a017 0%, #b8860b 100%);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .back-card .company-logo img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .back-card .placeholder-logo {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-card .company-info {
            margin-top: 15px;
            margin-bottom: 15px;
            border-bottom: 2px solid rgba(255,255,255,0.5);
            padding-bottom: 15px;
        }

        .back-card .contact-info {
            width: 100%;
            text-align: left;
            font-size: 0.85rem;
        }

        .back-card .contact-info p {
            margin-bottom: 5px;
        }

        .back-card .security-info {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.7);
            font-size: 0.75rem;
            width: 100%;
        }

        @media (max-width: 768px) {
            .id-card {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

@php
    $hasPhoto = $user_photo && file_exists(public_path('storage/' . $user_photo));
    $fullName = $employee->fname . ' ' . $employee->lname;
    $positionName = $position ? $position->name : 'DESIGNATION';
    $empID = $employee->emp_id ?? 'EMPLOYEE_ID';
    $companyID = $employee->company_id ?? 'COMPANY_ID';
    $employee_id = $employee->id;

    $qrData = json_encode([
        'emp_id' => $empID,
        'company_id' => $companyID,
        'id' => $employee_id
    ]);

    $qrContent = \App\Helpers\QrCodeEncryption::encrypt($qrData);
@endphp

<div class="card mb-3">
    <div class="card-body">
        <h6 class="card-title mb-4">Employee Identity Card</h6>

        <div id="employee-id-card-wrapper" class="row">

            <!-- Front Side -->
            <div class="col-md-6 mb-4">
                <h6 class="text-muted mb-3 text-center">Front Side</h6>
                <div class="id-card">

                    <div class="main-content">
                        <div class="text-center pt-3 company-logo">
                            @if($employee->company && $employee->company->logo_url)
                                <img src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}" alt="Company Logo">
                            @else
                                <div class="placeholder-logo"><i class="fa fa-building fa-2x text-muted"></i></div>
                            @endif
                        </div>

                        <div class="text-center px-3 py-2">
                            <h5 class="fw-bold mb-0" style="font-size:0.9rem; border-bottom: 2px solid #f0f0f0; padding-bottom:8px; display:inline-block;">
                                {{ $employee->company->name ?? 'COMPANY NAME' }}
                            </h5>
                        </div>

                        <div class="text-center my-3">
                            <div class="photo-wrapper">
                                @if($hasPhoto)
                                    <img src="{{ asset('storage/'.$user_photo) }}" alt="Employee Photo" class="employee-photo">
                                @else
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 115px; height: 115px;">
                                        <i class="fa fa-user fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="text-center px-3">
                            <h3 class="fw-bold mb-1 text-uppercase" style="font-size:1.15rem; letter-spacing:0.5px; line-height:1.3;">
                                {{ $fullName }}
                            </h3>
                        </div>

                        <div class="text-center px-3">
                            <p class="text-uppercase fw-bold mb-2" style="color:#d4a017; font-size:0.85rem; letter-spacing:0.5px;">
                                {{ $positionName }}
                            </p>
                        </div>
                    </div>

                    <div class="footer-qr">
                        <div class="qr-box">
                            {!! QrCode::size(140)->margin(0)->generate($qrContent) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back Side -->
            <div class="col-md-6 mb-4">
                <h6 class="text-muted mb-3 text-center">Back Side</h6>
                <div class="id-card">
                    <div class="back-card">
                        <div class="company-logo mb-3">
                            @if($employee->company && $employee->company->logo_url)
                                <img src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}" alt="Company Logo">
                            @else
                                <div class="placeholder-logo"><i class="fa fa-building fa-3x"></i></div>
                            @endif
                        </div>

                        <div class="company-info">
                            <h4 class="fw-bold mb-1">{{ $employee->company->name ?? 'COMPANY NAME' }}</h4>
                            <p class="mb-0" style="font-size:0.9rem;">{{ $employee->company->industry ?? 'Industry' }}</p>
                        </div>

                        <div class="contact-info">
                            <p><i class="fa fa-map-marker me-2"></i>{{ $employee->company->address ?? 'Address Not Available' }}</p>
                            <p><i class="fa fa-phone me-2"></i>{{ $employee->company->mobile ?? 'Phone Not Available' }}</p>
                            <p><i class="fa fa-envelope me-2"></i>{{ $employee->company->email ?? 'Email Not Available' }}</p>
                            @if($employee->company && $employee->company->website)
                                <p><i class="fa fa-globe me-2"></i>{{ $employee->company->website }}</p>
                            @endif
                        </div>

                        <div class="security-info">
                            <p class="m-0">Issue Date: {{ date('d/m/Y', strtotime($employee->created_at)) }}</p>
                            <p class="m-0">Authorized by Company</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-4">
            <button id="download-id-card" class="btn btn-success btn-sm"><i class="fa fa-file-pdf"></i> Download PDF Card</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('download-id-card').addEventListener('click', () => {
        const element = document.getElementById('employee-id-card-wrapper');
        const opt = {
            margin: [10, 10, 10, 10],
            filename: 'Employee_ID_Card.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
    });
</script>

</body>
</html>
@endsection