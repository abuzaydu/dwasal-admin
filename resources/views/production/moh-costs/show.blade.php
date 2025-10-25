@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-7 col-md-7 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{ url('moh-costs') }}">MOH Costs</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-5 col-md-5 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class=" col-md-12 mx-auto"> 
        <div class="card">
            <div class="card-body">
                
                <div class="row g-1 print_invoice" id="inv-content">
                    <div class="col-md-12">
                        <table class="table mb-1">
                            <tbody>
                                <tr>
                                    <td colspan="2" style="text-align: center; background:  #2874a6;">
                                        <h4 class="mb-0 text-uppercase" style="color: #fff;">{{$title}}</h4>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 border-bottom pb-0">
                        <table class="items mt-0">
                            <tr>
                                <td style="width: 60%; padding-left: 10%;">
                                    @if(!is_null($shop->logo_location))
                                    <figure>
                                        <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="250" style="border: 1px solid gray;">
                                    </figure>
                                    @endif
                                    <strong style="font-size: 14px;">{{$shop->name}}.</strong><br>
                                    <small style="font-size: 12px;">{{$shop->short_desc}}</small><br> <small>{{$shop->postal_address}} {{$shop->physical_address}} <br>{{$shop->street}} {{$shop->district}}, {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                </td>
                                <td style="width: 40%">
                                    <table style="width: 100%">
                                        <tbody>
                                            <tr>
                                                <td style="border: none;" class="meta-head">MOH No.</td>
                                                <td style="border: none;"><b>{{ sprintf('%04d', $moh->moh_no)}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="border: none;" class="meta-head">Date</td>
                                                <td style="border: none;"><b id="date">{{ date('d F, Y H:i:s', strtotime($moh->date)) }}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="border: none;" class="meta-head">Created By</td>
                                                <td style="border: none;"><b class="date">{{ $moh->first_name }} {{ $moh->last_name }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <table class="items mt-4" style="width: 100%; border: 1px solid gray;">
                            <thead>
                                <tr>
                                    <th style="border-bottom: 1px solid gray;">#</th>
                                    <th style="border-bottom: 1px solid gray;" class="desc">Description</th>
                                    <th style="border-bottom: 1px solid gray; text-align: center;" class="qty">Quantity</th>
                                    <th style="border-bottom: 1px solid gray;" class="unit">Unit Cost</th>
                                    <th style="border-bottom: 1px solid gray; text-align: right;" class="total" >Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0; ?>
                                @foreach($items as $key => $item)
                                <?php $total += $item->total; ?>
                                <tr>
                                    <td style="border-bottom: 1px solid #e0e0e0;"> {{$key+1}} </td>
                                    <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                    <td class="qty" style="border-bottom: 1px solid #e0e0e0; text-align: center;">{{$item->quantity+0}}</td>
                                    <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->unit_cost)}}</td>
                                    <td class="total" style="border-bottom: 1px solid #e0e0e0; text-align: right;">{{number_format($item->total, 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td style="border: none;" colspan="2"></td>
                                    <th style="border: none; text-align: right;" class="unit" colspan="2"><b>{{trans('navmenu.total')}} </b> :</th>
                                    <th style="border: none; text-align: right;" class="total" ><b>{{number_format($total, 2, '.', ',')}}</b></th>
                                </tr>
                                <tr>
                                    <td style="border: none;" colspan="2"></td>
                                    <td style="border: none; text-align: right;" class="unit" colspan="2"><b>{{trans('navmenu.paid')}} </b> :</td>
                                    <td style="border: none; text-align: right;" class="total" ><b>{{number_format($moh->amount_paid, 2, '.', ',')}}</b></td>
                                </tr>
                                <tr>
                                    <td style="border: none;" colspan="2"></td>
                                    <td style="border: none; text-align: right;" class="unit" colspan="2"><b>{{trans('navmenu.unpaid')}} </b> :</td>
                                    <td style="border: none; text-align: right;" class="total" ><b>{{number_format($total-$moh->amount_paid, 2, '.', ',')}}</b></td>
                                </tr>
                                <tr>
                                    <td style="border: none;" colspan="2"></td>
                                    <td colspan="3" style="padding-top: 10px;"><span>Payment already done</span>
                                        <table class="mt-1" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th style="border-top: 1px solid black; border-bottom: 1px solid black;">Date</th>
                                                    <th style="border-top: 1px solid black; border-bottom: 1px solid black;">Amount</th>
                                                    <th style="border-top: 1px solid black; border-bottom: 1px solid black;">Method</th>
                                                    <th style="border-top: 1px solid black; border-bottom: 1px solid black;">PV No.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($payments as $pay)
                                                <tr>
                                                    <td style="border-bottom: 1px solid black;">{{ date('d/m/Y', strtotime($pay->pay_date)) }}</td>
                                                    <td style="border-bottom: 1px solid black;">{{ number_format($pay->paid_amt, 2, '.', ',') }}</td>
                                                    <td style="border-bottom: 1px solid black;">{{$pay->pay_mode}}</td>
                                                    <td style="border-bottom: 1px solid black;">{{ sprintf('%05d', $pay->pv_no)}}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="invoice-footer">
                        <div class="notice" style="border-bottom">
                            <div>Remarks:</div>
                            <div>{{$moh->remarks}}</div>
                        </div>
                    </div>
                </div>
                <div id="editor"></div>          
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
          const element = document.getElementById("inv-content");
          var filename = "<?php echo 'MOH Cost_'.'_'.sprintf('%04d', $moh->moh_no); ?>";
          var opt = {
              margin:       0.5,
              filename:     filename+'.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
              jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).toPdf().save();
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            //     window.open(pdf.output('bloburl'), '_blank');
            // });
        }
    </script>