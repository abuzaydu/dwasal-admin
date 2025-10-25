@extends('layouts.prod')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item">{{$page}}</li>
                    <li class="breadcrumb-item active">{{trans('navmenu.production_settings')}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" action="{{ route('prod-settings.update' , encrypt($settings->id ))}}" method="POST">
                        <!-- Horizontal Form -->
                        @method('PUT')
                        @csrf
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection