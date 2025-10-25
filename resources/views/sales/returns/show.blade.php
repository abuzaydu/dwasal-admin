@extends('layouts.app')
    <script type="text/javascript">
            function detailUpdate(elem) {
        var b = document.getElementById('bankdetail');
        var m = document.getElementById('mobaccount');
        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
            if (elem.value === 'Bank') {
                m.style.display = 'none';
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            }else{
                m.style.display = 'none';
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        }else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            dpm.style.display = "none";
            slip.style.display = 'none'
            chq.style.display = 'none';
            expire.style.display = "none";
            m.style.display = 'block';
        }else{
            b.style.display = 'none';
            m.style.display = 'none';
            dpm.style.display = 'none';
            slip.style.display = "none";
            chq.style.display = "none";
            expire.style.display = "none";
        }
    }

    </script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('sales-returns') }}">Sales Returns</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
                <a href="#"  class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-bs-backdrop="static" data-bs-keyboard="false" style="margin-right: 2px; padding-top: 5px"><i class="bx bx-money"></i> Request Refund</a>
                <button onclick="javascript:savePdf()" class="btn btn-outline-success btn-sm" style="margin-left: 2px;">
                    <i class="fa fa-download"></i> {{trans('navmenu.download')}}
                </button>
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
                                        <h4 class="mb-0 text-uppercase" style="color: #fff;">{{trans('navmenu.sales_returns')}}</h4>
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
                                                <td style="border: none;" class="meta-head">Invoice No.</td>
                                                <td style="border: none;"><b>{{ sprintf('%06d', $salereturn->invoice_no)}}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="border: none;" class="meta-head">Return Date</td>
                                                <td style="border: none;"><b id="date">{{ date('d F, Y', strtotime($salereturn->return_date)) }}</b></td>
                                            </tr>
                                            <tr>
                                                <td style="border: none;" class="meta-head">Created By</td>
                                                <td style="border: none;"><b class="date">{{ $salereturn->first_name }} {{ $salereturn->last_name }}</b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 20px; border: 1px solid gray;">
                                                    <span>From: </span><br>
                                                    <strong style="font-size: 14px;">{{$salereturn->name}}</strong><br>
                                                    <small>
                                                        {{$salereturn->ph_address}}<br>
                                                        {{$salereturn->po_address}}<br>
                                                        Mobile : <a href="#">{{$salereturn->phone}} </a> Email :<a href="#" style="text-transform: lowercase;">{{$salereturn->email}}</a><br>
                                                        TIN : {{$salereturn->tin}} 
                                                        VRN : {{$salereturn->vrn}}<br>
                                                    </small>
                                                </td>
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
                                    <th style="border-bottom: 1px solid gray;" class="unit">Unit price</th>
                                    <th style="border-bottom: 1px solid gray; text-align: right;" class="total" >Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total = 0; $discount = 0; $tax = 0; ?>
                                @foreach($sritems as $key => $item)
                                <?php $total += $item->price; $discount += $item->discount; $tax += $item->tax_amount; ?>
                                <tr>
                                    <td style="border-bottom: 1px solid #e0e0e0;"> {{$key+1}} </td>
                                    <td class="desc" style="border-bottom: 1px solid #e0e0e0;">@if(!is_null($item->product_code)){{$item->product_code}} - @endif{{$item->name}}</td>
                                    <td class="qty" style="border-bottom: 1px solid #e0e0e0; text-align: center;">{{number_format($item->quantity)}}</td>
                                    <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->retail_price)}}</td>
                                    <td class="total" style="border-bottom: 1px solid #e0e0e0; text-align: right;">{{number_format($item->price, 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td style="border: none;" colspan="2"></td>
                                    <td style="border: none; text-align: right;" class="unit" colspan="2" ><b>{{trans('navmenu.total')}} (Excl.Tax)</b> :</td>
                                    <td style="border: none; text-align: right;" class="total" ><b>{{number_format($total, 2, '.', ',')}}</b></td>
                                </tr>
                                <tr>
                                    <td style="border: none;" colspan="2"></td>
                                    <td style="border: none; text-align: right;" class="unit" colspan="2" ><b>Discount</b> :</td>
                                    <td style="border: none; text-align: right;" class="total" ><b>{{number_format($discount, 2, '.', ',')}}</b></td>
                                </tr>

                                @if($settings->is_vat_registered)
                                <tr>
                                    <td style="border: none;" colspan="2"></td>
                                    <td style="border: none; text-align: right;" class="unit" colspan="2" ><b>{{trans('navmenu.vat')}} ({{number_format($settings->tax_rate)}}%)</b> :</td>
                                    @if($tax > 0)
                                    <td style="border: none; text-align: right;" class="total" ><b>{{number_format($tax, 2, '.', ',')}}</b></td>
                                    @else
                                    <td style="border: none; text-align: right;" class="total" >0</td>
                                    @endif
                                </tr>
                                @endif
                                <tr>
                                    <td style="border: none;" colspan="2"></td>
                                    <th style="border: none; text-align: right;" class="unit" colspan="2"><b>{{trans('navmenu.total')}} (Inc.Tax)</b> :</th>
                                    <th style="border: none; text-align: right;" class="total" ><b>{{number_format(($total-$discount)+$tax, 2, '.', ',')}}</b></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="invoice-footer">
                        <div class="notice" style="border-bottom">
                            <div>REASON:</div>
                            <div>{{$salereturn->reason}}</div>
                        </div>
                    </div>
                </div>
                <div id="editor"></div>          
            </div>
        </div>
    </div>

     <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="row g-3" method="POST" action="{{ route('refund-requests.store') }}">
                    <div class="modal-body row">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{$salereturn->customer_id}}">
                        <input type="hidden" name="an_sale_id" value="{{$salereturn->an_sale_id}}">
                        <div class="col-md-3">
                            <label class="form-label">Date</label>
                            <div class="inner-addon left-addon"> 
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="date" id="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.amount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="refund_amt" required placeholder="Enter Refund Amount" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control form-control-sm mb-1" rows="1" name="remarks" placeholder="Enter Remarks (Optional)...."></textarea>
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
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo 'Sale Return_'.'_'.$salereturn->created_at; ?>";
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

    <link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
    <script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>