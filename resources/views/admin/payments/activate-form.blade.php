@extends('layouts.adm')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Service Payments</li>       
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
                    <div class="p-2 rounded">
                         <form class="form row g-3" method="POST" action="{{ url('admin/activate-shop') }}" validate>
                            {{ csrf_field() }}
                            <div class="col-md-9">
                                <label class="form-label">Business</label>
                                <select name="shop_id" required class="form-select form-select-sm mb-1 border-primary mb-1 select2">
                                    <option value="">Select Business</option>
                                    @foreach ($shops as $key => $shop)
                                        <option value="{{ $shop->id }}">{{ $shop->name }} ({{ $shop->company }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Code</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="code" placeholder="Enter Username" id="userinput8" required>
                            </div>
                            <div class="col-md-12 pt-2">
                                <button account="submit" class="btn btn-primary">Activate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection