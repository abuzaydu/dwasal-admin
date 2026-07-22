@extends('layouts.sand')
@section('content')
    <!--breadcrumb-->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="icon-home"></i></a></li>   
                    <li class="breadcrumb-item">Washed Sand Productions</li>
                    <li class="breadcrumb-item"><a href="{{ url('raw-material-sources') }}">Raw Material Sources</a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right">

            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row mb-5">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="2">Raw Material Source Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Source Name</td>
                                    <td><b>{{$rmsource->source_name}}</b></td>
                                </tr>
                                <tr>
                                    <td>Source Location</td>
                                    <td><b>{{$rmsource->source_location}}</b></td>
                                </tr>
                                <tr>
                                    <td>Material Type</td>
                                    <td><b>{{$rmsource->material_type}}</b></td>
                                </tr>
                                <tr>
                                    <td>Contact Person</td>
                                    <td><b>{{$rmsource->contact_person}}</b></td>
                                </tr>
                                <tr>
                                    <td>Contact Number</td>
                                    <td><b>{{$rmsource->contact_number}}</b></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-warning btn-sm px-4 radius-30" onclick="window.history.back()">Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection