@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Service Payments</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="px-2 pt-3 rounded">
                        <form class="form" method="post" action="{{ route('payments.update', $payment->id)}}" validate>
                            {{csrf_field()}}
                            {{ method_field('PATCH') }}
                            <div class="form-body row g-1">
                                <div class="col-md-3">
                                    <label class="form-label">Pay number <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm mb-1 border-primary" type="text" placeholder="Enter Sender's Phone number" id="userinput6" name="phone_number" value="{{$payment->phone_number}}" readonly>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Transaction ID <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm mb-1 border-primary" type="text" name="reference" id="userinput8" placeholder="Enter Transaction ID" value="{{$payment->reference}}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Amount Paid <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm mb-1 border-primary" type="number" name="amount_paid" id="userinput5" placeholder="Enter amount paid" value="{{$payment->amount_paid}}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status <span style="color: red;">*</span></label>
                                    <select name="status" class="form-select form-select-sm mb-1">
                                        <option>{{$payment->status}}</option>
                                        <option>Received</option>
                                        <option>Activated</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Payment Date <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm mb-1 border-primary" type="text" name="created_at" id="userinput5" placeholder="Enter Pay date" value="{{$payment->created_at}}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Activated At</label>
                                    <input class="form-control form-control-sm mb-1 border-primary" type="text" name="activation_time" id="userinput5" placeholder="Enter Pay date" value="{{$payment->activation_time}}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Expire Date</label>
                                    <input class="form-control form-control-sm mb-1 border-primary" type="text" name="expire_date" id="userinput5" placeholder="Enter expire date" value="{{$payment->expire_date}}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Is Expired</label>
                                    <select name="is_expired" class="form-select form-select-sm mb-1">
                                        @if($payment->is_expired)
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                        @else
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                        @endif
                                    </select>
                                </div>

                            </div>
                            <div class="form-actions right">
                                <a href="{{ url('admin/payments') }}" type="button" class="btn btn-warning btn-sm mr-1">
                                    <i class="icon-cross2"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="icon-check2"></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection
