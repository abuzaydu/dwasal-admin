@extends('layouts.acc')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
               <form class="row g-3 dashform" action="{{ url('petty-cash-report') }}" method="POST">
                    @csrf
                    <div class="col-md-5"></div>
                    
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class=" col-md-7">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row g-3">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="payslip">
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center; background:  #2874a6;">
                                            <h6 class="mb-0 text-uppercase" style="color: #fff;">Petty Cash Report<br><small>{{$duration}}</small></h6>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 border-bottom pb-0">
                            <table class="items mt-0">
                                <tr>
                                    <td style="width: 40%; text-align: right; padding-left: 20px;"><span style="font-size: 16px;">Branch/Shop Name: </span> </td>
                                    <td style="width: 60%;">
                                        <strong style="font-size: 16px;">{{$shop->name}}</strong><br>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <table class="items mt-0">
                                <thead>
                                    <tr>
                                        <th>Petty Cash Requests</th>
                                        <th style="width: 100px; text-align: right;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totalpetty = 0; ?>
                                    @foreach($pettycash as $petty)
                                    <?php $totalpetty += $petty->amount; ?>
                                    <tr>
                                        <td>{{ date('d M, Y H:i:s', strtotime($petty->request_date)) }}<br><small>{{$petty->description}}</small></td>
                                        <td class="text-success text-end">{{ number_format($petty->amount, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td class="text-success"><strong>Total</strong></td>
                                        <td class="text-success text-end"><strong>{{ number_format($totalpetty, 2, '.', ',')}}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <table class="items mt-0">
                                <thead>
                                    <tr>
                                        <th>Petty Cash Usage</th>
                                        <th style="text-align: right;">Amount </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_usage = 0; $petyy_to_branch = 0; $exp_payments = 0; ?>

                                    @if($branch_pettycashes->count() > 0)
                                    <tr>
                                        <td colspan="2">Transfered to Branches</td>
                                    </tr>
                                    @foreach($branch_pettycashes as $petty)
                                    <?php $petyy_to_branch += $petty->amount; ?>
                                    <tr>
                                        <td>{{$petty->name}}</td>
                                        <td style="text-align: right;">{{ number_format($petty->amount, 2, '.', ',') }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    <tr style="border-top: 1px solid red; border-bottom: 1px solid gray;">
                                        <td colspan="2">Expense Payments</td>
                                    </tr>
                                    @foreach($expenses as $key => $exp)
                                    <?php $exp_payments += $exp->amount; ?>
                                    <tr>
                                        <td>{{$exp->expense_type}}</td>
                                        <td style="text-align: right;">{{ number_format($exp->amount, 2, '.', ',') }}</td>
                                    </tr>
                                    @endforeach
                                    @if($plc_payments > 0)
                                    <tr>
                                        <td>Direct Labour Cost Payments</td>
                                        <td style="text-align: right;">{{ number_format($plc_payments, 2, '.', ',') }}</td>
                                    </tr>
                                    @endif
                                    <?php $total_usage = $petyy_to_branch+$exp_payments+$plc_payments; ?>
                                    <tr>
                                        <td class="text-danger"><strong>Total</strong></td>
                                        <td class="text-danger text-end"><strong>{{ number_format($total_usage, 2, '.', ',')}}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <table class="items mt-0">
                                <tbody>
                                    <tr>
                                        <td class="text-end fs-6 text-success">Total Petty Cash Received</td>
                                        <td class="text-end fs-6 text-success">
                                            <strong>{{ number_format($totalpetty, 2, '.', ',')}}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fs-6 text-danger">Total Usage</td>
                                        <td class="text-end fs-6 text-danger">
                                            <strong>{{ number_format($total_usage+$plc_payments, 2, '.', ',') }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fs-5 mt-3">Petty Cash Balance</td> 
                                        <td class="text-end fs-5 mt-3">
                                            <strong style="width: 140px; display: inline-block;">{{ number_format($totalpetty-($total_usage+$plc_payments), 2, '.', ',') }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="text-end">
                        <button type="button" onclick="javascript:savePdf()" class="btn btn-sm btn-secondary"><i class="fa fa-file-pdf-o"></i> Download PDF / <i class="fa fa-print"></i> Print</button>
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
          var filename = "<?php echo 'Petty Cash Report_'.$duration; ?>";
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