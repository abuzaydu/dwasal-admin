@extends('layouts.inv')
<script type="text/javascript">
    function confirmDelete(id){
        document.getElementById('delete-form-'+id).submit();
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{ url('purchase-orders') }}">Purchase Orders</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="fa fa-user-plus"></i>{{trans('navmenu.new_supplier')}}</button>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form-validate row g-1" method="POST" action="{{ route('poitems.update', encrypt($item->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="id" value="{{$item->id}}">
                        <div class="col-md-4">
                            <label for="product_id" class="form-label">{{trans('navmenu.product_name')}}</label>
                            <select class="form-control form-control-sm mb-1 select2" name="product_id" required>
                                <option value="">{{trans('navmenu.select_product')}}</option>
                                @foreach($products as $prod)
                                @if($product->id == $prod->id)
                                <option value="{{$product->id}}" selected>{{$product->name}}</option>
                                @else
                                <option value="{{$product->id}}">{{$product->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="qty" class="form-label">{{trans('navmenu.quantity')}}</label>
                            <input type="number" step="any" name="qty" class="form-control form-control-sm mb-1" value="{{$item->qty}}">
                        </div>
                        <div class="col-md-4">
                            <label for="unit_cost" class="form-label">{{trans('navmenu.buying_price')}}</label>
                            <input type="number" step="any" name="unit_cost" class="form-control form-control-sm mb-1" value="{{$item->unit_cost}}">
                        </div>
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