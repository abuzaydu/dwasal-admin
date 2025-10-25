@extends('layouts.inv')
<?php $shop = App\Models\Shop::find(1); ?>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{url('transfer-orders')}}">{{trans('navmenu.stock_transfer')}}</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-header">
                </div>
                <div class="card-body">
                    <div id="invoice-view">
                        <div class="row g-1 print_invoice" id="print-st">
                            <div class="col-md-12">
                                <table class="table mb-1">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" style="text-align: center; background:  #2874a6;">
                                                <h6 class="mb-0 text-uppercase" style="color: #fff;">{{$title}}</h6>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 border-bottom pb-0" style="border-bottom: 1px solid gray;">
                                <table class="items mt-0">
                                    <tr>
                                        <td style="width: 50%; padding-left: 30px;">
                                            @if(!is_null($shop->logo_location))
                                            <figure>
                                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="250" style="border: 1px solid gray;">
                                            </figure>
                                            @endif
                                            <strong style="font-size: 14px;">{{$shop->name}}.</strong><br>
                                            <small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br> <small>{{$shop->postal_address}} {{$shop->physical_address}} <br>@if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                        </td>
                                        <td style="width: 50%;">
                                            <table class="mb-0" style="width: 100%;">
                                                <tbody>
                                                    <tr>
                                                        <td>{{trans('navmenu.sto_no')}} :</td>
                                                        <td><b>{{ sprintf('%04d', $transorder->order_no)}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{trans('navmenu.transfer_date')}} :</td>
                                                        <td><b>{{date("d, M Y", strtotime($transorder->order_date))}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{trans('navmenu.source_shop')}}: </td>
                                                        <td><b>{{$source->name}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{trans('navmenu.destin_shop')}}</td>
                                                        <td><b>{{$destin->name}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{trans('navmenu.reason')}}:</td>
                                                        <td><b>{{$transorder->reason}}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12 pt-3">
                                <p class="mb-1 text-uppercase text-center">{{trans('navmenu.transfer_items')}}</p>
                                <table class="list-items mt-0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th style="text-align: left;">{{trans('navmenu.item_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: right;">Price</th>
                                            <th style="text-align: right;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($orderitems as $key => $item)
                                        <?php 
                                            $price = number_format($item->source_unit_price); 
                                            if(fmod($item->source_unit_price, 1) != 0.0){
                                                $price = number_format($item->source_unit_price, 2, '.', ',');
                                            }
                                            $total += $item->source_unit_price*$item->quantity; ?>
                                        <tr>
                                            <td style="border: none;">{{$item->product_code}}</td>
                                            <td style="border: none;">{{$item->name}}</td>
                                            <td style="border: none; text-align: center;">{{$item->quantity+0}} {{$item->basic_uom}}</td>
                                            <td style="border: none; text-align: right;">{{ $price }}</td>
                                            <td style="border: none; text-align: right;">{{ number_format($item->source_unit_price*$item->quantity) }}</td>
                                        </tr>
                                        @endforeach
                                        <tr class="blank_row">
                                            <td colspan="5" style="border-bottom: 1px solid gray;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <table class="mt-0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;"></th>
                                            <th style="width: 30%"></th>
                                            <th style="width: 20%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="border: none; text-align: right; font-weight: bold;">Total (Excl. Tax)</td>
                                            <td style="border: none; text-align: right; font-weight: bold;">{{ number_format($total, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="border: none; text-align: right; font-weight: bold;">Total Tax ({{$settings->tax_rate+0}}%)</td>
                                            <td style="border: none; text-align: right; font-weight: bold;">
                                                @if($transorder->add_vat)
                                                {{ number_format($total*($settings->tax_rate/100), 2, '.', ',') }}
                                                @else
                                                {{ number_format(0, 2, '.', ',') }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="border-bottom: 1px solid gray; text-align: right; font-weight: bold;">Total (Incl. Tax)</td>
                                            <td style="border-bottom: 1px solid gray; text-align: right; font-weight: bold;">
                                                @if($transorder->add_vat)
                                                {{ number_format($total*(1+$settings->tax_rate/100), 2, '.', ',') }}
                                                @else
                                                {{ number_format($total, 2, '.', ',') }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($transorder->is_request)
                            <div class="col-md-12 clearfix order-bottom " style="margin-top: 15px;">
                                <div class="issuer" style="width: 50%; float: left; padding-left: 55px;">
                                    <table>
                                        <tr>
                                            <td style="font-size: 8px !important;">
                                                <span style="text-transform: uppercase; font-weight: bold;">{{trans('navmenu.transfer_by')}}</span><br>
                                                {{trans('navmenu.name')}} : <strong>@if(!is_null($user)){{$user->first_name}} {{$user->last_name}}@endif</strong><br>
                                                Date <strong>@if(!is_null($transorder->created_at)){{date('d M Y H:i A', strtotime($transorder->created_at))}}@endif</strong><br>
                                                {{trans('navmenu.signature')}} <strong>.....................</strong>
                                            </td>
                                        </tr>
                                    </table>                  
                                </div>
                                <div class="receiver" style="width: 50%; float: right; padding-left: 55px;">
                                    <table>
                                        <tr>
                                            <td style="font-size: 8px !important;">
                                                <span style="text-transform: uppercase; font-weight: bold;">{{trans('navmenu.stock_received_by')}}</span><br>
                                                {{trans('navmenu.name')}} : <strong>@if(!is_null($requester)){{$requester->first_name}} {{$requester->last_name}}@endif</strong><br>
                                                Date <strong>@if(!is_null($transorder->receive_time)){{date('d M Y H:i A', strtotime($transorder->receive_time))}}@endif</strong><br>
                                                {{trans('navmenu.signature')}} <strong>.....................</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="col-md-12 clearfix order-bottom " style="margin-top: 15px;">
                                <div class="issuer" style="width: 50%; float: left; padding-left: 55px;">
                                    <table>
                                        <tr>
                                            <td style="font-size: 8px !important;">
                                                <span style="text-transform: uppercase; font-weight: bold;">Returned By</span><br>
                                                {{trans('navmenu.name')}} : <strong>@if(!is_null($requester)){{$requester->first_name}} {{$requester->last_name}}@endif</strong><br>
                                                Date <strong>@if(!is_null($transorder->created_at)){{date('d M Y H:i A', strtotime($transorder->created_at))}}@endif</strong><br>
                                                {{trans('navmenu.signature')}} <strong>.....................</strong>
                                            </td>
                                        </tr>
                                    </table>                  
                                </div>
                                <div class="receiver" style="width: 50%; float: right; padding-left: 55px;">
                                    <table>
                                        <tr>
                                            <td style="font-size: 8px !important;">
                                                <span style="text-transform: uppercase; font-weight: bold;">{{trans('navmenu.stock_received_by')}}</span><br>
                                                {{trans('navmenu.name')}} : <strong>@if(!is_null($user)){{$user->first_name}} {{$user->last_name}}@endif</strong><br>
                                                Date <strong>@if(!is_null($transorder->receive_time)){{date('d M Y H:i A', strtotime($transorder->receive_time))}}@endif</strong><br>
                                                {{trans('navmenu.signature')}} <strong>.....................</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="text-center mt-3 mb-1">
                            <a href="#" onclick="javascript:savePdf()" class="btn btn-outline-warning btn-sm float-end" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF /Print </a>
                        </div>
                    </div>
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
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link href="https://fonts.cdnfonts.com/css/lt-binary-neue" rel="stylesheet"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo 'STO No_'.$transorder->order_no.'_'.$transorder->created_at ?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });
    </script>

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        
        function confirmReceive() {
            document.getElementById('receive-form').submit();
        }

        function showPreview(elem) {
            var rp = document.getElementById('receipt-view');
            var ip = document.getElementById('invoice-view');
            if (elem == 'show') {
                rp.style.display = 'block';
                ip.style.display = 'none';
            }else{
                rp.style.display = 'none';
                ip.style.display = 'block';
            }
        }
        
        function printOrder() {
            Swal.fire({
              title: "Are you sure you want to print this Order",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "Ye Print",
              cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.value) {
                    var stoid = "<?php echo $transorder->id; ?>";
                    $.ajax({
                        url:"{{ url('print-sto') }}",
                        type:'GET',
                        data:{'id':stoid},
                        success:function (response) {
                            
                        }
                    })   
                }
            })
        }

        function savePdf() {
          const element = document.getElementById("print-st");
          var filename = "<?php echo trans('navmenu.title').'_'.$transorder->created_at; ?>";
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