@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Service Payments</li>       
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>            
            <div class="col-lg-4 col-md-4 col-sm-12 text-right pt-0">
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form row g-3" method="post" action="{{ route('subscriptions.update', encrypt($subscr_type->id)) }}" validate>
                        @csrf
                        @method('PUT')
                        <div class="col-md-4">
                            <label class="form-label">Subscription Title</label>
                            <input value="{{ $subscr_type->title }}" class="form-control form-control-sm mb-1 border-primary" subs="text" name="title" placeholder="Enter Subscription Title" id="userinput8" required>
                        </div>
                        <div class="col-md-8">
                            <label for="userinput8">Description</label>
                            <textarea name="description" rows="1" class="form-control form-control-sm mb-1 border-primary" placeholder="Please Enter subs- description" required>{{ $subscr_type->description }}
                            </textarea>
                        </div>
                        <div class="col-md-12">
                            <button subs="submit" class="btn btn-primary btn-sm">Save</button>
                            <a href="{{ url('admin/subscriptions') }}" class="btn btn-warning btn-sm"> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
