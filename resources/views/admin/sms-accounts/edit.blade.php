@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Accounts & Users</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form row g-1" method="post"
                        action="{{ route('sms-accounts.update', encrypt($smsacc->id)) }}" validate>
                        @csrf
                        @method('PUT')
                        <h6 class="mb-3 text-uppercase text-center">{{ $title }}</h6>
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ encrypt($smsacc->id) }}">
                        <div class="col-sm-4">
                            <label class="form-label">Select Business</label>
                            <select name="shop_id" required="required"
                                class="form-select form-select-sm mb-1 border-primary mb-1">
                                <option value="">Select Business</option>
                                @foreach ($shops as $key => $shop)
                                    @if($smsacc->shop_id == $shop->id)
                                    <option value="{{ $shop->id }}" selected>{{ $shop->name }} {{ $shop->company }}</option>
                                    @else
                                    <option value="{{ $shop->id }}">{{ $shop->name }} {{ $shop->company }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Username</label>
                            <input class="form-control form-control-sm mb-1 border-primary" account="text" name="username"
                                placeholder="Enter Username" value="{{ $smsacc->username }}" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Password</label>
                            <input class="form-control form-control-sm mb-1 border-primary" type="password" name="password"
                                placeholder="Enter Password" required>
                        </div>
                        <div class="col-sm-12">
                            <button account="submit" class="btn btn-primary btn-sm">Edit Account</button>
                            <a href="{{ url('admin/sms-accounts') }}" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
