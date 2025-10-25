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
                <form class="dashform row g-3" action="{{url('closing-business-value')}}" method="POST">
                    @csrf
                    <div class="col-md-5">
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                    </div>
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
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="inv-content" class="print_invoice">
                        <div class="col-md-12" style="text-align: center; text-transform: uppercase; color: blue">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" height="50" alt="">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <span>{{trans('navmenu.monthly_value')}}
                            <p>{{$reporttime}}</p></span>
                        </div>
                        <div style="border-top: 2px solid #82B1FF; padding: 5px;" class="col-md-12 invoice-content">
                            <table class="items mt-0" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.assets')}} / {{trans('navmenu.credits')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.business_value')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.paid_expenses')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discounts_made')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bvalues as $value)
                                    <tr>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;">{{date('M d, Y', strtotime($value->date))}}</td>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.total_cash')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.stock_value')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.cust_debts')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.supp_debts')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.other_loan')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;"><b>{{trans('navmenu.total')}}</b></th>
                                                    </tr>
                                                    <tr>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->total_cash, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->stock_value, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->cust_debts, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->supp_debts, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->other_debts, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;"><b style="color: blue;">{{number_format(($value->total_cash+$value->cust_debts+$value->supp_debts+$value->other_debts), 2, '.', ',')}}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table>
                                                <tbody>
                                                    <tr>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.supp_credits')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.cust_credits')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.unpaid_expenses')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.other_credits')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;"><b>{{trans('navmenu.total_credits')}}</b></th>
                                                    </tr>
                                                    <tr>    
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->supp_credits, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->cust_credits, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->unpaid_expenses, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->other_credits, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;"><b style="color: red;">{{number_format(($value->supp_credits+$value->cust_credits+$value->unpaid_expenses+$value->other_credits), 2, '.', ',')}}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;"><b style="color: green;">{{number_format(($value->total_cash+$value->cust_debts+$value->supp_debts+$value->other_debts)-($value->supp_credits+$value->cust_credits+$value->unpaid_expenses+$value->other_credits), 2, '.', ',')}}</b></td>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;">{{number_format($value->paid_expenses, 2, '.', ',')}}</td>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;">{{number_format($value->discounts_made, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
    function printDivBS(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;
        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.monthly_value').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.monthly_value').'_'.$reporttime; ?>";
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