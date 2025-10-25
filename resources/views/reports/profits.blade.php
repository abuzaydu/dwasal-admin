@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('reports') }}">Reports </a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <form class="dashform row g-1" action="{{url('profits')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-12">
                        <div class="input-group">
                            <button type="button" class="btn btn-white float-end" id="reportrange">
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
                <div class="card-body">
                    <div class="col-xs-12 table-responsive">
                        <table id="profitst" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.percent')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sales as $index => $sale)
                                <?php 
                                $return = App\Models\SaleReturn::where('sale_returns.shop_id', $shop->id)->join('an_sales', 'an_sales.id','=', 'sale_returns.an_sale_id')->whereBetween('an_sales.time_created', [$start, $end])->join('sale_return_items', 'sale_return_items.sale_return_id', '=', 'sale_returns.id')->where('sale_return_items.product_id', $sale->id)->where('sale_return_items.unit_cost', $sale->unit_cost)->where('sale_return_items.retail_price', $sale->retail_price)->get();
                                ?>
                                <tr>
                                    <td>{{$sale->name}}</td>
                                    <td style="text-align: center;">{{$sale->quantity-$return->sum('quantity')}}</td>
                                    <td style="text-align: center;">{{number_format(((($sale->price-$return->sum('price'))-($sale->total_discount-$return->sum('total_discount')))+($sale->tax_amount-$return->sum('tax_amount')))-($sale->buying_price-$return->sum('buying_price')), 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format(((((($sale->price-$return->sum('price'))-($sale->total_discount-$return->sum('total_discount')))+($sale->tax_amount-$return->sum('tax_amount')))-($sale->buying_price-$return->sum('buying_price')))/($total_gross_profit))*100, 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><b>{{trans('navmenu.total')}}</b></th>
                                    <th></th>
                                    <th style="text-align: center;"><b>{{number_format($total_gross_profit)}}</b></th>
                                    <th style="text-align: center;"><b>100</b></th>
                                </tr>
                            </tfoot>
                        </table>
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

            var proftable = $('#profitst').DataTable({
                "scrollX": true,
                "order": [
                    [3, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.profit_report') }}_" + date,
                        title: "{{ trans('navmenu.profit_report') }}",
                        messageTop: duration,
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.profit_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.profit_report') }} \n"+ duration
                    }
                ],
            });
            proftable.buttons().container().appendTo('#profitst_wrapper .col-md-6:eq(1)');

        });
    </script>
@endsection