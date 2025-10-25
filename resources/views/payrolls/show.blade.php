@extends('layouts.pr')
    
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('payrolls')}}">Payrolls</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="payslip">
                        <div class="col-md-12">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="bg-primary text-light text-center">
                                            <h4>PAYSLIP</h4>
                                            <span>Salary Month: {{date('M Y', strtotime($payroll->month)) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40px; vertical-align: middle;">
                                            @if(!is_null($company->logo_url))
                                            <figure>
                                                <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="150" style="border: 1px solid white;">
                                            </figure>
                                            @endif
                                        </td>
                                        <td>
                                            <h5 class="fw-bold">From:</h5>
                                            <strong style="font-size: 14px;">{{$company->name}} - {{$shop->name}}.</strong><br>
                                            <small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br> <small>{{$shop->postal_address}} {{$shop->physical_address}} <br>@if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b></small>
                                            <span>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></span>
                                        </td>
                                        <td>
                                            <h5 class="fw-bold">To:</h5>
                                            <strong>{{$employee->fname}} {{$employee->lname}}</strong><br> {{$employee->address}},<br> Email: {{$employee->email}}<br> Phone: {{$employee->phone}}<br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <table class="table table-hover table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Earnings</th>
                                        <th style="width: 100px; text-align: right;">Total </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Basic Salary</td>
                                        <td class="text-success text-end">{{ number_format($monthly, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td>House Rent Allowance (H.R.A.)</td>
                                        <td class="text-success text-end">{{number_format($hra, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td>Transport Allowance</td>
                                        <td class="text-success text-end">{{number_format($ta, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td>Communication Allowance</td>
                                        <td class="text-success text-end">{{number_format($com_allowance, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td>Bonus</td>
                                        <td class="text-success text-end">{{ number_format($payroll->bonuses, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Earnings</strong></td>
                                        <td class="text-success text-end"><strong>{{ number_format($gross_income, 2, '.', ',')}}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <table class="table table-hover table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Deductions</th>
                                        <th style="width: 100px; text-align: right;">Total </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>P.A.Y.E</td>
                                        <td class="text-danger text-end">{{ number_format($payevalue, 2, '.', ',') }}</td>
                                    </tr>
                                    @if(!is_null($sscheme))
                                    <tr>
                                        <td>{{$sscheme->name}} ({{$sscheme->percent_rate}} %):</td>
                                        <td class="text-danger text-end"><b>{{ number_format($ssf, 2, '.', ',') }}</b></td>
                                    </tr>
                                    @endif
                                    @if(!is_null($hisscheme))
                                    <tr>
                                        <td>{{$hisscheme->name}} ({{$hisscheme->percent_rate}} %):</td>
                                        <td class="text-danger text-end">{{number_format($mif, 2, '.', ',')}}</td>
                                    </tr>
                                    @endif
                                    @if(!is_null($ps_wcf))
                                    <tr>
                                        <td>{{$ps_wcf->name}} ({{$ps_wcf->percent_rate}} %):</td>
                                        <td class="text-danger text-end"><b>{{ number_format($wcf, 2, '.', ',') }}</b></td>
                                    </tr>
                                    @endif
                                    @if(!is_null($ps_heslb))
                                    <tr>
                                        <td>{{$ps_heslb->name}}</td>
                                        <td class="text-danger text-end">{{number_format($heslb, 2, '.', ',')}}</td>
                                    </tr>
                                    @endif
                                    @if(!is_null($emploan))
                                    <tr>
                                        <td>Employee Loan</td>
                                        <td class="text-danger text-end">{{number_format($emploan_amount, 2, '.', ',')}}</td>
                                    </tr>
                                    @endif
                                    @if($penalty > 0)
                                    <tr>
                                        <td>Employee Penalties</td>
                                        <td class="text-danger text-end">{{number_format($penalty, 2, '.', ',')}}</td>
                                    </tr>
                                    @endif
                                    
                                    <tr>
                                        <td><strong>Total Deductions</strong></td>
                                        <td class="text-danger text-end"><strong>{{ number_format($total_deduction+$emploan_amount, 2, '.', ',') }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Bank Details</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start">
                                            <div>
                                                Bank Name: <strong style="display: inline-block;">{{$employee->bank_name}}</strong>
                                            </div>
                                            <div>
                                                Account Number: <strong style="display: inline-block;">{{$employee->account_number}}</strong>
                                            </div>
                                            <div">
                                                Account Name: <strong style="display: inline-block;">{{$employee->account_name}}</strong>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="fs-6 text-success">Total Earnings:- <strong style="width: 140px; display: inline-block;">{{ number_format($gross_income, 2, '.', ',')}}</strong></div>
                                            <div class="fs-6 text-danger">Total Deductions:- <strong style="width: 140px; display: inline-block;">{{ number_format($total_deduction+$emploan_amount, 2, '.', ',') }}</strong></div>
                                            <div class="fs-5 mt-3">Net Pay Salary:- <strong style="width: 140px; display: inline-block;">{{ number_format($gross_income-($total_deduction+$emploan_amount), 2, '.', ',') }}</strong></div>
                                        </td>
                                    </tr>
                                    @if(!is_null($payroll->note))
                                    <tr>
                                        <td>
                                            <h6 class="fw-bold">Note:</h6>
                                            <span class="text-muted">{{$payroll->note}}.</span>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="text-end">
                        <button type="button" onclick="javascript:savePdf()" class="btn btn-sm btn-secondary"><i class="fa fa-file-pdf-o"></i> Print Preview</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript">
        function savePdf() {
            const element = document.getElementById("payslip");
            var filename = "<?php echo 'Payslip_'.sprintf('%04d', $payroll->id).'_'.$payroll->created_at; ?>";
            var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                window.open(pdf.output('bloburl'), '_blank');
            });
        }
    </script>