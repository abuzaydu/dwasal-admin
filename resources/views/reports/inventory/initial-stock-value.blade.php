@extends('layouts.gen')

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
            <div class="col-lg-7 col-md-7 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="stockcapital" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="background:#E0E0E0;">
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
                                <?php $total_cost = 0; $total_rsales = 0; $total_wsales = 0; ?>
                                @foreach($products as $key => $product)
                                <?php 
                                    $total_cost += $product['qty']*$product['unit_cost'];
                                    $total_rsales += $product['qty']*$product['retail_price'];
                                    $total_wsales +=  $product['qty']*$product['wholesale_price'];
                                ?>
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$product['name']}}</td>
                                    <td style="text-align: center;">
                                        {{$product['qty']}}
                                    </td>
                                    <td style="text-align: center;">{{$product['basic_uom']}}</td>
                                    <td style="text-align: center;">{{number_format($product['unit_cost'], 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{(number_format($product['qty']*$product['unit_cost'], 2, '.', ','))}}</td>
                                    <td style="text-align: center;">{{number_format($product['retail_price'], 2, '.', ',')}}</td>
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
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

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

        })
    </script>
@endsection