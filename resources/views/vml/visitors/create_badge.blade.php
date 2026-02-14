@extends('layouts.vml')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item">Badges Management</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>    
            </div> 
        </div>
    </div>

    <!-- Badge Card Template -->
    <div class="row mt-4">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="badge-card">
                        <h5 class="card-title mb-3">ORGANIZATION NAME</h5>
                        
                        <div class="badge-info mb-3">
                            <p><strong>Badge Number:</strong> BADGE NUMBER</p>
                        </div>

                        <div class="organization-details">
                            <p><strong>Address:</strong> ADDRESS</p>
                            <p><strong>Email:</strong> EMAIL</p>
                            <p><strong>Phone:</strong> PHONE NUMBER</p>
                        </div>

                        <div class="mt-4">
                            {{-- <form action="" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary">Create Badge</button>
                            </form> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection          

                