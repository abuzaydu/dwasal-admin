@extends('layouts.prof')
@section('page-styles')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-1">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                         
                    <li class="breadcrumb-item">Settings</li>    
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-7 col-md-7 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-4 mx-auto">
            <div class="card">
                <div class="card-body radius-6 p-4">
                    <form class="row g-1" action="{{ route('settings.update', encrypt($settings->id)) }}" method="POST">
                        <!-- Horizontal Form -->
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="is_dct_settings" value="1">
                        <div class="col-md-12">
                            <label class="form-label">Daily Closing Time</label>
                            <input type="text" name="dc_time" id="timepicker" value="{{$settings->dc_time}}" class="form-control form-control-sm mb-1" placeholder="Enter your End note">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm">Update </button>
                            <a href="{{ url('settings')}}" class="btn btn-warning btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('page-scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#timepicker').timepicker({
                timeFormat: 'H:mm p',
                interval: 30,
                minTime: '17',
                maxTime: '23:59pm',
                startTime: '17:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true
            });
        });
    </script>
@endsection