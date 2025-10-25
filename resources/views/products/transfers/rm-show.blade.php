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
                <div class="card-body">
                    <div class="border rounded p-2 mb-1">
                        @if($transorder->status != 'Received')
                        <a href="{{ url('edit-item-to-rm/'.encrypt($transorder->id))}}" class="btn btn-outline-info btn-sm" style="margin: 5px;"><i class="fa fa-edit" ></i> Update</a>
                        @endif
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                    </div>
                    <div class="row g-1 print_invoice" id="print-st">
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center; background:  #2874a6;">
                                            <h6 class="mb-0 text-uppercase" style="color: #fff;">{{$title}} @if($transorder->is_request) Request @elseif($transorder->is_return) Return @else Normal @endif</h6>
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
                                        <th style="text-align: left;">{{trans('navmenu.item_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.source_stock')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.transfer_qty')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderitems as $key => $item)
                                    <tr>
                                        <td style="">@if(!is_null($item->product_code)){{$item->product_code}} - @endif {{$item->name}}</td>
                                        <td style="text-align: center;">{{$item->source_stock+0}}</td>
                                        <td style="text-align: center;">{{$item->quantity+0}}</td>
                                        <td style="text-align: center;">{{number_format($item->source_unit_cost, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p class="mb-1 text-uppercase text-center">End Raw Material</p>
                            <table class="list-items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">{{trans('navmenu.item_name')}}</th>
                                        <th style="text-align: center;">Quantity</th>
                                        <th style="text-align: center;">Unit Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rmitem as $key => $item)
                                    <tr>
                                        <td style="">{{$item->name}}</td>
                                        <td style="text-align: center;">{{number_format($item->qty+0)}}</td>
                                        <td style="text-align: center;">{{number_format($item->unit_cost, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 clearfix order-bottom " style="margin-top: 15px;">
                            <div class="issuer" style="width: 100%; float: left; padding-left: 55px;">
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
          const element = document.getElementById("print-st");
          var filename = "<?php echo trans('navmenu.title').'_'.$transorder->created_at; ?>";
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
            //     window.open(pdf.output('bloburl'), '_blank');
            // });
          
        }
    </script>