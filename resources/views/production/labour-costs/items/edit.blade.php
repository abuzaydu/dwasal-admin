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
                    <form class="form-validate" method="POST" action="{{ route('dlc-items.update', encrypt($lci->id))}}">
                        @csrf
                        {{ method_field('PATCH') }} 
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label">Production Stage</label>
                                    <select name="production_stage_id" class="form-select form-select-sm mb-4" required>
                                        @foreach($stages as $stage)
                                        @if($lci->production_stage_id == $stage->id)
                                        <option value="{{$stage->id}}" selected>{{$stage->stage}}</option>
                                        @else
                                        <option value="{{$stage->id}}">{{$stage->stage}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                     <input type="number" name="qty" placeholder="Please enter quantity" class="form-control form-control-sm mb-4" value="{{$lci->qty+0}}">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                     <input type="number" name="unit_cost" placeholder="Please enter unit cost" class="form-control form-control-sm mb-4" value="{{$lci->unit_cost+0}}">
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
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


