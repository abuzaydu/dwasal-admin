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

    <div class="row clearfix">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="print_invoice" id="payslip">
                        <div class="row g-1 p-2">  
                            <div class="col-md-12">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td colspan="3" style="background: #1B1464; padding-left: 15px;  border-radius: 0px; text-align: center; color: #fff; font-size: 20px; text-transform: uppercase;" class="text-center">
                                            <h4>PAYSLIP</h4></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-center" style="border-bottom: 2px solid #1B1464;">
                                                <span style="font-size: 16px">For the Month of: <b>{{date('M Y', strtotime($payroll->month)) }}</b></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 mx-auto P-3">
                                <table class="table table-bordered mb-0" style="border: 1.5px solid gray;">
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="text-align: center;">
                                                @if(!is_null($company->logo_url))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="100" style="border: 1px solid white;">
                                                </figure>
                                                @endif
                                                <strong style="font-size: 14px;">{{$company->name}}</strong> - {{$shop->name}}
                                            </th>
                                            <th colspan="2" style="text-align: center;">
                                                <h5>Payslip Confidential</h5>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="border: 1px solid gray;">Employee PF No:</td>
                                            <td style="border: 1px solid gray;"><b>{{ $employee->emp_id}}</b></td>
                                            <td style="border: 1px solid gray;">Birth Date</td>
                                            <td style="border: 1px solid gray;">@if(!empty($employee->birth_date)){{ date('d-M-Y', strtotime($employee->birth_date)) }}@endif</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Employee Name:</td>
                                            <td style="border: 1px solid gray;"><b>{{$employee->fname}} {{$employee->mname}} {{$employee->lname}}</b></td>
                                            <td style="border: 1px solid gray;">TIN #: </td>
                                            <td style="border: 1px solid gray;"><b>{{$employee->tin}}</b></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Job Tittle:</td>
                                            <td style="border: 1px solid gray;"><b>{{$position}}</b></td>
                                            <td style="border: 1px solid gray;">Department</td>
                                            <td style="border: 1px solid gray;"><b>{{$departments}}</b></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Pension type:</td>
                                            <td style="border: 1px solid gray;"><b> {{$employee->ssf}}</b></td>
                                            <td style="border: 1px solid gray;">Pension NO</td>
                                            <td style="border: 1px solid gray;"><b>{{$employee->ssf_no}}</b></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Date Joined</td>
                                            <td style="border: 1px solid gray;"><b>@if(!empty($employee->start_date)){{ date('d-M-Y', strtotime($employee->start_date)) }}@endif</b></td>
                                            <td style="border: 1px solid gray;">Retirement Date:</td>
                                            <td style="border: 1px solid gray;"><b>@if(!empty($employee->end_date)){{ date('d-M-Y', strtotime($employee->end_date)) }}@endif</b></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Employment Contract Type:</td>
                                            <td style="border: 1px solid gray;"><b>{{$employee->contract_type}}</b></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Bank Name:</td>
                                            <td style="border: 1px solid gray;"><b>{{$employee->bank_name}}</b></td>
                                            <td style="border: 1px solid gray;">Bank account Number:</td>
                                            <td style="border: 1px solid gray;"><b>{{$employee->account_number}}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 P-3">
                                <table class="table table-hover table-bordered mb-0" style="border: 1.5px solid gray;">
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="text-align: center;"><span style="text-transform: uppercase; font-size: 16px; font-weight: bold;">Allowances</span></th>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <th style="width: 100px; text-align: right;">Amount ({{$defcurr}}) </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="border: 1px solid gray;">Basic Salary</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-success text-end">{{ number_format($basic_salary, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Transport Allowance</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-success text-end">{{number_format($trans_allowance, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Communication Allowance</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-success text-end">{{number_format($com_allowance, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">House Allowance</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-success text-end">{{number_format($house_allowance, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Overtime</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-success text-end">{{number_format($overtime, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Bonuses</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-success text-end">{{number_format($bonuses, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1.5px solid gray;"><strong>Total Earnings</strong></td>
                                            <td style="border: 1.5px solid gray; text-align: right;" class="text-success text-end"><strong>{{ number_format($gross_pay, 2, '.', ',')}}</strong></td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">Statutory payments:</th>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">P.A.Y.E Tax</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-info text-end"><b>{{ number_format($payevalue, 2, '.', ',') }}</b></td>
                                        </tr>
                                        @if(!is_null($sscheme))
                                        <tr>
                                            <td style="border: 1px solid gray;">{{$sscheme->name}} ({{$sscheme->percent_rate+0}} %). Employee Contribution</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-info text-end"><b>{{ number_format($ssf, 2, '.', ',') }}</b></td>
                                        </tr>
                                        @endif
                                        <tr class="blank_row">
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1.5px solid gray;"><strong>Net Salary ({{$defcurr}})</strong></td>
                                            <td style="border: 1.5px solid gray; text-align: right;" class="text-success text-end"><strong>{{ number_format($net_pay, 2, '.', ',')}}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6 p-3">
                                <table class="table table-hover table-bordered mb-0" style="border: 1.5px solid gray;">
                                    <thead>
                                        <tr>
                                            <th colspan="2" style="text-align: center;"><span style="text-align: center; text-transform: uppercase; font-size: 16px; font-weight: bold;">Other Deductions</span></th>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <th style="width: 100px; text-align: right;">Amount ({{$defcurr}}) </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="border: 1px solid gray;">HESLB Loan Recovery</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-danger text-end">{{number_format($heslb, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Salary advance</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-danger text-end">{{number_format($advance_salary, 2, '.', ',')}}</td>
                                        </tr>
                                        @if(!is_null($emploan))
                                        <tr>
                                            <td style="border: 1px solid gray;">Employee Loan</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-danger text-end">{{number_format($emploan_amount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td style="border: 1px solid gray;">Attendance Deduction</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-danger text-end">{{number_format($attendance_deduction, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1px solid gray;">Recovery</td>
                                            <td style="border: 1px solid gray; text-align: right;" class="text-danger text-end">{{number_format($recovery, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="border: 1.5px solid gray;"><strong>Total Deductions</strong></td>
                                            <td style="border: 1.5px solid gray; text-align: right;" class="text-danger text-end"><strong>{{ number_format($total_deductions, 2, '.', ',') }}</strong></td>
                                        </tr>

                                        <tr class="blank_row">
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr class="blank_row">
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr class="blank_row">
                                            <td colspan="2"></td>
                                        </tr>

                                        <tr>
                                            <td style="border: 1.5px solid gray;"><strong>Amount Payable ({{$defcurr}})</strong></td>
                                            <td style="border: 1.5px solid gray; text-align: right;" class="text-success text-end"><strong>{{ number_format($adjusted_net_pay, 2, '.', ',') }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered mb-0">
                                    <tbody>
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
                    </div>
                    <hr/>
                    <div class="text-end">
                        <a href="#" onclick="javascript:exportToExcel()" class="btn btn-secondary btn-sm"><i class="fa fa-download"></i> Export to Excel</a>
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
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