@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="icon-home"></i></a></li>                         
                    <li class="breadcrumb-item">My Account</li>    
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form-horizontal" action="{{route('bank-details.update', $bankdetail->id)}}" method="POST">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="row pt-0">
                            <label class="col-sm-4 form-label">Bank Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control mb-1" id="bank_name" name="bank_name" placeholder="Bank Name" value="{{$bankdetail->bank_name}}">
                            </div>
                        </div>
                        <div class="row pt-0">
                            <label class="col-sm-4 form-label">Account Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control mb-1" id="account" name="account_name" placeholder="Account Name" value="{{$bankdetail->account_name}}">
                            </div>
                        </div>

                        <div class="row pt-0">
                            <label class="col-sm-4 form-label">Account Number</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control mb-1" id="account" name="account_number" placeholder="Account Number" value="{{$bankdetail->account_number}}">
                            </div>
                        </div>

                        <div class="row pt-0">
                            <label class="col-sm-4 form-label">Currency</label>
                            <div class="col-sm-8">
                                <select name="currency" class="form-select form-select-sm mb-1">
                                    @foreach($currencies as $curr)
                                    @if($bankdetail->currency == $curr->code)
                                    <option selected>{{$curr->code}}</option>
                                    @else
                                    <option>{{$curr->code}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row pt-0">
                            <label class="col-sm-4 form-label">Branch Name</label>

                            <div class="col-sm-8">
                              <input type="text" class="form-control form-control mb-1" id="branch_name" name="branch_name" placeholder="Branch Name" value="{{$bankdetail->branch_name}}">
                            </div>
                        </div>
                        <div class="row pt-0">
                            <label class="col-sm-4 form-label">Swift Code</label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control mb-1" id="swift_code" name="swift_code" placeholder="Swift Code" value="{{$bankdetail->swift_code}}">
                            </div>
                        </div>
                        <div class="row pt-2">
                            <div class="col-md-6">
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