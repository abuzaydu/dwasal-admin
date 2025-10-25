@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-10 mx-auto">
            <ul class="nav nav-tabs nav-success" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#sales-order" role="tab"
                        aria-selected="true">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                            </div>
                            <div class="tab-title">Sales Order A4 View</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#sales-order-rview" role="tab"
                        aria-selected="true">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                            </div>
                            <div class="tab-title">Sales Order Receipt View</div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="tab-content py-3">
                <div class="tab-pane fade show active" id="sales-order" role="tabpanel">    
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <a onclick="javascript:savePdf()" class="btn btn-sm btn-secondary btn-sm"><i class="fa fa-file me-2"></i>Download PDF / <i class="fa fa-printer me-2"></i>Print</a>
                                    @if($saleorder->sale_type == 'cash' && Auth::user()->can('create-invoice') && !$saleorder->is_invoiced)
                                        <form method="POST" action="{{ url('create-sale-from-so') }}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$saleorder->id}}">

                                            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-file"></i> Create Invoice</button>
                                        </form>
                                    @elseif($saleorder->sale_type == 'cash' && Auth::user()->can('package-sales-order') && !$saleorder->is_packaged)
                                        <form class="row g-3" method="POST" action="{{'so-packed'}}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$saleorder->id}}">
                                            <div class="col-md-8"></div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-check"></i> Confirm Packaged</button>
                                            </div>
                                        </form>
                                    @else
                                    @if($saleorder->is_approved)
                                        @if((Auth::user()->can('print-sales-order')) && !$saleorder->is_packaged)
                                        <a onclick="javascript:savePdf()" class="btn btn-sm btn-secondary btn-sm"><i class="fa fa-file me-2"></i>Download PDF / <i class="fa fa-printer me-2"></i>Print</a>
                                        @endif
                                        @if(Auth::user()->can('package-sales-order') && !$saleorder->is_packaged)
                                        <form class="row g-3" method="POST" action="{{'so-packed'}}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$saleorder->id}}">
                                            <div class="col-md-8"></div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-check"></i> Confirm Packaged</button>
                                            </div>
                                        </form>
                                        @endif

                                        @if(Auth::user()->can('create-invoice') && $saleorder->status == 'Order Packaged' || $saleorder->status == 'Partially Invoiced')
                                        <form method="POST" action="{{ url('create-sale-from-so') }}" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$saleorder->id}}">

                                            <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-file"></i> Create Invoice</button>
                                        </form>  
                                        @endif
                                    @else
                                        @if(Auth::user()->can('approve-sales-order'))
                                        <a type="button" class="btn bg-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#rejectModal" style="color: white;"><i class="fa fa-x"></i> Reject</a>
                                        <a href="{{ url('approve-so/'.encrypt($saleorder->id)) }}" class="btn btn-info btn-sm mt-2"><i class="fa fa-check"></i> Approve</a>
                                        @endif
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-1 print_invoice" id="print-order">
                                <div class="col-md-12">
                                    <table class="items mt-0 mb-1">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"style="text-align: center; background:  #2874a6;">
                                                    <h4 class="mb-0 text-uppercase text-light">{{$page}}</h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 border-bottom pb-0">
                                    <table class="items mt-0">
                                        <tr>
                                            <td style="width: 40%; text-align: right; padding-left: 20px;">
                                                @if(!is_null($shop->logo_location))
                                                <figure>
                                                    <img id="image" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="logo" alt="logo" width="100">
                                                </figure>
                                                @endif
                                            </td>
                                            <td style="width: 60%;">
                                                <strong>{{$shop->name}}.</strong><br><span>{{$shop->short_desc}}</span><br> <small> {{$shop->postal_address}}, {{$shop->physical_address}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->phone}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-12 customer mt-2 mb-0">
                                    <div class="row g-3">
                                        <div class="col-md-8" style="padding-left: 20px; font-size: 11px;">
                                            CUSTOMER ID : <b>{{ $saleorder->custid }}</b><br>
                                            CUSTOMER NAME : <b>{{ $saleorder->name }}</b><br>
                                            MOBILE : <b>{{ $saleorder->phone }}</b><br>
                                            EMAIL : <b>{{ $saleorder->email }}</b><br>
                                            TIN : <b>{{ $saleorder->tin }}</b> 
                                            VRN : <b>{{ $saleorder->vrn }}</b>
                                        </div>
                                        <div class="col-md-4" style="padding-right: 20px;">
                                            <table class="meta">
                                                <tbody>
                                                    <tr>
                                                        <td class="meta-head" style="text-align: right;">Order No.</td>
                                                        <td><b id="date" style="text-align: right;">{{ $saleorder->order_no }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="meta-head" style="text-align: right;">Order Date</td>
                                                        <td><b id="date" style="text-align: right;">{{ date('d M, Y H:i', strtotime($saleorder->order_date)) }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="meta-head" style="text-align: right;">Status</td>
                                                        <td><b id="date" style="text-align: right;">{{ $saleorder->status }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="meta-head" style="text-align: right;">Created By</td>
                                                        <td><b id="date" style="text-align: right;">{{ $saleorder->first_name }} {{ $saleorder->last_name }}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div style="clear:both"></div> -->
                                <div class="col-md-12">
                                    <table class="items" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="width: 2%;">#</th>
                                                <th style="width: 50%; text-align: left;">Item Description</th>
                                                <th style="text-align: center; width: 7%">Qty</th>
                                                <th style="text-align: center; width: 3%">UOM</th>
                                                @if(Auth::user()->can('view-order-amount'))
                                                <th style="text-align: center; width: 18%;">Price</th>
                                                <th style="text-align: right; width: 18%;">Total</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0; $discount = 0; $vat = 0; ?>
                                            @foreach($items as $key => $item)
                                            <?php 
                                                $total += $item->price;
                                                $discount += $item->total_discount;
                                                $vat += $item->vat_amount;
                                            ?>
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td style="text-align: left;">{{$item->name}}</td>
                                                <td style="text-align: center;">{{ $item->quantity+0 }}</td>
                                                <td style="text-align: center;">{{ $item->unit_name }}</td>
                                                @if(Auth::user()->can('view-order-amount'))
                                                <td style="text-align: center;">{{ $item->retail_price }}</td>
                                                <td style="text-align: right;">{{ number_format($item->price, 2, '.', ',') }}</td>
                                                @endif
                                            </tr>
                                            @endforeach
                                            @if(Auth::user()->can('view-order-amount'))
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align: right; text-transform: uppercase;border-bottom: 1px solid #e0e0e0;"><b>Subtotal</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{ number_format($total, 2, '.', ',') }}</b></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align: right; text-transform: uppercase;border-bottom: 1px solid #e0e0e0;"><b>Discount</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{ number_format($discount, 2, '.', ',') }}</b></td>
                                            </tr>

                                            @if($settings->is_vat_registered && $vat > 0)
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align: right; text-transform: uppercase;border-bottom: 1px solid #e0e0e0;"><b>VAT ({{$settings->vat_rate}} %)</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{ number_format($vat, 2, '.', ',') }}</b></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <th style="text-align: right; text-transform: uppercase;border-bottom: 1px solid #e0e0e0;"><b>Total</b></th>
                                                <th style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{ number_format(($total-$discount)+$vat, 2, '.', ',') }}</b></th>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Comments</label>
                                            <div>
                                                {{$saleorder->comments}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="sales-order-rview" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Auth::user()->can('print-sales-order'))
                                    <a id="btnPrint" class="btn btn-outline-warning btn-sm" style="margin-left: 2px; padding-top: 5px;"><i class="lni lni-printer"></i>Print Privew</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-4 mx-auto" style="border: 1px solid #e0e0e0;">
                                <div class="print_invoice" id="receipt">
                                    <center id="top">
                                        <div class="header-title" style="text-align: center;margin-bottom: 5px;">
                                            <span style="font-size: 20px; color: #2874a6; text-transform: uppercase;">POS</span>
                                        </div>
                                        <div class="logo">
                                            @if(!is_null($shop->logo_location))
                                            <figure>
                                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" style="width: 60px; height: 60px">
                                            </figure>
                                            @endif
                                        </div>
                                        <div class="info" style="text-align: center;"> 
                                            <span class="mb-0" style="font-size: 16px;">{{$shop->name}}</span><br>
                                            <small style="font-size: 11px">{{$shop->short_desc}}</small>
                                        </div><!--End Info-->
                                    </center><!--End InvoiceTop-->
                                    
                                    <div id="mid" style="text-align: center; font-size: 11px;">
                                        <div class="info">
                                            <p>
                                                {{$shop->postal_address}} {{$shop->physical_address}} @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br>
                                                {{trans('navmenu.email')}}   : {{$shop->email}} 
                                                {{trans('navmenu.mobile')}}   : {{$shop->mobile}}<br>
                                                {{trans('navmenu.tin')}}   : {{$shop->tin}} 
                                                {{trans('navmenu.vrn')}}   : {{$shop->vrn}}<br>
                                            </p>
                                        </div>
                                        <div>
                                            <span style="font-size: 16px;"><b>{{trans('navmenu.order_no')}}: {{ sprintf('%04d', $saleorder->order_no)}}</b></span>
                                        </div>
                                        <div>
                                            <span>{{trans('navmenu.date')}}: <strong>{{ date('d M, Y H:i', strtotime($saleorder->order_date)) }}</strong></span><br>
                                        </div>
                                    </div><!--End Invoice Mid-->
                                    
                                    <div id="bot">
                                        <div id="table">
                                            <table class="items" width="100%" style="font-size: 11px;">
                                                <thead>
                                                    <tr>
                                                        @if($settings->show_discounts)
                                                        <th style="text-align: left; width: 50%; border-bottom: 1px solid #000;"> Item Description</th>
                                                        <th style="text-align: center; width: 20%; border-bottom: 1px solid #000;">Qty</th>
                                                        <th style="text-align: center; width: 30%; border-bottom: 1px solid #000;">Disc @if($settings->discount_by_percent)(%)@endif</th>
                                                        @else
                                                        <th style="text-align: left; width: 70%; border-bottom: 1px solid #000;"> Item Description</th>
                                                        <th style="text-align: center; width: 30%; border-bottom: 1px solid #000;">Qty</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $total = 0; $discount = 0; $vat = 0; ?>
                                                    @foreach($items as $key => $item)
                                                    <?php 
                                                        $total += $item->price;
                                                        $discount += $item->total_discount;
                                                        $vat += $item->vat_amount;
                                                    ?>
                                                    <tr>
                                                        @if($settings->show_discounts)
                                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($item->quantity + 0)}} {{$item->unit_name}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                                            @if($settings->discount_by_percent)
                                                            {{$item->disc_percent+0}}
                                                            @else
                                                            {{number_format($item->total_discount, 2, '.', ',')}}
                                                            @endif
                                                        </td>
                                                        @else
                                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($quantity_sold + 0)}} {{$punit->unit_name}}</td>
                                                        @endif
                                                    </tr>
                                                    @endforeach 
                                                </tbody>
                                            </table>
                                            <table class="items" width="100%" style="font-size: 11px;">
                                                <tbody>
                                                    @if($settings->show_discounts)
                                                    <tr>
                                                        <td colspan="2" style="text-align: right; border-top: 2px solid #e0e0e0;">{{trans('navmenu.subtotal')}}</td>
                                                        <td style="text-align: right; border-top: 2px solid #e0e0e0;">{{number_format($total, 2, '.', ',')}}</td>
                                                    </tr>
                                                            
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">{{trans('navmenu.discount')}}</td>
                                                        <td style="text-align: right;">{{number_format($discount, 2, '.', ',')}}</td>
                                                    </tr>
                                                    @if($settings->is_vat_registered && $vat > 0)
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">{{trans('navmenu.vat')}}</td>
                                                        <td style="text-align: right;">{{number_format($vat, 2, '.', ',')}}</td>
                                                    </tr>
                                                    @endif
                                                
                                                    <tr>
                                                        <th colspan="2" style="text-align: right;"><b>{{trans('navmenu.total')}}</b></th>
                                                        <th style="text-align: right;"><b>{{number_format((($total-$discount)+$vat), 2, '.', ',')}}</b></th>
                                                    </tr>
                                                    @else
                                                    <tr>
                                                        <td style="text-align: right;">{{trans('navmenu.subtotal')}}</td>
                                                        <td style="text-align: right;">{{number_format(($total), 2, '.', ',')}}</td>
                                                    </tr>   

                                                    @if($settings->is_vat_registered && $total > 0)
                                                    <tr>
                                                        <td style="text-align: right;">{{trans('navmenu.vat')}}</td>
                                                        <td style="text-align: right;">{{number_format($vat, 2, '.', ',')}}</td>
                                                    </tr>
                                                    @endif
                                                
                                                    <tr>
                                                        <th style="text-align: right;"><b>{{trans('navmenu.total')}}</b></th>
                                                        <th style="text-align: right;"><b>{{number_format((($total-$discount)+$vat), 2, '.', ',')}}</b></th>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div><!--End Table-->
                                        <div style="text-align: center; margin-top: 5px;">
                                            <span>{{trans('navmenu.issued_by')}}: <strong>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</strong></span>
                                        </div>
                                        <div id="legalcopy" style="text-align: center; font-size: 11px; border-bottom: 2px dashed #000;">
                                            <p class="legal"><small>Thank you for shopping at {{$shop->name}}<br>For trading hours, please visit {{$shop->website}}</small>
                                            </p>
                                        </div>
                                    </div><!--End InvoiceBot-->
                                </div><!--End Invoice-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" style="overflow: hidden;">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ url('reject-so') }}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="sale_order_id" value="{{$saleorder->id}}">
                    <div class="form-group col-md-12">
                        <label class="form-label">{{trans('navmenu.reason')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <textarea class="form-control form-control-sm mb-1" rows="1" name="comments" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
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
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link href="https://fonts.cdnfonts.com/css/lt-binary-neue" rel="stylesheet"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo trans('navmenu.order_no').'_'.$saleorder->order_no.'_'.$saleorder->order_date ?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <script language="javascript" type="text/javascript">

        function savePdf() {
          const element = document.getElementById("print-order");
          var filename = "<?php echo 'SO_'.sprintf('%04d', $saleorder->order_no).'_'.$saleorder->created_at; ?>";
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