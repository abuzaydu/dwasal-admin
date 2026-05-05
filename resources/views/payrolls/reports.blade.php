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
            <div class="col-md-6 col-sm-12">
                <form class="row g-3 report-form pt-2" action="{{ url('payroll-reports') }}" method="POST">
                    @csrf
                    <div class="col-md-12">
                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">
                        <!-- Date and time range -->
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="bx bx-calendar"></i></span>
                                <i class="bx bx-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="payslip">
                        <div class="col-md-12">
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="7" style="text-align: center;">
                                        <span style="font-size: 18px">{{$company->name}}</span>
                                    </td>
                                </tr>
                            </table>
                            <table style="width: 100%;">
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="background: #0459c6; padding-left: 15px;  border-radius: 0px; text-align: center; color: #fff; font-size: 20px; text-transform: uppercase;">
                                            <span> {{ $page }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" style="text-align: center; text-transform: uppercase; color: blue;">
                                            <span>{{ $duration }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="table mb-0 table-striped">
                                <thead>
                                    <tr><th>#</th>
                                        <th>Month</th>
                                        <th>Gross Income</th>
                                        <th>P.A.Y.E</th>
                                        <th>NSSF</th>
                                        <th>NHIF</th>
                                        <th>WCF</th>
                                        <th>HESLB</th>
                                        <th>Emp. Loan</th>
                                        <th>Emp Recovery</th>
                                        <th>Net Pay</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payrolls as $key => $payroll)
                                    <tr>
                                        <td>{{ $key+1}}</td>
                                        <td style="padding: 5px;">{{ date('F Y', strtotime($payroll['month'])) }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['gross_income'], 2, '.', ',') }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['paye'], 2, '.', ',') }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['ssf'], 2, '.', ',') }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['mif'], 2, '.', ',') }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['wcf'], 2, '.', ',') }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['heslb'], 2, '.', ',') }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['nst_loan'], 2, '.', ',') }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['recovery'], 2, '.', ',') }}</td>
                                        <td style="padding: 5px;">{{ number_format($payroll['net_pay'], 2, '.', ',') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th><b>Total</b></th>
                                        <th><b>{{ number_format($total_gross_income, 2, '.', ',') }}</b></th>
                                        <th><b>{{ number_format($total_paye, 2, '.', ',') }}</b></th>
                                        <th><b>{{ number_format($total_ssf, 2, '.', ',') }}</b></th>
                                        <th><b>{{ number_format($total_mif, 2, '.', ',') }}</b></th>
                                        <th><b>{{ number_format($total_wcf, 2, '.', ',') }}</b></th>
                                        <th><b>{{ number_format($total_heslb, 2, '.', ',') }}</b></th>
                                        <th><b>{{ number_format($total_emp_loan, 2, '.', ',') }}</b></th>
                                        <th><b>{{ number_format($total_other_deductions, 2, '.', ',') }}</b></th>
                                        <th><b>{{ number_format($total_net_pay, 2, '.', ',') }}</b></th>
                                    </tr>
                                </tfoot>
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
            var filename = "<?php echo 'Payrolls Summary '.$duration; ?>";
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