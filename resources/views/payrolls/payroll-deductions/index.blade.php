@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
    <script type="text/javascript">
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');
            var newtitle = document.getElementById('new-title');
            var listtitle = document.getElementById('list-title');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                newbtn.style.display = 'none';
                listtitle.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newtitle.style.display = 'none';
                newbtn.style.display = 'block';
                listtitle.style.display = 'block';
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure, You want to delete this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Delete',
                denyButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                    Swal.fire('Deleted!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Record not deleted', '', 'info')
                }
            })
        }
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                <form class="dashform row g-3" action="{{ url('f-payroll-deductions')}}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-sm-12">
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="mytable p-0 border rounded">
                        <table id="payroll-deductions" class="table table-striped align-middle table-hover mb-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th style="text-align: center;">Amount</th>
                                    <th style="text-align: center;">Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deductions as $key => $deduction)
                                <?php 
                                    $paid = App\Models\PayrollDeductionPayment::where('payroll_deduction_id', $deduction->id)->sum('amount_paid');
                                    $status = 'Unpaid';
                                    if ($deduction->amount == $paid) {
                                        $status = 'Paid';
                                    }elseif ($paid > 0 && $paid < $deduction->amount) {
                                        $status = 'Partially Paid';
                                    }
                                ?>
                                <tr>
                                    <td class="width45">
                                        {{$key+1}}
                                    </td>
                                    <td>
                                        <a href="{{ route('payroll-deductions.show', encrypt($deduction->id)) }}">{{ $deduction->name }}</a>
                                    </td>
                                    <td style="text-align: center;">{{ date('d/m/Y', strtotime($deduction->date)) }}</td>
                                    <td style="text-align: center;">{{ number_format($deduction->amount, 2, '.', ',') }}</td>
                                    <td style="text-align: center;">{{ $status }}</td>
                                    <td>
                                        
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


    <!-- Modal -->  
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{ route('payroll-deduction-payments.store')}}">
                @csrf
                <div class="modal-body row">
                    <input type="hidden" name="purchase_id" id="id_hide">
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>                         
                                <input type="text" name="pay_date" id="pay_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" step="any" name="amount" required placeholder="{{trans('navmenu.hnt_amount_paid')}}" class="form-control form-control-sm mb-3">
                        </div>
                        @if($settings->allow_multi_currency)
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-3" onchange="wegCurr(this)" required>
                                    @foreach($currencies as $curr)
                                    <option>{{$curr->code}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" id="rate-mode-col" style="display: none;">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select id="ex-rate-mode" name="ex_rate_mode"  class="form-select form-select-sm mb-3" onchange="wegRate(this)">
                                </select>
                            </div>
                            <div class="col-md-3" id="locale" style="display: none;">
                                <label class="form-label" id="locale-label"></label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="col-md-3" id="foreign" style="display: none;">
                                <label class="form-label">Rate Amount in {{$defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3">
                            </div>
                        @endif
                            
                        <div class="col-md-3">
                            <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="pay_mode" onchange="detailUpdate(this)" required>
                                <option value="Cash">{{trans('navmenu.cash')}}</option>
                                <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                <option value="Bank">{{trans('navmenu.bank')}}</option>
                                <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-3" id="cashaccount">
                            <label class="form-label">Cash Account </label>
                            <select class="form-select form-select-sm mb-1" name="cash_acc_id"> 
                                @foreach($accounts->where('type', 'Cash') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3" id="deposit_mode" style="display: none;">
                            <label class="form-label">Deposit Mode</label>
                            <select name="deposit_mode" class="form-select form-select-sm mb-3">
                                <option>Direct Deposit</option>
                                <option>Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="bankdetail" style="display: none;">
                            <label class="form-label">Bank Account </label>
                            <select name="bank_acc_id" class="form-select form-select-sm mb-1">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Bank') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>                          
                        </div>

                        <div class="col-md-3" id="cheque" style="display: none;">
                            <label class="form-label">Cheque Number</label>
                            <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-md-3" id="expire" style="display: none;">
                            <label class="form-label">Expire Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div> 
                                <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-control-sm mb-3">
                            </div>
                        </div>

                        <div class="col-md-3" id="slip" style="display: none;">
                            <label class="form-label">Bank Slip Number</label>
                            <input id="name" type="text" name="slip_no" placeholder="Please enter Bank Slip number" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-3" id="mobaccount" style="display: none;">
                            <label class="form-label">Mobile Money Account </label>
                            <select class="form-select form-select-sm mb-1" name="mob_acc_id">
                                <option value="">---{{trans('navmenu.select')}}---</option>
                                @foreach($accounts->where('type', 'Mobile Money') as $acc)
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control form-control-sm mb-3" rows="1" name="comments" placeholder="Enter Comments (Optional)...."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    </div>
                </form>
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
        });

        function showModal(id) {
            $('#id_hide').val(id);
            $('#payModal').modal('show');
        }
    </script>
@endsection
    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function()
        {
            var $start = document.querySelector('[name="loan_date"]');


            $start.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>