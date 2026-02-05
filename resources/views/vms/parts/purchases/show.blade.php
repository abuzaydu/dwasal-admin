@extends('layouts.vms')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>               
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a  href="{{ url('part-purchases') }}">Part Purchases</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm " style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                <a href="{{url('pp-items/'.encrypt($purchase->id))}}" class="btn btn-primary btn-sm" style="margin: 5px;"><i class="fa fa-edit"></i> Update Items</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-voucher">
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center; background:  #2874a6;">
                                            <h4 class="mb-0 text-uppercase" style="color: #fff;">@if($purchase->is_production) PRODUCTION @else GOODS RECEIVED NOTE @endif</h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 border-bottom pb-4" style="border-bottom: 1px solid black;">
                            <table class="items mt-0">
                                <tr>
                                    <td style="width: 50%; padding-left: 30px;">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="250" style="border: 1px solid gray;">
                                        </figure>
                                        @endif
                                    </td>
                                    <td style="width: 50%; padding-right: 20px;">
                                        <table class="meta">
                                            <tbody>
                                                <tr>
                                                    <td class="meta-head" style="text-align: right; font-size: 14px;">{{trans('navmenu.date')}} : <b id="date">
                                                    {{date("d, M Y", strtotime($purchase->time_created))}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td class="meta-head" style="text-align: right; font-size: 14px;">@if($shop->business_type_id == 1)BATCH No:@else GRN NO:@endif <b id="date">{{ sprintf('%05d', $purchase->grn_no)}}</b></td>
                                                </tr>
                                                @if(!is_null($purchase->order_no))
                                                <tr>
                                                    <td class="meta-head" style="text-align: right;">{{trans('navmenu.lpo_no')}} : <b id="date">{{ sprintf('%05d', $purchase->order_no)}}</b></td>
                                                </tr>
                                                @endif
                                                @if(!is_null($purchase->delivery_note_no))
                                                <tr>
                                                    <td class="meta-head" style="text-align: right;">{{trans('navmenu.delivery_note_no')}} :  <b id="date">{{$purchase->delivery_note_no}}</b></td>
                                                </tr>
                                                @endif
                                                @if(!is_null($purchase->invoice_no))
                                                <tr>
                                                    <td class="meta-head" style="text-align: right;">{{trans('navmenu.invoice_no')}} :  <b id="date">{{$purchase->invoice_no}}</b></td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>    
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 50%; padding-left: 30px;">
                                        <span>From: </span><br>
                                        <strong style="font-size: 14px;">{{$company->name}} - {{$shop->name}}.</strong><br>
                                        @if(!is_null($shop->short_desc))<small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br>@endif
                                        <small>@if(!is_null($shop->postal_address)){{$shop->postal_address}}@endif @if(!is_null($shop->physical_address)){{$shop->physical_address}} <br>@endif @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif @if(!is_null($shop->city)){{$shop->city}}@endif<br> @if(!is_null($shop->email))Email: <b>{{$shop->email}}</b><br>@endif @if(!is_null($shop->tel))Tel: <b>{{$shop->tel}}</b>@endif @if(!is_null($shop->mobile))Mobile: <b>{{$shop->mobile}}</b><br>@endif @if(!is_null($shop->tin))TIN: <b>{{$shop->tin}}</b>@endif @if(!is_null($shop->vrn))VRN: <b>{{$shop->vrn}}</b>@endif</small>
                                    </td>
                                    <td style="width: 50%; border: 1px solid gray;">
                                        <span>To:</span><br>
                                        <strong style="font-size: 14px;">{{$vendor->vendor_name}}</strong><br>
                                        <small style="font-size: 8px">
                                            @if(!is_null($vendor->address)){{$vendor->address}}<br>@endif @if(!is_null($vendor->phone))Mobile : <a href="#">{{$vendor->phone}} </a>@endif @if(!is_null($vendor->email))Email :<a href="#" style="text-transform: lowercase;">{{$vendor->email}}</a><br>@endif @if(!is_null($vendor->tin)) TIN : {{$vendor->tin}}@endif @if(!is_null($vendor->vrn))VRN : {{$vendor->vrn}}<br>@endif
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="list-items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{trans('navmenu.description')}}</th>
                                        <th style="text-align: center;">UOM</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        @if($shop->business_type_id != 1)
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.total')}}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $tqty = 0; ?>
                                    @foreach($pitems as $key => $item)
                                    <?php $tqty += $item->pp_qty; ?>
                                    <tr>
                                        <td class="no">{{$key+1}}</td>
                                        <td class="text-left">{{$item->part_no}} {{$item->part_name}}</td>
                                        <td style="text-align: center;">{{$item->uom}}</td>
                                        <td style="text-align: center;">{{$item->pp_qty+0 }}</td>
                                        @if($shop->business_type_id != 1)
                                        <td style="text-align: center;">{{number_format($item->unit_price)}}</td>  
                                        <td style="text-align: right;">{{number_format($item->pp_qty*$item->unit_price)}}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td style="text-align: center;"><b>{{trans('navmenu.total')}}</b></td>
                                        <td></td>
                                        <td style="text-align: center;"><b>{{$tqty}}</b></td>
                                        @if($shop->business_type_id != 1)
                                        <td></td>
                                        <td style="text-align: right;"><b>{{number_format($purchase->total_amount)}}</b></td>
                                        @endif
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="notices">
                                <div>{{trans('navmenu.comments')}}:</div>
                                <div class="notice">{{$purchase->comments}}</div>
                            </div>
                            <div class="row pt-4">
                                <p class=" col font-12 text-center">
                                    {{trans('navmenu.received_by')}} : <br>{{trans('navmenu.name')}} : <b>{{$purchase->first_name}} {{$purchase->last_name}}</b> <br> {{trans('navmenu.signature')}} _________________ <br>{{trans('navmenu.date')}}   _________________
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
            const element = document.getElementById("print-voucher");
            var filename = "<?php echo $title.'_no_'.$purchase->grn_no.'_'.$purchase->time_created; ?>";
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
            //     window.open(pdf.output('bloburl'), '_blank');
            // });
          
        }
</script>