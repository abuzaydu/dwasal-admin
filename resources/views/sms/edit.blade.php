@extends('layouts.app')
<script>
    function weg(elem){
        var temp = document.getElementById('temp-options');

        if (elem.value == '1') {
            temp.style.display = 'block';
        }else{
            temp.style.display = 'none';
        }
    }
</script>
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
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form row g-1" method="POST" action="{{ route('sms-notifications.update', encrypt($temp->id)) }}" validate>
                        {{csrf_field()}}
                        {{ method_field('PATCH') }}
                        <div class="col-md-4">
                            <label class="form-label">Template name <span style="color: red;">*</span></label>
                            <input class="form-control form-control-sm mb-1" type="text" name="title" placeholder="Enter Username" id="userinput8" required value="{{$temp->title}}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Use as Auto SMS</label>
                            <select name="is_auto_sms" onchange="weg(this)" class="form-select form-select-sm mb-1">
                                @if($temp->is_auto_sms)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        @if($temp->is_auto_sms)
                        <div class="col-md-6" id="temp-options">
                            <label class="form-label">{{trans('navmenu.temp_used_when')}}</label>
                            <select name="temp_for" class="form-select form-select-sm mb-1">
                                <option value=""> --Select--</option>
                                @foreach($tempuses as $key => $tempuse)
                                    @if($temp->temp_for == $key)
                                    <option value="{{$key}}" selected>{{$tempuse}}</option>
                                    @else
                                    <option value="{{$key}}">{{$tempuse}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="col-md-6" id="temp-options" style="display: none;">
                            <label class="form-label">{{trans('navmenu.temp_used_when')}}</label>
                            <select name="temp_for" class="form-select form-select-sm mb-1">
                                <option value=""> --Select--</option>
                                @foreach($tempuses as $key => $tempuse)
                                    <option value="{{$key}}">{{$tempuse}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">Message <span style="color: red;">*</span></label>
                            <textarea name="message" id="dmsg" class="form-control form-control-sm mb-1" placeholder="Please Type her Your Message" required>{{$temp->message}}</textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>              
    </div>
    <!--end row-->
@endsection
