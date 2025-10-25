@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
     
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
                <form class="row g-1 dashform" action="{{url('sales-report')}}" method="POST">
                        @csrf
                        <div class="col-md-3">
                            <select name="user_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                                <option value="">{{trans('navmenu.select_by_seller')}} </option>
                                @foreach($users as $user1)
                                @if(!is_null($user) && $user->id == $user1->id)
                                <option value="{{$user1->id}}" selected>{{$user1->first_name}} {{$user1->last_name}}</option>
                                @else
                                <option value="{{$user1->id}}">{{$user1->first_name}} {{$user1->last_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="customer_id" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                                <option value="">{{trans('navmenu.select_by_customer')}} </option>
                                @foreach($customers as $cust)
                                @if(!is_null($customer) && $customer->id == $cust->id)
                                <option value="{{$cust->id}}" selected>{{$cust->name}}</option>
                                @else
                                <option value="{{$cust->id}}">{{$cust->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        @if($settings->is_school)
                        <div class="col-md-3">
                            <select name="grade_id" class="form-select form-select-sm mb-1">
                                <option value="">{{trans('navmenu.select_grade')}} </option>
                                @foreach($grades as $grd)
                                @if(!is_null($grade) && $grade->id == $grd->id)
                                <option value="{{$grd->id}}" selected>{{$grd->name}}</option>
                                @else
                                <option value="{{$grd->id}}">{{$grd->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="year" class="form-select form-select-sm mb-1">
                                @foreach($years as $yr)
                                @if(!is_null($year) && $year == $yr->year)
                                <option selected>{{$yr->year}}</option>
                                @else
                                <option>{{$yr->year}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                        <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                        <!-- Date and time range -->
                        <div class="col-md-6">  
                            <div class="input-group">
                                <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span>
                                  <i class="fa fa-caret-down"></i>
                                </button>
                            </div>
                            <!-- /.form group -->
                        </div>
                    </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#all-sales">All Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#vat-sales">VAT Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#non-vat-sales">Non VAT Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('daily-total-sales') }}">Daily Total Sales</a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="all-sales" role="tabpanel">
                            <div class="table-responsive">
                                <table id="sales" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="text-align: center;">{{trans('navmenu.saledate')}}</th>
                                            <th>{{trans('navmenu.user')}}</th>
                                            <th>{{trans('navmenu.customer_name')}}</th>
                                            <th>{{trans('navmenu.invoice_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sale_amount')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.paid')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unpaid')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sale_type')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.status')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.last_updated')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total_amount = 0; $total_paid = 0;?>
                                        @foreach($sales as $index => $sale)
                                        <?php
                                            $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                                            $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                                            $netsales_amount = $tnetsales-$tnetreturn;
                                            $total_amount += $netsales_amount;
                                            $total_paid += $sale->sale_amount_paid;
                                        ?>
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td style="text-align: center;"> {{$sale->time_created}} </td>
                                            <td>{{$sale->first_name}} {{$sale->last_name}}</td>
                                            <td>{{$sale->name}}</td>
                                            <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($sale->id)) }}">{{ sprintf('%04d', $sale->invoice_no)}}</a></td>
                                            <td style="text-align: center;">{{number_format($netsales_amount, 2, '.',',')}}</td>
                                            <td style="text-align: center;">{{number_format($sale->sale_amount_paid, 2, '.',',')}}</td>
                                            <td style="text-align: center;">{{number_format($netsales_amount-$sale->sale_amount_paid, 2, '.',',')}}</td>
                                            <td style="text-align: center;">
                                              {{$sale->sale_type}}
                                            </td>
                                            <td style="text-align: center;">{{$sale->status}}</td>
                                            <td style="text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th><th></th><th></th>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total_amount, 2,'.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_paid, 2,'.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_amount-$total_paid, 2,'.', ',')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="vat-sales" role="tabpanel">
                            <div class="table-responsive">
                                <table id="vatsales" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="text-align: center;">{{trans('navmenu.saledate')}}</th>
                                            <th>{{trans('navmenu.user')}}</th>
                                            <th>{{trans('navmenu.customer_name')}}</th>
                                            <th>{{trans('navmenu.invoice_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sale_amount')}}</th>
                                            <th style="text-align: center;">VAT Amount</th>
                                            <th style="text-align: center;">Net Sales Amount</th>
                                            <th style="text-align: center;">{{trans('navmenu.paid')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unpaid')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sale_type')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.status')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.last_updated')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total_before_tax = 0; $total_tax = 0; $total_v_amount = 0; $total_v_paid = 0; ?>
                                        @foreach($sales as $index => $sale)
                                        @if($sale->tax_amount > 0)
                                            <?php
                                                $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                                                $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;

                                                $amtbeforetax = ($sale->sale_amount-$sale->sale_discount)-($sale->return_amount-$sale->return_discount);
                                                $nettax = $sale->tax_amount-$sale->return_tax;
                                                $netsales_amount = $tnetsales-$tnetreturn;
                                                $total_before_tax += $amtbeforetax;
                                                $total_tax += $nettax;
                                                $total_v_amount += $netsales_amount;
                                                $total_v_paid += $sale->sale_amount_paid;
                                            ?>
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td style="text-align: center;"> {{$sale->time_created}} </td>
                                                <td>{{$sale->first_name}} {{$sale->last_name}}</td>
                                                <td>{{$sale->name}}</td>
                                                <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($sale->id)) }}">{{ sprintf('%04d', $sale->invoice_no)}}</a></td>
                                                <td style="text-align: center;">{{number_format($amtbeforetax, 2, '.',',')}}</td>
                                                <td style="text-align: center;">{{number_format($nettax, 2, '.',',')}}</td>
                                                <td style="text-align: center;">{{number_format($netsales_amount, 2, '.',',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale->sale_amount_paid, 2, '.',',')}}</td>
                                                <td style="text-align: center;">{{number_format($netsales_amount-$sale->sale_amount_paid, 2, '.',',')}}</td>
                                                <td style="text-align: center;">
                                                  {{$sale->sale_type}}
                                                </td>
                                                <td style="text-align: center;">{{$sale->status}}</td>
                                                <td style="text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}</td>
                                            </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th><th></th><th></th>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total_before_tax, 2,'.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_tax, 2,'.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_v_amount, 2,'.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_v_paid, 2,'.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_v_amount-$total_v_paid, 2,'.', ',')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="non-vat-sales" role="tabpanel">
                            <div class="table-responsive">
                                <table id="nonvatsales" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th style="text-align: center;">{{trans('navmenu.saledate')}}</th>
                                            <th>{{trans('navmenu.user')}}</th>
                                            <th>{{trans('navmenu.customer_name')}}</th>
                                            <th>{{trans('navmenu.invoice_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sale_amount')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.paid')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.unpaid')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sale_type')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.status')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.last_updated')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total_non_amount = 0; $total_non_paid = 0;?>
                                        @foreach($sales as $index => $sale)
                                            @if($sale->tax_amount == 0)
                                            <?php
                                                $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                                                $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                                                $netsales_amount = $tnetsales-$tnetreturn;
                                                $total_non_amount += $netsales_amount;
                                                $total_non_paid += $sale->sale_amount_paid;
                                            ?>
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td style="text-align: center;"> {{$sale->time_created}} </td>
                                                <td>{{$sale->first_name}} {{$sale->last_name}}</td>
                                                <td>{{$sale->name}}</td>
                                                <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($sale->id)) }}">{{ sprintf('%04d', $sale->invoice_no)}}</a></td>
                                                <td style="text-align: center;">{{number_format($netsales_amount, 2, '.',',')}}</td>
                                                <td style="text-align: center;">{{number_format($sale->sale_amount_paid, 2, '.',',')}}</td>
                                                <td style="text-align: center;">{{number_format($netsales_amount-$sale->sale_amount_paid, 2, '.',',')}}</td>
                                                <td style="text-align: center;">
                                                  {{$sale->sale_type}}
                                                </td>
                                                <td style="text-align: center;">{{$sale->status}}</td>
                                                <td style="text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }}</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>#</th><th></th><th></th>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total_non_amount, 2,'.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_non_paid, 2,'.', ',')}}</th>
                                            <th style="text-align: center;">{{number_format($total_non_amount-$total_non_paid, 2,'.', ',')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
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

            var satable = $('#sales').DataTable({
                "scrollX": true,
                "order": [
                    [0, "asc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_report') }}_" + date,
                        title: "{{ trans('navmenu.sales_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_report') }}_" + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: shop_name + " \n {{ trans('navmenu.sales_report') }} \n"+duration
                    }
                ],
            });
            satable.buttons().container().appendTo('#sales_wrapper .col-md-6:eq(1)');

            var vatsatable = $('#vatsales').DataTable({
                "scrollX": true,
                "order": [
                    [0, "asc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "VAT {{ trans('navmenu.sales_report') }}_" + date,
                        title: "VAT {{ trans('navmenu.sales_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "VAT {{ trans('navmenu.sales_report') }}_" + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: shop_name + " \n VAT {{ trans('navmenu.sales_report') }} \n"+duration
                    }
                ],
            });
            vatsatable.buttons().container().appendTo('#vatsales_wrapper .col-md-6:eq(1)');

            var nonvatsatable = $('#nonvatsales').DataTable({
                "scrollX": true,
                "order": [
                    [0, "asc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Non VAT {{ trans('navmenu.sales_report') }}_" + date,
                        title: "Non VAT {{ trans('navmenu.sales_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "Non VAT {{ trans('navmenu.sales_report') }}_" + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        title: shop_name + " \n Non VAT {{ trans('navmenu.sales_report') }} \n"+duration
                    }
                ],
            });
            nonvatsatable.buttons().container().appendTo('#nonvatsales_wrapper .col-md-6:eq(1)');

        });
    </script>
@endsection

