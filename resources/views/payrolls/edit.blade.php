@extends('layouts.pr')
    
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('payrolls')}}">Payrolls</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('payrolls.update', encrypt($payroll->id)) }}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-6">
                                <label for="validationCustom09" class="form-label">Month of Payment</label>
                                <select class="form-select" name="month" required disabled>
                                    @foreach($data as $d)
                                    @if($d['month'].' '.$d['year'] == $curmonth)
                                    <option selected value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                                    @else
                                    <option value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom05" class="form-label">Employee</label>
                                <select class="form-select" name="employee_id" required disabled>
                                    <option value="">--- Select ---</option>
                                    @foreach($employees as $employee)
                                    @if($employee->id == $payroll->employee_id)
                                    <option selected value="{{$employee->id}}">{{$employee->fname}} {{$employee->lname}}</option>
                                    @else
                                    <option value="{{$employee->id}}">{{$employee->fname}} {{$employee->lname}}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please select employee for the Employee.</div>
                            </div>
                            <input type="hidden" class="form-control" id="validationCustom01" name="days_work" value="{{$payroll->days_work}}">
                            <div class="col-md-6">
                                <label for="validationCustom04" class="form-label">Bonuses</label>
                                <input type="number" step="any" class="form-control" id="validationCustom04" name="bonuses" value="{{$payroll->bonuses}}">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                <a href="javascript:history.back()" class="btn btn-warning px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection