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
                    <form class="form" method="POST" action="{{route('raw-materials.update', encrypt($material->id))}}">
                        @csrf
                        {{ method_field('PUT') }}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.material_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-1" value="{{ $material->name }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.basic_uom')}} <span style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-select form-select-sm mb-1" name="basic_uom" required style="width: 100%;">
                                        @foreach($units as $key => $unit)
                                        @if ($material->basic_uom === $unit->unit_name)
                                        <option selected>{{$unit->unit_name}}</option>
                                        @else
                                        <option>{{$unit->unit_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Raw Material For</label>
                                <select class="form-select form-select-sm mb-1" name="product_id" id="product_id">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                    @if($product->id == $material->product_id)
                                    <option value="{{$product->id}}" selected>{{$product->name}}</option>
                                    @else
                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{trans('navmenu.description')}}</label>
                                    <textarea name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.hnt_product_desc')}}">{{ $material->pivot->description }}</textarea>
                                </div>
                            </div> 

                            <div class="col-md-12">
                                <div class="form-group float-end">
                                    <button type="submit" class="btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                    <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection


