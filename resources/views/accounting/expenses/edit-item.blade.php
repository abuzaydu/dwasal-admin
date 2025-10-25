@extends('layouts.acc')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Accounting</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-3 col-sm-12 text-right">
            
            </div>
        </div>
    </div>
    <!--end breadcrumb-->


    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('expense-items.update', encrypt($expitem->id)) }}">
                        @csrf
                        @method('PATCH')
                        <div class="row g-1 align-items-center">
                            <div class="col-md-12">
                                <label class="form-label">Expense Category</label>
                                <select name="expense_category_id" class="form-select form-select-sm mb-1" required>
                                    <option value="">Select Expense Category</option>
                                    @foreach ($expcategories as $expcat)
                                    @if($expitem->expense_category_id == $expcat->id)
                                    <option value="{{ $expcat->id }}" selected>{{ $expcat->name }}</option>
                                    @else
                                    <option value="{{ $expcat->id }}">{{ $expcat->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="register-username" class="form-label">{{ trans('navmenu.expense_type') }}
                                <span style="color: red;">*</span></label>
                                <input id="register-username" type="text" name="expense_type" value="{{$expitem->expense_type}}" required placeholder="{{ trans('navmenu.hnt_expense_type') }}" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Is Cost Of Sale?</label>
                                <select name="is_cost_of_sale" class="form-select form-select-sm mb-1">
                                    @if($expitem->is_cost_of_sale)
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                    @else
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success btn-sm" id="btn-submit-new">{{ trans('navmenu.btn_save') }}</button>
                                <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </form>              
                </div>
            </div>
        </div>
    </div>
@endsection