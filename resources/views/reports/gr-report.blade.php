@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-12 col-md-12 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('detailed-daily-profit-loss')}}" method="POST">
                    @csrf
                    <!-- /.form group -->
                    <div class="col-md-2">
                    @if($settings->is_service_per_device)
                        <select name="device_id" class="form-select form-select-sm mb-3" onchange="this.form.submit()">
                            <option value="0">{{trans('navmenu.select_device')}}</option>
                            @if(!is_null($devices))
                            @foreach($devices as $dev)
                            <option value="{{$dev->id}}">{{$dev->device_number}}</option>
                            @endforeach                           
                            @endif
                        </select>
                    @endif
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-5">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm float-end" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
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
                    <div id="inv-content" class="print_invoice">
                        <div class="row">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" height="50" alt="">
                                </figure>
                                @endif
                                <h5>{{$shop->name}}</h5>
                                @if(!is_null($device))
                                <span> {{trans('navmenu.gr_report')}} - {{$device->device_number}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                                @else
                                <span>{{trans('navmenu.gr_report')}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                                @endif
                            </div>
                            <?php $total_serv_sales = 0;  $tqty = 0; $total_exc = 0; $total_vat = 0; $total_cost = 0; $total_profit = 0; $total_revenue = 0; $trqty = 0; $total_r_exc = 0; $total_r_vat = 0; $total_r_cost = 0; $total_r_profit = 0; $total_r_revenue = 0; ?>
                            @if($shop->business_type_id != 3)
                            <div class="col-xs-12 invoice-content" style="border-top: 2px solid #82B1FF;">
                                <p class="lead" style="text-transform: uppercase; color: #33691e; font-weight: 200;">{{trans('navmenu.sales')}}:</p>
                                <table class="items mt-0" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.product_name')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.qty')}}</th>
                                            
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.unit_price')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.subtotal')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.vat')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total')}}</th>
                                            @if($shop->business_type_id != 1)
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total_cost')}}</th>
                                            @endif
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sales as $index => $sale)
                                        <?php
                                            $tqty += $sale->quantity;
                                            $total_exc += ($sale->price-$sale->total_discount);
                                            $total_vat += $sale->tax_amount;
                                            $total_cost += $sale->buying_price;
                                            $revenue = ($sale->price-$sale->total_discount)+$sale->tax_amount;
                                            $profit = $revenue-$sale->buying_price;
                                            $total_revenue += $revenue;
                                            $total_profit += $profit;
                                        ?>
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$sale->name}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$sale->quantity+0}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($sale->retail_price-$sale->discount), 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($sale->price-$sale->total_discount), 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->tax_amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($revenue, 2, '.', ',')}}</td>
                                            @if($shop->business_type_id != 1)
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->unit_cost, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->buying_price, 2, '.', ',')}}</td>
                                            @endif
                                            
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($profit, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><strong>{{trans('navmenu.total')}} ({{$defcurr->code}})</strong></td>
                                            <td style="text-align: center;"><strong>{{$tqty}}</strong></td>
                                            <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_exc, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_vat, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_exc+$total_vat, 2, '.', ',')}}/=</strong></td>
                                            @if($shop->business_type_id != 1)
                                            <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_cost, 2, '.', ',')}}/=</strong></td>
                                            @endif
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_profit, 2, '.', ',')}}/=</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                @if(!($returns->count() < 1))
                                <p class="lead" style="text-transform: uppercase; color: green;">{{trans('navmenu.sales_returns')}}:</p>
                                <table class="items mt-0" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.product_name')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.unit_price')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.subtotal')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.vat')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total')}}</th>
                                            @if($shop->business_type_id != 1)
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.unit_cost')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total_cost')}}</th>
                                            @endif
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($returns as $index => $return)
                                        <?php
                                            $trqty += $return->quantity;
                                            $total_r_exc += ($return->price-$return->total_discount);
                                            $total_r_vat += $return->tax_amount;
                                            $total_r_cost += $return->buying_price;
                                            $return_revenue = ($return->price-$return->total_discount)+$return->tax_amount;
                                            $return_profit = $return_revenue-$return->buying_price;
                                            $total_r_revenue += $return_revenue;
                                            $total_r_profit += $return_profit;
                                        ?>
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$return->name}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$return->quantity+0}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return->retail_price-$return->discount, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($return->price-$return->total_discount), 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return->tax_amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return_revenue, 2, '.', ',')}}</td>

                                            @if($shop->business_type_id != 1)
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return->unit_cost, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return->buying_price, 2, '.', ',')}}</td>
                                            @endif
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return_profit, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><strong>{{trans('navmenu.total')}} ({{$defcurr->code}})</strong></td>
                                            <td style="text-align: center;"><strong>{{$trqty}}</strong></td>
                                            <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_r_exc, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_r_vat, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_r_exc+$total_r_vat, 2, '.', ',')}}/=</strong></td>
                                            @if($shop->business_type_id != 1)
                                            <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_r_cost, 2, '.', ',')}}/=</strong></td>
                                            @endif
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_r_profit, 2, '.', ',')}}/=</strong></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <p class="lead" style="text-transform: uppercase; color: green;">{{trans('navmenu.turn_over')}}:</p>
                                <table class="items mt-0" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <thead>
                                        <tr>                                                
                                            <th></th>
                                            <th>{{trans('navmenu.sales')}}</th>
                                            <th>{{trans('navmenu.cost_of_sales')}}</th>
                                            <!-- <th>Discount</th> -->
                                            <th>{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><strong>{{trans('navmenu.total')}} ({{$defcurr->code}})</strong></td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format(($total_revenue-$total_r_revenue), 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_cost-$total_r_cost, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format(($total_revenue-$total_r_revenue)-($total_cost-$total_r_cost), 2, '.', ',')}}/=</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                @endif
                            </div>
                            <!-- /.col -->
                            @endif

                            @if($shop->business_type_id == 3 || $shop->business_type_id == 4)
                            <div class="col-xs-12 invoice-content" style="border-top: 2px solid #82B1FF; padding-top: 10px;">
                                <table class="items mt-0" border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <thead style="background:#BDBDBD;">
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.service')}}e</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.price')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">VAT</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">Net AMount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total_serv_exc = 0; $total_serv_vat = 0; ?>
                                        @foreach($servsales as $index => $sale)
                                        <?php
                                            $total_serv_exc += $sale->total-$sale->total_discount;
                                            $total_serv_vat += $sale->tax_amount;
                                        ?>
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$sale->name}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$sale->repeatition}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->price-$sale->discount, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->total-$sale->total_discount, 2, '.', ',')}}</td><td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->tax_amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($sale->total-$sale->total_discount)+$sale->tax_amount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <?php $total_serv_sales = ($total_serv_exc+$total_serv_vat); ?>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><strong>{{trans('navmenu.total')}} ({{$defcurr->code}})</strong></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_serv_exc, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_serv_vat, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_serv_sales, 2, '.', ',')}}/=</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            @endif
                        </div>
                        <!-- /.row -->

                        <div class="row"  style="border-top: 2px solid #82B1FF; padding: 25px;">
                            <div class="col-xs-12">
                                <p class="lead" style="text-transform: uppercase; color: #f44336;">{{trans('navmenu.operating_expense')}}:</p>
                                <div class="invoice-content">
                                    <table class="items mt-0" border="0" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <tr>
                                                <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.expense_type')}}</th>
                                                <th style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.amount')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $totalexpenses = 0; ?>
                                            @foreach($expenses as $expense)
                                            <?php $totalexpenses += $expense->amount; ?>
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$expense->expense_type}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($expense->amount, 2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                            <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.total_expenses')}} ({{$defcurr->code}})</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($totalexpenses, 2, '.', ',')}}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-12">
                                <p class="lead" style="text-transform: uppercase; color: blue;">{{trans('navmenu.evaluation')}}</p>
                                <?php $gross_profit = ($total_revenue-$total_r_revenue)-($total_cost-$total_r_cost)+$total_serv_sales; ?>
                                <div class="invoice-content">
                                    <table class="items mt-0" border="0" cellspacing="0" cel>
                                        <tr>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.gross_profit')}} ({{$defcurr->code}}):</td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($gross_profit, 2, '.', ',')}}/=</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.expenses')}} ({{$defcurr->code}}):</td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($totalexpenses, 2, '.', ',')}}/=</td>
                                        </tr>
                                        <tr style="border-bottom: 2px solid gray;">
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.profit')}} ({{$defcurr->code}}):</b>:</td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($gross_profit-$totalexpenses, 2, '.', ',')}}</b>/=</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var duration = "<?php echo $duration; ?>";
            var shop_name = "<?php echo $shop->name; ?>";

        });
    </script>
@endsection

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function printDiv(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;


            //File name for printed ducument
            document.title = "<?php echo trans('navmenu.gr_report').'_'.$duration; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo trans('navmenu.gr_report').'_'.$duration; ?>";
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