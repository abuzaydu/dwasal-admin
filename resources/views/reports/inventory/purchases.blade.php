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
                <form class="row g-1 dashform" id="filter-by-product" action="{{ url('stock-taking') }}" method="POST" id="stockform">
                    @csrf
                    <div class=" col-md-6 pt-2">
                        <input id="search_key" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                        <ul id="searchResult2"></ul>
                    </div>
                    <input type="hidden" name="product_id" id="product-id">

                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">

                    <div class="form-group col-md-6">
                        <div class="input-group form-control-sm d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-default btn-sm" id="reportrange">
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

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="col-xs-12 table-responsive">
                        <table id="stocktaking" class="table table-striped display nowrap"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('navmenu.product_name') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.quantity') }}</th>
                                    <!-- <th style="text-align: center;">{{ trans('navmenu.unit_cost') }}</th> -->
                                    <!-- <th style="text-align: center;">{{ trans('navmenu.total') }}</th> -->
                                    <th style="text-align: center;">{{ trans('navmenu.source') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.purchase_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $index => $stock)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stock->name }}</td>
                                        <td style="text-align: center;">
                                            @if (is_numeric($stock->quantity_in) && floor($stock->quantity_in) != $stock->quantity_in)
                                                {{ $stock->quantity_in }}
                                            @else
                                                {{ number_format($stock->quantity_in) }}
                                            @endif
                                        </td>
                                        <!-- <td style="text-align: center;">{{ number_format($stock->unit_cost) }}</td> -->
                                        <!-- <td style="text-align: center;"> {{ number_format($stock->quantity_in * $stock->unit_cost) }}</td> -->
                                        <td style="text-align: center;">{{ $stock->source }}</td>
                                        <td style="text-align: center;">{{ 
$stock->stock_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('navmenu.total') }}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;">{{ number_format($total_buying) }}</th>
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

            var stacktable = $('#stocktaking').DataTable({
                "scrollX": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_purchase_report') }}_" + date,
                        title: "{{ trans('navmenu.stock_purchase_report') }}",
                        messageTop: 'DATE: ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.stock_purchase_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.stock_purchase_report') }} \n"+duration
                    }
                ],
            });
            stacktable.buttons().container().appendTo('#stocktaking_wrapper .col-md-6:eq(1)');

        })
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
