@extends('layouts.hr')
@section('content')
     <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('hr-attendance')}}"><i class="fa fa-home"></i></a></li>
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
                            <h6 class="mb-0 text-uppercase" id="list-title">Attendace Settings</h6>
                        </div>
                    </div>

                    <div class="p-4 border rounded" id="new-form" >
                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('attendance-setting.update' , encrypt($setting->id)) }}">
                            @csrf
                            @method('PUT')
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Start of Day<span style="font: bold;">: {{$setting->start_of_day}}</span></label>
                                <input type="time" class="form-control form-control-sm mb-3" id="validationCustom01" name="start_of_day" value="{{$setting->start_of_day}}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">End of Day<span style="font: bold;">: {{$setting->end_of_day}}</span></label>
                                <input type="time" class="form-control form-control-sm mb-3" id="validationCustom01" name="end_of_day" value="{{$setting->end_of_day}}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Max Overtime Hours (Per Day)</label>
                                <input type="number" class="form-control form-control-sm mb-3" id="validationCustom02" name="max_overtime" value="{{$setting->max_overtime}}" >
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom02" class="form-label">Works on Weekends</label>
                                <input type="checkbox"  value="1"  id="validationCustom02" name="works_on_weekend" @if($setting->works_on_weekend) checked @endif>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Weekend Start of Day<span style="font: bold;">: {{$setting->w_start_of_day}}</span></label>
                                <input type="time" class="form-control form-control-sm mb-3" id="validationCustom01" name="w_start_of_day" value="{{$setting->w_start_of_day}}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="validationCustom01" class="form-label">Weekend End of Day<span style="font: bold;">: {{$setting->w_end_of_day}}</span></label>
                                <input type="time" class="form-control form-control-sm mb-3" id="validationCustom01" name="w_end_of_day" value="{{$setting->w_end_of_day}}" required>
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