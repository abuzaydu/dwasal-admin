@extends('layouts.acc')

<script type="text/javascript">
  function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('delete-cn/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

</script>
<style>
.grid-container {
  display: grid;
  grid-template-columns: auto auto auto;
  /*background-color: #2196F3;*/
  padding: 5px;
}
.grid-item {
  /*background-color: rgba(255, 255, 255, 0.8);*/
  /*border: 1px solid rgba(0, 0, 0, 0.8);*/
  padding: 5px;
  /*font-size: 20px;*/
  text-align: left;
}
.grid-item .shop-info {
  text-align: right;
}

.grid-item .date {
  padding-top: 80px;
}
</style>
@section('content')

<div class="card">
    <div class="card-header">
      <div class="float-end">
        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning " style="margin: 5px;"><i class="fa fa-download"></i> Download PDF</a></li>
        <a href="#" onclick="javascript:printDiv('inv-content')" class="btn bg-info" style="margin: 5px;"><i class="fa fa-printer"></i> Print</a></li>
        <a href="#" onclick="confirmDelete('<?php echo encrypt($acctrans->id); ?>')" class="btn bg-danger" style="margin: 5px;"><i class="fa fa-trash"></i> Delete</a>
      </div>
    </div>

    
    <div class="card-body">
      <div class="row">
          <div id="inv-content">  
            <div class="clearfix invoice-header">
              <figure>
                <img class="invoice-logo" src="../{{$shop->logo_location}}" alt="">
              </figure>
              <div class="company-address">
                <h2 class="title">{{$shop->display_name}}</h2>
                <p>
                  {{$shop->address}}
                  {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br>
                  E-Mail: <a href="#">{{$shop->email}}</a>
                  Tel: <a href="#">{{$shop->mobile}}</a>
                </p>
              </div>
              <div class="company-contact">
                TIN : {{$shop->tin}}<br>
                VRN : {{$shop->vrn}}
              </div>
            </div>

            <div class="invoice-content">
              <div class="details clearfix">
                  <div class="client pull-left">
                    <p class="name">CREDIT NOTE From : {{$acctrans->name}}</p>
                    <p>
                      {{$acctrans->address}}<br>
                      Email :<a href="#">{{$acctrans->email}}</a><br>
                      Tel : <a href="#">{{$acctrans->phone}}</a>
                    </p>
                  </div>
                  <div class="data pull-right">
                    <div class="date">
                      <p style="font-size: 14px; text-transform: uppercase;">
                        CN No : <strong>{{ sprintf('%04d', $acctrans->cn_no)}}</strong><br>
                        Date : {{date("d, M Y", strtotime($acctrans->date))}}<br>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="title text-center"><h3>CREDIT NOTE</h3></div>
            </div>

            <div class="invoice-footer">
              <div class="thanks">A toal amount of {{$amount_in_words}} ({{number_format($acctrans->adjustment)}} {{$settings->currency}}) was reduced for invoice number {{$acctrans->invoice_no}}.</div>
              
              <div class="notice">
                <div>REASON:</div>
                <div>{{$acctrans->reason}}</div>
              </div>
              <div class="end">This is an electronic Credit Note and is valid without the signature and seal.</div>
            </div>
          </div>
          <div id="editor"></div>
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
            document.body.innerHTML = 
              "<html><head><title></title></head><body>" + 
              divElements + "</body>";

            document.title = "<?php echo 'Supplier Credit Note_'.sprintf('%03d', $acctrans->cn_no).'_'.$acctrans->created_at; ?>";
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }


        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo 'Supplier Credit Note_'.sprintf('%03d', $acctrans->cn_no).'_'.$acctrans->created_at; ?>";
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