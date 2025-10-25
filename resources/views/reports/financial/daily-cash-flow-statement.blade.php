@extends('layouts.gen')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('daily-cash-flow-statement')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-7">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <a href="#" onclick="javascript:saveDCSPdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="print_invoice" id="inv-content">
                        <div class="col-md-12">
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="9" style="text-align: center;">
                                        <span style="font-size: 18px">{{$company->name}} <br>(<b>{{$shop->name}}</b>)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" style="background: <?php echo $settings->invoice_color; ?>; padding-left: 15px;  border-radius: 0px; text-align: center; color: <?php echo $settings->invoice_title_color; ?>; font-size: 20px; text-transform: uppercase;">
                                        <span> {{ $title }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <div class="row g-1 pt-4">
                                <div class="col-md-6">
                                    <h6 class="mt-0">Inflows</h6>
                                    <table class="items mt-0 mb-3" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                            <?php $totalBal_bf = ($cashBal_bf+$mobiBal_bf+$bankBal_bf); ?>
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #e0e0e0;"><b>Balance Before {{ date('d M Y', strtotime($start_date))}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Cash</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($cashBal_bf, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Bank</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($bankBal_bf, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Mobile Money</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($mobiBal_bf, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid black; border-top: 2px solid black;"><b>Total Opening Balance </b></td>
                                                <td style="text-align: right; border-bottom: 1px solid black; border-top: 2px solid black;"><b>{{number_format($totalBal_bf, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #e0e0e0;">Invoice Payments</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.cash_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cash_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.bank_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($bank_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.mob_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($mob_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>Total Payments</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($total_pay, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <?php $total_cash_in = ($cash_in+$mobi_in+$bank_in); ?>
                                            @if($total_cash_in > 0)
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #e0e0e0;">Cash in From Other Sources</td>
                                            </tr>
                                            @if($cash_in > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.cash_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cash_in, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($bank_in > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.bank_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($bank_in, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($mobi_in > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.mob_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($mobi_in, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>Total Cash Ins </b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($total_cash_in, 2, '.', ',')}}</b></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td colspan="2" style="padding-top: 30px;"></td>
                                            </tr>
                                            <tr style="background: #daebff;">
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0; text-transform: uppercase;"><b>Total Cash Inflow  </b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($total_pay+$total_cash_in, 2, '.', ',')}}</b></td>
                                            </tr>
                                            @if($cash_to_bank > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.cash_to_bank')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cash_to_bank, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($cash_to_mobile > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.cash_to_mobile')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cash_to_mobile, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($mobile_to_cash > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.mobile_to_cash')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($mobile_to_cash, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($mobile_to_bank > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.mobile_to_bank')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($mobile_to_bank, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($bank_to_cash > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.bank_to_cash')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($bank_to_cash, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($bank_to_mobile > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.bank_to_mobile')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($bank_to_mobile, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mt-0">Outflows</h6>
                                    <table class="items mt-0 mb-3" border="0" cellspacing="0" cellpadding="0">
                                        <tbody>
                                            @if($purchase_payments > 0)
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #e0e0e0;">{{trans('navmenu.purchase_payments')}}</td>
                                            </tr>
                                            @if($cpur_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Cash</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cpur_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($bpur_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Bank</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($bpur_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($mpur_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Mobile Money</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($mpur_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.total')}} {{trans('navmenu.purchase_payments')}}</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($purchase_payments, 2, '.', ',')}}</b></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #e0e0e0;">Operating Expenses Payments</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Cash</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cpaid_exp+$pettycpaid_exp, 2, '.', ',')}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Bank</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($bpaid_exp, 2, '.', ',')}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Mobile Money</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($mpaid_exp, 2, '.', ',')}}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.total')}} {{trans('navmenu.paid_expenses')}}</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($paid_expenses, 2, '.', ',')}}</b></td>
                                            </tr>
                                            @if($plc_payments > 0)
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #e0e0e0;">Lobour Cost Payments</td>
                                            </tr>
                                            @if($plc_cash_pay+$plc_petty_cash_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Cash</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($plc_cash_pay+$plc_petty_cash_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($plc_bank_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Bank</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($plc_bank_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($plc_mobi_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Mobile Money</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($plc_mobi_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>Total Labour Cost Payments</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($plc_payments, 2, '.', ',')}}</b></td>
                                            </tr>
                                            @endif
                                            @if($moh_cost_payments > 0)
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #e0e0e0;">MOH Cost Payments</td>
                                            </tr>
                                            @if($moh_cash_pay+$moh_petty_cash_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Cash</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($moh_cash_pay+$moh_petty_cash_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($moh_bank_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Bank</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($moh_bank_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($moh_mobi_pay > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Mobile Money</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($moh_mobi_pay, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>Total MOH Cost Payments</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($moh_cost_payments, 2, '.', ',')}}</b></td>
                                            </tr>
                                            @endif

                                            <?php $total_cash_out = ($cash_out+$mobi_out+$bank_out); ?>
                                            @if($total_cash_out > 0)
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #e0e0e0;">Cash out to Other reasons</td>
                                            </tr>
                                            @if($cash_out > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.cash_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cash_out, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($bank_out > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.bank_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($bank_out, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            @if($mobi_out > 0)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.mob_payments')}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($mobi_out, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>Total </b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($total_cash_out, 2, '.', ',')}}</b></td>
                                            </tr>
                                            @endif
                                            <?php $total_cash_out_flow = ($purchase_payments+$paid_expenses+$plc_payments+$moh_cost_payments+$total_cash_out); ?>
                                            <tr>
                                                <td colspan="2" style="padding-top: 30px;"></td>
                                            </tr>
                                            <tr style="background: #ffe2da;">
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0; text-transform: uppercase;"><b>Total Cash Outflow  </b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($total_cash_out_flow, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 30px;"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="text-align: left; background: #26e105;"><b>{{trans('navmenu.closing_balance')}}  at End of {{ date('d M Y', strtotime($end_date) )}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Cash</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($dcf_cashBal, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Bank</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($dcf_bankBal, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Mobile Money</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($dcf_mobiBal, 2, '.', ',')}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid black; border-top: 2px solid black;"><b>Total Closing Balance </b></td>
                                                <td style="text-align: right; border-bottom: 1px solid black; border-top: 2px solid black;"><b>{{number_format(($dcf_cashBal+$dcf_bankBal+$dcf_mobiBal), 2, '.', ',')}}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
    function printDivDCStmt(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;
        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.daily_cashflow_stmt').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function saveDCSPdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.daily_cashflow_stmt').'_'.$duration; ?>";
        var opt = {
            margin:       0.5,
            filename:     filename+'.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
            // Added after option to add spacing after page break
            pagebreak: { avoid: "tr", mode: "css"},
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).toPdf().save();
    }
</script>