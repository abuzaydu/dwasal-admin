
@extends('layouts.acc')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li><a href="{{ url('expense-categories') }}">Expense Categories</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('expense-categories.update', encrypt($expcategory->id)) }}">
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Transaction Account <span class="text-danger">*</span></label>
                                <select name="transaction_account_id" class="form-select form-select-sm mb-3" required>
                                    <option value="">Select Account</option>
                                    @foreach($traccounts as $acc)
                                    @if($expcategory->transaction_account_id == $acc->id)
                                    <option value="{{$acc->id}}" selected>{{$acc->account_number}} - {{$acc->account_name}}</option>
                                    @else
                                    <option value="{{$acc->id}}">{{$acc->account_number}} - {{$acc->account_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.name')}} <span class="text-danger">*</span></label>
                                <input class="form-control form-control-sm mb-3" type="text" name="name" placeholder="Enter Category name" value="{{$expcategory->name}}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{trans('navmenu.description')}}</label>
                                <input name="description" value="{{$expcategory->description}}" class="form-control form-control-sm mb-3" placeholder="Enter Category Description">                                
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn btn-success btn-sm" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </form>                
                </div>
            </div>
        </div>
    </div>
@endsection
