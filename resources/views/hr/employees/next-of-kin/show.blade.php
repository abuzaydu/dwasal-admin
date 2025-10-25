@extends('layouts.hr')

@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <h2 class="m-0 fs-5"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a> Dashboard</h2>
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" >Next Of Kin Information</h6>
                        </div>
                    </div>
                    <div class="p-4 border rounded">
                        <p class="card-text text-muted"></p>
                        <ul class="list-unstyled mb-0">
                          <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">First Name:</span>{{$nok->f_name}}</li>
                          <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">Middle Name:</span>{{$nok->m_name}}</li>
                          <li class="py-2"><span class="text-muted me-2 w92 d-inline-block">Surname Name:</span>{{$nok->l_name}}</li>
                          <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">Relationship:</span>{{$nok->relationship}}</li>
                          <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">Occupation:</span>{{$nok->occupation}}</li>
                          <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">Address:</span>{{$nok->address}}</li>
                          <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">Residence:</span>{{$nok->residence}}</li>
                          <li class="py-2"><span class="text-muted me-2 w90 d-inline-block">Mobile Number:</span>{{$nok->f_phone}} , {{$nok->s_phone}}</li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')

@endsection