@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
<div class="row">
  <div class="col-md-12 ms-auto">
  <div class="card">
          <div class="card-body">
            <div id="invoice">
              <div class="toolbar hidden-print">
                <div class="text-end">
                  <div class="btn-group">
                    <a href="#" onclick="javascript:savePdf()" class="btn bg-warning " style="margin: 5px;"><i class="fa fa-download"></i> Download PDF</a>
                    <a href="#" onclick="javascript:printDiv('inv-content')" class="btn bg-info " style="margin: 5px;"><i class="fa fa-print"></i> Print</a>
                    <a href="{{ route('rm-purchases.edit' , encrypt($purchase->id))}}" class="btn bg-primary" style="margin: 5px;"><i class="fa fa-edit"></i> Update</a>
                  </div>
                </div>
                <hr/>
              </div>
              
            <div id="inv-content">
              <div class="invoice overflow-auto">
                <div style="min-width: 600px">
                  <header>
                    <div class="row">
                      @if(!is_null($shop->logo_location))
                      <div class="col">
                        <a href="javascript:;">
                          <img src="assets/images/logo-icon.png" width="80" alt="" />
                        </a>
                      </div>
                      @endif
                      <div class="col company-details">
                         <h2 class="name">{{$shop->name}} <br>
                          <small style="font-size: 12px;">{{$shop->short_desc}}</small></h2>
                          <div>
                            {{$shop->postal_address}} {{$shop->physical_address}}
                            {{$shop->street}} {{$shop->district}}, {{$shop->city}}
                          </div>
                           <div>E-Mail: <a href="#">{{$shop->email}}</a></div>
                           <div>Tel: <a href="#">{{$shop->mobile}}</a></div> 
                           <div>Web: <a href="#">{{$shop->website}}</a></div>
                           <div>TIN : {{$shop->tin}}</div>
                           <div>VRN : {{$shop->vrn}}</div>
                      </div>
                    </div>
                  </header>
                  <main>
                    <div class="text-center"><h3>{{trans('navmenu.grn')}}</h3></div>
                    <div class="row contacts">
                      <div class="col invoice-to">
                        <div class="text-gray-light">{{trans('navmenu.from')}} (SUPPLIE):</div>
                        @if(!is_null($supplier))
                        <h2 class="to">{{$supplier->name}}</h2>
                        <div class="address">{{$supplier->address}}</div>
                        <div class="email"><a href="mailto:{{$supplier->email}}">{{$supplier->email}}</a>
                        </div>
                        @else
                        <div class="to">{{trans('navmenu.unknown')}}</div>
                        @endif
                      </div>
                      <div class="col invoice-details">
                        <h1 class="invoice-id">GRN No: {{ sprintf('%04d', $purchase->grn_no)}}</h1>
                        <div class="date">{{trans('navmenu.order_no')}} : <b>{{$purchase->order_no}}</b></div>
                        <div class="date">{{trans('navmenu.delivery_note_no')}} : <b>{{$purchase->delivery_note_no}}</b></div>
                        <div class="date">{{trans('navmenu.invoice_no')}} : <b>{{$purchase->invoice_no}}</b></div>
                        <div class="date">{{trans('navmenu.date')}} : <b>
                        {{date("d, M Y", strtotime($purchase->time_created))}}</b></div>
                      </div>
                    </div>
                    <table>
                      <thead>
                        <tr>
                          <th>#</th>
                          <th class="text-left">{{trans('navmenu.description')}}</th>
                          <th class="text-right">UOM</th>
                          <th class="text-right">{{trans('navmenu.qty')}}</th>
                          <th class="text-right">{{trans('navmenu.unit_cost')}}</th>
                          <th>{{trans('navmenu.total')}}</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($ritems as $key => $item)
                        <tr>
                          <td class="no">{{$key+1}}</td>
                          <td class="text-left">{{$item->name}}</td>
                          <td class="text-center">{{$item->basic_uom}}</td>
                          <td class="qty">{{number_format($item->qty)}}</td>
                          <td class="unit">{{number_format($item->unit_cost)}}</td>  
                          <td class="total">{{number_format($item->qty * $item->unit_cost)}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="2"></td>
                          <td colspan="3">{{trans('navmenu.total')}}</td>
                          <td>{{number_format($purchase->total_amount)}}</td>
                        </tr>
                      </tfoot>
                    </table>
                    <div class="notices">
                      <div>{{trans('navmenu.comments')}}:</div>
                      <div class="notice">{{$purchase->comments}}</div>
                    </div>
                    <div class="row pt-4">
                    <p class=" col font-18">
                      {{trans('navmenu.store_keeper')}} : {{trans('navmenu.name')}} : <b>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</b>  {{trans('navmenu.signature')}} _________________ {{trans('navmenu.date')}}   _________________
                    </p>
                    </div>
                  </main>
                 <!-- <footer>Invoice was created on a computer and is valid without the signature and seal.</footer> -->
                </div>
                <div></div>
              </div>
            </div>
          </div>
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
            document.title = "<?php echo $title.'_no_'.$purchase->grn_no.'_'.$purchase->time_created; ?>";
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo $title.'_no_'.$purchase->grn_no.'_'.$purchase->time_created; ?>";
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