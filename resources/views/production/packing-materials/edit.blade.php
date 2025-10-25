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
                    <form class="form row g-1" method="POST" action="{{route('packing-materials.update', encrypt($material->id))}}">
                        @csrf
                        {{ method_field('PUT') }}
                        <div class="col-sm-3">
                            <label class="form-label">Parent Material</label>
                            <select class="form-select form-select-sm mb-3" name="parent_pm_id" style="width: 100%;">
                                <option value="">--Select Parent Material--</option>
                                @foreach($pmaterials as $key => $pm)
                                @if($material->parent_pm_id == $pm->id)
                                <option value="{{$pm->id}}" selected>{{$pm->name}}</option>
                                @else
                                <option value="{{$pm->id}}">{{$pm->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.packing_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-3" value="{{ $material->name }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{trans('navmenu.basic_uom')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="basic_uom" required style="width: 100%;">
                                @foreach($units as $key => $unit)
                                @if($material->basic_uom == $unit->unit_name)
                                <option selected>{{$unit->unit_name}}</option>
                                @else
                                <option>{{$unit->unit_name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>    
                        <div class="col-md-12">
                        <div class="form-group float-end">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="{{ url('packing-materials')}}" class="btn btn-secondary btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


