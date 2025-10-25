@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Ouotes & Orders</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>         
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="form row g-3" method="POST" action="{{ route('order-deliveries.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label">Customer Order<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="order_detail_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($orders as $key => $order)
                                    <option value="{{$order->id}}">{{$order->uuid}} - {{ $order->first_name }} {{ $order->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Vehicle<span style="color: red; font-weight: bold;">*</span></label>
                                <select name="vehicle_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">--Select--</option>
                                    @foreach($vehicles as $key => $vehicle)
                                    @if($vehicles->count() == 1)
                                    <option value="{{$vehicle->id}}" selected>{{$vehicle->plate_no}}</option>
                                    @else
                                    <option value="{{$vehicle->id}}">{{$vehicle->plate_no}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">Create Delivery</button>
                                <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideForm('hide')">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection
<link rel="stylesheet" href="{{ asset('assets/css/DatePickerX.css') }}">
<script src="{{ asset('assets/js/DatePickerX.min.js') }}"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var $min = document.querySelector('[name="sourcing_date"]');

        $min.DatePickerX.init({
            mondayFirst: true,
            format: 'yyyy-mm-dd',
            maxDate: new Date()
        });
    });
</script>