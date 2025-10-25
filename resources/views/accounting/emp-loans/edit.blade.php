@extends('layouts.acc')
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
            <div class="col-md-3"></div>
            <div class="col-md-3 col-sm-12 text-md-end">
            
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="p-0 border rounded">
                        <form class="row g-3" id="basic-form" novalidate method="POST" action="{{ route('emp-loans.update', encrypt($emploan->id)) }}">
                            @csrf
                            {{ method_field('PATCH')}}
                            <div class="col-md-3">
                                <label class="form-label">Employee <span style="color: red;">*</span></label>
                                <select class="form-select form-select-sm mb-1" name="employee_id" required>
                                    @foreach($employees as $employee)
                                    @if($employee->id == $emploan->employee_id)
                                    <option value="{{$employee->id}}" selected>{{$employee->fname}} - {{$employee->lname}}</option>
                                    @else
                                    <option value="{{$employee->id}}">{{$employee->fname}} - {{$employee->lname}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please select an Employee.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="validationCustom02" class="form-label"> Date </label>
                                <div class="input-group date">
                                    <input type="text" class="form-control form-control-sm mb-1" name="loan_date" value="{{$emploan->loan_date}}"  placeholder="Pick Start Date" autocomplete="off">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"><i class="fa fa-calendar"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount <span style="color: red;">*</span></label>
                                <input type="number" min="0" step="any" name="amount" value="{{$emploan->amount}}" class="form-control form-control-sm mb-1" placeholder="Enter Loan Amount" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please Enter Loan Amount.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Return Rate (Percent) <span style="color: red;">*</span></label>
                                <input type="number" min="0" step="any" name="return_rate" value="{{$emploan->return_rate}}" class="form-control form-control-sm mb-1" placeholder="Enter Loan Return rate (Percent)" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please Enter Loan Return rate.</div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" value="{{$emploan->remarks}}" class="form-control form-control-sm mb-1" placeholder="Enter Remarks">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm px-4 radius-30" type="submit"> Submit</button>
                                <a href="javascript:;" onclick="showHideForm('hide')" class="btn btn-warning btn-sm px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

    <link rel="stylesheet" href="{{ asset('assets/css/DatePickerX.css') }}">
    <script src="{{ asset('assets/js/DatePickerX.min.js') }}"></script>
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