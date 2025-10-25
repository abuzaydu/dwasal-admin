@extends('layouts.app')

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('del-recy-sale/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmRecycle(id) {
        Swal.fire({
            title: "{{ trans('navmenu.sure_restore') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.yes_restore') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('recycle-sale/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.restored') }}",
                    "{{ trans('navmenu.res_succ') }}",
                    'success'
                )
            }
        })
    }


    function confirmDeletePurchase(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('del-recy-purchase/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
    }

    function confirmRecyclePurchase(id) {
        Swal.fire({
            title: "{{ trans('navmenu.sure_restore') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.yes_restore') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                window.location.href = "{{ url('recycle-purchase/') }}/" + id;
                Swal.fire(
                    "{{ trans('navmenu.restored') }}",
                    "{{ trans('navmenu.res_succ') }}",
                    'success'
                )
            }
        })
    }
</script>
@section('content')
    <div class="box box-warning">
        <div class="box-header">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form class="dashform form-horizontal" action="{{ url('recyclebin') }}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="form-group col-md-4">
                        <div class="input-group">
                            <button type="button" class="btn btn-default pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.form group -->

                </form>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <!-- Custom Tabs (Pulled to the right) -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs pull-right">
                    <li class="active"><a href="#tab_1-1" data-bs-toggle="tab">{{ trans('navmenu.sales') }}</a></li>
                    <li><a href="#tab_2-2" data-bs-toggle="tab">{{ trans('navmenu.sale_items') }}</a></li>
                    <li><a href="#tab_3-3" data-bs-toggle="tab">{{ trans('navmenu.purchases') }}</a></li>
                    <li><a href="#tab_4-4" data-bs-toggle="tab">{{ trans('navmenu.purchase_items') }}</a></li>
                    <li><a href="#tab_5-5" data-bs-toggle="tab">{{ trans('navmenu.expenses') }}</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1-1">
                        <form id="del-sales" action="{{ url('empty-multiple-sales') }}" method="POST">
                            @csrf

                            <table id="del-multiple" class="table table-striped display nowrap"
                                style="width: 100%;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th></th>
                                        <th>{{ trans('navmenu.seller') }}</th>
                                        <th>{{ trans('navmenu.customer') }}</th>
                                        <th>{{ trans('navmenu.sale_amount') }}</th>
                                        <th>{{ trans('navmenu.discount') }}</th>
                                        <th>{{ trans('navmenu.total_payable') }}</th>
                                        <th>{{ trans('navmenu.paid') }}</th>
                                        <th>{{ trans('navmenu.unpaid') }}</th>
                                        <th>{{ trans('navmenu.del_by') }}</th>
                                        <th>{{ trans('navmenu.status') }}</th>
                                        <th>{{ trans('navmenu.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $index => $sale)
                                        <tr>
                                            <td>{{ $sale->id }}</td>
                                            <td>{{ $sale->first_name }}</td>
                                            <td>{{ $sale->name }}</td>
                                            <td>{{ number_format($sale->sale_amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($sale->sale_discount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment, 2, '.', ',') }}
                                            </td>
                                            <td>{{ number_format($sale->sale_amount_paid, 2, '.', ',') }}</td>
                                            <td>{{ number_format($sale->sale_amount - $sale->sale_discount - $sale->adjustment - $sale->sale_amount_paid, 2, '.', ',') }}
                                            </td>
                                            <td>{{ $sale->del_by }} </td>
                                            <td>
                                                @if ($sale->status == 'Paid')
                                                    <div class="color-palette-set" style="text-align: center;">
                                                        <div class="bg-green-active color-palette">
                                                            @if (app()->getLocale() == 'en')
                                                                <span>{{ $sale->status }}</span>
                                                            @else
                                                                <span>{{ trans('navmenu.paid_sale') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($sale->status == 'Partially Paid')
                                                    <div class="color-palette-set" style="text-align: center;">
                                                        <div class="bg-light-blue-active color-palette">
                                                            @if (app()->getLocale() == 'en')
                                                                <span>{{ $sale->status }}</span>
                                                            @else
                                                                <span>{{ trans('navmenu.partially_paid') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif($sale->status == 'Excess Paid')
                                                    <div class="color-palette-set" style="text-align: center;">
                                                        <div class="bg-yellow-active color-palette">
                                                            @if (app()->getLocale() == 'en')
                                                                <span>{{ $sale->status }}</span>
                                                            @else
                                                                <span>{{ trans('navmenu.excess_paid') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="color-palette-set" style="text-align: center;">
                                                        <div class="bg-red-active color-palette">
                                                            @if (app()->getLocale() == 'en')
                                                                <span>{{ $sale->status }}</span>
                                                            @else
                                                                <span>{{ trans('navmenu.un_paid') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="button"
                                                    onclick="confirmRecycle('<?php echo encrypt($sale->id); ?>')">
                                                    <i class="fa fa-recycle"></i> Restore
                                                </a> | <a href="#" class="button"
                                                    onclick="confirmDelete('<?php echo encrypt($sale->id); ?>')"><i class="fa fa-trash"
                                                        style="color: red;"></i> Delete Parmanently</a>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button id="submitButton"
                                class="btn btn-danger">{{ trans('navmenu.delete_selected') }}</button>
                        </form>
                    </div>
                    <!-- tab-pane -->
                    <div class="tab-pane" id="tab_2-2">
                      

                    </div>
                    <!-- /.tab-pane -->
                    
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_4-4">

                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_5-5">

                    </div>
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->

        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection


<link rel="stylesheet" href="css/DatePickerX.css">

<script src="js/DatePickerX.min.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="pay_date"]'),
            $max = document.querySelector('[name="sale_date"]');


        $min.DatePickerX.init({
            mondayFirst: true,
            // minDate    : new Date(),
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });

        $max.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            // minDate    : new Date(),
            maxDate: new Date()
        });
    });
</script>
