@extends('layouts.sand')
@section('content')
    <!--breadcrumb-->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="icon-home"></i></a></li>   
                    <li class="breadcrumb-item">Washed Sand Productions</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form class="form" method="POST" action="{{ route('quality-tests.update', encrypt($qtest->id)) }}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Test Date</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon fa fa-calendar"></i>
                                    <input type="text" name="test_date" id="test_date" value="{{$qtest->test_date}}" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Test Type <span style="color: red; font: bold;">*</span></label>
                                <select name="test_type" class="form-select form-select-sm mb-1" required>
                                    @foreach($testtypes as $ttype)
                                    @if($ttype == $qtest->test_type)
                                    <option selected>{{$ttype}}</option>
                                    @else
                                    <option>{{$ttype}}</option>
                                    @endif
                                    @endforeach
                                  </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Result</label>
                                <input type="text" name="result" value="{{$qtest->result}}" placeholder="Please enter Test Results" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Passed</label>
                                <select name="passed" class="form-select form-select-sm mb-1" required>
                                    @if($qtest->passed)
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                    @else
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                            </div>         
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection


<link rel="stylesheet" href="{{ asset('css/DatePickerX.css')}}">
<script src="{{ asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="test_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>