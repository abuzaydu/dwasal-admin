@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/prod-home')}}"><i class="fa fa-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-10 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card radius-6">
            <div class="card-body">
                <form class="form" method="POST" action="{{route('mro.update', encrypt($mro->id))}}">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.mro_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-4" value="{{ $mro->name }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group float-end">
                                <button type="submit" class="btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="reset" class="btn btn-secondary">{{trans('navmenu.btn_reset')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


