@extends('layouts.app')
 
@section('content')

    <div class="col-md-9">
        <div class="card radius-6">
            <div class="card-body">
                <form class="form" method="POST" action="{{ route('inv-payments.update', $payment->id)}}">
                    @csrf
              		{{ method_field('PATCH') }} 
                    <div class="row">
                        
                        <div class=" col-md-4">
                            <label>Pay Date</label>
                            <div class="input-group date">
                                <div class="input-group-text">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="pay_date" id="pay_date" placeholder="Choose date payment" class="form-control" required value="{{$payment->pay_date}}">
                                
                            </div>
                        </div>

                        <div class=" col-md-4">
                            <label class="form-label">Amount Paid <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" required placeholder="Please enter Amount Paid" class="form-control" value="{{$payment->amount}}">
                        </div>

                        <div class=" col-md-4">
                            <label class="form-label">Pay Mode <span  style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="pay_mode" required>
                            	<option value="{{$payment->pay_mode}}">{{$payment->pay_mode}}</option>
                                <option value="">Select A Payment Mode</option>
                                <option>Bank</option>
                                <option>Cash</option>
                                <option>Mobile Money</option>
                            </select>
                        </div>
                        <div class=" col-md-4">
                            <label class="form-label">Bank Name </label>
                            <input id="name" type="text" name="bank_name" placeholder="Please enter Bank Name" class="form-control" value="{{$payment->bank_name}}">
                        </div>

                        <div class=" col-md-4">
                            <label class="form-label">Bank Branch </label>
                            <input id="name" type="text" name="bank_brach" placeholder="Please enter Bank Branch" class="form-control" value="{{$payment->bank_brach}}">
                        </div>

                        <div class=" col-md-4">
                            <label class="form-label">Cheque Number</label>
                            <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control" value="{{$payment->cheque_no}}">
                        </div>
                            
                        <div class=" col-md-4">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="reset" class="btn btn-white">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

<link rel="stylesheet" href="../../css/DatePickerX.css">

<script src="../../js/DatePickerX.min.js"></script>
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