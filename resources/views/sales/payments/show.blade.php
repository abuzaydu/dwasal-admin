@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="receipt">
                        <center id="top">
                            <div class="logo">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" style="width: 60px; height: 60px">
                                </figure>
                                @endif
                            </div>
                            <div class="info" style="text-align: center;"> 
                                <h5>{{$shop->name}}</h5>
                                <small style="font-size: 12px;">{{$shop->short_desc}}</small>
                            </div><!--End Info-->
                        </center><!--End InvoiceTop-->
                        
                        <div id="mid" style="text-align: center; font-size: 11px;">
                            <div class="info">
                                <p>
                                    {{$shop->postal_address}} {{$shop->physical_address}} {{$shop->street}}, {{$shop->district}},{{$shop->city}}<br>
                                    @if(!is_null($shop->email)){{trans('navmenu.email')}}   : {{$shop->email}}<br>@endif
                                    @if(!is_null($shop->mobile)){{trans('navmenu.mobile')}}   : {{$shop->mobile}}<br>@endif

                                    @if(!is_null($shop->tin))<span>{{trans('navmenu.tin')}}: <strong>{{$shop->tin}}</strong></span><br>@endif
                                    @if(!is_null($shop->vrn))<span>{{trans('navmenu.vrn')}}: <strong>{{$shop->vrn}}</strong></span><br>@endif
                                </p>
                            </div>
                            <h6 style="text-transform: uppercase;">{{trans('navmenu.receipt_no')}}: {{$payment->receipt_no}}</h6>
                            <div>
                                <span>{{trans('navmenu.date')}}: <strong>{{date('d-m-Y h:i:s A', strtotime($payment->created_at))}}</strong></span>
                            </div>
                            <div>
                                <span>{{trans('navmenu.customer')}}: <strong>{{$sale->name}}</strong></span><br>
                                @if(!is_null($sale->phone))<span>{{trans('navmenu.mobile')}}: <strong>{{$sale->phone}}</strong></span>@endif
                                @if(!is_null($sale->email))<span>{{trans('navmenu.mobile')}}: <strong>{{$sale->email}}</strong></span>@endif
                                @if(!is_null($sale->tin))<span>{{trans('navmenu.tin')}}: <strong>{{$sale->tin}}</strong></span><br>@endif
                                @if(!is_null($sale->vrn))<span>{{trans('navmenu.vrn')}}: <strong>{{$sale->vrn}}</strong></span><br>@endif
                            </div>
                        </div><!--End Invoice Mid-->
                        
                        <div id="bot">
                            <div id="table">
                                <table class="table" width="100%">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; font-size: 9px !important;">
                                                {{trans('navmenu.description')}}
                                            </th>
                                            <th style="text-align: right; font-size: 9px !important;">{{trans('navmenu.total')}} ({{$sale->currency}})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $key => $item)
                                        <?php
                                            $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                            $quantity_sold = $item->quantity_sold/$punit->qty_equal_to_basic;
                                            $retail_price = $item->retail_price*$punit->qty_equal_to_basic;
                                            $unit_discount = $item->discount*$punit->qty_equal_to_basic;

                                        ?>
                                        <tr>
                                            <td style="font-size: 9px !important;">{{$item->name}}<br>
                                                <small style="color: gray;">
                                                    @if($settings->show_discounts)
                                                    {{$quantity_sold + 0}} {{$punit->unit_name}} x {{number_format($retail_price*$sale->ex_rate, 2, '.', ',')}}
                                                    @else
                                                    {{$quantity_sold}} x {{number_format(($retail_price-$unit_discount)*$sale->ex_rate, 2, '.', ',')}}
                                                    @endif
                                                </small>
                                            </td>
                                            <td style="text-align: right; font-size: 9px !important;">
                                                @if($settings->show_discounts)
                                                {{number_format($item->price*$sale->ex_rate, 2, '.', ',')}}
                                                @else
                                                {{number_format(($item->price-$item->total_discount)*$sale->ex_rate, 2, '.', ',')}}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @foreach($servitems as $key => $item)
                                        <tr>
                                            <td style="font-size: 9px !important;">{{$item->name}}<br>
                                                <small>{{number_format($item->no_of_repeatition)}} x {{number_format($item->price*$sale->ex_rate, 2, '.', ',')}}</small>
                                            </td>
                                            <td style="text-align: right;">{{number_format($item->total*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        @if($settings->show_discounts)
                                        <tr style="border-top: 2px dashed #000; font-size: 9px !important;">
                                            <th style="text-align: right;">{{trans('navmenu.subtotal')}}</th>
                                            <td style="text-align: right;">{{number_format(($sale->sale_amount-$sale->tax_amount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                                
                                        <tr>
                                            <th style="text-align: right; font-size: 9px !important;">{{trans('navmenu.discount')}}</th>
                                            <td style="text-align: right; font-size: 9px !important;">{{number_format($sale->sale_discount*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @else
                                        <tr style="border-top: 2px dashed #000;">
                                            <th style="text-align: right; font-size: 9px !important;">{{trans('navmenu.subtotal')}}</th>
                                            <td style="text-align: right; font-size: 9px !important;">{{number_format(($sale->sale_amount-$sale->sale_discount-$sale->tax_amount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif    

                                        @if($settings->is_vat_registered && $sale->tax_amount > 0)
                                        <tr>
                                            <th style="text-align: right; font-size: 9px !important;">{{trans('navmenu.vat')}}</th>
                                            <td style="text-align: right; font-size: 9px !important;">{{number_format($sale->tax_amount*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                    
                                        <tr class="amount-total">
                                            <th style="text-align: right; font-size: 9px !important;">{{trans('navmenu.total')}}</th>
                                            <td style="text-align: right; font-size: 9px !important;">{{number_format(($sale->sale_amount-$sale->sale_discount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                    
                                        <tr data-hide-on-quote="true">
                                            <th style="text-align: right; font-size: 9px !important;">{{trans('navmenu.paid')}}</th>
                                            <td style="text-align: right; font-size: 9px !important;">{{number_format($sale->sale_amount_paid*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!--End Table-->
                            <div style="text-align: center; font-size: 12px;">
                                <span>{{trans('navmenu.pay_method')}}: <strong>
                                @if($payment->pay_mode == 'Cash')
                                    @if(app()->getLocale() == 'en')
                                    {{$payment->pay_mode}}
                                    @else
                                    {{trans('navmenu.cash')}}
                                    @endif
                                @elseif($payment->pay_mode == 'Mobile Money')
                                    @if(app()->getLocale() == 'en')
                                    {{$payment->pay_mode}}
                                    @else
                                    {{trans('navmenu.mobilemoney')}}
                                    @endif
                                @elseif($payment->pay_mode == 'Bank')
                                    @if(app()->getLocale() == 'en')
                                    {{$payment->pay_mode}}
                                    @else
                                    {{trans('navmenu.bank')}}
                                    @endif            
                                @endif</strong></span><br>
                                <span>{{trans('navmenu.issued_by')}}: <strong>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</strong></span>
                            </div>
                            <div id="legalcopy" style="text-align: center; font-size: 12px; border-bottom: 2px dashed #000;">
                                <p class="legal"><strong>{{trans('navmenu.welcome_again')}} </strong>
                                </p>
                            </div>
                        </div><!--End InvoiceBot-->
                    </div><!--End Invoice-->
                </div>
            </div>
        </div>
        <div class="col-md-10 mx-auto mb-5">
            <div class="row">
                <div class="col-md-12 p-2 text-center">
                    <a id="btnDownload" onclick="savePdf()" class="btn btn-success btn-sm">
                        <i class="fa fa-download"></i> DOWNLOAD
                    </a>
                    <a id="btnPrint" type="button" class="btn btn-primary btn-sm"><i class="fa fa-printer"></i>{{trans('navmenu.print')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
    <link rel="stylesheet" type="text/css" href="{{ asset('css/receipt.css') }}">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
    <script>    
        $(document).ready( function ()  {
            $('#btnPrint').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#receipt").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link rel="stylesheet" href="../side/assets/css/bootstrap.min.css"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo trans('navmenu.receipt_no').'_'.$payment->receipt_no.'_'.$payment->created_at ?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });

    </script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script type="text/javascript">
        function savePdf() {
            const element = document.getElementById("receipt");
            var filename = "<?php echo 'Receipt No_'.sprintf('%03d', $payment->receipt_no).'_'.$payment->created_at; ?>";
            var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).toPdf().save();
            // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                // window.open(pdf.output('bloburl'), '_blank');
            // });
        }
    </script>
