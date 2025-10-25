@extends('layouts.hr')
@section('content')
     <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('position')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" id="list-title">Edit Position</h6>
                        </div>
                    </div>

                    <div class="p-4 border rounded" id="new-form" >
                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('positions.update' , encrypt($position->id)) }}">
                            @csrf
                            @method('PUT')
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Position Name<span style="color: red;">*</span></label>
                                <input type="text" class="form-control form-control-sm mb-3" id="validationCustom01" name="name" value="{{$position->name}}" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Position name.</div>
                                </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Basic Pay (Per Hour)</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="basic_pay_hourly" value="{{$position->basic_pay_hourly}}" >
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Basic Pay (Per Month)</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="basic_pay_monthly" value="{{$position->basic_pay_monthly}}">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Transport Allowance</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="trans_allowance" value="{{$position->trans_allowance}}">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">House Allowance</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="house_allowance" value="{{$position->house_allowance}}">
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Communication Allowance</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="com_allowance" value="{{$position->com_allowance}}">
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