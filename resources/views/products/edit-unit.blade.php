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
        <div class=" col-md-9 mx-auto"> 
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" method="POST" action="{{route('product-units.update', encrypt($prod_unit->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-sm-4">
                            <label class="form-label">Unit</label>
                            <select class="form-select form-select-sm mb-1" name="unit_name" required>
                                <option value=""> ---Select--</option>
                                @foreach($units as $key => $unit)
                                    @if($prod_unit->unit_name == $unit->unit_name)
                                    <option selected>{{$unit->unit_name}}</option>
                                    @else
                                    <option>{{$unit->unit_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Qty equivalent to Basic Unit</label>
                            <input class="form-control form-control-sm mb-1" type="number" min="0" step="any" name="qty_equal_to_basic" placeholder="Enter quantity" value="{{$prod_unit->qty_equal_to_basic}}" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Unit Price</label>
                            <input class="form-control form-control-sm mb-1" type="number" min="0" step="any" name="unit_price" placeholder="Enter Unit Price" value="{{$prod_unit->unit_price}}" required>
                        </div>
                        <div class="col-sm-6">
                            <button class="btn btn-primary btn-sm pull-right" style="margin-left: 5px;">Update</button>
                            <a href="#" onclick="history.back()" class="btn btn-warning btn-sm pull-right">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection