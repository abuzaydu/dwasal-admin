@extends('layouts.vml')
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>  
                    <li class="breadcrumb-item">Visitors Managment</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-4 col-sm-12 text-right">
               
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="card">
            <div class="card-body">
                
            </div>
        </div>
    </div>
<div class="row clearfix">
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body">
                <div class="text-center">
                    <h6 class="text-muted mb-3">Total Visitors Today</h6>
                    <h2 class="mb-0">{{ $totalVisitors ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body">
                <div class="text-center">
                    <h6 class="text-muted mb-3">Pending Visitors</h6>
                    <h2 class="mb-0">{{ $pendingVisitors ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex  align-items-stretch">
        <div class="card w-100">
            <div class="card-body">
                <div class="text-center">
                    <h6 class="text-muted mb-3">Checked-in Visitors</h6>
                    <h2 class="mb-0">{{ $checkedinVisitors ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body">
                <div class="text-center">
                    <h6 class="text-muted mb-3">Total Visitors Monthly</h6>
                    <h2 class="mb-0">
                        {{ $visitorsMonthly ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity Log</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Visitor Name</th>
                                <th>Email</th>
                                <th>Host</th>
                                <th>Visit Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visitorsLogs as $log)
                                <tr>
                                  
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->name }}</td>
                                    <td>{{ $log->email }}</td>
                                    <td>{{ $log->user->first_name }} {{ $log->user->last_name }}</td>
                                    <td>{{ $log->created_at }}</td>
                                    <td>
                                       @if ($log->status == 'Awaiting Host permission')
                                            <span class="badge badge-warning">{{ $log->status }}</span>
                                       @elseif ($log->status == 'Checked In')
                                            <span class="badge badge-info">{{ $log->status }}</span>
                                        @elseif($log->status == 'Checked Out')
                                            <span class="badge badge-secondary">{{ $log->status }}</span>
                                        @else
                                        <span class="badge badge-success">{{ $log->status }}</span>
                                       @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No visitor logs found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
