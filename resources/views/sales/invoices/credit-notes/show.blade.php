@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('an-sales') }}">Invoices</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                <a href="{{ route('credit-notes.edit', encrypt($creditnote->id))}}" class="btn btn-primary btn-sm" style="margin: 5px;"><i class="fa fa-edit"></i> Update</a>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-md-9 mx-auto"> 
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-cn">
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center; background:  #2874a6;">
                                            <h4 class="mb-0 text-uppercase" style="color: #fff;">CREDIT NOTE</h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 border-bottom pb-0">
                            <table class="items mt-0">
                                <tr>
                                    <td style="width: 40%; text-align: right; padding-left: 20px;">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" width="200">
                                        </figure>
                                        @endif
                                    </td>
                                    <td style="width: 60%;">
                                        <strong style="font-size: 14px;">{{$shop->name}}.</strong><br>
                                        <small style="font-size: 12px;">{{$shop->short_desc}}</small><br> <small>{{$shop->postal_address}} {{$shop->physical_address}} {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br> Email: <b>{{$shop->email}}</b><br> Tel: <b>{{$shop->tel}}</b> Phone: <b>{{$shop->mobile}}</b><br>TIN: <b>{{$shop->tin}}</b> VRN: <b>{{$shop->vrn}}</b></small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12 customer mt-2 mb-0">
                            <div class="row">
                                <div class="col-md-7" style="padding-left: 30px;">
                                    {{trans('navmenu.customer_name')}} : <b>{{$creditnote->name}}</b><br>
                                        {{trans('navmenu.customer_id')}} : <b>{{ sprintf('%03d', $creditnote->cust_no)}}</b><br>
                                     Email :<a href="#" style="text-transform: lowercase;">{{$creditnote->email}}</a>
                                    Tel : <a href="#">{{$creditnote->phone}}</a><br>
                                    TIN : {{$creditnote->tin}} 
                                    VRN : {{$creditnote->vrn}}<br>
                                </div>
                                <div class="col-md-5">
                                    <table class="meta">
                                        <tbody>
                                            <tr>
                                                <td class="meta-head">CN No.</td>
                                                <td><b>{{ sprintf('%04d', $creditnote->credit_note_no)}}</b></td>
                                            </tr>
                                            <tr>
                                                <td class="meta-head">Date</td>
                                                <td><b id="date">{{ date('d F, Y', strtotime($creditnote->created_at)) }}</b></td>
                                            </tr>
                                            <tr>
                                                <td class="meta-head">Invoice No.</td>
                                                <td><b>{{ sprintf('%04d', $creditnote->invoice_no)}}</b></td>
                                            </tr>
                                            <tr>
                                                <td class="meta-head">Invoice Date</td>
                                                <td><b id="date">{{ date('d F, Y', strtotime($creditnote->time_created)) }}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table class="items" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="desc">{{trans('navmenu.description')}}</th>
                                        <th class="qty">{{trans('navmenu.invoice_no')}}</th>
                                        <th class="total">{{trans('navmenu.amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: left;">{{$creditnote->reason}}</td>
                                        <td class="qty">{{ sprintf('%04d', $creditnote->inv_no)}}</td>
                                        <td class="total">{{number_format($creditnote->amount)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="no-break">
                                <table class="grand-total">
                                    <tbody>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty"></td>
                                            <td class="unit">SUBTOTAL:</td>
                                            <td class="total">{{number_format($creditnote->amount)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="unit" colspan="2">GRAND TOTAL ({{$creditnote->currency}}):</td>
                                            <td class="total">{{number_format($creditnote->amount)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="invoice-footer col-md-12">
                            <div class="thanks">Thank you!</div>
                            <div class="end">This is an electronic Credit Note and is valid without the signature and seal.</div>
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
        function printDiv(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;
            //File name for printed ducument
            document.title = "<?php echo 'Credit Note_'.sprintf('%06d', $creditnote->credit_note_no).'_'.$creditnote->created_at; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
          const element = document.getElementById("print-cn");
          var filename = "<?php echo 'Credit Note_'.sprintf('%06d', $creditnote->credit_note_no).'_'.$creditnote->created_at; ?>";
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