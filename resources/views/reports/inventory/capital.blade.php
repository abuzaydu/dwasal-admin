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
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="row g-3" id="filter-form" action="{{url('stock-capital')}}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Shops</label>
                        <select name="store" class="form-select form-select-sm mb-1" onchange='this.form.submit();'>
                            <option value="All">All Stores & Warehouses</option>
                            @foreach($shops as $store)
                            @if(!is_null($currstore) && $currstore->id == $store->id)
                            <option value="{{$currstore->id}}" selected>{{$currstore->name}}</option>
                            @else
                            <option value="{{$store->id}}">{{$store->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    @if(!is_null($currstore))
                    @if(!is_null($locations))
                    <div class="col-md-3">
                        <label class="form-label">Location</label>
                        <select name="location" id="location" class="form-select form-select-sm mb-1">
                            <option value="">All</option>
                            @foreach($locations as $prodloc)
                            @if($location == $prodloc->location)
                            <option value="{{$prodloc->location}}" selected>{{$prodloc->location}}</option>
                            @else
                            <option value="{{$prodloc->location}}">{{$prodloc->location}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @if(!is_null($categories))
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" id="category" class="form-select form-select-sm mb-1">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                            @if($categoryid == $cat->id)
                            <option value="{{$cat->id}}" selected>{{$cat->name}}</option>
                            @else
                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @endif
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stockcapital" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">UOM</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.selling_price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.expected_profit')}}</th>
                                    @if($settings->retail_with_wholesale)
                                    <th style="text-align: center;">{{trans('navmenu.wholesale_price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.expected_profit')}}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <?php $tqty = 0; $total_cost = 0; $total_rsales = 0; $total_wsales = 0; ?>
                                @foreach($products as $key => $product)
                                <?php 
                                    $tqty += $product['qty'];
                                    $total_cost += $product['qty']*$product['unit_cost'];
                                    $total_rsales += $product['qty']*$product['retail_price'];
                                    $total_wsales +=  $product['qty']*$product['wholesale_price'];
                                ?>
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$product['name']}}</td>
                                    <td style="text-align: center;">
                                        {{$product['qty']+0}}
                                    </td>
                                    <td style="text-align: center;">{{$product['basic_uom']}}</td>
                                    <td style="text-align: center;">{{number_format($product['unit_cost'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{(number_format($product['qty']*$product['unit_cost'], 2, '.', ','))}}</td>
                                    <td style="text-align: center;">{{number_format($product['retail_price'])}}</td>
                                    <td style="text-align: center;">{{number_format($product['qty']*$product['retail_price'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format(($product['qty']*$product['retail_price'])-($product['qty']*$product['unit_cost']), 2,'.', ',')}}</td>
                                    @if($settings->retail_with_wholesale)
                                    <td style="text-align: center;">{{number_format($product['wholesale_price'])}}</td>
                                    <td style="text-align: center;">{{number_format($product['qty']*$product['wholesale_price'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format(($product['qty']*$product['wholesale_price'])-($product['qty']*$product['unit_cost']), 2, '.', ',')}}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{$tqty}}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($total_cost, 2, '.',',')}}</th>
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($total_rsales, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($total_rsales-$total_cost, 2, '.', ',')}}</th>
                                    @if($settings->retail_with_wholesale)
                                    <th></th>
                                    <th style="text-align: center;">{{number_format($total_wsales, 2, '.', ',')}}</th>
                                    <th style="text-align: center;">{{number_format($total_wsales-$total_cost, 2, '.', ',')}}</th>
                                    @endif
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
            var shop_name = "<?php echo $shop->name; ?>";

            var captable = $('#stockcapital').DataTable({
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
                        filename: "{{ trans('navmenu.current_stock_capital') }}_" + date,
                        title: "{{ trans('navmenu.current_stock_capital') }}",
                        messageTop: 'DATE : ' + date
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "{{ trans('navmenu.current_stock_capital') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.current_stock_capital') }} \n Date : " + date,
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                    }
                ],
            });
            captable.buttons().container().appendTo('#stockcapital_wrapper .col-md-6:eq(1)');


            $('#location').on('change', function(){
                $('#category').val('');
                $('#filter-form').submit();
            });

            $('#category').on('change', function(){
                $('#location').val('');
                $('#filter-form').submit();
            });
        });
    </script>
@endsection
