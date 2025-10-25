@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products</li>
                    <li class="breadcrumb-item"><a href="{{ url('categories') }}">Product Categories</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="border rounded p-4">
                        <h6>New Product Category</h6>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Row end  -->
@endsection