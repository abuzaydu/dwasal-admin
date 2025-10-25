@extends('layouts.app')
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
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row g-3">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row g-1 print_invoice" id="payslip">
                        <div class="col-md-12">
                            <form class="row g-3" method="POST" action="{{ route('refund-requests.update', encrypt($refund->id)) }}">
                                @csrf
                                {{ method_field('PATCH') }}
                                <div class="col-md-3">
                                    <label class="form-label">Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="date" value="{{$refund->date}}" id="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{trans('navmenu.amount')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="number" step="any" name="refund_amt" value="{{$refund->refund_amt}}" required placeholder="Enter Refund Amount" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Remarks</label>
                                    <input class="form-control form-control-sm mb-1" rows="1" name="remarks" value="{{$refund->remarks}}" placeholder="Enter Remarks (Optional)....">
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                                    <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    <link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
    <script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>