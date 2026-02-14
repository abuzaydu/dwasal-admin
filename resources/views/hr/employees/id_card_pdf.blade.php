<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* PDF Global Reset */
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            margin: 0;
            padding: 0;
            background: #fff;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* Universal Card Container */
        .id-card {
            width: 350px;
            height: 500px;
            border-radius: 15px;
            border: 1px solid #d4a017;
            position: relative;
            overflow: hidden;
            margin: 20px auto;
            background: #fff;
        }
        
        /* Shared Header Style */
        .header { 
            text-align: center; 
            padding-top: 30px; 
            height: 60px;
        }
        
        .company-header {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .company-icon {
            background: #d4a017;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: inline-block;
            text-align: center;
            line-height: 25px;
            margin-right: 8px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .company-name { 
            color: #d4a017; 
            font-weight: bold; 
            font-size: 20px; 
            text-transform: uppercase;
            letter-spacing: 1px;
            vertical-align: middle;
        }

        /* Front Specific */
        .photo-area { 
            text-align: center; 
            margin: 20px 0; 
        }
        
        .photo { 
            width: 140px; 
            height: 140px; 
            border-radius: 50%; 
            border: 4px solid #d4a017; 
        }
        
        .placeholder {
            width: 140px; 
            height: 140px; 
            border-radius: 50%;
            border: 4px solid #d4a017; 
            background: #f0f0f0;
            display: inline-block;
            line-height: 140px;
            color: #999; 
            font-size: 60px;
        }
        
        .details { 
            text-align: center; 
            padding: 0 10px; 
        }
        
        .name { 
            font-size: 22px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #222;
            margin-bottom: 5px;
        }
        
        .pos { 
            color: #d4a017; 
            font-weight: bold; 
            text-transform: uppercase;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .info-box { 
            display: inline-block;
            text-align: left;
            font-size: 12px; 
            color: #444;
            line-height: 1.6;
        }

        /* Back Specific Alignment */
        .back-tagline {
            text-align: center;
            font-size: 10px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: -5px;
        }

        .contact-section {
            margin-top: 40px;
            padding: 0 35px;
        }

        .contact-item {
            font-size: 12px;
            margin-bottom: 15px;
            color: #333;
            border-left: 3px solid #d4a017;
            padding-left: 10px;
        }

        .contact-label {
            font-weight: bold;
            display: block;
            color: #d4a017;
            font-size: 10px;
            text-transform: uppercase;
        }

        /* Shared Footer Bars */
        .footer-yellow {
            position: absolute; 
            bottom: 0; 
            width: 100%; 
            height: 90px;
            background: #d4a017;
        }
        
        .qr-code {
            position: absolute; 
            bottom: 12px; 
            right: 15px;
            background: white; 
            padding: 5px; 
            border-radius: 5px;
        }

        .website-bar {
            position: absolute;
            bottom: 25px;
            width: 100%;
            text-align: center;
            color: white;
            font-weight: bold;
            font-size: 13px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="id-card">
        <div class="header">
            <div class="company-header">
                <span class="company-icon">&check;</span>
                <span class="company-name">{{ $employee->company->name ?? 'COMPANY' }}</span>
            </div>
        </div>

        <div class="photo-area">
            @php
                $hasPhoto = false;
                if($user_photo){
                    $path = public_path('storage/' . $user_photo);
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        $hasPhoto = true;
                    }
                }
            @endphp
            
            @if($hasPhoto)
                <img src="{{ $base64 }}" class="photo">
            @else
                <div class="placeholder">?</div>
            @endif
        </div>

        <div class="details">
            <div class="name">{{$employee->fname}} {{$employee->lname}}</div>
            <div class="pos">{{$employee->position->name ?? 'Staff Member'}}</div>
            
            <div class="info-box">
                <strong>ID NUMBER:</strong> {{$employee->emp_id ?? 'N/A'}}<br>
                <strong>VALID THROUGH:</strong> {{ $employee->end_date ?? 'PERMANENT' }}
            </div>
        </div>

<div style="position: absolute; bottom: 100px; right: 20px; background: white; padding: 5px; border-radius: 5px; z-index: 100; line-height: 0;">
    @php
        $fullName = $employee->fname . ' ' . $employee->lname;
        $posName = $employee->position->name ?? 'Staff Member';
        $empID = $employee->emp_id ?? 'N/A';
        $qrContent = "FULL NAME: $fullName\nPOSITION: $posName\nID: $empID";
        
        // Generate as SVG and base64 encode it for PDF compatibility
        $qrcode = base64_encode(QrCode::format('svg')->size(70)->margin(1)->generate($qrContent));
    @endphp
    <img src="data:image/svg+xml;base64,{{ $qrcode }}" width="70" height="70">
</div>

<div class="footer-yellow" style="position: absolute; bottom: 0; width: 100%; height: 90px; background: #d4a017; z-index: 1;">
     <div class="website-bar">
                {{ $employee->company->website ?? 'www.company.com' }}
     </div>
</div>

    </div>

    <div class="page-break"></div>

    <div class="id-card">
        <div class="header">
            <div class="company-header">
                <span class="company-icon">&check;</span>
                <span class="company-name">{{ $employee->company->name ?? 'COMPANY' }}</span>
            </div>
            <div class="back-tagline">Official Identity Card</div>
        </div>

        <div class="contact-section">
            <div class="contact-item">
                <span class="contact-label">Phone</span>
                {{ $employee->company->mobile ?? $employee->company->phone ?? 'Contact Office' }}
            </div>
            
            <div class="contact-item">
                <span class="contact-label">Address</span>
                {{ $employee->company->address ?? 'Company Headquarters' }}
            </div>
            
            <div class="contact-item">
                <span class="contact-label">Email</span>
                {{ $employee->company->email ?? 'info@company.com' }}
            </div>

            <div style="margin-top: 30px; font-size: 10px; color: #888; text-align: center; font-style: italic;">
                If found, please return to the address above.
            </div>
        </div>

        <div class="footer-yellow">
            <div class="website-bar">
                {{ $employee->company->website ?? 'www.company.com' }}
            </div>
        </div>
    </div>

</body>
</html>