@extends('layouts.gen')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoicing</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-4 col-sm-12 text-right pt-0">
                <form class="row g-3 dashform" action="{{ url('rental-status-report') }}" method="POST">
                    @csrf
                    <div class="col-md-5">
                        <select name="device_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                            <option value="">All Devices/Properties</option>
                            @foreach($devices as $device)
                            @if(!is_null($currdevice) && $device->id == $currdevice->id)
                            <option value="{{$device->id}}" selected>{{$device->device_number}} - {{$device->device_name}}</option>
                            @else
                            <option value="{{$device->id}}">{{$device->device_number}} - {{$device->device_name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class=" col-md-7">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body row">
                    <div id="inv-content" class="col-md-12 print_invoice p-3" style="border: 1px solid gray;">
                        <div class="row">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" width="200" style="border: 1px solid gray;">
                                </figure>
                                @endif
                                <h5>{{$shop->name}}</h5>
                                <span> {{ $title }}<br> @if(!is_null($currdevice)) <b class="text-success">{{$currdevice->device_number}} - {{$currdevice->device_name}}</b> @else <b class="text-success">All Devices/Properties</b> @endif <br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></span>
                            </div>
                            <div class="col-xs-12 invoice-content" style="border-top: 2px solid #82B1FF;">
                                <table class="table mt-0" style="width: 100%; white-space: nowrap;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                            <th style="text-align: center; border-top: 1px solid gray; border-left: 1px solid gray; border-right: 1px solid gray;"></th>
                                            <th style="text-align: center; border-top: 1px solid gray;  border-right: 1px solid gray;"></th>
                                            <th style="text-align: center; border-top: 1px solid gray;  border-right: 1px solid gray;"></th>
                                            <th style="text-align: center; border-top: 1px solid gray; border-left: 1px solid gray;border-bottom: 1px solid gray; border-right: 1px solid gray;" colspan="3">Contract</th>
                                            <th style="text-align: center; border-top: 1px solid gray; border-right: 1px solid gray;"></th>
                                            <th style="text-align: center; border-top: 1px solid gray; border-right: 1px solid gray;"></th>
                                            <th style="text-align: center; border-top: 1px solid gray; border-right: 1px solid gray;"></th>
                                        </tr>
                                        <tr>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-left: 1px solid gray;  border-right: 1px solid gray;">S/N</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-left: 1px solid gray;  border-right: 1px solid gray;">Tenant Name</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-left: 1px solid gray;border-right: 1px solid gray;">Mobile</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-left: 1px solid gray;border-right: 1px solid gray;">Property</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">Start Date</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">End Date</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">Status</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">Amount ({{$code}})</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">No .Months</th>
                                            <th style="text-align: center; border-bottom: 1px solid gray; border-right: 1px solid gray;">Total ({{$code}})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $totalamt = 0; ?>
                                        @foreach($sales as $key => $sale)
                                        <?php 
                                            $total = ($sale->total-$sale->total_discount)+$sale->tax_amount;
                                            $totalamt += $total; ?>
                                        <tr>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle; text-align: center;">{{$key+1}}</td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; vertical-align: middle;">{{$sale->name}}</td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; text-align: center;">{{$sale->phone}}</td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; text-align: center;">{{$sale->device_number}} - {{$sale->device_name}}</td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; text-align: center;">{{date('d-M-Y', strtotime($sale->rent_start_date)) }}</td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; text-align: center;">{{date('d-M-Y', strtotime($sale->rent_end_date)) }}</td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; text-align: center;">
                                                <?php
                                                    $days = 0;
                                                    if (!is_null($sale->rent_end_date)) {

                                                        $now = \Carbon\Carbon::now()->format('Y-m-d');
                                                        $diff = strtotime($sale->rent_end_date) - strtotime($now);
                                                        $days = round($diff / (60 * 60 * 24));
                                                    }
                                                ?> 
                                                @if($days > 0)
                                                    @if($days > 7)
                                                    <span class="badge rounded-pill bg-success">{{$days}} Days Remain </span>
                                                    @else
                                                    <span class="badge rounded-pill bg-warning">{{$days}} Days Remain </span>
                                                    @endif
                                                @else 
                                                    <span class="badge rounded-pill bg-danger">Expired in {{$days}} Days </span>
                                                @endif
                                            </td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; text-align: center;">{{number_format($sale->price, 2,'.', ',')}}</td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; text-align: center;">{{$sale->qty}}</td>
                                            <td style="border-right: 1px solid gray; border-left: 1px solid gray; border-bottom: 1px solid gray; text-align: right;">{{number_format($total, 2, '.', ',')}}</td>
                                            
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="border-bottom: 1px solid gray;"></th>
                                            <th colspan="7" style="border-bottom: 1px solid gray;"><b>{{trans('navmenu.total')}}</th>
                                            <th colspan="2" style="text-align: right; border-bottom: 1px solid gray;"><b>{{number_format($totalamt, 2, '.', ',')}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                    <div class="col-md-12 pt-4">
                        <a href="#" onclick="javascript:exportToExcel()" class="btn btn-secondary btn-sm"><i class="fa fa-download"></i> Export to Excel</a>
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> {{trans('navmenu.print')}}</a>
                    </div>
                </div>
            </div>
        </b>
    </th>
@endsection

    
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function savePdf() {
            const element = document.getElementById("inv-content");
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
        }

        function exportToExcel() {
            var filename = "<?php echo $title.'_'.$duration; ?>";
            var location = 'data:application/vnd.ms-excel;base64,';
            var excelTemplate = '<html> ' +
                '<head> ' +
                '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/> ' +
                '</head> ' +
                '<body> ' +
                document.getElementById("inv-content").innerHTML +
                '</body> ' +
                '</html>'
                var a = document.createElement('a');
                a.href = location + window.btoa(excelTemplate);
                a.download = filename + '.xls';
                a.click();
        }

    </script>