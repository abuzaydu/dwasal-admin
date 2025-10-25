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
                <form class="dashform row g-3" id="filter-form" action="{{ url('f-company-cf-stmt') }}" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <select name="shop_id" class="form-select form-select-sm mb-1" id="shop-id">
                            <option value="">All Stores</option>
                            @foreach($cshops as $mshop)
                            @if(!is_null($currshop) && $currshop->id == $mshop->id)
                            <option value="{{$mshop->id}}" selected>{{$mshop->name}}</option>
                            @else
                            <option value="{{$mshop->id}}">{{$mshop->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-8 mb-1">
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
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="row g-1 print_invoice" id="print-stmt" style="border: 1px solid #e0e0e0;">                                
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="text-align: left; padding-left: 15px;">
                                                @if(!is_null($company->logo_url))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="200">
                                                </figure>
                                                @endif
                                            </td>
                                            <td>
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">
                                                            <span style="font-size: 18px">{{$company->name}}</span><br>
                                                            <small>{{$company->slogan}}</small><br>
                                                            <?php $shop = $company->shops()->first(); ?> 
                                                            <p>
                                                                {{$shop->postal_address}} {{$shop->physical_address}}<br> @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}} {{$shop->country}} <br>  Tel: <b>{{$shop->tel}}</b> | <b>{{$shop->mobile}}</b> WhatsApp : <b>{{$shop->whatsapp}}</b><br> Email: <b>{{$shop->email}}</b> Website: <b>{{$shop->website}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="background: #0459c6; padding-left: 15px;  border-radius: 0px; text-align: center;">
                                                    <h6 class="mb-0 text-uppercase" style="color: #fff;"> {{ $title }}</h6>
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
                                            <?php $texpense += $expcat['amount']; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$expcat['category']}}</div></td>
                                                <td class="text-danger text-end">{{ number_format($expcat['amount'], 2, '.', ',')}}</td>
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
                                            <?php $totalinv_in += $cashin['amount']; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$cashin['source']}}</div></td>
                                                <td class="text-success text-end">{{ number_format($cashin['amount'], 2, '.', ',')}}</td>
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
                                            <?php $totalinv_out += $cashout['amount']; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$cashout['reason']}}</div></td>
                                                <td class="text-red text-end">{{ number_format($cashout['amount'], 2, '.', ',')}}</td>
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
                                            <?php $totalfin += $cashin['amount']; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$cashin['source']}}</div></td>
                                                <td class="text-success text-end">{{ number_format($cashin['amount'], 2, '.', ',')}}</td>
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
                                            <?php $totalfout += $cashout['amount']; ?>
                                            <tr>
                                                <td><div style="padding-left: 65px;">{{$cashout['reason']}}</div></td>
                                                <td class="text-success text-end">{{ number_format($cashout['amount'], 2, '.', ',')}}</td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
<script type="text/javascript">
    $('#shop-id').on('change', function(){
        $('#filter-form').submit();
    });
</script>
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
        document.title = "<?php echo trans('navmenu.cash_flow_stmt').'_'.$reporttime; ?>";
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
            // Added after option to add spacing after page break
            pagebreak: { avoid: "tr", mode: "css"},
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).toPdf().save();
        // New Promise-based usage:
        // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            // window.open(pdf.output('bloburl'), '_blank');
        // });
    }

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.cash_flow_stmt').'_'.$reporttime; ?>";
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
        // New Promise-based usage:
        // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            // window.open(pdf.output('bloburl'), '_blank');
        // });
    }
</script>