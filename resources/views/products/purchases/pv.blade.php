@extends('layouts.inv')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm " style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-voucher">
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center; background:  #2874a6;">
                                            <h4 class="mb-0 text-uppercase" style="color: #fff;">Payment Voucher</h4>
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
                                                    <td class="meta-head" style="text-align: right;">{{trans('navmenu.pv_no')}}   : <strong>{{ sprintf('%05d', $voucher->pv_no)}}</strong></td>
                                                </tr>
                                                <tr>
		                          					<td class="meta-head" style="text-align: right;">Mode of Payment : <b>{{$voucher->payment_mode}}</b></td>
		                          				</tr>
		                          				<tr>
		                          					<td class="meta-head" style="text-align: right;"> @if($voucher->payment_mode == 'Cheque') Cheque No : <b>{{$voucher->cheque_no}}</b>@endif</td>
		                          				</tr>
		                          					<td class="meta-head" style="text-align: right;">Date   : <b>{{date("d M, Y", strtotime($voucher->created_at))}}</b></td>
                                                </tr>
                                            </tbody>
                                        </table>    
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 50%; padding-left: 30px;">
                                        <span>From: </span><br>
                                        <strong style="font-size: 14px;">{{$shop->name}}.</strong><br>
                                        <small style="font-size: 8px !important;">{{$shop->short_desc}}</small><br> <small>{{$shop->postal_address}} {{$shop->physical_address}} <br>@if(!is_null($shop->street)){{$shop->street}},@endif @if(!is_null($shop->district)){{$shop->district}},@endif {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                    </td>
                                    <td style="width: 50%; border: 1px solid gray;">
                                        <span>To:</span><br>
                                        <strong style="font-size: 14px;">{{$supplier->name}}</strong><br>
                                        <small style="font-size: 8px">
                                            {{$supplier->address}}<br>Mobile : <a href="#">{{$supplier->phone}} </a> Email :<a href="#" style="text-transform: lowercase;">{{$supplier->email}}</a><br>TIN : {{$supplier->tin}} VRN : {{$supplier->vrn}}<br>
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
		                  	<table class="list-items mt-2" style="width: 100%">
			                    <thead>
			                      	<tr>
				                        <th style="text-align: left;">{{trans('navmenu.pay_type')}}</th>
				                        <th class="desc">{{trans('navmenu.description')}}</th>
				                        <th class="qty" style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
				                        <th class="total" style="text-align: right;">{{trans('navmenu.amount')}}</th>
			                      	</tr>
			                    </thead>
			                    <tbody>
                                    @if($voucher->trans_ob_amount > 0)
                                    <tr>
                                        <td style="text-align: left;">{{trans('navmenu.invoice_payment')}}</td>
                                        <td class="desc" style="">{{trans('navmenu.pay_for')}} {{trans('navmenu.opening_balance')}} {{trans('navmenu.paid_on')}} {{date('d.m.Y', strtotime($voucher->date))}}</td>
                                        <td class="qty" style="">OB</td>
                                        <td class="total" style="text-align: right;">{{number_format($voucher->trans_ob_amount)}}</td>
                                    </tr>
                                    @endif
                                    @if($voucher->trans_credit_amount > 0)
                                    <tr>
                                        <td style="text-align: left;">{{trans('navmenu.other_debts')}}</td>
                                        <td class="desc" style="">{{trans('navmenu.pay_for')}} {{trans('navmenu.other_debts')}} {{trans('navmenu.paid_on')}} {{date('d.m.Y', strtotime($voucher->date))}}</td>
                                        <td class="qty" style="">COC</td>
                                        <td class="total" style="text-align: right;">{{number_format($voucher->trans_credit_amount)}}</td>
                                    </tr>
                                    @endif
			                      	@foreach($ppays as $key => $pay)
			                      	<tr>
				                        @if($shop->subscription_type_id == 2)
				                        <td style="text-align: left;">{{trans('navmenu.invoice_payment')}}</td>
				                        @else
				                        <td style="text-align: left;">{{trans('navmenu.purchase_payments')}}</td>
				                        @endif
				                        <td class="desc">{{trans('navmenu.pay_for')}} {{date('d M, Y', strtotime($pay->date))}} {{trans('navmenu.paid_on')}} {{date('d.m.Y', strtotime($pay->pay_date))}}</td>
				                        <td class="qty" style="text-align: center;">
				                        	@if(!is_null($pay->invoice_no))
				                        	{{ sprintf('%04d', $pay->invoice_no)}}
				                        	@else
				                        	-
				                        	@endif
				                        </td>
				                        <td class="total" style="text-align: right;">{{number_format($pay->amount)}}</td>
				                    </tr>
			                      	@endforeach
                                            <tr class="blank_row">
                                        <td style="border-bottom: 1px solid black;" class="desc"></td>
                                        <td style="border-bottom: 1px solid black; text-align: center;" class="qty"></td>
                                        <td style="border-bottom: 1px solid black;" class="unit"></td>
                                        <td style="border-bottom: 1px solid black;" class="unit"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="mt-0 mb-4" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;"></th>
                                        <th style="width: 30%;"></th>
                                        <th style="width: 20%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="border: none;"></td>
                                        <td class="unit" style="border-bottom: 1px solid gray; text-align: right;"><b>GRAND TOTAL:</b></td>
                                        <td class="total" style="border-bottom: 1px solid gray; text-align: right;"><b>{{number_format($voucher->payment)}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                            <h6 style="padding-left: 20px;"><span>Amount in Words : <b>{{$amount_in_words}}</b></span></h6>
                        </div>

                        <div class="invoice-footer" style="text-align: center;">
                          <h6><span class="thanks">Authorizations</span></h6>

                          <table style="width: 100%; padding: 10px;">
                            <tbody>
                              <tr>
                                <td style="text-align: right; width: 20%; padding: 5px;">Prepared By : </td>
                                <td style=" width: 20%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"><b>{{$user->first_name}} {{$user->last_name}}</b></td>
                                <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.signature')}} : </td>
                                <td style=" width: 15%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"></td>
                                <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.date')}} : </td>
                                <td style=" width: 15%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"></td>
                              </tr>
                              <tr>
                                <td style="text-align: right; width: 15%; padding: 5px;">Authorized By : </td>
                                <td style=" width: 25%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"></td>
                                <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.signature')}} : </td>
                                <td style=" width: 15%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"></td>
                                <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.date')}} : </td>
                                <td style=" width: 15%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"></td>
                              </tr>
                              <tr>
                                <td style="text-align: right; width: 15%; padding: 5px;">Approved By : </td>
                                <td style=" width: 25%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"></td>
                                <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.signature')}} : </td>
                                <td style=" width: 15%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"></td>
                                <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.date')}} : </td>
                                <td style=" width: 15%; padding: 5px; border-bottom: dotted 1px #e1f5fe;"></td>
                              </tr>
                            </tbody>
                          </table>
                          @if($settings->show_end_note)
                          <div class="end" style="margin-top: 15px;">This is an electronic Payment Voucher and is valid without the signature and seal.</div>
                          @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function printDiv(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;
            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;
            //File name for printed ducument
            document.title = "<?php echo $title.'_no_'.$voucher->pv_no.'_'.$voucher->created_at; ?>";
            //Print Page
            window.print();
            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
          	const element = document.getElementById("print-voucher");
          	var filename = "<?php echo $title.'_no_'.$voucher->pv_no.'_'.$voucher->created_at; ?>";
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