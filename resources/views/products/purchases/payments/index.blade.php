@extends('layouts.inv')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item">Purchases</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right pt-0">
                <form class="dashform row g-3" action="{{url('f-sale-payments')}}" method="POST">
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
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#all-collections"><i class='fa fa-list'></i> {{$page}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#excess-payments"><i class='fa fa-list-alt'></i> Excess Payments</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('total-purchase-payments') }}" class="btn btn-primary btn-sm">Total Purchase Payments</a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="all-collections" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="sale-payments" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.supplier')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.pv_no')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date_of_pay')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.payment_mode')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.purchase_date')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.purchase_type')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($payments as $payment)
                                        <?php  
                                            $total += $payment->amount;
                                            $trans = App\Models\SupplierTransaction::find($payment->trans_id);
                                            $user = null;
                                            if (!is_null($trans)) {
                                                $user = App\Models\User::find($trans->user_id);
                                            }
                                        ?>
                                        <tr>
                                            <td>{{$payment->name}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{ route('purchase-payments.show', encrypt($payment->id)) }}">{{sprintf('%04d', $payment->pv_no)}}</a></td>
                                            <td style="text-align: center;">{{date('d-m-Y', strtotime($payment->pay_date))}}</td>
                                            <td style="text-align: center;">{{$payment->account}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{date('d-m-Y h:i:s A', strtotime($payment->time_created))}}</td>
                                            <td style="text-align: center;">{{$payment->purchase_type}}</td>
                                            <td style="text-align: center;">
                                                @if(!is_null($user)) {{$user->first_name}}@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total, 2, '.', ',')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="excess-payments" role="tabpanel">
                            <div class="col-xs-12 table-responsive">
                                <table id="exc-payments" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>{{trans('navmenu.supplier')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($excpayments as $payment)
                                        <tr>
                                            <td><a href="{{ url('supplier-account-stmt/'.encrypt($payment['supplier_id'])) }}">{{ $payment['name'] }}</a></td>
                                            <td style="text-align: center;">{{number_format($payment['amount'], 2, '.', ',')}}</td>
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

            var collectable = $('#sale-payments').DataTable({
                "scrollX": true,
                "order": [
                    [5, "desc"]
                ],
                'bInfo': true,
                buttons: [
                    {
                        extend: 'excel',
                        footer: true,
                        filename: "{{ trans('navmenu.collections_report') }}_" + date,
                        title: " {{ trans('navmenu.collections_report') }}",
                        messageTop: duration
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        orientation: 'landscape',
                        filename: "{{ trans('navmenu.collections_report') }}_" + date,
                        title: shop_name + "\n {{ trans('navmenu.collections_report') }} \n"+duration
                    }
                ],
            });
            collectable.buttons().container().appendTo('#sale-payments_wrapper .col-md-6:eq(1)');

            $('#exc-payments').DataTable({
                'scrollX': true,
                'bInfo': true,
            })
        })
    </script>
@endsection