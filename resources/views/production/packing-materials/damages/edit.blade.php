@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">{{$title}}</li>
                    <li class="breadcrumb-item active">{{$packing_material->name}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-7 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="card radius-6">
        <div class="card-body">
            <form class="form-validate row g-1" method="POST" action="{{ route('pm-damages.update', encrypt($pmdamage->id))}}">
                @csrf
                {{ method_field('PATCH') }} 
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="form-label">{{trans('navmenu.quantity')}}</label>
                        <input type="number" min="1" name="quantity" class="form-control form-control-sm" value="{{$pmdamage->quantity}}">
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                        <input type="number" step="any" name="unit_cost" class="form-control form-control-sm" value="{{$pmdamage->unit_cost}}">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="form-label">{{trans('navmenu.damage_cause')}}<span style="color: red;"> *</span></label>
                        <textarea name="reason" rows="1" placeholder="{{trans('navmenu.hnt_damage_cause')}}" class="form-control form-control-sm" required>{{$pmdamage->reason}}</textarea>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                        <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                    </div>
                </div>
            </form>
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


