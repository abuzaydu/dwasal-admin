@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-home') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Production</li>
                    <li class="breadcrumb-item"><a href="{{ url('production-stages') }}">Product Production Stages</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="tab_2-2" role="tabpanel">
                            <form class="form row g-3" method="POST" action="{{ route('production-stages.update', encrypt($stage->id)) }}">
                                @csrf
                                {{ method_field('PUT') }}
                                <div class="col-sm-12">
                                    <label class="form-label">{{trans('navmenu.name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="text" name="stage" value="{{$stage->stage}}" required placeholder="{{trans('navmenu.name')}}" class="form-control form-control-sm mb-4">
                                </div>
                                <div class="col-sm-12">
                                    <div class="float-start">
                                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                                        <a href="{{ url('production-stages') }}" class="btn btn-warning btn-sm">Cancel</a>
                                    </div>
                                </div>
                            </form> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection