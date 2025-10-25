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
        <div class="col-md-9 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form-validate" method="POST" action="{{route('pm-items.update', encrypt($pmitem->id)) }}">
                        @csrf
                        {{ method_field('PATCH') }} 
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                    <input type="number" min="1" name="qty" class="form-control form-control-sm mb-3" value="{{$pmitem->qty}}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                    <input type="number" step="any" name="unit_cost" class="form-control form-control-sm mb-3" value="{{$pmitem->unit_cost}}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
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

<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="mnf_date"]'),
                $max = document.querySelector('[name="exp_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : $max
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min,
                // maxDate    : new Date()
            });

        });
    </script>


