@extends('layouts.prof')

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Account & Settings</li>                   
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
                    <form class="row g-3 needs-validation my-form" method="POST" action="{{ route('shops.store')}}">
                        @csrf
                        <div class="col-sm-6">
                            <label class="form-label">Company Name <span style="color:red">*</span></label>
                            <select name="company_id" class="form-select form-select-sm mb-1" required>
                                @foreach(Auth::user()->companies()->get() as $key => $company)
                                @if($company->id == Session::get('company_id'))
                                <option value="{{$company->id}}" selected>{{$company->name}}</option>
                                @else
                                <option value="{{$company->id}}">{{$company->name}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.business_name')}} <span style="color:red">*</span></label>
                            <input id="shopname" type="text" name="shop_name"  data-rule="minlen:5" data-msg="{{trans('navmenu.hnt_enter_business_name')}}" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.business_name')}}" value="{{old('shop_name')}}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.business_type')}} <span style="color:red">*</span></label>
                            <select name="business_type_id" id="btypes" class="form-select form-select-sm mb-1" required>
                                <option value="">{{trans('navmenu.select_business_type')}}</option>
                                @foreach($btypes as $key => $type)
                                @if(app()->getLocale() == 'en')
                                <option value="{{$type->id}}">{{$type->id}}. {{$type->type}}</option>
                                @else
                                <option value="{{$type->id}}">{{$type->id}}. {{$type->type_sw}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Create A Default Warehouse/Store <span style="color:red">*</span></label>
                            <select name="create_warehouse" class="form-select form-select-sm mb-1">
                                <option value="0">NO</option>
                                <option value="1">YES</option>
                            </select>
                        </div>
                        <input type="hidden" name="is_warehouse" value="0">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit">Add</button>
                            <a href="{{ url('user-profile')}}" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
