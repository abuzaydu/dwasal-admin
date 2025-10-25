@extends('layouts.acc')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
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
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('f-expense-payments')}}" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <select name="pay_mode" class="form-select form-select-sm mb-1" onchange="this.form.submit()">
                            <option value="">All Payments</option>
                            @foreach($paytypes as $type)
                            @if($type == $pay_mode)
                            <option selected>{{$type}}</option>
                            @else
                            <option>{{$type}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-md-8 mb-1">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
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
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2 nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#expense-payments" role="tab" aria-selected="false">Expense Payments</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#manage" role="tab" aria-selected="false">Manage Expense Payments</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="expense-payments" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="payments" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.expense_type')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.expense_date')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.pv_no')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date_of_pay')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.payment_mode')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($payments as $payment)
                                        <?php  $total += $payment->amount; ?>
                                        <tr>
                                            <td style="text-align: center;">{{$payment->expense_type}}</td>
                                            <td style="text-align: center;">{{date('d-m-Y h:i:s A', strtotime($payment->time_created))}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{ route('expense-payments.show', encrypt($payment->id)) }}">{{sprintf('%04d', $payment->pv_no)}}</a></td>
                                            <td style="text-align: center;">{{date('d-m-Y', strtotime($payment->pay_date))}}</td>
                                            <td style="text-align: center;">{{$payment->pay_mode}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total, 2, '.', ',')}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="manage" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="mpayments" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">{{trans('navmenu.expense_type')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.expense_date')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.pv_no')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.date_of_pay')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.payment_mode')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                            <th style="text-align: center;">{{ trans('navmenu.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($payments as $index => $payment)
                                        <?php  $total += $payment->amount; ?>
                                        <tr>
                                            <td style="text-align: center;">{{$payment->expense_type}}</td>
                                            <td style="text-align: center;">{{date('d-m-Y h:i:s A', strtotime($payment->time_created))}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{ route('expense-payments.show', encrypt($payment->id)) }}">{{sprintf('%04d', $payment->pv_no)}}</a></td>
                                            <td style="text-align: center;">{{date('d-m-Y', strtotime($payment->pay_date))}}</td>
                                            <td style="text-align: center;">{{$payment->pay_mode}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                            <td>
                                                <a href="{{ route('expense-payments.edit', encrypt($payment->id)) }}"> <i class="fa fa-edit" style="color: blue;"></i></a> |
                                                <form id="delete-form-{{ $index }}" method="POST" action="{{ route('expense-payments.destroy', encrypt($payment->id)) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" class="button" onclick="confirmDelete('{{ $index }}')"><i class="fa fa-trash" style="color: red;"></i></a>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

            var collectable = $('#payments').DataTable({
                "scrollX": true,
                "order": [
                    [2, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "Expense Payments_" + date,
                        title: " Expense Payments",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        orientation: 'landscape',
                        filename: "Expense Payments_" + date,
                        title: shop_name + "\n Expense Payments \n"+duration
                    }
                ],
            });
            collectable.buttons().container().appendTo('#payments_wrapper .col-md-6:eq(1)');

            $('#mpayments').DataTable({
                "scrollX": true,
                "order": [
                    [2, "desc"]
                ]
            })
        })
    </script>
@endsection