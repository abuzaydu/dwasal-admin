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
                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                <a href="{{ route('delivery-notes.edit', encrypt($dnote->id))}}" class="btn btn-primary btn-sm" style="margin: 5px;"><i class="fa fa-edit"></i> Update</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class=" col-md-12 mx-auto"> 
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-dn">
                        <div class="col-md-12">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="text-align: left; padding-left: 15px;">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="200">
                                        </figure>
                                        @endif
                                    </td>
                                    <td>
                                        <table style="width: 100%;">
                                            <tr>
                                                <td colspan="2" style="text-align: right;">
                                                    <span style="font-size: 18px">{{$shop->name}}</span><br>
                                                    <small>{{$shop->short_desc}}</small><br> 
                                                    <p>
                                                        {{$shop->postal_address}} {{$shop->physical_address}}<br> @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}} {{$shop->country}} <br>  Tel: <b>{{$shop->tel}}</b> | <b>{{$shop->mobile}}</b> WhatsApp : <b>{{$shop->whatsapp}}</b><br> Email: <b>{{$shop->email}}</b> Website: <b>{{$shop->website}}</b>
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
                                            <h6 class="mb-0 text-uppercase" style="color: #fff;">Delivery Note</h6>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 customer mt-2 mb-0">
                            <table style="width: 100%">
                                <tr>
                                    <td style="padding-left: 30px;">
                                        <table>
                                            <tr>
                                                <td style="vertical-align: top; text-align: right;">Customer :</td>
                                                <td>
                                                    <span class="text-uppercase" style="font-size: 14px;">{{$sale->name}}</span><br>
                                                    @if(!is_null($sale->po_address)){{$sale->po_address}} <br>@endif @if(!is_null($sale->street)){{ $sale->street}}@endif @if(!is_null($sale->ph_address)){{$sale->ph_address}}<br>@endif
                                                    Mobile: <a href="tel:{{$sale->phone}}">{{$sale->phone}}</a><br> Email : <a href="mailto:{{$sale->email}}" style="text-transform: lowercase;">{{$sale->email}}</a><br>
                                                    TIN : <b>{{$sale->tin}}</b><br> VRN : <b>{{$sale->vrn}}</b>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                            <tr style="border: 1px solid gray; border-radius: 20px;">
                                                <td colspan="2" style="font-size: 16px; text-align: right;">DN No  : <b>{{ sprintf('%04d', $dnote->note_no)}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;"> Date :</td>
                                                <td><b>{{ date('d F, Y', strtotime($dnote->created_at)) }}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;">PFI No: </td>
                                                <td>@if(!is_null($proinvoice)) {{ sprintf('%04d',$proinvoice->invoice_no)}}@endif</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;">LPO No: </td>
                                                <td>{{ $sale->lpo_no }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: right;">Invoice No: </td>
                                                <td>@if(!is_null($sale->invoice_no)){{ sprintf('%04d',$sale->invoice_no)}}@endif</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="mt-3" style="width: 100%;">
                                <thead>
                                    <tr style="background: #0459c6; color: #fff; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                        <th style="text-align: center; width: 3%;">#</th>
                                        <th style="text-align: right; width: 20%; border-left: 1px solid #fff;">Code</th>
                                        <th style="width: 63%;">Item Description</th>
                                        <th style="text-align: center; width: 7%; border-left: 1px solid #fff;">UOM</th>
                                        <th style="text-align: center; width: 7%; border-left: 1px solid #fff;">Qty</th>                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $tqty = 0; ?>
                                    @foreach($items as $key => $item)
                                    <?php
                                        $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                        $quantity_sold = $item->quantity_sold;
                                        $tqty += $quantity_sold;
                                        $slug = str_replace($item->name, '', $item->slug);
                                    ?>
                                    <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                        <td style="text-align: center; "> {{$key+1}} </td>
                                        <td style="text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;">@if(!is_null($item->product_code)){{$item->product_code}}@endif</td>
                                        <td class="desc" style="">{{$item->name}} @if($slug != '')- {{$slug}}@endif</td>
                                        <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">{{$punit->unit_name}}</td>
                                        <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">{{$item->quantity_sold + 0}}</td>
                                    </tr>
                                    @endforeach

                                    <?php $tsqty = 0; ?>
                                    @foreach($servitems as $key => $item)
                                    <?php
                                        $quantity_sold = $item->qty;
                                        $tsqty += $quantity_sold;                                    ?>
                                    <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                        <td style="text-align: center; "> {{$key+1}} </td>
                                        <td style="text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;">@if(!is_null($item->code)){{$item->code}}@endif</td>
                                        <td class="desc" style="">{{$item->name}}</td>
                                        <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">unit</td>
                                        <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">{{$item->qty + 0}}</td>
                                    </tr>
                                    @endforeach
                                    <tr class="blank_row" style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                        <td colspan="3" style="" class="desc"><b></b></td>
                                        <td style=" text-align: center; border-left: 1px solid gray;" class="qty"></b></td>
                                        <td style="border-left: 1px solid gray;"></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                        <td></td>
                                        <td colspan="3" class="desc"><b>{{trans('navmenu.total')}} Items</b></td>
                                        <td class="qty" style="text-align: center; border-left: 1px solid gray;"><b>{{$tqty+$tsqty}}</b></td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 invoice-footer row g-1" style="margin-top: 20px;">
                            @if(!is_null($dnote->comments))
                            <div class="notice text-center col-md-12">
                            <!-- <div>COMMENTS:</div> -->
                                <div><b>***</b>{{$dnote->comments}}<b>***</b></div>
                            </div>
                            @endif
                            <div class="text-center col-md-6">
                                <p><span style="text-transform: uppercase; font-size: 14px; font-weight: bold;">{{trans('navmenu.issued_by')}}</span><br>
                                    {{trans('navmenu.name')}} :  @if(!is_null($dnote->issued_by))<strong>{{$dnote->issued_by}}</strong> @else<strong> ..................................</strong>@endif<br>
                                    {{trans('navmenu.signature')}} <strong>.......................</strong>
                                </p>
                            </div>
                            <div class="text-center col-md-6">
                                <p><span style="text-transform: uppercase; font-size: 14px; font-weight: bold;">{{trans('navmenu.received_by')}}</span><br>
                                    {{trans('navmenu.name')}} : @if(!is_null($dnote->received_by))<strong>{{$dnote->received_by}}</strong> @else<strong> ..................................</strong>@endif<br>
                                    {{trans('navmenu.signature')}} <strong>........................</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
            const element = document.getElementById("print-dn");
            var filename = "<?php echo 'Delivery Note_'.sprintf('%06d', $dnote->note_no).'_'.$dnote->created_at; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr", mode: "css"},
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).toPdf().get('pdf').save();
          // New Promise-based usage:
          // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                // window.open(pdf.output('bloburl'), '_blank');
            // });
          
        }
    </script>