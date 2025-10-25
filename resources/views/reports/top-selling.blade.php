@extends('layouts.gen')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-3">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoicing</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
               <form class="dashform row g-1" id="filter-by-product" action="{{url('top-selling-products')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-12">  
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
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="col-xs-12 table-responsive">
                        <table id="top-selling" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $tqty = 0; $total_exc = 0; ?>
                                @foreach($sales as $index => $sale)
                                <?php
                                    $tqty += $sale->quantity;
                                    $total_exc += ($sale->price-$sale->total_discount);
                                ?>
                                <tr>
                                    <td>{{$sale->name}}</td>
                                    <td style="text-align: center;">{{$sale->quantity+0}}</td>
                                    <td style="text-align: center;">{{number_format($sale->retail_price-$sale->discount, 2, '.', ',')}}</td>
                                    <td style="text-align: center;">{{number_format($sale->price-$sale->total_discount, 2, '.', ',')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;"><b>{{$tqty}}</b></th>
                                    <th></th>
                                    <th style="text-align: center;"><strong>{{number_format($total_exc, 2, '.', ',')}}</strong></th>
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

            var sptable = $('#top-selling').DataTable({
                "scrollX": true,
                "order": [
                    [1, "desc"]
                ],
                "bInfo": true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Top Selling Products_" + date,
                        title: "Top Selling Products",
                        messageTop: duration,
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        filename: "Top Selling Products_" + date,
                        title: shop_name + " \n Top Selling Products \n" +duration,
                    }
                ],
            });
            sptable.buttons().container().appendTo('#top-selling_wrapper .col-md-6:eq(1)');
        });
    </script>
@endsection
