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
                <a href="{{ route('devices.edit', encrypt($device->id))}}" class="btn btn-primary btn-sm" style="margin: 5px;"><i class="fa fa-edit"></i> Update</a>

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-trip-logs">
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
                        <div class="col-md-12">
                            <table class="table mb-1">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align: center; background:  #2874a6;">
                                            <h4 class="mb-0 text-uppercase" style="color: #fff;">{{$device->device_number}} - {{$device->device_name}} : TRIP LOGS </h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="items mt-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="border-top: 1px solid #b9b7b5; border-left: 1px solid #b9b7b5; border-right: 1px solid #b9b7b5;"></th>
                                        <th style="border-top: 1px solid #b9b7b5; border-left: 1px solid #b9b7b5; border-right: 1px solid #b9b7b5;"></th>
                                        <th colspan="2" style="text-align: center; border: 1px solid #b9b7b5;">TRIP</th>
                                        <th colspan="3" style="text-align: center; border: 1px solid #b9b7b5;">MILEAGE</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">AMOUNT</th>
                                    </tr>
                                    <tr>
                                        <th style="border: 1px solid #b9b7b5;">DATE</th>
                                        <th style="border: 1px solid #b9b7b5;">DESCRIPTION</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">FROM</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">TO</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">OUT</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">IN</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">USED</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">{{$currency}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($triplogs as $key => $trip)
                                    <tr>
                                        <td class="no" style="border: 1px solid #b9b7b5;">{{ date('d/m/Y', strtotime($trip->trip_date))}}</td>
                                        <td class="text-left" style="border: 1px solid #b9b7b5;">{{$trip->trip_title}}</td>
                                        <td style="text-align: center;border: 1px solid #b9b7b5;">{{$trip->from}}</td>
                                        <td style="text-align: center;border: 1px solid #b9b7b5;">{{$trip->to }}</td>
                                        <td style="text-align: center;border: 1px solid #b9b7b5;">{{$trip->mileage_out+0}}</td> 
                                        <td style="text-align: center;border: 1px solid #b9b7b5;">{{$trip->mileage_in+0}}</td> 
                                        <td style="text-align: center;border: 1px solid #b9b7b5;">{{$trip->mileage_in-$trip->mileage_out}}</td>  
                                        <td style="text-align: right;border: 1px solid #b9b7b5;">{{number_format(($trip->sale_amount-$trip->sale_discount)+$trip->tax_amount, 2, '.', ',') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
          const element = document.getElementById("print-trip-logs");
          var filename = "<?php echo $device->device_number.'_trip_logs'; ?>";
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