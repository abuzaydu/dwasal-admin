@extends('layouts.app')
<link href="https://fonts.cdnfonts.com/css/lt-binary-neue" rel="stylesheet">
                
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('an-sales') }}">Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <button  id="btnPrintInvoice" type="button" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> {{trans('navmenu.print')}}</button>
                <a href="javascript:history.back()" class="btn btn-warning btn-sm"><i class="fa fa-arrow-to-left"></i>{{trans('navmenu.btn_back')}}</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="print_invoice" id="receipt-invoice">
                        <center id="top">
                            <div class="logo">
                                @if(!is_null($company->logo_url))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="100">
                                </figure>
                                @endif
                            </div>
                            <div class="info" style="text-align: center; border-bottom: 1px dashed black; padding-bottom: 5PX;"> 
                                <span class="mb-0" style="font-size: 11px; font-weight: bold; color: black;">{{$company->name}}<br> {{$shop->name}}</span><br>
                                @if(!is_null($shop->short_desc))<small style="font-size: 8px; font-weight: bold; color: black;">{{$shop->short_desc}}</small><br>@endif
                                <span style="font-size: 11px; font-weight: bold; color: black;">{{$shop->postal_address}} {{$shop->physical_address}} @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}} @if(!is_null($shop->country)){{$shop->country}}@endif<br>
                                @if(!is_null($shop->email)){{trans('navmenu.email')}}   : {{$shop->email}} @endif @if(!is_null($shop->mobile)){{trans('navmenu.mobile')}}   : {{$shop->mobile}}@endif<br>
                                @if(!is_null($shop->tin)){{trans('navmenu.tin')}}   : {{$shop->tin}} @endif @if(!is_null($shop->vrn)){{trans('navmenu.vrn')}}   : {{$shop->vrn}}@endif</span>
                            </div><!--End Info-->
                            <div class="header-title" style="text-align: center; print margin-bottom: 5px; margin-top: 5px; border-bottom: 1px dashed black;">
                                <span style="font-size: 14px; font-weight: bold; color: black; text-transform: uppercase;"><b>{{$settings->invoice_title}}</b></span>
                            </div>
                        </center><!--End InvoiceTop-->
                                    
                        <div id="mid" style="text-align: center;">
                            <table style="font-size: 11px !important; font-weight: bold; color: black;">
                                <tr>
                                    <td>{{trans('navmenu.invoice_no')}}:</td>
                                    <td>{{ sprintf('%04d', $sale->invoice_no)}}</td>
                                </tr>
                                <tr>
                                    <td>{{trans('navmenu.date')}}:</td>
                                    <td>{{date('l, d/mY H:i:s:A', strtotime($sale->time_created)) }}</td>
                                </tr>
                                <tr>
                                    <td>{{trans('navmenu.customer')}}:</td>
                                    <td>{{$sale->name}}</td>
                                </tr>
                                @if(!is_null($sale->phone))
                                <tr>
                                    <td>{{trans('navmenu.mobile')}}:</td>
                                    <td>{{$sale->phone}}</td>
                                </tr>
                                @endif
                                @if(!is_null($sale->tin))
                                <tr>
                                    <td>{{trans('navmenu.tin')}}:</td>
                                    <td>{{$sale->tin}}</td>
                                </tr>
                                @endif 
                                @if(!is_null($sale->vrn))
                                <tr>
                                    <td>{{trans('navmenu.vrn')}}:</td>
                                    <td>{{$sale->vrn}}</td>
                                </tr>
                                @endif
                            </table>
                        </div><!--End Invoice Mid-->
                        <div id="bot">
                            <div id="table" style="padding: 0px;">
                                <table class="items mt-0" style="width: 100%; font-size: 11px; font-weight: bold; color: black;">
                                    <thead>
                                        <tr>
                                            @if($settings->show_discounts)
                                            <th style="text-align: left; width: 60%; border-bottom: 1px solid #000;"> {{trans('navmenu.description')}}</th>
                                            <th style="text-align: center; width: 10%; border-bottom: 1px solid #000;">Qty</th>
                                            <th style="text-align: center; width: 20%; border-bottom: 1px solid #000;">Price({{$sale->currency}})</th>
                                            <th style="text-align: center; width: 10%; border-bottom: 1px solid #000;">Disc @if($settings->discount_by_percent)(%)@endif</th>
                                            <!-- <th style="text-align: right; width: 25%; border-bottom: 1px solid #000;  padding-right: 5px;">{{trans('navmenu.total')}} ({{$sale->currency}})</th> -->
                                            @else
                                            <th style="text-align: left; width: 65%; border-bottom: 1px solid #000;"> {{trans('navmenu.description')}}</th>
                                            <th style="text-align: center; width: 15%; border-bottom: 1px solid #000;">Qty</th>
                                            <th style="text-align: center; width: 20%; border-bottom: 1px solid #000;">Price({{$sale->currency}})</th>
                                            <!-- <th style="text-align: right; width: 25%; border-bottom: 1px solid #000;  padding-right: 5px;">{{trans('navmenu.total')}} ({{$sale->currency}})</th> -->
                                            @endif
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
                                            @if($settings->show_discounts)
                                            <td style="">{{$item->name}}</td>
                                            <td style="text-align: center; ">{{$quantity_sold + 0}} {{$punit->unit_name}}</td>
                                            <td style="text-align: center; ">{{number_format($retail_price/$ex_rate, 2, '.', ',')}}</td>
                                            <td style="text-align: center; ">
                                                @if($settings->discount_by_percent)
                                                {{$item->disc_percent+0}}
                                                @else
                                                {{number_format($item->total_discount/$ex_rate, 2, '.', ',')}}
                                                @endif
                                            </td>
                                            <!-- <td style="text-align: right; padding-right: 5px; ">{{number_format(($item->price-$item->total_discount)/$ex_rate, 2, '.', ',')}}</td> -->
                                            @else
                                            <td style="">{{$item->name}}</td>
                                            <td style="text-align: center; ">{{$quantity_sold + 0}} {{$punit->unit_name}}</td>
                                            <td style="text-align: center; ">{{number_format($retail_price/$ex_rate, 2, '.', ',')}}</td>
                                            <!-- <td style="text-align: right;  padding-right: 5px;">{{number_format($item->price/$ex_rate, 2, '.', ',')}}</td> -->
                                            @endif
                                        </tr>
                                        @endforeach
                                        @foreach($servitems as $key => $item)
                                        <tr>
                                            @if($settings->show_discounts)
                                            <td style="">{{$item->name}}</td>
                                            <td style="text-align: center; ">{{round($item->qty,2)}}</td>
                                            <td style="text-align: center; ">{{number_format($item->price/$ex_rate, 2, '.', ',')}}</td>
                                            <td style="text-align: center; ">
                                                @if($settings->discount_by_percent)
                                                {{$item->disc_percent+0}}
                                                @else
                                                {{number_format($item->total_discount, 2, '.', ',')}}
                                                @endif
                                            </td>
                                            <!-- <td style="text-align: right;  padding-right: 5px;">{{number_format(($item->total-$item->total_discount)/$ex_rate, 2, '.', ',')}}</td> -->
                                            @else
                                            <td style="">{{$item->name}}</td>
                                            <td style="text-align: center; ">{{$item->qty}}</td>
                                            <td style="text-align: center; ">{{number_format($item->price/$ex_rate, 2, '.', ',')}}</td>
                                            <!-- <td style="text-align: right;  padding-right: 5px;">{{number_format(($item->total-$item->total_discount)/$ex_rate, 2, '.', ',')}}</td> -->
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <table class="items" width="100%" style="font-size: 11px; font-weight: bold; color: black; padding-right: 5px;">
                                    <tbody>
                                        @if($settings->show_discounts)
                                        <tr style="border-top: 2px dashed #000;">
                                            <td colspan="2" style="text-align: right;">{{trans('navmenu.subtotal')}}</td>
                                            <td style="text-align: right; padding-right: 5px;">{{number_format(($sale->sale_amount-$sale->sale_discount)/$ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td colspan="2" style="text-align: right; border-top: 2px solid #e0e0e0;">{{trans('navmenu.subtotal')}}</td>
                                            <td style="text-align: right; border-top: 2px solid #e0e0e0; padding-right: 5px;">{{number_format($sale->sale_amount/$ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @if($sale->sale_discount > 0)
                                        <tr>
                                            <td colspan="2" style="text-align: right;">{{trans('navmenu.discount')}}</td>
                                            <td style="text-align: right;">{{number_format($sale->sale_discount/$ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                        @endif    
                                        @if($settings->is_vat_registered && $sale->tax_amount > 0)
                                        <tr>
                                            <td colspan="2" style="text-align: right;">{{trans('navmenu.vat')}}</td>
                                            <td style="text-align: right;">{{number_format($sale->tax_amount/$ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th colspan="2" style="text-align: right;"><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: right;"><b>{{number_format((($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount)/$ex_rate, 2, '.', ',')}}</b></th>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: right;"><span>{{trans('navmenu.sale_type')}} : <b>{{$sale->sale_type}}</b></span></td>
                                            <td style="text-align: right;"> <span>{{trans('navmenu.pay_type')}} : <b>{{$sale->pay_type}}</span><br></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!--End Table-->
                            <div id="legalcopy" style="text-align: center; font-size: 11px; color: black; border-bottom: 1px dashed black;">
                                <p class="legal">
                                    <b>Thank you for shopping with us.<br>@if(!is_null($shop->website))For more info.about our products & services, please visit {{$shop->website}}@endif</b>
                                </p>
                            </div>
                            <div class="text-center" id="legalcopy" style="text-align: center; font-size: 11px; color: black; padding: 5;">
                                <b>Powered by: Smart Mauzo</b><br>
                                {{QrCode::size(80)->generate('https://smartmauzo.com/verify-invoice/'.$sale->id)}}
                                <hr>
                                <h6 class="legal">***END OF INVOICE***</h6>
                            </div>
                        </div><!--End InvoiceBot-->
                    </div><!--End Invoice-->
                    <div class="row align-items-center">
                        <!-- <div class="col-md-6 p-2">
                            <a href="javascript:;" onclick="javascript:savePdf()" class="btn btn-success btn-sm" style="width: 100%;">
                                <i class="fa fa-download"></i> DOWNLOAD
                            </a>
                        </div> -->
                        <div class="col-md-6 p-2">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    <link href="https://fonts.cdnfonts.com/css/thegoodmonolith" rel="stylesheet">
                
    <link rel="stylesheet" type="text/css" href="{{ asset('css/receipt.css') }}">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
    <script>    
        $(document).ready( function ()  {
            $('#btnPrintInvoice').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#receipt-invoice").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link href="https://fonts.cdnfonts.com/css/lt-binary-neue" rel="stylesheet"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo trans('navmenu.invoice_no').'_'.$sale->invoice_no.'_'.$date ?>";
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
            const element = document.getElementById("receipt-invoice");
            var filename = "<?php echo 'Invoice_'.sprintf('%04d', $sale->invoice_no).'_'.$sale->created_at; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr", mode: "css"},
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

          // New Promise-based usage:
            html2pdf().set(opt).from(element).toPdf().save();
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                // window.open(pdf.output('bloburl'), '_blank');
            // });
        }
    </script>