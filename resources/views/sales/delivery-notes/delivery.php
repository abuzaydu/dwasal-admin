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
                    <div class="print_invoice p-1" id="print-dn">
                        <div class="row g-1 p-2" style="border: 1px solid black;">
                            <div class="col-md-12">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="text-align: left; padding-left: 15px;">
                                            @if(!is_null($company->logo_url))
                                            <figure>
                                                <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="150" style="border: 1px solid white;">
                                            </figure>
                                            @endif
                                        </td>
                                        <td>
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td colspan="2" style="text-align: right;">
                                                        <strong style="font-size: 14px;">{{$company->name}}</strong><br>
                                                        @if(!is_null($shop->short_desc))<small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br>@endif                        
                                                        <p class="invoice-address">
                                                            {{$shop->postal_address}} {{$shop->physical_address}}<br> @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}@if(!is_null($shop->country)), {{$shop->country}}@endif <br>@if(!is_null($shop->tel) || !is_null($shop->mobile)) Tel: @if(!is_null($shop->tel))<b>{{$shop->tel}}</b> |@endif <b>{{$shop->mobile}}</b> @if(!is_null($shop->whatsapp))WhatsApp : <b>{{$shop->whatsapp}}</b>@endif<br> @endif @if(!is_null($shop->email)) Email: <b>{{$shop->email}}</b>@endif @if(!is_null($shop->website)), Website: <b>{{$shop->website}}</b>@endif
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
                                            <td style="background: <?php echo $settings->invoice_color; ?>; padding-left: 15px;  border-radius: 30px; text-align: center;">
                                                <h6 class="mb-0 text-uppercase" style="color: <?php echo $settings->invoice_title_color; ?>;">Delivery Note</h6>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12 customer mt-0 mb-0">
                                <table style="width: 98%; margin-left: 1%; margin-right: 1%;">
                                    <tr>
                                        <td style="padding-left: 0px; width: 40%; border: 1px solid gray; border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                            <table>
                                                
                                                    <tr>
                                                        <td style="width: 35%;"><span class="text-uppercase" style="font-size: 14px; font-weight: 400;">Client Name :</span></td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">Address:</td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">
                                                        Contact Person </td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">Mobile:</td>
                                                        <td style="width: 65%; border-bottom: 1px dotted black;"> <b></b></td>
                                                    </tr>
                                            </table>
                                        </td>
                                        <td style="width: 30%; border: 1px solid gray;">
                                            <table width="100%;">
                                                <tr>
                                                    <td style="vertical-align: top; text-align: left;">Delivery To :</td>
                                                </tr>
                                                <tr>
                                                    <td style="border-bottom: 1px dotted;">.</td>
                                                </tr>
                                                <tr>
                                                    <td style="border-bottom: 1px dotted;">.</td>
                                                </tr>
                                                <tr>
                                                    <td style="border-bottom: 1px dotted;">.</td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td style="width: 30%; border: 1px solid gray; border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
                                            <table style="width: 100%;">
                                                <tr>
                                                    <td style="width: 50%; font-size: 16px; text-align: right;">DN No  :</td>
                                                    <td> <b>{{ sprintf('%04d', $dnote->note_no)}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 50%; text-align: right;">Delivery  Date :</td>
                                                    <td style="border-bottom: 1px dotted;"><b></b></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 50%; text-align: right;">PFI No: </td>
                                                    <td style="border-bottom: 1px dotted;"></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 50%; text-align: right;">LPO No: </td>
                                                    <td style="border-bottom: 1px dotted;"></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 50%; text-align: right;">Invoice No: </td>
                                                    <td style="border-bottom: 1px dotted;"></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 50%; text-align: right;">Truck No: </td>
                                                    <td style="border-bottom: 1px dotted;"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12 mt-0">
                                <table class="mt-0" style="width: 100%;">
                                    <thead>
                                        <tr style="background: <?php echo $settings->invoice_color; ?>; color: <?php echo $settings->invoice_title_color; ?>; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                            <th style="text-align: center; width: 3%;">#</th>
                                            <th style="text-align: right; width: 20%; border-left: 1px solid #fff;">Code</th>
                                            <th style="width: 63%;">Item Description</th>
                                            <th style="text-align: center; width: 7%; border-left: 1px solid #fff;">Qty</th>
                                            <th style="text-align: center; width: 7%; border-left: 1px solid #fff;">UOM</th>     
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $tqty = 0; ?>
                                        @if($items->count() < 0)
                                        @foreach($items as $key => $item)
                                        <?php $tqty += $item->delivery_qty; ?>
                                        <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                            <td style="text-align: center; "> {{$key+1}} </td>
                                            <td style="text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;">@if(!is_null($item->product_code)){{$item->product_code}}@endif</td>
                                            <td class="desc" style="">{{$item->slug}}</td>
                                            <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">{{$item->delivery_qty + 0}}</td>
                                            <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;">{{$item->uom}}</td>
                                        </tr>
                                        @endforeach
                                        @endif

                                        @for($i=0; $i<5; $i++)
                                        <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                            <td style="text-align: center; "> {{$i+1}} </td>
                                            <td style="text-align: right; border-left: 1px solid gray; border-left: 1px solid gray;"></td>
                                            <td class="desc" style=""></td>
                                            <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;"></td>
                                            <td class="qty" style=" text-align: center; border-left: 1px solid gray; border-left: 1px solid gray;"></td>
                                        </tr>
                                        @endfor
                                        <tr class="blank_row" style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                            <td colspan="3" style="" class="desc"><b></b></td>
                                            <td style=" text-align: center; border-left: 1px solid gray;" class="qty"></b></td>
                                            <td style="border-left: 1px solid gray;"></td>
                                        </tr>
                                        <tr style="border-bottom: 1px solid gray; border-left: 1px solid #0459c6; border-right: 1px solid #0459c6;">
                                            <td colspan="3" class="desc"><b>{{trans('navmenu.total')}} Items</b></td>
                                            <td class="qty" style="text-align: center; border-left: 1px solid gray;"><b></b></td>
                                            <td style="border-left: 1px solid gray;"></td>
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
                                <div class="text-center col-md-4">
                                    <p><span style="text-transform: uppercase; font-size: 14px; font-weight: bold;">{{trans('navmenu.issued_by')}}</span><br>
                                        {{trans('navmenu.name')}} :  @if(is_null($dnote->issued_by))<strong>{{$dnote->issued_by}}</strong> @else<strong> ..................................</strong>@endif<br>
                                        {{trans('navmenu.signature')}} <strong>.......................</strong>
                                    </p>
                                </div>
                                <div class="text-center col-md-4">
                                    @if(!empty($dnote->checked_by))
                                    <p><span style="text-transform: uppercase; font-size: 14px; font-weight: bold;">Checked By</span><br>
                                        Guard Name : <strong>.....................................</strong><br>
                                        Checked At <strong>.................................</strong><br>
                                        {{trans('navmenu.signature')}} <strong>.......................</strong>
                                    </p>
                                    @endif
                                </div>
                                <div class="text-center col-md-4">
                                    <p><span style="text-transform: uppercase; font-size: 14px; font-weight: bold;">{{trans('navmenu.received_by')}}</span><br>
                                        {{trans('navmenu.name')}} : @if(!empty($dnote->received_by))<strong>{{$dnote->received_by}}</strong> @else<strong> ..................................</strong>@endif<br>
                                        {{trans('navmenu.signature')}} <strong>........................</strong>
                                    </p>
                                </div>
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