@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item"><a href="{{ url('contracts') }}">Contracts</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" id="mycontroller" ng-controller="SearchItemCtrl">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-3 border rounded">
                        <form class="form" id="pos-form"  method="POST" action="{{ route('contracts.store') }}">
                            @csrf
                            <div class="row g-1 mb-1">
                                <div class="col-sm-12" id="ermsg"></div>
                                <div class="col-sm-3">
                                    <label class="form-label">TL Name</label>
                                    <select class="form-select form-select-sm mb-1" name="tl_name" required>
                                        <option value="">Select Team Leader</option>
                                        @foreach($users as $key => $user)
                                        <option>{{$user->first_name}} {{$user->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="device_id" id="device-id" class="form-control form-control-sm mb-1">
                                <div class="col-sm-3">
                                    <label class="form-label">Motor Bike </label>
                                    <a href="#"  data-bs-toggle="modal" data-bs-target="#deviceModal"><i class="fa fa-plus mr-1" class="float-end"></i> New</a>
                                    <input id="search_key" placeholder="Search Motorbike" class="form-control form-control-sm mb-1" autocomplete="off" required>
                                    <ul id="searchResult2"></ul>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Contract Type </label>
                                    <select class="form-select form-select-sm mb-1" name="type" required>
                                        <option>New</option>
                                        <option>Replacement</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Start Date</label>
                                    <div class="inner-addon left-addon"> 
                                        <i class="myaddon fa fa-calendar"></i>
                                        <input type="text" name="start_date" id="start_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <label class="form-label">Period (in Days)</label>
                                    <input type="number" name="period" class="form-control form-control-sm mb-1" placeholder="Enter Expected number of days" required>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Rider's Name <span style="color: red; font: bold;">*</span></label>
                                    <input type="text" name="name" required placeholder="{{ trans('navmenu.hnt_customer_name') }}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Rider's Mobile <span style="color: red; font: bold;">*</span></label>
                                    <input type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control form-control-sm mb-1" required>
                                </div>
                                <div class="col-sm-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input id="address" type="text" name="physical_address" placeholder="Enter customer address" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.tin')}}</label>
                                    <input type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-1"  data-inputmask='"mask": "999-999-999"' data-mask>
                                </div>
                                <div class="col-sm-12" style="border-bottom: 1px solid gray;">
                                    <label class="form-label">Garantors</label>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Garantor 1 <span style="color: red;">*</span></label>
                                    <input type="text" name="garantor_1" class="form-control form-control-sm mb-1" placeholder="Enter Garantor name" required>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Garantor 1 Mobile <span style="color: red;">*</span></label>
                                    <input type="text" name="garantor_1_mobile" class="form-control form-control-sm mb-1" placeholder="Enter Garantor Mobile number" required>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Garantor 2 <span style="color: red;">*</span></label>
                                    <input type="text" name="garantor_2" class="form-control form-control-sm mb-1" placeholder="Enter Garantor name" required>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">Garantor 2 Mobile <span style="color: red;">*</span></label>
                                    <input type="text" name="garantor_2_mobile" class="form-control form-control-sm mb-1" placeholder="Enter Garantor Mobile number" required>
                                </div>
                                <div class="col-sm-12" style="margin-top: 5px;">
                                    <button type="submit" id="btn-next" class="btn btn-primary btn-sm">Next</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <div class="modal fade" id="deviceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">New Motor Bike</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>   
                </div>
                <div class="modal-body row">
                    <form class="form row g-3" method="POST" action="{{route('devices.store')}}">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Plate No.<span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="device_number" required placeholder="{{trans('navmenu.hnt_device_number')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chasis No. <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="device_name" required placeholder="{{trans('navmenu.hnt_device_name')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.device_cost')}}</label>
                            <input id="name" type="text" name="device_cost" placeholder="{{trans('navmenu.hnt_device_cost')}} (Optional)" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="showHideDeviceForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#search_key').on('keyup',function () {
                var query = $(this).val();
                $.ajax({
                    url:"{{ url('search-device') }}",
                    type:'GET',
                    data:{'search_key':query},
                    success:function (response) {
                        var len = response.length;
                        $("#searchResult2").empty();
                        for( var i = 0; i<len; i++){
                            var id = response[i]['id'];
                            var name = response[i]['device_number']+" - "+response[i]['device_name'];
                            $("#searchResult2").append("<li value='"+id+"'>"+name+"</li>");
                        }

                        // binding click event to li
                        $("#searchResult2 li").bind("click",function(){
                            setSelectedDevice(this);
                        });
                    }
                })
            });
        });

        function setSelectedDevice(element) {
            var value = $(element).text();
            var devId = $(element).val();
            $('#device-id').val(devId); 
            $("#search_key").val(value);
            $("#searchResult2").empty();
        }
    </script>   

<link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
<script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="start_date"]');
            // var $max = document.querySelector('[name="end_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd'
            });

            // $max.DatePickerX.init({
            //     mondayFirst: true,
            //     format     : 'yyyy-mm-dd',
            //     minDate    : $min
            // });
        });
    </script>