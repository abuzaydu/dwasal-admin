@extends('layouts.prod')

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">{{$page}}</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="fa fa-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
        </div>
    </div>

<div class="row">
	<div class="col-md-12">
	  <div class="card radius-10">
	    <div class="card-header">
	      <div class="btn-group float-start">
	        <a href="#" onclick="javascript:savePdf()" class="btn btn-warning " style="margin: 5px;"><i class="fa fa-download"></i> Download PDF</a>
	        <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn-info" style="margin: 5px;"><i class="fa fa-printer"></i> Print</a>
	      </div>
	    </div>
	    <div class="card-body">
	          <div id="inv-content">
	              <div class="clearfix invoice-header">
	                @if(!is_null($shop->logo_location))
	                  <figure>
	                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
	                  </figure>
	                @endif
	                <div class="company-address">
	                  <h2 class="title">{{$shop->display_name}} <br>
	                  <small style="font-size: 12px;">{{$shop->short_desc}}</small></h2>
	                  <p>
	                    {{$shop->postal_address}} {{$shop->physical_address}}
	                    {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br>
	                    E-Mail: <a href="#">{{$shop->email}}</a>
	                    Tel: <a href="#">{{$shop->mobile}}</a>
	                    Web: <a href="#">{{$shop->website}}</a>
	                  </p>
	                </div>
	                <div class="company-contact">                    
	                  TIN : {{$shop->tin}}<br>
	                  VRN : {{$shop->vrn}}
	                </div>
	              </div>

	              <div class="invoice-content">

	                <div class="title text-center"><h4>Payment Voucher</h4></div>

	                <div class="details clearfix">
	                  <div class="client float-start">
	                    <!-- <h4></h4> -->
	                    <p>
	                      Payment To : <b>{{$supplier->name}}</b><br>
	                      {{trans('navmenu.address')}} : <b>{{$supplier->address}}</b><br>
	                      {{trans('navmenu.contact_number')}} : <b>{{$supplier->contact_no}}</b><br>
	                      {{trans('navmenu.email')}} : <b>{{$supplier->email}}</b>
	                    </p>
	                  </div>
	                  <div class="data float-end">
	                    <div class="date">
	                      <p style="font-size: 14px; text-transform: uppercase;">
	                        {{trans('navmenu.pv_no')}}   : <strong>{{ sprintf('%05d', $voucher->pv_no)}}</strong><br>
	                        Mode of Payment : <b>{{$voucher->payment_mode}}</b><br>
	                        @if($voucher->payment_mode == 'Cheque')
	                        Cheque No : <b>{{$voucher->cheque_no}}</b><br>
	                        @endif
	                        Date   : <b>{{date("d M, Y", strtotime($voucher->created_at))}}</b><br>
	                      </p>
	                    </div>
	                  </div>
	                </div>

	                <table border="0" cellspacing="0" cellpadding="0">
	                  <thead>
	                    <tr>
	                      <th>#</th>
	                      <th style="text-align: left;">{{trans('navmenu.pay_type')}}</th>
	                      <th class="desc">{{trans('navmenu.description')}}</th>

	                      @if($shop->subscription_type_id == 2)
	                      <th class="qty">{{trans('navmenu.invoice_no')}}</th>
	                      @else
	                      <th class="qty">{{trans('navmenu.purchase_no')}}</th>
	                      @endif
	                      <!-- <th class="unit">{{trans('navmenu.currency')}}</th> -->
	                      <th class="total">{{trans('navmenu.amount')}}</th>
	                    </tr>
	                  </thead>
	                  <tbody>
	                    @foreach($ppays as $key => $pay)
	                    <tr>
	                      <td> {{$key+1}} </td>
	                      <td style="text-align: left;">{{trans('navmenu.invoice_payment')}}</td>
	                      <td class="desc"><h4>{{trans('navmenu.pay_for')}} {{date('d M, Y', strtotime($pay->date))}} {{trans('navmenu.paid_on')}} {{date('d.m.Y', strtotime($pay->pay_date))}}</h4></td>
	                      <td class="qty">{{ sprintf('%04d', $pay->invoice_no)}}</td>
	                      <!-- <td class="unit">{{$settings->currency}}</td> -->
	                      <td class="total">{{number_format($pay->amount)}}</td>
	                    </tr>
	                    @endforeach
	                  </tbody>
	                </table>
	                <div class="no-break">
	                  <table class="grand-total">
	                    <tbody>
	                      <tr>
	                        <td class="desc"></td>
	                        <td class="qty"></td>
	                        <td class="unit">SUBTOTAL:</td>
	                        <td class="total">{{number_format($voucher->payment)}}</td>
	                      </tr>
	                      <tr>
	                        <td class="desc"></td>
	                        <td class="unit" colspan="2">GRAND TOTAL ({{$settings->currency}}):</td>
	                        <td class="total">{{number_format($voucher->payment)}}</td>
	                      </tr>
	                    </tbody>
	                  </table>
	                </div>

	                <h6 style="padding-left: 20px;"><span>Amount in Words : <b>{{$amount_in_words}}</b></span></h6>
	              </div>

	              <div class="invoice-footer" style="text-align: center;">
	                <h6><span class="thanks">Authorizations</span></h6>

	                <table style="width: 100%; padding: 10px;">
	                  <tbody>
	                    <tr>
	                      <td style="text-align: right; width: 20%; padding: 5px;">Prepared By : </td>
	                      <td style=" width: 20%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"><b>{{$user->first_name}} {{$user->last_name}}</b></td>
	                      <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.signature')}} : </td>
	                      <td style=" width: 15%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"></td>
	                      <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.date')}} : </td>
	                      <td style=" width: 15%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"></td>
	                    </tr>
	                    <tr>
	                      <td style="text-align: right; width: 15%; padding: 5px;">Authorised By : </td>
	                      <td style=" width: 25%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"></td>
	                      <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.signature')}} : </td>
	                      <td style=" width: 15%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"></td>
	                      <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.date')}} : </td>
	                      <td style=" width: 15%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"></td>
	                    </tr>
	                    <tr>
	                      <td style="text-align: right; width: 15%; padding: 5px;">Approved By : </td>
	                      <td style=" width: 25%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"></td>
	                      <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.signature')}} : </td>
	                      <td style=" width: 15%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"></td>
	                      <td style="text-align: right; width: 15%; padding: 5px;">{{trans('navmenu.date')}} : </td>
	                      <td style=" width: 15%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"></td>
	                    </tr>
	                  </tbody>
	                </table>
	                <div class="notice" style="margin-top: 25px;">
	                  <!-- <p>
	                    <span>_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</span><br>
	                    <span><b>{{trans('navmenu.cashier_signature')}}</b></span>
	                  </p> -->
	                </div>
	                <div class="end" style="margin-top: 15[x;">This is an electronic Payment Voucher and is valid without the signature and seal.</div>
	              </div>
	            </div>
	            <div id="editor"></div>
	        </div>
	  </div>
	</div>
</div>

@endsection

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
          const element = document.getElementById("inv-content");
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