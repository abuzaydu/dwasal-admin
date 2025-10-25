@extends('layouts.inv')
@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Products & Services</li>
                    <li class="breadcrumb-item"><a href="{{url('categories')}}">{{trans('navmenu.categories')}}</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                     <form class="row g-3 form-validate" method="POST" action="{{ route('serv-categories.update', encrypt($category->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.category_name')}}</label>
                            <input type="text" name="name" class="form-control form-control-sm mb-1" value="{{$category->name}}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">{{trans('navmenu.description')}}</label>
                            <input name="description" class="form-control form-control-sm mb-1" value="{{$category->description}}">
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
    </div>
@endsection