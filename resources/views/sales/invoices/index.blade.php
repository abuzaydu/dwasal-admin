@extends('layouts.app')

@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
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
                document.getElementById('delete-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
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
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                @if(Auth::user()->can('view-delivery-note'))
                <a class="btn btn-primary btn-sm" href="{{ url('delivery-notes') }}">{{ trans('navmenu.delivery_notes') }}</a>
                @endif
                @if(Auth::user()->can('view-credit-note'))
                <a class="btn btn-warning btn-sm" href="{{ url('credit-notes') }}">{{ trans('navmenu.credit_notes') }}</a>
                @endif
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="row g-1">
                    <div class="col-md-6">
                        <form class="form" method="POST" action="{{ url('an-sales') }}">
                            @csrf
                            <div class="col-md-12">
                                <div class="input-group mb-0">
                                    <input type="text" class="form-control form-control-sm mb-1" name="search_key" placeholder="Search Any Invoice by Invoice Number,  Customer or User" autocomplete="off" aria-label="Input Keayword" required>
                                    <button class="btn btn-default btn-sm" type="submit" id="button-addon2"><i class='fa fa-search'></i> Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form class="dashform row g-1" action="{{ url('an-sales') }}" method="POST">
                            @csrf
                            <div class="col-sm-3">
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="sale_date" id="saledate" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" autocomplete="off">
                                </div>
                            </div>
                            <input type="hidden" name="start_date" id="start_input" value="">
                            <input type="hidden" name="end_date" id="end_input" value="">
                            <!-- Date and time range -->
                            <div class="col-sm-9">
                                <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                            </div>
                            <!-- /.form group -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row clearfix">
        <div class="card">
            <div class="card-body">
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="manage-sales" role="tabpanel">
                        <div class="table-responsive">
                            <table id="del-multiple" class="table table-striped table-bordered display nowrap">
                                <thead style="font-weight: bold;">
                                    <tr>
                                        <th></th>
                                        <th style="text-align: center;">{{ trans('navmenu.saledate') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.user') }}</th>
                                        @if($settings->is_school)
                                        <th style="text-align: center;">{{ trans('navmenu.student_name') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.grade') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.year_of_study') }}</th>
                                        @else
                                        <th style="text-align: center;">@if($settings->is_cm_business) Rider @else {{ trans('navmenu.customer') }}@endif</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.invoice_no')}}</th>
                                        <th style="text-align: center;">Net Sale Amount</th>
                                        <th style="text-align: center;">{{ trans('navmenu.paid') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.unpaid') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.sale_type') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.status') }}</th>
                                        <th style="text-align: center;">{{trans('navmenu.created_at')}}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.last_updated') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $total_amount = 0; $total_paid = 0; ?>
                                    @foreach ($sales as $index => $sale)
                                        <?php 
                                            $tnetsales = ($sale->sale_amount-$sale->sale_discount)+$sale->tax_amount;
                                            $tnetreturn = ($sale->return_amount-$sale->return_discount)+$sale->return_tax;
                                            $netsales_amount = $tnetsales-$tnetreturn;
                                            $total_amount += $netsales_amount;
                                            $total_paid += $sale->sale_amount_paid; 
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">{{ $sale->id }}</td>
                                            <td style="text-align: center;">{{ date('d, M Y H:i:s', strtotime($sale->time_created)) }}</td>
                                            <td>{{ $sale->first_name }}</td>
                                            @if ($settings->is_school)
                                            <td><a href="{{ route('an-sales.show', encrypt($sale->id)) }}">{{ $sale->name }}</a></td>
                                            <td style="text-align: center;">
                                                    @if (!is_null($sale->grade_id))
                                                        {{ App\Grade::find($sale->grade_id)->name }}
                                                    @endif
                                            </td>
                                            <td style="text-align: center;">{{ $sale->year }}</td>
                                            @else
                                            <td><a href="{{ route('an-sales.show', encrypt($sale->id)) }}">{{ $sale->name }}</a></td>
                                            @endif
                                            <td style="text-align: center;"><a href="{{ route('invoices.show', encrypt($sale->id)) }}">{{ sprintf('%04d', $sale->invoice_no)}}</a></td>
                                            <td style="text-align: center;">{{ number_format($netsales_amount, 2, '.', ',') }}</td>
                                            <td style="text-align: center;">{{ number_format($sale->sale_amount_paid, 2, '.', ',') }}</td>
                                            <td style="text-align: center;">{{ number_format($netsales_amount - $sale->sale_amount_paid, 2, '.', ',') }}</td>
                                            <td style="text-align: center;">{{$sale->sale_type}}</td>
                                            <td style="text-align: center;">
                                                @if ($sale->status == 'Paid')
                                                    @if (app()->getLocale() == 'en')
                                                        <span class="badge rounded-pill bg-success">{{ $sale->status }}</span>
                                                    @else
                                                        <span
                                                            class="badge rounded-pill bg-success">{{ trans('navmenu.paid_sale') }}</span>
                                                    @endif
                                                @elseif($sale->status == 'Partially Paid')
                                                    @if (app()->getLocale() == 'en')
                                                        <span class="badge rounded-pill bg-primary">{{ $sale->status }}</span>
                                                    @else
                                                        <span
                                                            class="badge rounded-pill bg-primary">{{ trans('navmenu.partially_paid') }}</span>
                                                    @endif
                                                @elseif($sale->status == 'Excess Paid')
                                                    @if (app()->getLocale() == 'en')
                                                        <span
                                                            class="badge rounded-pill bg-warning text-dark">{{ $sale->status }}</span>
                                                    @else
                                                        <span
                                                            class="badge rounded-pill bg-warning text-dark">{{ trans('navmenu.excess_paid') }}</span>
                                                    @endif
                                                @else
                                                    @if (app()->getLocale() == 'en')
                                                        <span class="badge rounded-pill bg-danger">{{ $sale->status }}</span>
                                                    @else
                                                        <span
                                                            class="badge rounded-pill bg-danger">{{ trans('navmenu.un_paid') }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td style="text-align: center;">{{date('d-m-Y H:i:s', strtotime($sale->created_at))}}</td>
                                            <td style="text-align: center;">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sale->updated_at)->diffForHumans() }} </td>
                                            <td>
                                                <!-- <a href="{{ url('issue-vfd/' . encrypt($sale->id)) }}" title="VFD Receipt"><i class="fa fa-receipt"></i></a> | -->
                                                <!-- <a href="{{ url('print-receipt/'.encrypt($sale->id)) }}"><i class="fa fa-eye"></i></a> |  -->
                                                <a href="{{ url('create-dnote/' . encrypt($sale->id)) }}" title="Create Delivery Note" style="color: black;"><i class="fa fa-file"></i></a> |
                                                @if ($sale->status == 'Partially Paid' || $sale->status == 'Unpaid')
                                                    <a href="{{ url('send-sms/' . encrypt($sale->id)) }}" title="{{ trans('navmenu.send_sms') }}" style="color: orange;"><i class="fa fa-send"></i></a> |
                                                @endif
                                                @if(Auth::user()->can('edit-invoice'))
                                                <a href="{{ route('an-sales.edit', encrypt($sale->id)) }}" title="Edit"><i class="fa fa-edit"></i> </a> |
                                                @endif
                                                @if(Auth::user()->can('cancel-invoice'))
                                                <form id="delete-form-{{ $index }}" method="POST"
                                                    action="{{ route('an-sales.destroy', encrypt($sale->id)) }}"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" title="Cancel Invoice" onclick="confirmDelete('<?php echo $index; ?>')"><i class="fa fa-times" style="color: red;"></i></a>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                @if(Auth::user()->can('view-invoices-total'))
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        @if ($settings->is_school)
                                            <th>{{ trans('navmenu.total') }}</th>
                                            <th></th>
                                            <th></th>
                                        @else
                                            <th>{{ trans('navmenu.total') }}</th>
                                        @endif
                                        <th></th>
                                        <th style="text-align: center;"><b>{{ number_format($total_amount, 2, '.', ',') }}</b></th>
                                        <th style="text-align: center;"><b>{{ number_format($total_paid, 2, '.', ',') }}</b></th>
                                        <th style="text-align: center;"><b>{{ number_format($total_amount - $total_paid, 2, '.', ',') }}</b></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                        <form id="frm-example" action="{{ url('delete-multiple-sales') }}" method="POST">
                            @csrf
                            <button id="submitButton" class="btn btn-danger btn-sm">{{ trans('navmenu.delete_selected') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('side/assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 
    <script src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>

    <script>
        $(function () {

            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('side/assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('side/assets/vendor/libs/Swahili.json') }}";
            }

            //Exportable table
            $('#cashsales').DataTable();
            $('#creditsales').DataTable();

            var deltable = $('#del-multiple').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                'columnDefs': [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                }],
                'select': {
                    'style': 'multi'
                },
                // 'order': [[1, 'asc']]
            })

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false) : $('#submitButton').prop(
                    "disabled", true);
                counterChecked < 0 ? counterChecked = 0 : counterChecked;
                console.log(counterChecked);
            });

            $('#submitButton').on('click', function(e) {
                e.preventDefault();
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
                        $('#frm-example').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });

              // Handle form submission event 
            $('#frm-example').on('submit', function(e) {
                var form = this;
                var rows_selected = deltable.column(0).checkboxes.selected();
                if (rows_selected.length > 0) {
                    // Iterate over all selected checkboxes
                    $.each(rows_selected, function(index, rowId) {
                        // Create a hidden element 
                        $(form).append(
                            $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'ids[]')
                            .val(rowId)
                        );
                    });
                }
            });
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
