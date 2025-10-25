
@extends('layouts.app')

@section('content')

    <div class="col-md-3">

        <div class="card radius-6">
            <div class="card-body">
                <h5 class="profile-username text-center">Invoice Payment Status</h5>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>Total amount</b> <a class="float-end"> TZS {{number_format($grandtotal)}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Total Paid</b> <a class="float-end"> TZS {{number_format($total_paid)}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Total Unpaid</b> <a class="float-end"> TZS {{number_format($grandtotal-$total_paid)}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Due Date</b> <a class="float-end">{{date("d, M Y", strtotime($invoice->due_date))}}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card radius-6">
            <div class="card-header text-center">
                <h4>Add New Payment</h4>
            </div>
            <div class="card-body">
                <form class="form" method="POST" action="{{ route('inv-payments.store')}}">
                    @csrf
                    <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                    <div class="row">
                        
                        <div class="col-md-4">
                            <label>Pay Date</label>
                            <div class="input-group date">
                                <div class="input-group-text">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="pay_date" id="pay_date" placeholder="Choose date payment" class="form-control" required>
                                
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Amount Paid <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required placeholder="Please enter Amount Paid" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Pay Mode <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="pay_mode" required>
                                <option value="">Select A Payment Mode</option>
                                <option>Bank</option>
                                <option>Cash</option>
                                <option>Mobile Money</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Bank Name </label>
                            <input id="name" type="text" name="bank_name" placeholder="Please enter Bank Name" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Bank Branch </label>
                            <input id="name" type="text" name="bank_brach" placeholder="Please enter Bank Branch" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Cheque Number</label>
                            <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control">
                        </div>
                            
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="reset" class="btn btn-default">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card radius-6">
            
            <div class="card-body">
                <form id="frm-example" action="{{url('delete-multiple-inv-payments')}}" method="POST">
                    @csrf
                    <table id="del-multiple" class="table table-striped display nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Pay Date</th>
                                <th>Amount</th>
                                <th>Pay Mode</th>
                                <th>Bank Name</th>
                                <th>Bank Branch</th>
                                <th>Cheque No</th>
                                <th>Time Recorded</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $index => $payment)
                            <tr>
                                <td>{{$payment->id}}</td>
                                <td>{{$payment->pay_date}}</td>
                                <td>{{$payment->amount}}</td>
                                <td>{{$payment->pay_mode}}</td>
                                <td>{{$payment->bank_name}}</td>
                                <td>{{$payment->bank_brach}}</td>
                                <td>{{$payment->cheque_no}}</td>
                                <td>{{$payment->created_at}}</td>
                                <td>
                                    <a href="{{ route('inv-payments.edit', encrypt($payment->id))}}">
                                        <i class="fa fa-edit" style="color: blue;"></i>
                                    </a>
                                    <a href="{{url('inv-payments/destroy/'.encrypt($payment->id))}}" onclick="return confirm('Are you sure you want to delete this record')">
                                        <i class="fa fa-trash" style="color: red;"></i>
                                    </a>      
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Pay Date</th>
                                <th>Amount</th>
                                <th>Pay Mode</th>
                                <th>Bank Name</th>
                                <th>Bank Branch</th>
                                <th>Cheque No</th>
                                <th>Time Recorded</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                    <button id="submitButton" class="btn btn-danger">Delete Selected</button>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>

@endsection
<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]'),
                $max = document.querySelector('[name="end_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

        });
    </script>