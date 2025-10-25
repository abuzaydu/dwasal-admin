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
                <form class="dashform row g-3" action="{{url('cash-flow-statement')}}" method="POST">
                    @csrf
                    <div class="col-md-3">
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-9 mb-3">
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

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body" style="padding: 35px;">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_0">Cash Flow Statement</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_1">Cash Flow By Payment Mode</a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="row g-1 print_invoice" id="print-stmt" style="border: 1px solid #e0e0e0;">
                                <div class="col-md-12 border-bottom">
                                    <table class="items mt-0">
                                        <tr>
                                            <td style="width: 40%; text-align: right; padding-left: 20px; padding-right: 20px;">
                                                @if(!is_null($company->logo_url))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="200">
                                                </figure>
                                                @endif
                                            </td>
                                            <td style="width: 60%;">
                                                <strong style="font-size: 22px;">{{$company->name}} <br>{{$shop->name}}</strong><br> <small>{{$shop->address}}, {{$shop->poaddress}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->phone}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-12">
                                    <table class="table table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <td colspan="3" style="text-align: center; background:  #2874a6;">
                                                    <h4 style="color: fff;">{{$title}}</h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                               
                                <div class="col-md-12 p-0">
                                    <table class="table table-hover table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-end">
                                                    <div class="fs-6 mt-0">
                                                        Cash To Date Ending<strong  style="width: 140px; display: inline-block;">{{ date('d M Y', strtotime($endingdate))}}</strong>
                                                    </div>
                                                    <div class="fs-6 mt-0">
                                                        Amount <strong  style="width: 140px; display: inline-block;">{{ number_format($cash_balance, 2, '.', ',') }}</strong>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class="items mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase">Operations</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3" style="font-size: 14px; background-color: #e0e0e0;">Cash Receipts From</td>
                                            </tr>
                                            <tr>
                                                <td><div style="padding-left: 65px;">Customers</div></td>
                                                <td class="text-success text-end">{{ number_format($invoice_payments, 2, '.', ',')}}</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td><div style="padding-left: 65px;">Other Operations</div></td>
                                                <td class="text-success text-end">{{ number_format($other_payments, 2, '.', ',')}}</td>
                                                <td class="text-success text-end">{{ number_format($invoice_payments+$other_payments, 2, '.', ',')}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="font-size: 14px; background-color: #e0e0e0;">Cash Paid For</td>
                                            </tr>
                                            @if($purchase_payments > 0)
                                            <tr>
                                                <td><div style="padding-left: 65px;">Purchase of Inventory</div></td>
                                                <td class="text-danger text-end">{{ number_format($purchase_payments, 2, '.', ',')}}</td>
                                                <td></td>
                                            </tr>
                                            @endif
                                            @if($rm_purchase_payments > 0)
                                            <tr>
                                                <td><div style="padding-left: 65px;">Purchase of Raw Materials</div></td>
                                                <td class="text-danger text-end">{{ number_format($rm_purchase_payments, 2, '.', ',')}}</td>
                                                <td></td>
                                            </tr>
                                            @endif
                                            @if($pm_purchase_payments > 0)
                                            <tr>
                                                <td><div style="padding-left: 65px;">Purchase of Packing Materials</div></td>
                                                <td class="text-danger text-end">{{ number_format($pm_purchase_payments, 2, '.', ',')}}</td>
                                                <td></td>
                                            </tr>
                                            @endif
                                            @if($dlc_payments > 0)
                                            <tr>
                                                <td><div style="padding-left: 65px;">Labour Costs Payments</div></td>
                                                <td class="text-danger text-end">{{ number_format($dlc_payments, 2, '.', ',')}}</td>
                                                <td></td>
                                            </tr>
                                            @endif
                                            @if($moh_cost_payments > 0)
                                            <tr>
                                                <td><div style="padding-left: 65px;">MOH Costs Payments</div></td>
                                                <td class="text-danger text-end">{{ number_format($moh_cost_payments, 2, '.', ',')}}</td>
                                                <td></td>
                                            </tr>
                                            @endif
                                            <?php $texpense = 0; ?>
                                            @foreach($expcategories as $key => $expcat)
                                            <?php $texpense += $expcat->amount; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$expcat->category}}</div></td>
                                                <td class="text-danger text-end">{{ number_format($expcat->amount, 2, '.', ',')}}</td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                                            @if($income_tax > 0)
                                            <tr>
                                                <td><div style="padding-left: 65px;">Income Tax Paid</div></td>
                                                <td class="text-danger text-end">{{ number_format($income_tax, 2, '.', ',')}}</td>
                                                <td class="text-danger text-end">{{ number_format($purchase_payments+$texpense+$income_tax, 2, '.', ',')}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td class="text-danger text-end">{{ number_format($rm_purchase_payments+$pm_purchase_payments+$dlc_payments+$moh_cost_payments+$purchase_payments+$texpense+$income_tax, 2, '.', ',')}}</td>
                                            </tr>
                                            <tr>
                                                <td style="border-top: 1px solid gray; border-bottom: 1px solid gray; padding-left: 65px;"><strong>Net Cash Flow From Operations</strong></td>
                                                <td colspan="2" class="text-end" style="border-top: 1px solid gray; border-bottom: 1px solid gray;"><strong>{{ number_format(($invoice_payments+$other_payments)-($rm_purchase_payments+$pm_purchase_payments+$dlc_payments+$moh_cost_payments+$purchase_payments+$texpense+$income_tax), 2, '.', ',')}}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class="items mb-0">
                                        <thead>
                                            <tr>
                                                <th>Investing Activities</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3" style="font-size: 14px;">Cash Receipt From</td>
                                            </tr>
                                            <?php $totalinv_in = 0; ?>
                                            @foreach($inv_cashins as $key => $cashin)
                                            <?php $totalinv_in += $cashin->amount; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$cashin->source}}</div></td>
                                                <td class="text-success text-end">{{ number_format($cashin->amount, 2, '.', ',')}}</td>
                                                @if($loop->last)
                                                <td colspan="2" class="text-end">{{ number_format($totalinv_in, 2, '.', ',')}}</td>
                                                @else
                                                <td></td>
                                                @endif
                                           </tr>
                                           @endforeach
                                            <tr>
                                                <td colspan="2" style="font-size: 14px;">Cash Paid For</td>
                                            </tr>
                                            <?php $totalinv_out = 0 ?>
                                            @foreach($inv_cashouts as $key => $cashout)
                                            <?php $totalinv_out += $cashout->amount; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$cashout->reason}}</div></td>
                                                <td class="text-red text-end">{{ number_format($cashout->amount, 2, '.', ',')}}</td>
                                                @if($loop->last)
                                                <td class="text-end">{{ number_format($totalinv_out, 2, '.', ',')}}</td>
                                                @else
                                                <td></td>
                                                @endif
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td style="padding-left: 65px; border-top: 1px solid gray; border-bottom: 1px solid gray;"><strong>Net Cash Flow From Investing Activities</strong></td>
                                                <td colspan="2" class="text-end" style="border-top: 1px solid gray; border-bottom: 1px solid gray;"><strong>{{ number_format($totalinv_in-$totalinv_out, 2, '.', ',')}}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class="items mb-0">
                                        <thead>
                                            <tr>
                                                <th>Financing Activities</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3" style="font-size: 14px;">Cash Receipt From</td>
                                            </tr>
                                            <?php $totalfin = 0; ?>
                                            @foreach($fin_cashins as $key => $cashin)
                                            <?php $totalfin += $cashin->amount; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$cashin->source}}</div></td>
                                                <td class="text-success text-end">{{ number_format($cashin->amount, 2, '.', ',')}}</td>
                                                @if($loop->last)
                                                <td class="text-end">{{ number_format($totalfin, 2, '.', ',')}}</td>
                                                @else
                                                <td></td>
                                                @endif
                                           </tr>
                                           @endforeach
                                            <tr>
                                                <td colspan="2" style="font-size: 14px;">Cash Paid For</td>
                                            </tr>
                                            <?php $totalfout = 0; ?>
                                            @foreach($fin_cashouts as $key => $cashout)
                                            <?php $totalfout += $cashout->amount; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$cashout->reason}}</div></td>
                                                <td class="text-success text-end">{{ number_format($cashout->amount, 2, '.', ',')}}</td>
                                                @if($loop->last)
                                                <td class="text-end">{{ number_format($totalfout, 2, '.', ',')}}</td>
                                                @else
                                                <td></td>
                                                @endif
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td style="padding-left: 65px; border-top: 1px solid gray; border-bottom: 1px solid gray;"><strong>Net Cash Flow From Financing Activities</strong></td>
                                                <td colspan="2" class="text-end" style="border-top: 1px solid gray; border-bottom: 1px solid gray;"><strong>{{ number_format($totalfin-$totalfout, 2, '.', ',')}}</strong></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <?php $net_increase = ($invoice_payments+$other_payments)-($rm_purchase_payments+$pm_purchase_payments+$dlc_payments+$moh_cost_payments+$purchase_payments+$texpense+$income_tax)+($totalinv_in-$totalinv_out)+($totalfin-$totalfout); ?>
                                            <tr>
                                                <th style="color: blue;"><strong>Net Increase In Cash</strong></th>
                                                <th colspan="2" class="text-end" style="color: blue;"><strong>{{ number_format($net_increase, 2, '.', ',') }}</strong></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="col-md-12 p-0">
                                    <table class="items mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-end" style="background-color: #e0e0e0;">
                                                    <div class="fs-5">Cash at End of {{ date('d M Y', strtotime($end_date) )}}:- <strong style="width: 200px; display: inline-block;">{{ number_format($cash_balance+$net_increase, 2, '.', ',') }}</strong></div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-12 pt-4">
                                <a href="#" onclick="javascript:saveStmtPdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_1" role="tabpanel">
                            <div id="inv-content" class="print_invoice">
                                <div class="col-md-12" style="text-align: center; text-transform: uppercase; color: blue">
                                    @if(!is_null($shop->logo_location))
                                    <figure>
                                        <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" height="50" alt="">
                                    </figure>
                                    @endif
                                    <h5>{{$shop->name}}</h5>
                                    <span>{{trans('navmenu.cash_flow_stmt')}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                                </div>

                                <div class="col-md-12 text-center" style="border-top: 2px solid #82B1FF;">
                                    <p class="p-2" style="text-transform: uppercase; color: blue; font-weight: bold;">{{trans('navmenu.cash_inflow')}}:</p>
                                    <div class="table-responsive">
                                        <table class="items mt-0">
                                            <thead>
                                                <th>{{trans('navmenu.account')}}</th>
                                                <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                            </thead>
                                            <tbody>
                                                @foreach($cashins as $key => $cin)
                                                <tr>
                                                    <td>
                                                      @if($cin['account'] == 'Cash')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cin['account']}}
                                                        @else
                                                        {{trans('navmenu.cash')}}
                                                      @endif
                                                      @elseif($cin['account'] == 'Mobile Money')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cin['account']}}
                                                        @else
                                                          {{trans('navmenu.mobilemoney')}}
                                                        @endif
                                                      @elseif($cin['account'] == 'Bank')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cin['account']}}
                                                        @else
                                                          {{trans('navmenu.bank')}}
                                                        @endif                           
                                                      @endif
                                                    </td>
                                                    <td style="text-align: right;">{{number_format($cin['amount'], 2, '.', ',')}}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td><strong>{{trans('navmenu.total')}}</strong></td>
                                                    <td style="text-align: right;"><strong>{{number_format($total_cashin, 2, '.', ',')}}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center">
                                    <p class="p-2" style="text-transform: uppercase; color: red; font-weight: bold;">{{trans('navmenu.cash_outflow')}}:</p>
                                    <div class="table-responsive">
                                        <table class="items mt-0">
                                            <thead>
                                                <th>{{trans('navmenu.account')}}</th>
                                                <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                            </thead>
                                            <tbody>
                                                @foreach($cashouts as $key => $cout)
                                                <tr>
                                                    <td>
                                                      @if($cout['account'] == 'Cash')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cout['account']}}
                                                        @else
                                                        {{trans('navmenu.cash')}}
                                                      @endif
                                                      @elseif($cout['account'] == 'Mobile Money')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cout['account']}}
                                                        @else
                                                          {{trans('navmenu.mobilemoney')}}
                                                        @endif
                                                      @elseif($cout['account'] == 'Bank')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cout['account']}}
                                                        @else
                                                          {{trans('navmenu.bank')}}
                                                        @endif                           
                                                      @endif
                                                    </td>
                                                    <td style="text-align: right;">{{number_format($cout['amount'], 2, '.', ',')}}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td><strong>{{trans('navmenu.total')}}</strong></td>
                                                    <td style="text-align: right;"><strong>{{number_format($total_cashout, 2, '.', ',')}}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center">
                                    <p class="p-2" style="text-transform: uppercase; color: green; font-weight: bold;">{{trans('navmenu.account_balance')}}:</p>
                                    <div class="table-responsive">
                                        <table class="items mt-0">
                                            <thead>
                                                <th>{{trans('navmenu.account')}}</th>
                                                <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                            </thead>
                                            <tbody>
                                                @foreach($cashin_outs as $key => $cashbal)
                                                <tr>
                                                    <td>
                                                      @if($cashbal['account'] == 'Cash')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cashbal['account']}}
                                                        @else
                                                        {{trans('navmenu.cash')}}
                                                      @endif
                                                      @elseif($cashbal['account'] == 'Mobile Money')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cashbal['account']}}
                                                        @else
                                                          {{trans('navmenu.mobilemoney')}}
                                                        @endif
                                                      @elseif($cashbal['account'] == 'Bank')
                                                        @if(app()->getLocale() == 'en')
                                                          {{$cashbal['account']}}
                                                        @else
                                                          {{trans('navmenu.bank')}}
                                                        @endif                           
                                                      @endif
                                                    </td>
                                                    <td style="text-align: right;">{{number_format($cashbal['amount'], 2, '.', ',')}}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <td><strong>{{trans('navmenu.total')}}</strong></td>
                                                    <td style="text-align: right;"><strong>{{number_format($total_balance, 2, '.', ',')}}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pt-4">
                                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
    function printDiv(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;
        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.cash_flow_stmt').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function saveStmtPdf() {
        const element = document.getElementById("print-stmt");
        var filename = "<?php echo trans('navmenu.cash_flow_stmt').'_'.$reporttime; ?>";
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

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.cash_flow_stmt').'_'.$reporttime; ?>";
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