@extends('layouts.acc')
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
        
        function detailUpdate(elem) {
            var b = document.getElementById('bankdetail');
            var m = document.getElementById('mobaccount');
            var ca = document.getElementById('cashaccount');
            var dpm = document.getElementById('deposit_mode');
            var chq = document.getElementById('cheque');
            var slip = document.getElementById('slip');
            var expire = document.getElementById('expire');
            if (elem.value === 'Bank' || elem.value === 'Cheque') {
                b.style.display = 'block';
                m.style.display = 'none';
                ca.style.display = 'none';
                if (elem.value === 'Bank') {
                    m.style.display = 'none';
                    dpm.style.display = "block";
                    slip.style.display = 'block'
                    chq.style.display = 'none';
                    expire.style.display = "none";
                }else{
                    m.style.display = 'none';
                    dpm.style.display = 'none';
                    slip.style.display = "none";
                    chq.style.display = "block";
                    expire.style.display = "block";
                }
            }else if (elem.value === 'Mobile Money') {
                ca.style.display = 'none';
                b.style.display = 'none';
                dpm.style.display = "none";
                slip.style.display = 'none'
                chq.style.display = 'none';
                expire.style.display = "none";
                m.style.display = 'block';
            }else{
                ca.style.display = 'block';
                b.style.display = 'none';
                m.style.display = 'none';
                dpm.style.display = 'none';
                slip.style.display = "none";
                chq.style.display = "none";
                expire.style.display = "none";
            }
        }
    </script>
@section('content')
    <div class="block-header pt-4">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                <button type="button" id="new-act-btn" class="btn btn-primary btn-sm float-end mb-3" onclick="showHideForm('show')"><i class="fa fa-plus-square"></i> New Employee Loan Request</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="p-0 border rounded" id="new-form" style="display: none;">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('emp-loans.store') }}">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">Employee <span style="color: red;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="employee_id" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach($employees as $employee)
                                    <option value="{{$employee->id}}">{{$employee->fname}} - {{$employee->lname}}</option>
                                    @endforeach
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please select an Employee.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="validationCustom02" class="form-label"> Date </label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" class="form-control form-control-sm mb-1" name="loan_date"  placeholder="Pick Start Date" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount <span style="color: red;">*</span></label>
                                <input type="number" min="0" step="any" name="amount" class="form-control form-control-sm mb-1" placeholder="Enter Loan Amount" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please Enter Loan Amount.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Return Rate (Percent) <span style="color: red;">*</span></label>
                                <input type="number" min="0" step="any" name="return_rate" class="form-control form-control-sm mb-1" placeholder="Enter Loan Return rate (Percent)" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please Enter Loan Return rate.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control form-control-sm mb-1" placeholder="Enter Remarks">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="pay_mode" onchange="detailUpdate(this)" required>
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
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
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
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm px-4 radius-30" type="submit"> Submit</button>
                                <a href="javascript:;" onclick="showHideForm('hide')" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                            
                    <div class="mytable p-0 border rounded">
                        <table id="emp-loans" class="table table-striped align-middle table-hover mb-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th style="text-align: center;">Amount</th>
                                    <th style="text-align: center;">Return Rate(%)</th>
                                    <th style="text-align: center;">Amount Paid</th>
                                    <th style="text-align: center;">Status</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emploans as $key => $emploan)
                                <tr>
                                    <td class="width45">
                                        {{$key+1}}
                                    </td>
                                    <td><span>{{ $emploan->emp_id }}</span></td>
                                    <td>
                                        <a href="{{ route('emp-loans.show', encrypt($emploan->id)) }}">{{ $emploan->fname }} {{ $emploan->fname }}</a>
                                    </td>
                                    <td style="text-align: center;">{{ date('d M, Y', strtotime($emploan->loan_date)) }}</td>
                                    <td style="text-align: center;">{{ number_format($emploan->amount, 2, '.', ',') }}</td>
                                    <td style="text-align: center;">{{ $emploan->return_rate+0 }}</td>
                                    <td style="text-align: center;">{{ number_format($emploan->amount_paid, 2, '.', ',') }}</td>
                                    <td style="text-align: center;">{{ $emploan->status }}</td>
                                    <td>{{ $emploan->remarks }}</td>
                                    <td>
                                        @if(!$emploan->is_approved)
                                        <a class="text-primary" href="{{ route('emp-loans.edit', encrypt($emploan->id)) }}"><i class='fa fa-pencil mr-1'></i> Edit</a> | 
                                        <form id="delete-form-{{$key}}" method="POST" action="{{ route('emp-loans.destroy', encrypt($emploan->id))}}" style="display: inline;"> 
                                            @csrf
                                            @method("DELETE")
                                            <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $key; ?>')"><i class='fa fa-trash mr-1'></i> Delete</a>
                                        </form>
                                        @endif
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
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    
    <script>
        $(function () {
            //Exportable table
            $('#emp-loans').DataTable({
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
            var $start = document.querySelector('[name="loan_date"]');


            $start.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>