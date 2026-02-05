@extends('layouts.vms')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>               
                    <li class="breadcrumb-item">Vehicle Management</li>
                    <li class="breadcrumb-item"><a  href="{{ url('part-purchases') }}">Part Purchases</a></li>
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
                    <form class="form-validate row g-3" method="POST" action="{{route('part-purchase-items.update' , encrypt($stock->id))}}">
                        @csrf
                        @method('PATCH')
                        <div class="col-sm-6 pt-2">
                            <label class="form-label">Part Name</label>
                            <select class="form-select form-select-sm mb-1" name="part_id" required>
                                <option value="{{$part->id}}">{{$part->part_no}} {{$part->part_name}}</option>
                          </select>
                        </div>
                        <div class="col-sm-3 pt-2">
                            <label class="form-label">{{trans('navmenu.quantity')}}</label>
                            <input type="number" step="any" name="pp_qty" class="form-control form-control-sm mb-1" value="{{$stock->pp_qty+0}}">
                        </div>
                        <?php 
                        $unit_price = $stock->unit_price;
                        if ($stock->unit_price == 0) {
                            $unit_price = $stock->unit_price;
                        }?>
                        <div class="col-sm-3 pt-2">
                            <label class="form-label">{{trans('navmenu.buying_price')}}</label>
                            <input type="number" step="any" name="unit_price" class="form-control form-control-sm mb-1" value="{{$unit_price+0}}">
                        </div>
                    
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