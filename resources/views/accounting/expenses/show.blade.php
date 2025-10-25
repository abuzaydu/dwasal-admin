@extends('layouts.acc')

    <script type="text/javascript">
        function confirmApproval(id) {
            Swal.fire({
                title: 'Are you sure to approve the requested Expense?',
                showDenyButton: true,
                confirmButtonText: 'Yes Approve',
                denyButtonText: `Don't Approve`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    window.location.href = 'approve-expense/'+id;
                    Swal.fire('Approved!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Not Approved', '', 'info')
                }
            })
        }
    </script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item"><a href="{{ url('expenses') }}">Expenses</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-3 col-sm-12 text-right">
            
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="payslip">
                        <div class="col-md-12">
                            <table class="table table-striped display nowrap" style="width: 100%; font-size: 14px;">
                                <?php $user = App\Models\User::find($expense->user_id); ?>
                                <tbody>
                                    <tr>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <td>{{ date('d M Y H:i:s', strtotime($expense->time_created)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{trans('navmenu.expense_type')}}</th>
                                        <td>{{$expense->expense_type}}</td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td>{{ number_format($expense->amount, 2, '.', ',') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created By</th>
                                        <td>{{$user->first_name}} {{$user->last_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{$expense->status}}</td>
                                    </tr>
                                    @if($expense->status == 'Rejected')
                                    <tr>
                                        <th>Reject Reason : </th>
                                        <td><span class="text-danger">{{$expense->reject_reason}}</span></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Approved / Rejected By</th>
                                        <td>{{$expense->approved_by}}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved / Rejected At</th>
                                        <td>{{$expense->approved_at}}</td>
                                    </tr>
                                    @if($exppays->count() > 0)
                                    <tr>
                                        <td>Payments</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Date</th>
                                        <th>PV No</th>
                                        <th>Paid From</th>
                                        <th>Account</th>
                                        <th>Amount</th>
                                    </tr>
                                    @foreach($exppays as $exppay)
                                    <?php $account = App\Models\AccountStatement::where('exp_supplier_transaction_id', $exppay->trans_id)->join('accounts', 'accounts.id', '=', 'account_statements.account_id')->select('account_number', 'account_name')->first(); ?>
                                    <tr>
                                        <td>{{ date('d M Y', strtotime($exppay->pay_date)) }}</td>
                                        <td>{{sprintf('%04d', $exppay->pv_no)}}</td>
                                        <td>{{$exppay->pay_mode}}</td>
                                        <td>@if(!is_null($account)){{ $account->account_name }} {{$account->account_number}}@endif</td>
                                        <td>{{ number_format($exppay->amount, 2, '.', ',') }}</td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr/>
                    <div class="text-end">
                        @if($expense->status == 'Awaiting for Approval')
                        <a href="{{ route('expenses.edit', encrypt($expense->id))}}" class="btn btn-sm btn-secondary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                        @if(Auth::user()->can('approve-expense-payment'))
                        <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#rejectModal" id="reject-btn" class="btn btn-sm btn-warning btn-sm"><i class="fa fa-check"></i> Reject</a>
                        <button type="button" onclick="confirmApproval('<?php echo encrypt($expense->id); ?>')" class="btn btn-sm btn-primary btn-sm"><i class="fa fa-check"></i> Approve</button>
                        @endif
                        @endif
                        @if($expense->status == 'Approved' && Auth::user()->can('confirm-expense-payment'))
                        <a href="#"  class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#payModal" data-backdrop="static" data-keyboard="false" style="margin-right: 2px;"><i class="fa fa-money"></i> Confirm Expense Payment</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="addLeaveModal" aria-hidden="false">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Expense rejection reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="basic-form" method="POST" action="{{ url('reject-expense') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="expense_id" value="{{$expense->id}}">
                            <div class="col-md-12 mb-3">
                                <textarea name="reject_reason" class="form-control form-control-sm" placeholder="Enter rejection reason" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="submit()" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('navmenu.add_payment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ url('expense-payments') }}">
                    @csrf
                    <input type="hidden" name="expense_id" value="{{$expense->id}}">
                    <div class="modal-body row">
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('navmenu.pay_date') }}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="pay_date" id="pay_date"
                                    placeholder="{{ trans('navmenu.pick_date') }}"
                                    class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ trans('navmenu.amount_paid') }} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="inputAmount" name="amount" value="{{$expense->amount}}" required placeholder="{{ trans('navmenu.hnt_amount_paid') }}"
                                class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ trans('navmenu.paid_from') }} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="account_id">
                                <option value="">Petty Cash</option>
                                @foreach($accounts as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference</label>
                            <input type="text" name="reference" placeholder="optional" class="form-control form-control-sm mb-1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm">{{ trans('navmenu.btn_save') }}</button>
                        <button type="button" class="btn btn-orange btn-sm"
                            data-bs-dismiss="modal">{{ trans('navmenu.btn_cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
    $( document ).ready(function() {
        inputamt = $("#inputAmount");
        var n = inputamt.val();
        var output = getCommaSeparatedTwoDecimalsNumber(n);
        inputamt.val(output);

        inputamt.on('focus', function(){
            var n = $(this).val();
            let output = parseFloat(n.replace(/,/g, ''));
            $(this).val(output);
        });

        inputamt.on('blur', function(){
            var n = $(this).val();
            var output = getCommaSeparatedTwoDecimalsNumber(n);
            $(this).val(output);
        });
    });

    function getCommaSeparatedTwoDecimalsNumber(number) {
        const fixedNumber = Number.parseFloat(number).toFixed(2);
        return String(fixedNumber).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
</script>
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="pay_date"]');

        $min.DatePickerX.init({
            mondayFirst: true,
            // minDate    : new Date(),
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });
    });
</script>
