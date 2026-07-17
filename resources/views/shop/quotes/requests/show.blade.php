@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('quote-requests') }}">Quotes</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5>Quote Request #{{ $qrequest->id }}</h5>

                    <div class="float-right">
                        <a href="{{ url('quote-requests') }}" class="btn btn-default btn-sm">Back to List</a>

                        @if($qrequest->status == 'SENT')
                            <form action="{{ route('quote-requests.approve', encrypt($qrequest->id)) }}" method="POST" style="display:inline;" onsubmit="return confirm('Approve this quote request?');">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fa fa-check"></i> Approve
                                </button>
                            </form>
                        @endif

                        @if($qrequest->status == 'Approved' && !$qrequest->is_quoted)
                            <form action="{{ route('quotations.from-request') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="id" value="{{ $qrequest->id }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fa fa-file"></i> Create Quotation
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Customer Name</strong></div>
                        <div class="col-md-3">{{ $qrequest->name ?? '-' }}</div>
                        <div class="col-md-3"><strong>Email</strong></div>
                        <div class="col-md-3">{{ $qrequest->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Mobile</strong></div>
                        <div class="col-md-3">{{ $qrequest->phone }}</div>
                        <div class="col-md-3"><strong>Address</strong></div>
                        <div class="col-md-3">{{ $qrequest->address ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Status</strong></div>
                        <div class="col-md-3">
                            <span class="badge
                                @if($qrequest->status == 'Approved') bg-success
                                @elseif($qrequest->status == 'Cancelled') bg-danger
                                @elseif($qrequest->status == 'Awaiting for Approval') bg-warning
                                @else bg-info @endif">
                                {{ $qrequest->status }}
                            </span>
                        </div>
                        <div class="col-md-3"><strong>Quoted</strong></div>
                        <div class="col-md-3">
                            {{ $qrequest->is_quoted ? 'Yes' : 'No' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Processed By</strong></div>
                        <div class="col-md-3">{{ $qrequest->processed_by ?? '-' }}</div>
                        <div class="col-md-3"><strong>Quoted At</strong></div>
                        <div class="col-md-3">{{ $qrequest->quoted_at ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Created At</strong></div>
                        <div class="col-md-3">{{ $qrequest->created_at }}</div>
                        <div class="col-md-3"><strong>Last Updated</strong></div>
                        <div class="col-md-3">{{ \Carbon\Carbon::parse($qrequest->updated_at)->diffForHumans() }}</div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Product(s)</strong></div>
                        <div class="col-md-9">{{ $qrequest->product ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Message</strong></div>
                        <div class="col-md-9">{{ $qrequest->message }}</div>
                    </div>

                    @if($qrequest->is_quoted)
                        <hr>
                        <div class="alert alert-success mb-0">
                            <i class="fa fa-check-circle"></i> This request has already been quoted.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection