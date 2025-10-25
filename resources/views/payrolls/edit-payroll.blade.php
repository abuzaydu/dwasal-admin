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
                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{ url('update-payroll') }}">
                            @csrf
                            <input type="hidden" name="id" value="{{$mpayroll->id}}">
                            <div class="col-md-6">
                                <label for="validationCustom09" class="form-label">Month of Payment</label>
                                <select class="form-select" name="month" required>
                                    @foreach($data as $d)
                                    @if($d['month'].' '.$d['year'] == $curmonth)
                                    <option selected value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                                    @else
                                    <option value="{{$d['month'].' '.$d['year']}}">{{$d['month'].' '.$d['year']}}</option>
                                    @endif
                                    @endforeach
                                </select>
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