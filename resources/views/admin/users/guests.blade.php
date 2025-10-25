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
                    <div class="table-responsive">
                        <table class="table table-striped display nowrap" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Last Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guests as $activity)
                                <tr>
                                    <td>{{ $activity->id  }}</td>
                                    <td>{{ $activity->ip_address }}</td>
                                    <td>{{ $activity->user_agent }}</td>
                                    <td>{{ $activity->last_activity }} </td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection