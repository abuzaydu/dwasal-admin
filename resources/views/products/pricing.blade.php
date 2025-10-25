@extends('layouts.inv')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right pt-0">
                <div class="d-flex align-items-end  px-1 py-1">
                    <ul class="nav nav-tabs nav-tabs-new2" role="tablist">
                        @if($shop->business_type_id == 4 || $settings->is_manufacturing_with_service)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#product_list" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">{{ trans('navmenu.products') }}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#service_list" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='fa fa-left-indent font-18 me-1'></i></div>
                                    <div class="tab-title">{{ trans('navmenu.services') }}</div>
                                </div>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-1">
                        @if($shop->business_type_id != 3)
                        <div class="tab-pane fade show active" id="product_list" role="tabpanel">
                            <table id="example2" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        @if ($pnos > 0)
                                        <th>{{ trans('navmenu.product_code') }}</th>
                                        @endif
                                        @if ($settings->use_barcode)
                                        <th>{{ trans('navmenu.barcode') }}</th>
                                        @endif
                                        @if ($pls > 0)
                                        <th style="text-align: center;">{{ trans('navmenu.location') }}</th>
                                        @endif
                                        <th style="text-align: center;">{{ trans('navmenu.product_name') }}</th>
                                            @if (Auth::user()->can('view-stock'))
                                        <th style="text-align: center;">{{ trans('navmenu.in_stock') }}</th>
                                        @endif
                                        <th style="text-align: center;">{{ trans('navmenu.basic_uom') }}</th>
                                        @if (Auth::user()->can('view-purchase-cost'))
                                        <th style="text-align: center;">{{ trans('navmenu.unit_cost') }}</th>
                                        @endif
                                        <th style="text-align: center;">{{ trans('navmenu.selling_per_unit') }}</th>
                                        @if ($settings->retail_with_wholesale)
                                        <th style="text-align: center;">{{ trans('navmenu.wholesaleprice') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($prices as $index => $price)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            @if ($pnos > 0)
                                                <td>{{ $price->product_code }}</td>
                                            @endif
                                            @if ($settings->use_barcode)
                                                <td>{{ $price->barcode }}</td>
                                            @endif
                                            @if ($pls > 0)
                                                <td style="text-align: left;">{{ $price->location }}</td>
                                            @endif
                                            <td>{{ $price->name }}</td>
                                            @if (Auth::user()->can('view-stock'))
                                                <td style="text-align: center;">{{ $price->in_stock+0 }}</td>
                                            @endif
                                            <td style="text-align: center;">{{ $price->basic_uom }}</td>
                                            @if (Auth::user()->can('view-purchase-cost'))
                                            <td style="text-align: center;">{{ number_format($price->unit_cost, 2, '.', ',') }}</td>
                                            @endif
                                            <td style="text-align: center;">{{ number_format($price->retail_price, 2, '.', ',') }}</td>
                                            @if ($settings->retail_with_wholesale)
                                            <td style="text-align: center;">{{ number_format($price->wholesale_price, 2, '.', ',') }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.tab-pane -->
                        @endif

                        @if($shop->business_type_id == 3)
                        <div class="tab-pane fade show active" id="service_list" role="tabpanel">
                            <table id="example7" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('navmenu.service') }}</th>
                                        <th>{{ trans('navmenu.price') }}</th>
                                        @if ($settings->is_vat_registered)
                                        <th>{{ trans('navmenu.price') }} + {{ trans('navmenu.vat') }} </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($serv_prices as $index => $serv_price)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $serv_price->name }}</td>
                                        <td>{{ number_format($serv_price->price) }}</td>
                                        @if ($settings->is_vat_registered)
                                        <td>{{ number_format($serv_price->price_vat) }}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @elseif($shop->business_type_id == 4 || $settings->is_manufacturing_with_service)
                        <div class="tab-pane fade" id="service_list" role="tabpanel">
                            <table id="example7" class="table table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('navmenu.service') }}</th>
                                        <th>{{ trans('navmenu.price') }}</th>
                                        @if ($settings->is_vat_registered)
                                        <th>{{ trans('navmenu.price') }} + {{ trans('navmenu.vat') }} </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($serv_prices as $index => $serv_price)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $serv_price->name }}</td>
                                        <td>{{ number_format($serv_price->price) }}</td>
                                        @if ($settings->is_vat_registered)
                                        <td>{{ number_format($serv_price->price_vat) }}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                    <!-- nav-tabs-custom -->
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
@endsection

@section('page-scripts')
     <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(function(){
                    
            var table = $('#example2').DataTable({
                'scrollX': true,
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(1)');

            var table7 = $('#example7').DataTable({
                'scrollX': true,
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table7.buttons().container()
                .appendTo('#example7_wrapper .col-md-6:eq(1)');


        })
    </script>
@endsection