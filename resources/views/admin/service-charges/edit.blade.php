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
                    <div class="p-2 rounded">
                        <form class="form row g-1" method="POST" action="{{ route('service-charges.update', encrypt($charge->id)) }}" validate>
                            {{csrf_field()}}
                            {{ method_field('PATCH') }}
                            <div class="col-md-3">
                                <label class="form-label">Package <span style="color:red;">*</span></label>
                                <select class="form-select form-select-sm mb-1 border-primary+" id="userinput6" name="type" required>
                                    @foreach($subscriptions as $type)
                                    <option value="{{$type->id}}">{{$type->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Amount <span style="color:red;">*</span></label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="number" name="initial_pay" value="{{$charge->initial_pay}}" placeholder="Enter Initial Payment amount" id="userinput8" required>
                            </div>
                            <div class="col-md-3">
                                <label for="form-label">Duration <span style="color:red;">*</span></label>
                                <select class="form-select form-select-sm mb-1 border-primary+" id="userinput6" name="duration" required>
                                    <option>{{$charge->duration}}</option>
                                    <option value="">Select Duration</option>
                                    <option>Monthly</option>
                                    <option>Quarterly</option>
                                    <option>Semi Annually</option>
                                    <option>Annually</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('admin/service-charges') }}" class="btn btn-warning btn-sm">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection