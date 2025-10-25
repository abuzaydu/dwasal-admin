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
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Phone number</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $key =>  $user)
                                <tr>
                                    <td>{{ $key+1  }}</td>
                                    <td>{{ $user['first_name'] }}</td>
                                    <td>{{ $user['last_name'] }}</td>
                                    <td>{{ $user['phone'] }} </td>
                                    @if($user['away'])
                                    <td><a href="#"><i class="fa fa-circle text-orange"></i></a></td>
                                    @else
                                    <td><a href="#"><i class="fa fa-circle text-success"></i></a></td>
                                    @endif
                            
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