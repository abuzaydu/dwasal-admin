@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
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
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <form class="dashform row g-1" id="filter-by-product" action="{{url('sales-by-product')}}" method="POST">
                            @csrf
                            <div class="col-md-3">
                                <select name="customer_id" id="customer-id" class="form-select form-select-sm select2" onchange="this.form.submit()">
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
                            <div class=" col-md-4">
                                <input id="search_key" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                <ul id="searchResult2"></ul>
                            </div>
                            <input type="hidden" name="product_id" id="product-id">
                            <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                            <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                            <!-- Date and time range -->
                            <div class="col-md-5">  
                                <div class="form-group">
                                    <div class="input-group">
                                        <button type="button" class="btn btn-white pull-right" id="reportrange">
                                            <span><i class="fa fa-calendar"></i></span>
                                            <i class="fa fa-caret-down"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.form group -->
                            </div>
                        </form>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="salesproduct" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.subtotal')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                    <th style="text-align: center;">Profit Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $tqty = 0; $total_exc = 0; $total_vat = 0; $total_cost = 0; $total_profit = 0; $total_profit_margin = 0; ?>
                                @foreach($sales as $index => $sale)
                                <?php
                                    $tqty += $sale->quantity;
                                    $total_exc += ($sale->price-$sale->total_discount);
                                    $total_vat += $sale->tax_amount;
                                    $total_cost += $sale->buying_price;
                                    $revenue = ($sale->price-$sale->total_discount)+$sale->tax_amount;
                                    $profit = $revenue-$sale->buying_price;
                                    if ($revenue > 0) {
                                        $profit_margin = round(($profit/$revenue)*100, 2);
                                    }
                                    $total_profit += $profit;
                                    $total_profit_margin += $profit_margin;
                                ?>
                                <tr>
                                    <td>{{$sale->name}}</td>
                                    <td style="text-align: center;">{{$sale->quantity+0}}</td>
                                    <td style="text-align: center;">{{number_format($sale->retail_price-$sale->discount, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->price-$sale->total_discount, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->tax_amount, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format(($sale->price-$sale->total_discount)+$sale->tax_amount, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->unit_cost, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->buying_price, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($profit, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{$profit_margin}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;"><b>{{$tqty}}</b></th>
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_exc, 2, '.', ',')}}</strong></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_vat, 2, '.', ',')}}</strong></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_exc+$total_vat, 2, '.', ',')}}</th>
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_cost, 2, '.', ',')}}</strong></th>
                                    <th><strong>{{number_format($total_profit, 2, '.', ',')}}</strong></th>
                                    <th style="text-align: center;"><strong>{{$total_profit_margin}}</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($returns->count() > 0)
                    <div class="col-md-12">
                        <h6>{{trans('navmenu.sales_returns')}}</h6>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="returnsproduct" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $tsrqty = 0; $total_rco = 0; $total_sra = 0;?>
                                @foreach($returns as $index => $sale)
                                <?php $tsrqty += $sale->quantity; $total_rco += $sale->buying_price; $total_sra += ($sale->price-$sale->total_discount); ?>
                                <tr>
                                    <td>{{$sale->name}}</td>
                                    <td style="text-align: center;">{{$sale->quantity+0}}</td>
                                    <td style="text-align: center;">{{number_format($sale->unit_cost, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->buying_price, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->retail_price-$sale->discount, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->price-$sale->total_discount, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format(($sale->price-$sale->total_discount)-$sale->buying_price, 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;"><strong>{{$tsrqty}}</strong></th>
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_rco, 2, '.', ',')}}</strong></th>
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_sra, 2, '.', ',')}}</strong></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_sra-$total_rco, 2, '.', ',')}}</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.col -->
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/select2/js/select2.min.js') }}"></script>
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

            $('#customer-id').select2();

            var sptable = $('#salesproduct').DataTable({
                "scrollX": true,
                "order": [
                    [0, "asc"]
                ],
                "bInfo": true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_by_product') }}_" + date,
                        title: "{{ trans('navmenu.sales_by_product') }}",
                        messageTop: duration,
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.sales_by_product') }}_" + date,
                        title: shop_name + " \n {{ trans('navmenu.sales_by_product') }} \n" +duration,
                    }
                ],
            });
            sptable.buttons().container().appendTo('#salesproduct_wrapper .col-md-6:eq(1)');

            var rptable = $('#returnsproduct').DataTable({
                "scrollX": true,
                "order": [
                    [0, "asc"]
                ],
                "bInfo": true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Sales Return By Product_" + date,
                        title: "Sales Return By Product",
                        messageTop: duration,
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "Sales Return By Product_" + date,
                        title: shop_name + " \n Sales Return By Product \n" +duration,
                    }
                ],
            });
            rptable.buttons().container().appendTo('#returnsproduct_wrapper .col-md-6:eq(1)');

        });
    </script>
@endsection
    <?php
        $prodID = '';
        if (!is_null($product)) {
            $prodID = $product->id;
        }
    ?>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var prodid = "<?php echo $prodID; ?>";
            $('#product-id').val(prodid);
            $('#search_key').on('keyup',function () {
                var query = $(this).val();
                $.ajax({
                    url:"{{ url('search-product') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        // $('#product_list').html(data);
                        var len = response.length;
                        $("#searchResult2").empty();
                        $("#searchResult2").append("<li value=''>All</li>");
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['name'];
                            $("#searchResult2").append("<li value='"+id+"'>"+name+"</li>");
                        }

                        // binding click event to li
                        $("#searchResult2 li").bind("click",function(){
                            searchProduct(this);
                        });

                    }
                })
            });
        });

        function searchProduct(element) {
            var value = $(element).text();
            var productid = $(element).val();
            $('#product-id').val(productid);
            $('#filter-by-product').submit(); 
            // $("#search_key").val('');
            $("#searchResult2").empty();  
        }

    </script>
