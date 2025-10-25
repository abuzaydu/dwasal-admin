@extends('layouts.prod')

@section('content')
 <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form" method="POST" action="{{route('rm-uses.update', encrypt($rmuses->id))}}">
                        @csrf
                        {{ method_field('PUT') }}
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.batch_no')}}</label>
                                    <input id="name" type="text" name="prod_batch" class="form-control form-control-sm mb-4" value="{{$rmuses->prod_batch}}">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.total_cost')}}</label>
                                    <input id="name" type="text" name="total_cost"  class="form-control form-control-sm mb-4" value="{{$rmuses->total_cost}}" readonly="">
                                </div>
                            </div>

                             <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.date')}}</label>
                                    <input id="date" type="text" placeholder="{{trans('navmenu.pick_date')}}" name="date" class="form-control form-control-sm mb-4" value="{{$rmuses->date}}" required="">
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group float-end">
                                    <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                    <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                                </div>
                            </div>
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
        window.addEventListener('DOMContentLoaded', function()
        {
            var $max = document.querySelector('[name="date"]');

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

        });
    </script>


