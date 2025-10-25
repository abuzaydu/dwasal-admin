@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <form class="dashform row g-1" action="{{url('invoice-reports')}}" method="POST" id="stockform">
                @csrf
                <div class="col-sm-6">
                    <select name="customer_id" id="customer-id" class="form-select form-select-sm mb-1 select2" onchange="this.form.submit()">
                        @foreach($customers as $cust)
                        @if(!is_null($customer) && $customer->id == $cust->id)
                        <option value="{{$customer->id}}" selected>{{$customer->name}}</option>
                        @else
                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-sm-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-white pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                </div>
            </form>

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-xl-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#report-excel" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.invoices')}} (Excel)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#report-pdf" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.invoices_with_items')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{url('aging-report')}}" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.aging_report')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="report-excel" role="tabpanel">
                            <!-- Table row -->
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="all-invoices" class="table table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_id')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_name')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.invoice_no')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.due_date')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.status')}}</th>
                                                <!-- <th>{{trans('navmenu.check_no')}}</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total_amount = 0; ?>
                                            @foreach($allinvoices as $index => $invoice)
                                            <?php 
                                                $invamount = ($invoice->amount-$invoice->discount)+$invoice->tax_amount;
                                                $total_amount += $invamount;
                                            ?>
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d M, Y', strtotime( $invoice->created_at))}}</td>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{sprintf('%03d', $invoice->cust_no)}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->name}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{sprintf('%04d', $invoice->invoice_no)}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($invamount, 2, '.', ',')}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d M, Y', strtotime($invoice->due_date))}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->status}}</td>
                                                <!-- <td></td> -->
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                                <th></th>
                                                <th></th>
                                                <th style="text-align: center;">{{number_format($total_amount, 2, '.', ',')}}/=</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <div class="tab-pane fade" id="report-pdf" role="tabpanel">
                            <div class="row g-1 print_invoice" id="print-invoice">
                                <div class="col-md-12">
                                    <table class="table mb-1">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" style="text-align: center; background:  #2874a6;">
                                                    <h6 class="mb-0 text-uppercase" style="color: #fff;">{{trans('navmenu.invoice_report')}}</h6><br>
                                                    <small style="color: #fff;">@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</small>
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
                                <div class="col-md-12">
                                    <table class="items" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date')}}</th>
                                                @if($settings->is_filling_station)
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.name')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.vehicle_no')}}</th>
                                                @endif
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.particular')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.quantity')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.price')}}</th>
                                                @if($settings->is_vat_registered)
                                                <th style="text-align: center; text-transform: uppercase;">VAT</th>
                                                @endif
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.invoice_no')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.due_date')}}</th>
                                                <!-- <th>{{trans('navmenu.check_no')}}</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $total = 0; ?>
                                            @foreach($invoices as $index => $invoice)
                                            <?php 
                                                $sellprice = $invoice->price - $invoice->discount;
                                                $totalsell = ($sellprice*$invoice->qty)+$invoice->tax_amount;
                                                $total += $totalsell;
                                            ?>
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d-m-Y', strtotime( $invoice->date))}}</td>
                                                @if($settings->is_filling_station)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->customer}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->vehicle_no}}</td>
                                                @endif
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$invoice->name}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->qty}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($sellprice), 2, '.', ',')}}</td>
                                                @if($settings->is_vat_registered)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($invoice->tax_amount, 2, '.', ',')}}</td>
                                                @endif
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($totalsell, 2, '.', ',')}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{sprintf('%04d', $invoice->invoice_no)}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->due_date}}</td>
                                                <!-- <td></td> -->
                                            </tr>
                                            @endforeach
                                            <tr >
                                                <td></td>
                                                <td style="text-align: right; text-transform: uppercase; font-size: 14px;"><b>{{trans('navmenu.total')}}</b></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align: center; font-size: 14px;"><b>{{number_format($total, 2, '.', ',')}}</b>/=</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="invoice-footer">
                                    <div class="end">This is an electronic Statement and is valid without the signature and seal.</div>
                                </div>
                            </div>
                            <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="fa fa-download"></i> Download PDF / <i class="fa fa-printer"></i> Print</a>
                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/select2/js/select2.min.js') }}"></script>
    <script>
        $(function(){
            var d = new Date();
            const months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"];
            var day = d.getDate();
            var month = d.getMonth();
            var year = d.getFullYear();
            var date = day + " " + months[month] + " " + year;
            var duration = "<?php echo $duration; ?>";
            var shop_name = "<?php echo $shop->name; ?>";
            var customer = "<?php echo $customer; ?>";

            $('#customer-id').select2();

            var invtable = $('#all-invoices').DataTable({
                "scrollX": true,
                "order": [
                    [3, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.invoice_report') }}_" + date,
                        title: customer + " {{ trans('navmenu.invoices') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.invoice_report') }}_" + date,
                        title: shop_name + ' \n'+ customer + " {{ trans('navmenu.invoices') }} \n"+duration
                    }
                ],
            });
            invtable.buttons().container().appendTo('#all-invoices_wrapper .col-md-6:eq(1)');

        });
    </script>
@endsection
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

    <script language="javascript" type="text/javascript">

        function savePdf() {
          const element = document.getElementById("print-invoice");
          var filename = "<?php echo trans('navmenu.debtor_account_stmt').'_'.$reporttime; ?>";
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