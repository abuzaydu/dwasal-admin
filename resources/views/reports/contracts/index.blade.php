@extends('layouts.gen')
@section('page-styles')
    <link href="{{ asset('assets/vendor/highcharts/css/highcharts.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-9 col-md-9 col-sm-12 text-right">
                <form class="row g-1 d-flex justify-content-end dashform" action="{{ url('cm-dashboard') }}" method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{ $start_date }}">
                    <input type="hidden" name="end_date" id="end_input" value="{{ $end_date }}">
                    <!-- Date and time range -->
                    <div class="col-md-5">
                        <div class="input-group d-flex justify-content-end">
                            <button type="button" class="btn btn-white btn-sm mb-1 pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ url('home') }}" class="btn btn-primary btn-sm">General Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->
    <div id="dash-section">
        <h6 class="mb-0 text-uppercase">Working Days and Collections</h6>
        <hr>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">Expected Amount </p>
                            <p class="mb-0 p-0 ms-auto text-center" style="font-size: 11px; font-weight: bold;">
                                <span class="text-primary">
                                    Working Days <br>{{$tworkingdays}}
                                </span>
                            </p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-primary">{{ $currency }}
                                {{ number_format($texpected_amt, 2, '.', ',') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">Paid Amount</p>
                            <p class="mb-0 p-0 ms-auto text-center" style="font-size: 11px; font-weight: bold;">
                                <span class="text-success">
                                    Paid Days <br>{{$tpaiddays}}
                                </span>
                            </p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-success">{{ $currency }}
                                {{ number_format($tpaid_amt, 2, '.', ',') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">Pending Amount</p>
                            <p class="mb-0 p-0 ms-auto text-center" style="font-size: 11px; font-weight: bold;">
                                <span class="text-danger">
                                   Pending Days <br> {{$tpendingdays}}
                                </span>
                            </p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-danger">{{ $currency }}
                                {{ number_format($tpending_amt, 2, '.', ',') }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->

        <h6 class="mb-0 text-uppercase">Contracts Statuses</h6>
        <hr>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5">
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">Registered </p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-dark" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-dark">{{ $registered }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">Working</p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-primary">{{ $working }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">Graduated</p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-success">{{ $graduations }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">Terminated</p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 55%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-danger">{{ $terminated }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fs-6">Replaced</p>
                        </div>
                        <div class="progress mb-2" style="height:4px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="75"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex fs-6 align-items-center">
                            <h6 class="mb-0 py-1 text-warning">{{ $replaced }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
@endsection