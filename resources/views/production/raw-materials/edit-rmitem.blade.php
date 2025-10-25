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
        <div class="col-md-10 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form-validate row g-1" method="POST" action="{{route('rm-items.update', $rmitem->id)}}">
                        @csrf
                        {{ method_field('PATCH') }} 
                        <input type="hidden" name="id" value="{{$rmitem->id}}">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                <input type="number" min="0" step="any" name="qty" class="form-control form-control-sm mb-1" value="{{$rmitem->qty}}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                <input type="number" step="any" name="unit_cost" class="form-control form-control-sm mb-1" value="{{$rmitem->unit_cost}}">
                            </div>
                        </div>

                        <div class="row mt-3">
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


