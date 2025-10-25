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
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-1" method="POST" action="{{ route('expense-payments.update', encrypt($payment->id)) }}">
                        @csrf
                        @method('PATCH')
                        <div class="col-md-4">
                            <label class="form-label">{{ trans('navmenu.pay_date') }}</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="pay_date" id="pay_date" value="{{$payment->pay_date}}" placeholder="{{ trans('navmenu.pick_date') }}" class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ trans('navmenu.amount_paid') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="number" name="amount" value="{{$payment->amount}}" required placeholder="{{ trans('navmenu.hnt_amount_paid') }}" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{ trans('navmenu.paid_from') }} <span
                                    style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="account_id" required>
                                <option value="">Petty Cash</option>
                                @foreach($accounts as $acc)
                                @if($payment->pay_mode == $acc->type))
                                <option value="{{$acc->id}}" selected>{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @else
                                <option value="{{$acc->id}}">{{$acc->account_name}} @if(!is_null($acc->account_number)) - {{$acc->account_number}}@endif</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn btn-success btn-sm">{{ trans('navmenu.btn_save') }}</button>
                            <a href="{{ url('expense-payments') }}" class="btn btn-warning btn-sm">{{ trans('navmenu.btn_cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="pay_date"]');
        $min.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });
    });
</script>