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
                    <div id="payslip" class="print_invoice p-2">
                        <div class="row g-1">                                
                            <div class="col-md-12" style="overflow-x: auto;">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-striped" style="width: 100%; white-space: nowrap; border: 1px solid black;">
                                        <thead>
                                            <tr class="blank_row">
                                                <td colspan="19"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td colspan="8" style="text-align: left;">
                                                    @if(!is_null($company->logo_url))
                                                    <figure>
                                                        <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="150">
                                                    </figure>
                                                    @endif
                                                </td>
                                                <td colspan="15" style="text-align: left;">
                                                    <p>
                                                        <strong style="font-size: 28px !important;">{{$company->name}}</strong><br>
                                                        @if(!is_null($company->slogan))<small>{{$company->slogan}}</small><br>@endif
                                                        {{$company->address}} <br> 
                                                        {{$company->postal_code}} <br> 
                                                        Tel: <b>{{$company->mobile}}</b><br> 
                                                        Email: <b>{{$company->email}}</b><br>
                                                        TIN: <b>{{$company->tin}}</b><br> {{$company->city}} {{$company->country}} <br> 
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr class="blank_row">
                                                <td colspan="25"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="background: #1B1464;"></td>
                                                <td colspan="7" style="background: #1B1464; padding-left: 15px;  border-radius: 0px; text-align: center; color: #fff; font-size: 20px; text-transform: capitalize;">
                                                    <span> {{ $title }} - {{date('F Y', strtotime($mpayroll->month)) }}</span>
                                                </td>
                                                <td colspan="14" style="background: #1B1464;"></td>
                                            </tr>
                                            <tr>
                                                <th style="border: 1px solid gray; text-align: center;">S/N</th>
                                                <th style="border: 1px solid gray; ">Employee Name</th>
                                                <th style="border: 1px solid gray; text-align: center;">PF Number</th>
                                                <th style="border: 1px solid gray; text-align: center;">NSSF Number</th>
                                                <th style="border: 1px solid gray; text-align: center;">TIN #</th>
                                                <th style="border: 1px solid gray; text-align: center;">Basic Salary</th>
                                                <th style="border: 1px solid gray; text-align: center;">Transport<br> Allowance</th>
                                                <th style="border: 1px solid gray; text-align: center;">House<br> Allowance</th>
                                                <th style="border: 1px solid gray; text-align: center;">Communication<br> Allowance</th>
                                                <th style="border: 1px solid gray; text-align: center;">Overtime</th>
                                                <th style="border: 1px solid gray; text-align: center;">Bonuses</th>
                                                <th style="border: 1px solid gray; text-align: center;">Gross Other<br> Earnigs</th>
                                                <th style="border: 1px solid gray; text-align: center;">Total Gross Pay</th>
                                                <th style="border: 1px solid gray; text-align: center;">10% NSSF <br> Emp. Contribution</th>
                                                <th style="border: 1px solid gray; text-align: center;">Taxable Income</th>
                                                <th style="border: 1px solid gray; text-align: center;">P.A.Y.E</th>
                                                <th style="border: 1px solid gray; text-align: center;">Net Pay</th>
                                                <th style="border: 1px solid gray; text-align: center;">HESLB<br> Deduction</th>
                                                <th style="border: 1px solid gray; text-align: center;">Advance<br> Salary </th>
                                                <th style="border: 1px solid gray; text-align: center;">Employee<br> Loan </th>
                                                <th style="border: 1px solid gray; text-align: center;">Attendance<br> Deductions </th>
                                                <th style="border: 1px solid gray; text-align: center;">Recovery </th>
                                                <th style="border: 1px solid gray; text-align: center;">Adjusted<br> Net Pay</th>
                                                <th style="border: 1px solid gray;">Bank Acc</th>
                                                <th style="border: 1px solid gray;">Bank Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $numemployees = count($payrolls); ?>
                                            @foreach($payrolls as $key => $payroll)
                                            <tr>
                                                <td style="border: 1px solid gray; text-align: center;">{{ $key+1}}</td>
                                                <td style="border: 1px solid gray; padding: 5px;">{{ $payroll['name'] }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: center;">{{ $payroll['emp_id'] }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: center;">{{ $payroll['ssf_no'] }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: center;">{{ $payroll['tin'] }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['basic_salary'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['transport_allowance'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['house_allowance'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['com_allowance'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['overtime'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['bonuses'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['gross_other_earnings'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['gross_pay'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['ssf'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['taxable_income'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['paye'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['net_pay'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['heslb'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['advance_salary'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['emploan_amount'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['attendance_deduction'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['recovery'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; padding: 5px; text-align: right;">{{ number_format($payroll['adjusted_net_pay'], 2, '.', ',') }}</td>
                                                <td style="border: 1px solid gray; text-align: center; mso-number-format:'\@';">{{ ' '.$payroll['bank_acc'].' ' }}</td>
                                                <td style="border: 1px solid gray; text-align: center;">{{ $payroll['bank_name'] }}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="5" style="border: 2px solid #1B1464; text-align: right;"><b>Total</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_basic_salary, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_transport, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_house_allowance, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_com_allowance, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_overtime, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_bonuses, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_gross_other_earnings, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_gross_pay, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_ssf, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_taxable_income, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_paye, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_net_pay, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_heslb, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_advance_salary, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_emp_loan, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_attendance_deduction, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_recovery, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464; text-align: right;"><b>{{ number_format($total_adjusted_net_pay, 2, '.', ',') }}</b></td>
                                                <td style="border: 2px solid #1B1464;"></td>
                                                <td style="border: 2px solid #1B1464;"></td>
                                            </tr>
                                                    
                                            <tr class="blank_row">
                                                <td colspan="25" style="border: 1px solid gray; padding: 45px;"></td>
                                            </tr>

                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="6" style="border-bottom: 2px solid #1B1464;"><strong>Report Summary</strong></td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5" style="border-bottom: 2px solid #1B1464;">Cost Item</td>
                                                <td style="border-bottom: 2px solid #1B1464; text-align: right;">Amount</td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <?php 
                                                $sdl_amt = 0;
                                                if ($numemployees >= 10) {
                                                    $sdl_amt = (3.5/100)*$total_gross_pay;
                                                }

                                                $wcf = (0.5/100)*$total_gross_pay;
                                                $totalssf = $total_ssf*2; 
                                            ?>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5" style="border: 1px solid gray; color: red;">3.5% SDL( 10 or more Empoyees)</td>
                                                <td style="border: 1px solid gray; color: red; text-align: right;">{{ number_format($sdl_amt,  2, '.', ',') }}</td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5" style="border: 1px solid gray; color: red;">0.5% WCF</td>
                                                <td style="border: 1px solid gray; color: red; text-align: right;">{{ number_format($wcf,  2, '.', ',') }}</td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5" style="border: 1px solid gray;">10% NSSF contribution-Employee</td>
                                                <td style="border: 1px solid gray; text-align: right;">{{ number_format($total_ssf, 2, '.', ',') }}</td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5" style="border: 1px solid gray; color: red;">10% NSSF contribution-Employer</td>
                                                <td style="border: 1px solid gray; color: red; text-align: right;">{{ number_format($total_ssf, 2, '.', ',') }}</td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5" style="border: 1px solid gray;">P.A.Y.E</td>
                                                <td style="border: 1px solid gray; text-align: right;">{{ number_format($total_paye, 2, '.', ',') }}</td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5" style="border: 1px solid gray;">Net pay to employees</td>
                                                <td style="border: 1px solid gray; text-align: right;">{{ number_format($total_net_pay, 2, '.', ',') }}</td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5" style="border: 1px solid gray;"><b>Total Employees related cost(ERC)-Faharimotors</b></td>
                                                <td style="border: 1px solid gray; text-align: right;"><b>{{ number_format(($sdl_amt+$wcf+$totalssf+$total_paye+$total_net_pay),  2, '.', ',') }}</b></td>
                                                <td colspan="15"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="11"></td>
                                                <td style="border: 1px solid gray;">NSSF</td>
                                                <td style="border: 1px solid gray; text-align: right;">{{ number_format($totalssf, 2, '.', ',') }}</td>
                                                <td colspan="12"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="11"></td>
                                                <td style="border: 1px solid gray;">P.A.Y.E</td>
                                                <td style="border: 1px solid gray; text-align: right;">{{ number_format($total_paye, 2, '.', ',') }}</td>
                                                <td colspan="12"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="11"></td>
                                                <td style="border: 1px solid gray; color: red;">WCF</td>
                                                <td style="border: 1px solid gray; color: red; text-align: right;">{{ number_format($wcf,  2, '.', ',') }}</td>
                                                <td colspan="12"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="11"></td>
                                                <td style="border: 1px solid gray; color: red;">SDL</td>
                                                <td style="border: 1px solid gray; color: red; text-align: right;">{{ number_format($sdl_amt,  2, '.', ',') }}</td>
                                                <td colspan="12"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="11"></td>
                                                <td style="border: 1px solid gray;"><b>Total Gvt Payments</b></td>
                                                <td style="border: 1px solid gray; text-align: right;"><b>{{ number_format(($sdl_amt+$wcf+$totalssf+$total_paye),  2, '.', ',') }}</b></td>
                                                <td colspan="12"></td>
                                            </tr>
                                            <tr class="blank_row">
                                                <td colspan="25" style="padding: 45px;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="11"></td>
                                                <td style="border: 1px solid gray;"><b>Net Salary</b></td>
                                                <td style="border: 1px solid gray; text-align: right;"><b>{{ number_format($total_net_pay, 2, '.', ',') }}</b></td>
                                                <td colspan="12"></td>
                                            </tr>

                                            <tr class="blank_row">
                                                <td colspan="25" style="padding: 45px;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td colspan="8">
                                                    <p>
                                                        <strong>Prepared By</strong><br>
                                                        <strong>.......................................</strong><br>
                                                        <strong>{{ $user->first_name }} {{ $user->last_name }}</strong><br>
                                                        <strong>{{ $user->roles[0]['display_name'] }}</strong><br>
                                                        {{ $company->name }}
                                                    </p>
                                                </td>
                                                <td colspan="14"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="text-end">
                        @if(!$mpayroll->is_added_to_expense)
                        <a href="{{ url('add-to-expenses/'.encrypt($mpayroll->id)) }}" class="btn btn-primary btn-sm">Add to Expenses</a>
                        @endif
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
            var filename = "<?php echo 'Payrolls For '.$mpayroll->month; ?>";
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