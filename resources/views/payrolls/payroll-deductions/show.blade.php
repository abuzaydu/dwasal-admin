@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
    <script type="text/javascript">
        
        function detailUpdate(elem) {
            var b = document.getElementById('bankdetail');
            var m = document.getElementById('mobaccount');
            var ca = document.getElementById('cashaccount');
            if (elem.value === 'Bank' || elem.value === 'Cheque') {
                b.style.display = 'block';
                m.style.display = 'none';
                ca.style.display = 'none';
                if (elem.value === 'Bank') {
                    m.style.display = 'none';
                }else{
                    m.style.display = 'none';
                }
            }else if (elem.value === 'Mobile Money') {
                ca.style.display = 'none';
                b.style.display = 'none';
                m.style.display = 'block';
            }else{
                ca.style.display = 'block';
                b.style.display = 'none';
                m.style.display = 'none';
            }
        }

    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('payroll-deductions') }}">Payroll Deductions</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-md-8 col-sm-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <?php 
                        $pdpayments = App\Models\PayrollDeductionPayment::where('payroll_deduction_id', $deduction->id)->get();
                        $paid = $pdpayments->sum('amount_paid');
                        $status = 'Unpaid';
                        if ($deduction->amount == $paid) {
                            $status = 'Paid';
                        }elseif ($paid > 0 && $paid < $deduction->amount) {
                            $status = 'Partially Paid';
                        }
                    ?>
                    <div class="mytable p-0 border rounded">
                        <table class="table table-striped align-middle table-hover mb-0">
                            <tbody>
                                <tr>
                                    <td>Name</td>
                                    <td><b>{{ $deduction->name }}</b></td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td><b>{{ date('d/m/Y', strtotime($deduction->date)) }}</b></td>
                                </tr>
                                <tr>
                                    <td>Amount</td>
                                    <td><b>{{ number_format($deduction->amount, 2, '.', ',') }}</b></td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td><b>{{ $status }}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if ($deduction->amount > $paid)
                    <div class="border rounded p-4">
                        <h4>Add Payment</h4>
                        <form class="form row g-1" method="POST" action="{{ route('payroll-deduction-payments.store')}}">
                            @csrf
                            <input type="hidden" name="payroll_deduction_id" value="{{$deduction->id}}">
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>                         
                                    <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="number" step="any" name="amount_paid" value="{{$deduction->amount}}" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-3" name="pay_mode" onchange="detailUpdate(this)" required>
                                    <option value="Cash">{{trans('navmenu.cash')}}</option>
                                    <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6" id="cashaccount">
                                <label class="form-label">Cash Account </label>
                                <select class="form-select form-select-sm mb-1" name="cash_acc_id"> 
                                    @foreach($accounts->where('type', 'Cash') as $acc)
                                    <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" id="bankdetail" style="display: none;">
                                <label class="form-label">Bank Account </label>
                                <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                    @foreach($accounts->where('type', 'Bank') as $acc)
                                    <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                    @endforeach
                                </select>                          
                            </div>

                            <div class="col-md-6" id="mobaccount" style="display: none;">
                                <label class="form-label">Mobile Money Account </label>
                                <select class="form-select form-select-sm mb-1" name="mob_acc_id">
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                    @foreach($accounts->where('type', 'Mobile Money') as $acc)
                                    <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference No.</label>
                                <input class="form-control form-control-sm mb-3" name="reference" placeholder="Enter reference">
                            </div>
                            <div class="col-md-12 modal-footer">
                            <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                    @else
                    <div class="border rounded p-4">
                        <table class="table table-striped align-middle table-hover mb-0">
                            <tbody>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount Paid</th>
                                    <th>Payment Method</th>
                                    <th>Reference</th>
                                </tr>
                                @foreach($pdpayments as $payment)
                                <tr>
                                    <td><b>{{ date('d/m/Y', strtotime($payment->pay_date)) }}</b></td>
                                    <td><b>{{ number_format($payment->amount_paid, 2, '.', ',') }}</b></td>
                                    <td><b>{{ $payment->pay_mode }}</b></td>
                                    <td><b>{{ $payment->reference }}</b></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
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
        $(function () {
            //Exportable table
            $('#payroll-deductions').DataTable({
                'scrollX': true,
            });

            $('#cancel-petty-cash').DataTable({
                'scrollX': true,
            });
            $('#branch-petty-cash').DataTable({
                'scrollX': true,
            });
        });
    </script>
@endsection
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function()
        {
            var $start = document.querySelector('[name="pay_date"]');


            $start.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>