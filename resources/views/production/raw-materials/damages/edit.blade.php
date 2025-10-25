@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form-validate" method="POST" action="{{ route('rm-damages.update', encrypt($rmdamage->id))}}">
                        @csrf
                        {{ method_field('PATCH') }} 
                        <div class="row g-1">
                            <div class="col-sm-12">
                                <label class="form-label">Material Name</label>
                                <input type="text" name="" value="{{$raw_material->name}}" readonly class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                    <input type="number" min="1" name="quantity" class="form-control form-control-sm mb-1" value="{{$rmdamage->quantity}}">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                    <input type="number" step="any" name="unit_cost" class="form-control form-control-sm mb-1" value="{{$rmdamage->unit_cost}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.damage_cause')}}<span style="color: red;"> *</span></label>
                                    <textarea name="reason" rows="1" placeholder="{{trans('navmenu.hnt_damage_cause')}}" class="form-control form-control-sm mb-1" required>{{$rmdamage->reason}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-3">
                            <div class="col-sm-12">
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


