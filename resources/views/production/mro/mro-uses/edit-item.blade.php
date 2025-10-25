@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-12 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card radius-6">
            <div class="card-body">
                <form class="form-validate" method="POST" action="{{ route('mro-used-items.update', encrypt($mro_item->id))}}">
                    @csrf
                    {{ method_field('PATCH') }} 
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.mro_name')}}</label>
                                <input type="text" class="form-control form-control-sm mb-4" value="{{$mro_item->name}}" readonly=""></label>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                 <input id="name" type="text" name="qty" placeholder="Please enter quantity" class="form-control form-control-sm mb-4" value="{{$mro_item->qty}}">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                            <input id="name" type="text" name="unit_cost" placeholder="Please enter quantity" class="form-control form-control-sm mb-4" value="{{$mro_item->unit_cost}}">
                        </div>  

                        <div class="col-sm-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

<link rel="stylesheet" href="../../css/DatePickerX.css">

<script src="../../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]'),
                $max = document.querySelector('[name="end_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

        });
    </script>


