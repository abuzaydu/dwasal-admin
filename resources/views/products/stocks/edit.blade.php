@extends('layouts.inv')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <!-- SELECT2 EXAMPLE -->
    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card radius-10">
                <div class="card-body">
                    <form class="form-validate row g-3" method="POST" action="{{route('stocks.update' , encrypt($stock->id))}}">
                        @csrf
                        @method('PATCH')
                        <div class="col-sm-6 pt-2">
                            <label class="form-label">{{trans('navmenu.product_name')}}</label>
                            <select class="form-select form-select-sm mb-1" name="product_id" required>
                                <option value="{{$product->id}}">{{$product->slug}}</option>
                          </select>
                        </div>
                        <div class="col-sm-3 pt-2">
                            <label class="form-label">{{trans('navmenu.quantity')}}</label>
                            <input type="number" step="any" name="quantity_in" class="form-control form-control-sm mb-1" value="{{$stock->quantity_in+0}}">
                        </div>
                        <?php 
                        $unit_cost = $stock->unit_cost;
                        if ($stock->unit_cost == 0) {
                            $unit_cost = $stock->unit_cost;
                        }?>
                        @if(is_null($stock->production_run_id))
                        <div class="col-sm-3 pt-2">
                            <label class="form-label">{{trans('navmenu.buying_price')}}</label>
                            <input type="number" step="any" name="unit_cost" class="form-control form-control-sm mb-1" value="{{$unit_cost+0}}">
                        </div>
                        @endif
                        
                        @if($settings->enable_exp_date)
                        <div class="col-sm-12 pt-2">
                            <label for="expired" class="form-label">{{trans('navmenu.exp_date')}}</label>
                            <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                             </div>
                            <input type="text" name="exp_date" placeholder="Choose Expire date yyyy-mm-dd" class="form-control form-control-sm mb-1" value="{{$stock->expire_date}}" placeholder="yyyy-mm-dd" onkeyup="
                                    var v = this.value;
                                    if (v.match(/^\d{4}$/) !== null) {
                                        this.value = v + '-';
                                    } else if (v.match(/^\d{4}\-\d{2}$/) !== null) {
                                        this.value = v + '-';
                                    }"
                                    maxlength="10">
                            </div>
                        </div>
                        @endif
                        <div class="col-sm-12 pt-2">
                            <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box -->
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