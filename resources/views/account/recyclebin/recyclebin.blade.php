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


    function detailUpdate(elem) {
        var b = document.getElementById('bankdetail');
        var m = document.getElementById('mobaccount');

        var dpm = document.getElementById('deposit_mode');
        var chq = document.getElementById('cheque');
        var slip = document.getElementById('slip');
        var expire = document.getElementById('expire');
        if (elem.value === 'Bank' || elem.value === 'Cheque') {
            b.style.display = 'block';
            m.style.display = 'none';
            if (elem.value === 'Bank') {
                dpm.style.display = "block";
                slip.style.display = 'block'
                chq.style.display = 'none';
                expire.style.display = "none";
            } else {
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "block";
                expire.style.display = "block";
            }
        } else if (elem.value === 'Mobile Money') {
            b.style.display = 'none';
            m.style.display = 'block';
        } else {
            b.style.display = 'none';
            m.style.display = 'none';
        }
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div>
        <form class="dashform row g-3" action="{{ url('recyclebin') }}" method="get" id="stockform">
            @csrf
            <div class="col-sm-5"></div>
            <div class="form-group col-sm-3">
                {{-- <div class="input-group mb-1"> <span class="input-group-text" id="basic-addon1"><i
                            class="fa fa-calendar"></i></span>
                    <input type="text" name="sale_date" id="saledate" placeholder="{{ trans('navmenu.pick_date') }}"
                        class="form-control form-control-sm mb-1" autocomplete="off">
                </div> --}}
            </div>
            <input type="hidden" name="start_date" id="start_input" value="">
            <input type="hidden" name="end_date" id="end_input" value="">
            <!-- Date and time range -->
            <div class="form-group col-md-12">
                <div class="input-group d-flex justify-content-between justify-space-between">
                    <select id="searchCustomer" method="get" name="customer" class="form-control"
                        value="{{ old('customer') }}">
                        {{-- <option value="{{$user->roles[0]['name']}}">{{$user->roles[0]['display_name']}}</option> --}}
                        <option value="">--- Select Customer ---</option>
                        @foreach ($customers as $customer)
                            <option value="{{ encrypt($customer->id) }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    {{-- <input id="register-username" type="text" name="last_name" placeholder="Search Here . . ." class="form-control"> --}}
                    <button type="button" class="btn btn-white pull-right" id="reportrange"><span><i
                                class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                </div>
            </div>
            {{-- <div class="form-group col-sm-4">
                
                <div class="input-group">
                    <button type="button" class="btn btn-white pull-right" id="reportrange"><span><i
                                class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                </div>
            </div> --}}
            <!-- /.form group -->
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-success" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="{{ url('recyclebin') }}" role="tab"
                        aria-selected="true">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-list-plus font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.sales') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    {{-- <a class="nav-link" href="{{ url('del-purchases') }}" aria-selected="false"> --}}
                    <a class="nav-link" href="{{ url('recycle-purchases') }}">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-export font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.purchases') }}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ url('recycle-expenses') }}">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-list-check font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.expenses') }}</div>
                        </div>
                    </a>
                </li>
                {{-- <li class="flex align-right" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#credit-sales" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-trash font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.credit_sales') }}</div>
                        </div>
                    </a>
                </li> --}}
                {{-- <li class="nav-item" role="presentation">
                    <a class="nav-link" href="{{ url('sales-returns') }}" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='fa fa-undo font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{ trans('navmenu.sales_returns') }}</div>
                        </div>
                    </a>
                </li> --}}
            </ul>


        </div>
    </div>
    {{-- <div class="btn btn-outline-danger float-right d-flex flex-row justify-content-end"> --}}

    <div class="row">
        <div class="col-md-12 d-flex justify-content-end">
            <form action="{{ url('empty-recycle-sales') }}" method="POST" id="empty-recycle-sales">
                @csrf
                <button type="submit" class="btn btn-outline-danger " id="submitdr"> <i class="fa fa-trash"></i> Empty
                    Sale's Recyclebin</button>
            </form>
        </div>
    </div>

    {{-- </div> --}}





    <div class="tab-content py-3">
        <div class="tab-pane fade show active" id="manage-sales" role="tabpanel">
            <div class="table-responsive">
                <form id="multiple-sales-recycle-form" action="{{ url('del-multiple-recycle-sales') }}" method="POST">
                    @csrf
                    <table class="table table-striped display nowrap" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th></th>
                                <th>#</th>
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
                                    <td>
                                        <input type="checkbox" name="id[]" value="{{ encrypt($sale->id) }}">
                                    </td>
                                    <td>{{ $index + 1 }}</td>
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
                                        <a href="#" class="button" onclick="confirmRecycle('<?php echo encrypt($sale->id); ?>')">
                                            <i class="fa fa-recycle"></i> Restore
                                        </a> | <a href="#" class="button"
                                            onclick="confirmDelete('<?php echo encrypt($sale->id); ?>')"><i class="fa fa-trash"
                                                style="color: red;"></i> Delete Parmanently</a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="action" value="restore">

                    <button id="submitRecycleSales" type="submit" name="action" value="delete"
                        class="btn btn-danger">{{ trans('navmenu.delete_selected') }}</button>
                    <button id="submitRecycleSalesRestore" type="submit" name="action" value="restore"
                        class="btn btn-primary">{{ trans('navmenu.restore_selected') }}</button>
                </form>


                <div class="row">
                    {{ $sales->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">�</span></button>
                    <h4 class="modal-title" id="myModalLabel">{{ trans('navmenu.add_payment') }}</h4>
                </div>
                <form class="form" method="POST" action="{{ route('sale-payments.store') }}">
                    @csrf
                    <input type="hidden" name="an_sale_id" id="id_hide">
                    <div class="modal-body">

                        <div class="form-group col-md-4">
                            <label>{{ trans('navmenu.pay_date') }}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="pay_date" id="pay_date"
                                    placeholder="{{ trans('navmenu.pick_date') }}" class="form-control" required>

                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="label-control">{{ trans('navmenu.amount_paid') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required
                                placeholder="{{ trans('navmenu.hnt_amount_paid') }}" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success">{{ trans('navmenu.btn_save') }}</button>
                        <button type="button" class="btn btn-warning"
                            data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
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
