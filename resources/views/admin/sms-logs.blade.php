@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <table id="" class="table table-striped" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>SMS Response Log</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $key => $log)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    @if (strlen($log->response) == 81)
                                        <td> {{ $log->response }} </td>
                                    @else
                                        <td>{{ json_encode(json_decode($log->response, true)) }}</td>
                                    @endif
                                    <td> {{ $log->created_at }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
