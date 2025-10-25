@extends('layouts.inv')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-6 mx-auto">
            <h6 class="mb-0 text-uppercase" id="new-title">{{$title}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="form row g-3" method="POST" action="{{route('grades.update', encrypt($grade->id))}}">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-6">
                                <label class="control-label">{{trans('navmenu.grade')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" value="{{$grade->name}}" required placeholder="{{trans('navmenu.hnt_grade_name')}}" class="form-control"> 
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success px-4 radius-30" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning px-4 radius-30" onclick="showHideGradeForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                       </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection