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

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('products.update' ,encrypt($product->id))}}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="col-sm-4">
                            <label class="form-label">{{ trans('navmenu.product_name') }} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="name" value="{{$product->name}}" required placeholder="{{ trans('navmenu.hnt_product_name') }}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">{{ trans('navmenu.basic_uom') }} <span style="color: red; font-weight: bold;">*</span></label>
                             <select class="form-select form-select-sm mb-1" name="basic_uom" required style="width: 100%;">
                                @foreach ($units as $key => $unit)
                                @if($product->basic_uom == $unit->unit_name)
                                <option selected>{{ $unit->unit_name }}</option>
                                @else
                                <option>{{ $unit->unit_name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{ trans('navmenu.product_code') }} </label>
                            <input id="name" type="text" name="product_code" value="{{$product->product_code}}" placeholder="{{ trans('navmenu.hnt_product_code') }}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">{{ trans('navmenu.barcode_label') }}</label>
                            <input name="barcode" value="{{$product->barcode}}" class="form-control form-control-sm mb-1" placeholder="Scan/Type Barcode number." type="text" />
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">{{ trans('navmenu.location') }}</label>
                            <input id="unit_price" type="text" name="location" value="{{$product->location}}" placeholder="{{ trans('navmenu.hnt_location') }} (Optional)" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-5">
                            <label class="form-label">General {{ trans('navmenu.description') }}</label>
                            <textarea name="description" rows="1" class="form-control form-control-sm mb-1" placeholder="{{ trans('navmenu.hnt_product_desc') }}">{{$product->description}}</textarea>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{ trans('navmenu.product_image') }} (Optional)</label>
                            <input name="image" class="form-control form-control-sm mb-1" type="file" />
                        </div>
                        @if($shop->business_type_id == 1)
                        <div class="col-sm-4">
                            <label class="form-label">Is By Product?</label>
                            <select class="form-select form-select-sm mb-1" name="is_by_product">
                                @if($product->is_by_product)
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                                @else
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                                @endif
                            </select>
                        </div>
                        @endif
                        @if($settings->allow_more_product_desc)
                        <span>Detailed Descriptions (<span class="text-danger">For Fields that require units Enter value with its unit eg 6mm, 350ml, 10ft e.t.c.</span>)</span>
                        <div class="col-sm-2">
                            <label class="form-label">Brand</label>
                            <select class="form-select form-select-sm mb-1" name="brand">
                                <option value="">Select a Brand</option>
                                @foreach($brands as $br)
                                @if($product->brand == $br->name)
                                <option selected>{{$br->name}}</option>
                                @else
                                <option>{{$br->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" value="{{$product->model}}" class="form-control form-control-sm mb-1" placeholder="Enter Model">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Type</label>
                            <input type="text" name="type" value="{{$product->type}}" class="form-control form-control-sm mb-1" placeholder="Enter Product Type">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Size</label>
                            <input type="text" name="size" value="{{$product->size}}" class="form-control form-control-sm mb-1" placeholder="Enter Size">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Color</label>
                            <input type="text" name="color" value="{{$product->color}}" class="form-control form-control-sm mb-1" placeholder="Enter Color Name">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Length</label>
                            <input type="text" name="length" value="{{$product->length}}" class="form-control form-control-sm mb-1" placeholder="Enter Length">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Width</label>
                            <input type="text" name="width" value="{{$product->width}}" class="form-control form-control-sm mb-1" placeholder="Enter Width">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Thickness</label>
                            <input type="text" name="thick" value="{{$product->thick}}" class="form-control form-control-sm mb-1" placeholder="Enter Thickness">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Height</label>
                            <input type="text" name="height" value="{{$product->height}}" class="form-control form-control-sm mb-1" placeholder="Enter Height">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Volume</label>
                            <input type="text" name="volume" value="{{$product->volume}}" class="form-control form-control-sm mb-1" placeholder="Enter Volume">
                        </div>
                        <div class="col-sm-2">
                            <label class="form-label">Weight</label>
                            <input type="text" name="weight" value="{{$product->weight}}" class="form-control form-control-sm mb-1" placeholder="Enter Weight">
                        </div>
                        @endif        
                        <div class="col-md-12">
                            <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
    </div>
@endsection