<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee ID Card PDF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }
        .id-card {
            width: 350px;
            height: 500px;
            border-radius: 15px;
            border: 1px solid #d4a017;
            overflow: hidden;
            margin: 20px auto;
            position: relative;
            background: #fff;
        }
        .header-logo {
            text-align: center;
            padding-top: 15px;
        }
        .header-logo img {
            max-width: 65px;
            max-height: 65px;
            object-fit: contain;
        }
        .company-name {
            text-align: center;
            font-weight: bold;
            font-size: 0.9rem;
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 5px;
            display: inline-block;
            margin: 10px 0;
        }
        .photo-wrapper {
            display: inline-block;
            padding: 3px;
            border-radius: 50%;
            border: 3px solid #d4a017;
            box-shadow: 0 3px 10px rgba(212,160,23,0.3);
            margin: 15px auto;
        }
        .photo-wrapper img {
            width: 115px;
            height: 115px;
            border-radius: 50%;
            object-fit: cover;
        }
        .placeholder {
            width: 115px;
            height: 115px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: #999;
        }
        .details {
            text-align: center;
            padding: 0 10px;
        }
        .name {
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        .pos {
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
            color: #d4a017;
            margin-bottom: 15px;
        }
        .footer-gold {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 170px;
            background: linear-gradient(180deg, #d4a017 0%, #b8860b 100%);
            border-radius: 0 0 15px 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-box {
            background: #fff;
            padding: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="card-title mb-4">Employee Identity Card</h6>

        <div class="row">
            <!-- Front Side -->
            <div class="col-md-6 mb-4">
                <h6 class="text-muted mb-3 text-center">Front Side</h6>

                <div id="id-card-front" class="position-relative bg-white border mx-auto" style="width: 350px; height: 500px; border-radius: 15px; overflow: hidden; font-family: 'Arial', sans-serif;">

                    <!-- Company Logo -->
                    <div class="header-logo">
                        @if($employee->company && $employee->company->logo_url)
                            <img src="{{ asset('storage/clogos/' . $employee->company->logo_url) }}" alt="Company Logo">
                        @else
                            <div style="width:65px;height:65px;background:#f5f5f5;border-radius:8px;display:flex;align-items:center;justify-content:center;border:2px solid #e0e0e0;">
                                <i class="fa fa-building fa-2x" style="color:#999;"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Company Name -->
                    <div class="company-name">{{ $employee->company->name ?? 'COMPANY NAME' }}</div>

                    <!-- Employee Photo -->
                    <div class="text-center my-3">
                        <div class="photo-wrapper">
                            @if($user_photo)
                                <img src="{{ asset('storage/' . $user_photo) }}" alt="Employee Photo">
                            @else
                                <div class="placeholder">
                                    <i class="fa fa-user"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Employee Name & Position -->
                    <div class="details">
                        <div class="name">{{ $employee->fname }} {{ $employee->lname }}</div>
                        <div class="pos">{{ $employee->position->name ?? 'DESIGNATION' }}</div>
                    </div>

                    <!-- Footer with QR Code -->
                    <div class="footer-gold">
                        <div class="qr-box">
                            @php
                                $empID = $employee->emp_id ?? 'EMPLOYEE_ID';
                                $companyID = $employee->company_id ?? 'COMPANY_ID';
                                $employee_id = $employee->id;

                                $data = json_encode([
                                    'emp_id' => $empID,
                                    'company_id' => $companyID,
                                    'id' => $employee_id
                                ]);

                                $qrContent = \App\Helpers\QrCodeEncryption::encrypt($data);
                            @endphp
                            {!! QrCode::size(140)->margin(0)->generate($qrContent) !!}
                        </div>
                    </div>

                </div>
            </div>

            <!-- Back Side Placeholder -->
            <div class="col-md-6 mb-4">
                <h6 class="text-muted mb-3 text-center">Back Side</h6>
                <div id="id-card-back" class="position-relative bg-white border mx-auto" style="width:350px; height:500px; border-radius:15px; overflow:hidden;">
                    <!-- Back Side Content like contact info can go here -->
                    <div class="text-center mt-4" style="font-size:0.8rem;">
                        <p><strong>Phone:</strong> {{ $employee->company->mobile ?? $employee->company->phone ?? 'Contact Office' }}</p>
                        <p><strong>Address:</strong> {{ $employee->company->address ?? 'Company Headquarters' }}</p>
                        <p><strong>Email:</strong> {{ $employee->company->email ?? 'info@company.com' }}</p>
                        <p style="font-style:italic;color:#777;">If found, please return to the address above.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>