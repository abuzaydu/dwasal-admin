@extends('layouts.vml')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                     <li class="breadcrumb-item">
                <a href="javascript:history.back();" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth">
                    <i class="fa fa-arrow-left"></i>
                </a>
            </li>
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>   
                    <li class="breadcrumb-item">Visitors Management</li>                         
                    <li class="breadcrumb-item active">{{$page}}</li>
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
                    <div class="row g-3 p-4 border rounded">
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Badge No.</th>
                                        <td>{{$visitor->badge_no}}</td>
                                        <th>Created At</th>
                                        <td>{{$visitor->created_at}}</td>
                                    </tr>
                                    <tr>
                                        <th>visitor Name</th>
                                        <td>{{$visitor->name}}</td>
                                        <th>Mobile</th>
                                        <td>{{$visitor->mobile}}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{$visitor->address}}</td>
                                        <th>Email</th>
                                        <td>{{$visitor->email}}</td>
                                    </tr>
                                    <tr>
                                        <th>ID Type</th>
                                        <td>{{$visitor->id_type}}</td>
                                        <th>ID Number</th>
                                        <td>{{$visitor->id_number}}</td>
                                    </tr>
                                    <tr>
                                        <th>Purpose</th>
                                        <td>{{$visitor->purpose}}</td>
                                        <th>Host</th>
                                        <td>{{$visitor->fname}} {{$visitor->lname}}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{$visitor->status}}</td>
                                        <th>Guard on Duty</th>
                                        <td>{{$guard->first_name}} {{$guard->last_name}}</td>
                                    </tr>
                                    <tr>
                                        <th>Time IN</th>
                                        <td>@if(!empty($visitor->time_in)) {{date('d/m/Y h:i:s A', strtotime($visitor->time_in)) }}@endif</td>
                                        <th>Time Out</th>
                                        <td>@if(!empty($visitor->time_out)) {{date('d/m/Y h:i:s A', strtotime($visitor->time_out)) }}@endif</td>
                                    </tr> 
                                    <tr>
                                        <th>Came In With</th>
                                        <td>{{$visitor->came_in_with}}</td>
                                        <th>Came Out With</th>
                                        <td>{{$visitor->came_out_with}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                            <div class="col-md-4 text-center">
                                @if($visitor->visitor_photo)
                                        <img src="{{ asset('storage/visitors/' . $visitor->visitor_photo) }}" 
                                            alt="Visitor Photo"
                                            width="150">
                                @else
                                        <img src="{{ asset('images/default-avatar.png') }}" alt="No Photo">
                                @endif
                                <hr>
                                @if(!$visitor->is_granted)
                                <a href="{{ url('grant-permission/'.encrypt($visitor->id))}}" class="btn btn-outline-success font-13">
                                <i class="fa fa-check"></i> Grant Permission</a>
                                @endif
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection