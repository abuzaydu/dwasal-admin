@extends('layouts.adm')

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Account Informaton</li>                   
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="box box-success">
                <div class="card radius-6 p-4">
                    <form class="row g-3 needs-validation my-form" method="POST" action="{{ url('admin/update-shop-detail')}}">
                        @csrf
                        <input type="hidden" name="id" value="{{$shop->id}}">
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.name')}} <span style="color:red">*</span></label>
                            <input id="shopname" type="text" name="shop_name"  data-rule="minlen:5" data-msg="{{trans('navmenu.hnt_enter_business_name')}}" class="form-control form-control-sm mb-1" placeholder="Enter Warehouse/Store Name" value="{{ $shop->name }}" readonly required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Parent Shop <span style="color:red">*</span></label>
                            <select name="parent_shop_id" class="form-select form-select-sm mb-1">
                                @foreach ($company->shops()->where('is_warehouse', false)->get() as $key => $myshop)
                                @if ($myshop->id == $shop->parent_shop_id)
                                <option value="{{ $myshop->id }}" selected>{{ $myshop->name }}</option>
                                @else
                                <option value="{{ $myshop->id }}">{{ $myshop->name }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="subscription_type_id" value="1">
                        <input type="hidden" name="business_type_id" value="2">
                        <input type="hidden" name="is_warehouse" value="1">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">Update</button>
                            <a href="{{ url('shops')}}" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
