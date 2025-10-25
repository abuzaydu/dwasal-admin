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
                    <form class="row g-1" method="POST" action="{{ route('sender-ids.update', encrypt($senderid->id)) }}" validate>
                        {{csrf_field()}}
                        {{ method_field('PATCH') }}
                        <div class="col-md-4">
                            <label class="form-label">Business</label>
                            <select class="form-select form-select-sm mb-1" id="userinput6" name="sms_account_id" required>
                                @foreach($sms_accounts as $key => $sms_account)
                                @if($senderid->sms_account_id == $sms_account->id)
                                <option value="{{$sms_account->id}}" selected>{{$sms_account->username}} ({{$sms_account->name}})</option>
                                @else
                                <option value="{{$sms_account->id}}">{{$sms_account->username}} ({{$sms_account->name}})</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sender ID</label>
                            <input class="form-control form-control-sm mb-1" type="text" name="name" placeholder="Enter Sender ID name" id="userinput8" required value="{{$senderid->name}}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.auto_sms')}}</label>
                            <select name="auto_sms" class="form-select form-select-sm mb-1">
                                @if($senderid->auto_sms)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-12">
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
