@extends('layouts.inv')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item">{{$page}}</li>
                    <li class="breadcrumb-item active"><b>{{$title}}</b></li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{ url('product-sale-history/'.encrypt($product->id)) }}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="form-group col-sm-12">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card radius-10">
                <div class="card-body table-responsive">
                    <table id="example2" class="table table-striped display nowrap" style="width: 100%;">
                        <thead>
                            <th style="width: 10px">#</th>
                            <th>{{trans('navmenu.date')}}</th>
                            <th>{{trans('navmenu.qty')}}</th>
                            <th>{{trans('navmenu.selling')}}</th>
                            <th>{{trans('navmenu.total')}} </th>
                            <th>{{trans('navmenu.discount')}}</th>
                            @if($settings->is_vat_registered)
                            <th>{{trans('navmenu.vat')}} </th>
                            @endif
                            <th>Customer</th>
                            <th>User</th>
                            <td>{{trans('navmenu.actions')}}</td>
                        </thead>
                        <tbody>
                            @foreach($sale_items as $index => $item)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>{{date('d M, Y', strtotime($item->created_at))}}</td>
                                <td>{{$item->quantity_sold}}</td>
                                <td>{{number_format($item->retail_price, 2, '.', ',')}}</td>
                                <td>{{number_format($item->price, 2, '.', ',')}}</td>
                                <td>{{number_format($item->total_discount, 2, '.', ',')}}</td>
                                @if($settings->is_vat_registered)
                                <td>{{number_format($item->tax_amount,2, '.', ',')}}</td>
                                @endif
                                <td>{{$item->customer}}</td>
                                <td>{{$item->first_name}}</td>
                                <td><a href="{{url('edit-sale-item/'.encrypt($item->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a></td>
                            </tr>  
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <h6 class="mb-0 text-uppercase text-center">Product Summary</h6>
            <hr>
            <table class="table table-striped" style="width: 100%;">
                <tbody>
                    <tr>
                        <th>{{trans('navmenu.purchased')}}</th>
                        <td><b>{{$t_in+0}}</b></td>
                    </tr>
                    <tr>
                        <th>Stock Corrections</th>
                        <td><b>{{$diff_qty+0}}</b></td>
                    </tr>
                    <tr>
                        <th>{{trans('navmenu.sold')}}</th>
                        <td><b>{{$t_out+0}}</b></td>
                    </tr>
                    <tr>
                        <th>{{trans('navmenu.returned')}}</th>
                        <td><b>{{$returned+0}}</b></td>
                    </tr>
                    <tr>
                        <th>{{trans('navmenu.transfered')}}</th>
                        <td><b>{{$t_transfer+0}}</b></td>
                    </tr>
                    <tr>
                        <th>@if($settings->is_filling_station) {{trans('navmenu.total_g_or_l')}} @else {{trans('navmenu.damaged')}} @endif</th>
                        <td><b>@if($settings->is_filling_station) {{-($t_dam+0)}} @else {{$t_dam+0}} @endif</b></td>
                    </tr>
                    <tr>
                        <th>{{trans('navmenu.in_stock')}}</th>
                        <td><b>{{$product->in_stock+0}}</b></td>
                    </tr>
                </tbody>
            </table>
            <small style="color: blue;">{{trans('navmenu.in_stock')}} = ({{trans('navmenu.purchased')}}+{{trans('navmenu.returned')}})-(Stock Corrections+{{trans('navmenu.sold')}}+{{trans('navmenu.transfered')}}+@if($settings->is_filling_station) {{trans('navmenu.total_g_or_l')}} @else {{trans('navmenu.damaged')}} @endif)</small>
        </div>
    </div>
@endsection 

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 

    <script>
        $(function () {
            var table = $('#example2').DataTable({
                'scrollX': true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                // lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(1)');

            $('#saledate').on('change', function(){
              $(".dashform").submit();
            })
        });
    </script>
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $max = document.querySelector('[name="sale_date"]');

        $max.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            // minDate    : new Date(),
            maxDate: new Date()
        });
    });
</script>