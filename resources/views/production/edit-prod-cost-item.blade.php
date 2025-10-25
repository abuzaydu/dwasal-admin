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
        <div class="col-xl-12 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form-validate row g-1" method="POST" action="{{ route('prod-cost-items.update', encrypt($proditem->id))}}">
                        @csrf
                        {{ method_field('PATCH') }} 
                        <div class="col-sm-4">
                            <label class="form-label">{{trans('navmenu.product_packed')}}</label>
                            <select name="product_id" class="form-select form-select-sm mb-4" required>
                                <option value="{{$proditem->product_id}}">{{$proditem->name}}</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                 <input id="name" type="text" name="quantity" placeholder="Please enter quantity" class="form-control form-control-sm mb-4" value="{{$proditem->quantity+0}}">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">{{trans('navmenu.unit_packed')}}</label>
                            <input id="name" type="text" name="unit_packed" placeholder="Please enter quantity" class="form-control form-control-sm mb-4" value="{{$proditem->unit_packed+0}}">
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection