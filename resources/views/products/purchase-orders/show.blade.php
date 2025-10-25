@extends('layouts.inv')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-0">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{ url('purchase-orders') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <div class="p-0">
                    @if($porder->status == 'Approved' || $porder->status == 'Partially Delivered')
                    <form action="{{ url('purchases/create-purchase') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="id" value="{{$porder->id}}">
                        <button type="submit" class="btn btn-success btn-sm" style="margin-right: 5px;"><i class="fa fa-file"></i> Receive PO</button>
                    </form>
                    @endif
                    @if($porder->status == 'Awaiting for Approval')
                    @if(Auth::user()->can('edit-purchase-order'))
                    <a href="{{ route('purchase-orders.edit', encrypt($porder->id))}}" class="btn btn-outline-primary btn-sm" style="margin-right: 5px;"><i class="fa fa-edit"></i> Update</a>
                    @endif
                    @if(Auth::user()->can('approve-po'))
                    <a href="{{ url('approve-po/'.encrypt($porder->id))}}" class="btn btn-outline-warning btn-sm" style="margin-right: 5px;"><i class="fa fa-check"></i> Approve PO</a>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 ms-auto">
            <ul class="nav nav-tabs nav-tabs-new2">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#lpo-value" onclick="showHideContent('hide')"><i class='fa fa-list-check font-18 me-1'></i> LPO Value</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#lpo-qty" onclick="showHideContent('show')"><i class='fa fa-list-check font-18 me-1'></i> LPO Qty Only</a>
                </li>
            </ul>
            <div class="tab-content py-0">
                <div id="lpo-value" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-1 print_invoice" id="print-order">
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="text-align: left; padding-left: 15px;">
                                                @if(!is_null($company->logo_url))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/'.$company->logo_url)}}" alt="" width="250" style="border: 1px solid white;">
                                                </figure>
                                                @endif
                                            </td>
                                            <td>
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">
                                                            <strong style="font-size: 14px;">{{$company->name}}</strong><br><span style="text-align: center;">({{$shop->name}})</span><br>
                                                            @if(!is_null($shop->short_desc))<small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br>@endif
                                                            <p>
                                                                @if(!is_null($shop->postal_address)){{$shop->postal_address}}@endif @if(!is_null($shop->physical_address)){{$shop->physical_address}}<br>@endif @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}} {{$shop->country}} <br>  Tel: <b>{{$shop->tel}}</b> | <b>{{$shop->mobile}}</b> WhatsApp : <b>{{$shop->whatsapp}}</b><br> Email: <b>{{$shop->email}}</b> Website: <b>{{$shop->website}}</b><br> TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b>
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
                                                <td style="background: #037c1e; padding-left: 15px;  border-radius: 30px; text-align: center;">
                                                    <h6 class="mb-0 text-uppercase" style="color: #fff;">Purchase Order (LPO)</h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 border-bottom pb-4" style="border-bottom: 1px solid black;">
                                    <table class="items mt-0">
                                        <tr>
                                            <td style="width: 50%; padding-left: 30px;">
                                                <span>To:</span><br>
                                                <strong style="font-size: 14px;">{{$supplier->name}}</strong><br>
                                                <small style="font-size: 8px">
                                                    {{$supplier->address}}<br>Mobile : <a href="#">{{$supplier->phone}} </a> Email :<a href="#" style="text-transform: lowercase;">{{$supplier->email}}</a><br>TIN : {{$supplier->tin}} VRN : {{$supplier->vrn}}<br>
                                                </small>
                                            </td>
                                            <td style="width: 50%; padding-right: 20px;">
                                                <table class="meta">
                                                    <tbody>
                                                        <tr>
                                                            <td class="meta-head" style="text-align: right; font-size: 14px;">{{trans('navmenu.date')}} : <b id="date">
                                                                {{date("d, M Y", strtotime($porder->created_at))}}</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="meta-head" style="text-align: right; font-size: 14px;">{{trans('navmenu.lpo_no')}}: <b id="date">{{ sprintf('%05d', $porder->order_no)}}</b></td>
                                                        </tr>
                                                    </tbody>
                                                </table>   
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class="items mt-0" style="width: 100%;">
                                        <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th class="desc">{{trans('navmenu.description')}}</th>
                                                    <th class="unit" style="text-align: center;">UOM</th>
                                                    <th class="qty" style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                                    @if(Auth::user()->can('view-purchase-cost'))
                                                    <th class="unit" style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                                    <th class="total" style="text-align: right;">{{trans('navmenu.total')}}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pitems as $key => $item)
                                                <tr>
                                                    <td> {{$key+1}} </td>
                                                    <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                                    <td class="unit" style="border-bottom: 1px solid #e0e0e0; text-align: center;">{{$item->basic_uom}}</td>
                                                    <td class="qty" style="border-bottom: 1px solid #e0e0e0; text-align: center;">{{number_format($item->qty)}}</td>
                                                    @if(Auth::user()->can('view-purchase-cost'))
                                                    <td class="unit" style="border-bottom: 1px solid #e0e0e0; text-align: center;">{{number_format($item->unit_cost)}}</td>
                                                    <td class="total" style="border-bottom: 1px solid #e0e0e0; text-align: right;">{{number_format($item->qty*$item->unit_cost)}}</td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                                @if(Auth::user()->can('view-purchase-cost'))
                                                <tr>
                                                    <td colspan="4"></td>
                                                    <th class="unit" style="text-transform: uppercase; text-align: right; border-top: 1px solid gray; border-bottom: 1px solid gray;">{{trans('navmenu.total')}}:</th>
                                                    <th class="total" style="border-top: 1px solid gray; border-bottom: 1px solid gray; text-align: right;">{{number_format($porder->amount)}}</th>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="invoice-footer mt-4">
                                        @if(!is_null($porder->comments))
                                        <div class="notice">
                                            <div>{{trans('navmenu.comments')}}:</div>
                                            <div>{{$porder->comments}}</div>
                                        </div>
                                        @endif
                                        <div class="thanks text-center" style="border-top: 1px solid gray; padding-top: 10px;">
                                            <p>
                                                {{trans('navmenu.created_by')}} : <b>{{$user->first_name}} {{$user->last_name}}</b>  {{trans('navmenu.signature')}} _________________ {{trans('navmenu.date')}}   _________________
                                            </p>
                                        </div>
                                        <div class="thanks text-center">
                                            <p>
                                                {{trans('navmenu.approved_by')}} : <b>{{$porder->approved_by}}</b>  {{trans('navmenu.signature')}} _________________ {{trans('navmenu.date')}}   _________________
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="#" onclick="javascript:savePdf()" class="btn btn-outline-success btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                        </div>
                    </div>
                </div>
                <div id="lpo-qty" role="tabpanel" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-1 print_invoice" id="print-order-qty">
                                <div class="col-md-12">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="text-align: left; padding-left: 15px;">
                                                @if(!is_null($company->logo_url))
                                                <figure>
                                                    <img class="invoice-logo" src="{{asset('storage/'.$company->logo_url)}}" alt="" width="250" style="border: 1px solid white;">
                                                </figure>
                                                @endif
                                            </td>
                                            <td>
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td colspan="2" style="text-align: right;">
                                                            <strong style="font-size: 14px;">{{$company->name}}</strong><br><span style="text-align: center;">({{$shop->name}})</span><br>
                                                            @if(!is_null($shop->short_desc))<small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br>@endif
                                                            <p>
                                                                @if(!is_null($shop->postal_address)){{$shop->postal_address}}@endif @if(!is_null($shop->physical_address)){{$shop->physical_address}}<br>@endif @if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}} {{$shop->country}} <br>  Tel: <b>{{$shop->tel}}</b> | <b>{{$shop->mobile}}</b> WhatsApp : <b>{{$shop->whatsapp}}</b><br> Email: <b>{{$shop->email}}</b> Website: <b>{{$shop->website}}</b><br> TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b>
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
                                                <td style="background: #037c1e; padding-left: 15px;  border-radius: 30px; text-align: center;">
                                                    <h6 class="mb-0 text-uppercase" style="color: #fff;">Purchase Order (LPO)</h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 border-bottom pb-4" style="border-bottom: 1px solid black;">
                                    <table class="items mt-0">
                                        <tr>
                                            <td style="width: 50%; padding-left: 30px;">
                                                <span>To:</span><br>
                                                <strong style="font-size: 14px;">{{$supplier->name}}</strong><br>
                                                <small style="font-size: 8px">
                                                    {{$supplier->address}}<br>Mobile : <a href="#">{{$supplier->phone}} </a> Email :<a href="#" style="text-transform: lowercase;">{{$supplier->email}}</a><br>TIN : {{$supplier->tin}} VRN : {{$supplier->vrn}}<br>
                                                </small>
                                            </td>
                                            <td style="width: 50%; padding-right: 20px;">
                                                <table class="meta">
                                                    <tbody>
                                                        <tr>
                                                            <td class="meta-head" style="text-align: right; font-size: 14px;">{{trans('navmenu.date')}} : <b id="date">
                                                                {{date("d, M Y", strtotime($porder->created_at))}}</b></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="meta-head" style="text-align: right; font-size: 14px;">{{trans('navmenu.lpo_no')}}: <b id="date">{{ sprintf('%05d', $porder->order_no)}}</b></td>
                                                        </tr>
                                                    </tbody>
                                                </table>   
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <table class="items mt-0" style="width: 100%;">
                                        <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th class="desc">{{trans('navmenu.description')}}</th>
                                                    <th class="unit" style="text-align: center;">UOM</th>
                                                    <th class="qty" style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $tqty = 0; ?>
                                                @foreach($pitems as $key => $item)
                                                <?php $tqty += $item->qty; ?>
                                                <tr>
                                                    <td> {{$key+1}} </td>
                                                    <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                                    <td class="unit" style="border-bottom: 1px solid #e0e0e0; text-align: center;">{{$item->basic_uom}}</td>
                                                    <td class="qty" style="border-bottom: 1px solid #e0e0e0; text-align: center;">{{number_format($item->qty)}}</td>
                                                </tr>
                                                @endforeach
                                                @if(Auth::user()->can('view-purchase-cost'))
                                                <tr>
                                                    <td></td>
                                                    <th colspan="2" class="unit" style="text-transform: uppercase; text-align: left; border-top: 1px solid gray; border-bottom: 1px solid gray;">{{trans('navmenu.total')}}:</th>
                                                    <th class="total" style="border-top: 1px solid gray; border-bottom: 1px solid gray; text-align: center;">{{$tqty}}</th>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="invoice-footer mt-4">
                                        @if(!is_null($porder->comments))
                                        <div class="notice">
                                            <div>{{trans('navmenu.comments')}}:</div>
                                            <div>{{$porder->comments}}</div>
                                        </div>
                                        @endif
                                        <div class="thanks text-center" style="border-top: 1px solid gray; padding-top: 10px;">
                                            <p>
                                                {{trans('navmenu.created_by')}} : <b>{{$user->first_name}} {{$user->last_name}}</b>  {{trans('navmenu.signature')}} _________________ {{trans('navmenu.date')}}   _________________
                                            </p>
                                        </div>
                                        <div class="thanks text-center">
                                            <p>
                                                {{trans('navmenu.approved_by')}} : <b>{{$porder->approved_by}}</b>  {{trans('navmenu.signature')}} _________________ {{trans('navmenu.date')}}   _________________
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="#" onclick="javascript:saveQtyPdf()" class="btn btn-outline-success btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function showHideContent(elem) {
            var qty = document.getElementById('lpo-qty');
            var val = document.getElementById('lpo-value');
            if (elem === 'show') {
                qty.style.display = 'block';
                val.style.display = 'none';
            }else{
                val.style.display = 'block';
                qty.style.display = 'none';
            }
        }
        function savePdf() {
            const element = document.getElementById("print-order");
            var filename = "<?php echo $title.'_no_'.$porder->grn_no.'_'.$porder->time_created; ?>";
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

        function saveQtyPdf() {
            const element = document.getElementById("print-order-qty");
            var filename = "<?php echo $title.'_no_'.$porder->grn_no.'_'.$porder->time_created; ?>";
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