@extends('layouts.inv')

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="icon-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>     
            <div class="col-lg-12 col-md-12 col-sm-12 text-right">
                <form class="dashform row g-1" action="{{ url('trip-logs-report') }}" method="POST">
                    @csrf
                    <div class="col-sm-3">
                        <select name="device_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                            <option value="">All Vehicles</option>
                            @foreach($devices as $dev)
                            @if(!is_null($device) && $device->id == $dev->id)
                            <option value="{{$dev->id}}" selected>{{$dev->device_number}} - {{$dev->device_name}}</option>
                            @else
                            <option value="{{$dev->id}}">{{$dev->device_number}} - {{$dev->device_name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <select name="driver" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                            <option value="">All Drivers</option>
                            @foreach($drivers as $trip)
                            @if($currdriver == $trip->driver)
                            <option selected>{{$trip->driver}}</option>
                            @else
                            <option>{{$trip->driver}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                    <!-- /.form group -->
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix mt-0">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="print-trip-logs">
                        <div class="col-md-12 border-bottom pb-0">
                            <table style="width: 100%;">
                                <tr>
                                    <td colspan="16" style="text-align: center;">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="{{$shop->name}}" width="200">
                                        </figure>
                                        @endif
                                        <h5><span>{{$shop->name}}</span></h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="16" style="text-align: center; background: #dff1fa;">
                                        <b>TRIP LOGS FOR @if(!is_null($device)) VEHICLE : {{$device->device_number}} - {{$device->device_name}} @else : ALL VEHICLES @endif</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="16" style="text-align: center;">
                                        <b>{{$duration}}</b>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <table class="mt-0" style="width: 100%; border-bottom: 2px solid #1B1464; display: block; overflow: auto;">
                                <thead>
                                    <!-- <tr style="border-top: 2px solid #dff1fa; border-bottom: 2px solid #1B1464; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                        <th style="border-top: 1px solid #b9b7b5; border-left: 1px solid #b9b7b5; border-right: 1px solid #b9b7b5;"></th>
                                        @if(is_null($device))
                                        <th colspan="6" style="text-align: center; border: 1px solid #b9b7b5;">TRIP DETAILS</th>
                                        @else
                                        <th colspan="5" style="text-align: center; border: 1px solid #b9b7b5;">TRIP DETAILS</th>
                                        @endif
                                        <th colspan="3" style="text-align: center; border: 1px solid #b9b7b5;">MILEAGE</th>

                                        <th colspan="2" style="text-align: center; border: 1px solid #b9b7b5;">FUEL</th>
                                        <th colspan="4" style="border-top: 1px solid #b9b7b5; border-left: 1px solid #b9b7b5; border-right: 1px solid #b9b7b5;"></th>
                                    </tr> -->
                                    <tr style="border-bottom: 2px solid #1B1464;">
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">S/N</th>
                                        @if(is_null($device))
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">@if($settings->enable_trip_logs) Vehicle/Truck No @else Device/Property @endif</th>
                                        @endif
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Driver</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Client</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Date Began</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Date Ended</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Container No.</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Size</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Bill No.</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Shipping Line</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">from</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">To</th>
                                        <th style="text-align: center; border: 1px solid #b9b7b5;">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($triplogs as $key => $trip)
                                    <?php
                                        $first_name = '';
                                        $user = App\Models\User::find($trip->user_id);
                                        if (!is_null($user)) {
                                            $first_name = $user->first_name;
                                        }
                                    ?>
                                    <tr>
                                        <td style="text-align: center; border: 1px solid #b9b7b5;">{{$key+1}}</td>
                                        @if(is_null($device))
                                        <td class="text-left" style="border: 1px solid #b9b7b5;">{{$trip->device_number}} - {{$trip->device_name}}</td>
                                        @endif
                                        <td class="text-left" style="border: 1px solid #b9b7b5;">{{$trip->driver}}</td>

                                        <td class="text-left" style="border: 1px solid #b9b7b5;">{{$trip->name}}</td>
                                        <td class="no" style="border: 1px solid #b9b7b5;">{{ date('d/m/Y H:i', strtotime($trip->trip_date))}}</td>
                                        <td class="no" style="border: 1px solid #b9b7b5;">{{ date('d/m/Y H:i', strtotime($trip->trip_end_date))}}</td>
                                        <td style="border: 1px solid #b9b7b5;">{{$trip->container_no}}</td>
                                        <td style="border: 1px solid #b9b7b5;">{{$trip->container_size}}</td>
                                        <td style="border: 1px solid #b9b7b5;">{{$trip->bill_no}}</td>
                                        <td style="border: 1px solid #b9b7b5;">{{$trip->shipping}}</td>
                                        <td style="text-align: center;border: 1px solid #b9b7b5;">{{$trip->from}}</td>
                                        <td style="text-align: center;border: 1px solid #b9b7b5;">{{$trip->to }}</td>
                                        <td class="text-left" style="border: 1px solid #b9b7b5;">{{$trip->trip_title}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <a href="#" onclick="javascript:exportToExcel()" class="btn btn-secondary btn-sm"><i class="fa fa-download"></i> Export to Excel</a>
            <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
        </div>
    </div>
@endsection

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
            const element = document.getElementById("print-trip-logs");
            var filename = "<?php echo $title.'_'.$duration; ?>";
            var opt = {
                margin:       0.5,
                filename:     filename+'.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, scrollY: 0, scrollX: 0 },
                // Added after option to add spacing after page break
                pagebreak: { avoid: "tr", mode: "css"},
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
            };

            html2pdf().set(opt).from(element).toPdf().save();
            // New Promise-based usage:
            // html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                // window.open(pdf.output('bloburl'), '_blank');
            // });
        }

        function exportToExcel() {
            var filename = "<?php echo $title.'_'.$duration; ?>";
            var location = 'data:application/vnd.ms-excel;base64,';
            var excelTemplate = '<html> ' +
                '<head> ' +
                '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/> ' +
                '</head> ' +
                '<body> ' +
                document.getElementById("print-trip-logs").innerHTML +
                '</body> ' +
                '</html>'
                var a = document.createElement('a');
                a.href = location + window.btoa(excelTemplate);
                a.download = filename + '.xls';
                a.click();
        }
</script>