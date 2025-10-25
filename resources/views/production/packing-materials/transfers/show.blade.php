@extends('layouts.prod')
<?php $shop = App\Models\Shop::find(1); ?>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{url('pm-transfers')}}">PM Transfers</a></li>
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
                    <div class="btn-group float-end">
                        @if(Auth::user()->can('receive-stock-transfer') && is_null($pmt->received_at))
                        
                        <a href="{{ url('receive-pm-transfer/'.encrypt($pmt->id))}}" class="btn btn-outline-success btn-sm float-end" style="margin: 5px;"><i class="fa fa-check"></i> Receive Order</a>
                        @endif
                        @if($pmt->status != 'Received')
                        <a href="{{route('pm-transfers.edit', encrypt($pmt->id))}}" class="btn btn-outline-info btn-sm float-end" style="margin: 5px;"><i class="fa fa-edit" ></i> Update</a>
                        @endif
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                    </div>
                </div>
                <div class="card-body">
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
                                        @if(!is_null($company->logo_url))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/clogos/'.$company->logo_url)}}" alt="" width="150" style="border: 1px solid gray;">
                                        </figure>
                                        @endif
                                        <strong style="font-size: 14px;">{{$company->name}}.</strong><br>
                                        <small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br> <small>{{$shop->postal_address}} {{$shop->physical_address}} <br>@if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                    </td>
                                    <td style="width: 50%;">
                                        <table class="mb-0" style="width: 100%;">
                                            <tbody>
                                                <tr>
                                                    <td>PMT No :</td>
                                                    <td><b>{{ sprintf('%04d', $pmt->pmt_no)}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.transfer_date')}} :</td>
                                                    <td><b>{{date("d, M Y", strtotime($pmt->pm_transfer_date))}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.source_shop')}}: </td>
                                                    <td><b>{{$source->name}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.destin_shop')}}</td>
                                                    <td><b>@if(!is_null($destin)){{$destin->name}}@endif</b></td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.reason')}}:</td>
                                                    <td><b>{{$pmt->reason}}</b></td>
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
                                        <th style="text-align: left;">{{trans('navmenu.item_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.source_stock')}}</th>
                                        @if($pmt->status == 'Received')
                                        <th style="text-align: center;">{{trans('navmenu.destin_stock')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.transfer_qty')}}</th>
                                        @if($pmt->status == 'Received')
                                        <th style="text-align: center;">Received Qty</th>
                                        <th style="text-align: center;">Variation</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $key => $item)
                                    <tr>
                                        <td style="">{{$item->name}}</td>
                                        <td style="text-align: center;">{{number_format($item->src_qty)}}</td>
                                        @if($pmt->status == 'Received')
                                        <td style="text-align: center;">{{number_format($item->des_qty)}}</td>
                                        @endif
                                        <td style="text-align: center;">{{number_format($item->qty)}}</td>
                                        @if($pmt->status == 'Received')
                                        <td style="text-align: center;">{{number_format($item->rec_qty)}}</td>
                                        <td style="text-align: center;">{{$item->qty-$item->rec_qty}}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 clearfix order-bottom " style="margin-top: 15px;">
                            <div class="issuer" style="width: 50%; float: left; padding-left: 55px;">
                                <table>
                                    <tr>
                                        <td style="font-size: 8px !important;">
                                            <span style="text-transform: uppercase; font-weight: bold;">{{trans('navmenu.transfer_by')}}</span><br>
                                            {{trans('navmenu.name')}} : <strong>@if(!is_null($user)){{$user->first_name}} {{$user->last_name}}@endif</strong><br>
                                            Date <strong>@if(!is_null($pmt->created_at)){{date('d M Y H:i A', strtotime($pmt->created_at))}}@endif</strong><br>
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
                                            {{trans('navmenu.name')}} : <strong>{{$pmt->receiver}}</strong><br>
                                            Date <strong>@if(!is_null($pmt->received_at)){{date('d M Y H:i A', strtotime($pmt->received_at))}}@endif</strong><br>
                                            {{trans('navmenu.signature')}} <strong>.....................</strong>
                                        </td>
                                    </tr>
                                </table>
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
        function confirmReceive() {
            document.getElementById('receive-form').submit();
        }
        function savePdf() {
          const element = document.getElementById("print-st");
          var filename = "<?php echo $title.'_'.$pmt->created_at; ?>";
          var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).toPdf().save();          
        }
    </script>