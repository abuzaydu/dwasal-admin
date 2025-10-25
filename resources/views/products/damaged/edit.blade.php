@extends('layouts.inv')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3 form-validate" method="POST" action="{{route('damages.update' , encrypt($pdam->id))}}">
                        @csrf
                        @method('PATCH')
                        @if($settings->is_filling_station)
                        <div class="col-md-6">
                            <label class="form-label" for="deph_measure">{{trans('navmenu.depth_measure')}}</label>
                            <input type="number" step="any" name="deph_measure" class="form-control form-control-sm mb-3" value="{{$pdam->deph_measure}}">
                        </div>
                        @else
                        <div class="col-md-6">
                            <label class="form-label" for="quantity">{{trans('navmenu.quantity')}}</label>
                            <input type="number" min="0" step="any" name="quantity" class="form-control form-control-sm mb-3" value="{{$pdam->quantity}}">
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon fa fa-calendar"></i>
                                <input type="text" name="dam_date" placeholder="Choose Date" class="form-control" value="{{$pdam->time_created}}">
                                <input type="hidden" name="realDPX-max" placeholder="Pick Date">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="reason">{{trans('navmenu.damage_cause')}}</label>
                            <textarea name="reason" class="form-control">{{$pdam->reason}}</textarea>
                        </div>
                        <div class="col-md-12">
                             <button type="submit" class="btn btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                             <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a> 
                        </div>
                    </form>  
                </div>
            </div>
        </div>
    </div>
@endsection