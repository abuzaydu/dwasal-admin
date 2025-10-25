@extends('layouts.gen')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                <form class="row g-3 dashform" action="{{url('discount-by-sales')}}" method="POST">
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
                    <!-- Table row -->
                    <div class="row">
                        <div class="col-xs-12 table-responsive">
                            <table id="discount-sales-report" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="text-align: center;">{{trans('navmenu.saledate')}}</th>
                                        <th>{{trans('navmenu.user')}}</th>
                                        <th>{{trans('navmenu.customer_name')}}</th>
                                        <th>{{trans('navmenu.invoice_no')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.sale_amount')}}</th>
                                        <th style="text-align: center;">Return Amount</th>
                                        <th style="text-align: center;">Net {{trans('navmenu.discount')}}(%)</th>
                                        <th style="text-align: center;">Net {{trans('navmenu.discount')}}</th>
                                        <th style="text-align: center;">VAT Amount</th>
                                        <th style="text-align: center;">Net Sales Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_amount = 0; $total_discount = 0; $total_discount_percent = 0; $total_return = 0; $total_vat = 0; $total_netsales = 0; ?>
                                    @foreach($sales as $index => $sale)
                                    <?php 
                                        $total_amount += $sale->sale_amount;
                                        $discount = $sale->sale_discount-$sale->return_discount;
                                        $discount_percent = round(($discount/($sale->sale_amount-$sale->return_amount))*100, 2);
                                        $vat = ($sale->tax_amount-$sale->return_tax);
                                        $netsale = (($sale->sale_amount-$sale->sale_discount)-($sale->return_amount-$sale->return_discount))+$vat;
                                        $total_discount_percent += $discount_percent;
                                        $total_discount += $discount;
                                        $total_return += $sale->return_amount;
                                        $total_vat += $vat;
                                        $total_netsales += $netsale;
                                    ?>
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td style="text-align: center;"> {{$sale->time_created}} </td>
                                        <td>{{$sale->first_name}} {{$sale->last_name}}</td>
                                        <td>{{$sale->name}}</td>
                                        <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($sale->id)) }}">{{ sprintf('%04d', $sale->invoice_no)}}</a></td>
                                        <td style="text-align: center;">{{number_format($sale->sale_amount, 2, '.',',')}}</td>
                                        <td style="text-align: center;">{{number_format($sale->return_amount, 2, '.',',')}}</td>
                                        <td style="text-align: center;">
                                            {{ $discount_percent }}
                                        </td>
                                        <td style="text-align: center;">{{number_format($discount, 2, '.',',')}}</td>
                                        <td style="text-align: center;">{{number_format($vat, 2, '.', ',')}}</td>
                                        <td style="text-align: center;">{{number_format($netsale, 2, '.',',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>#</th><th></th><th></th>
                                        <th>{{trans('navmenu.total')}}</th>
                                        <th></th>
                                        <th style="text-align: center;">{{number_format($total_amount, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{number_format($total_return, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{$total_discount_percent}}</th>
                                        <th style="text-align: center;">{{number_format($total_discount, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{number_format($total_vat, 2,'.', ',')}}</th>
                                        <th style="text-align: center;">{{number_format($total_netsales, 2,'.', ',')}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.col -->
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

             var discsareport = $('#discount-sales-report').DataTable({
                "scrollX": true,
                "order": [
                    [3, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.discount_by_sales_report') }}_" + date,
                        title: "{{ trans('navmenu.discount_by_sales_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        orientation: 'landscape',
                        filename: "{{ trans('navmenu.discount_by_sales_report') }}_" + date,
                        title: shop_name + " \n {{ trans('navmenu.discount_by_sales_report') }} \n"+duration,
                    }
                ],
            });  
            discsareport.buttons().container().appendTo('#discount-sales-report_wrapper .col-md-6:eq(1)');

        })
    </script>
@endsection