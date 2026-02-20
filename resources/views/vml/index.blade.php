@extends('layouts.vml')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')

<!--breadcrumb-->
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a>
                </li>
                <li class="breadcrumb-item">Visitors Management</li>
                <li class="breadcrumb-item active">{{ $page }}</li>
            </ul>
        </div>
    </div>
</div>
<!--end breadcrumb-->

<!-- Stats Cards -->
<div class="row clearfix">

    <!-- Total Visitors Today -->
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fa fa-users fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted mb-2">Total Visitors Today</h6>
                <h2 class="mb-0 fw-bold">{{ $totalVisitors ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <!-- Pending Visitors -->
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fa fa-users fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted mb-2">Pending Visitors</h6>
                <h2 class="mb-0 fw-bold">{{ $pendingVisitors ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <!-- Checked-in Visitors -->
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm border-0">
            <div class="card-body text-center">
                 <div class="mb-2">
                    <i class="fa fa-users fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted mb-2">Checked-in Visitors</h6>
                <h2 class="mb-0 fw-bold">{{ $checkedinVisitors ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <!-- Total Visitors Monthly -->
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm border-0">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fa fa-users fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted mb-2">Total Visitors Monthly</h6>
                <h2 class="mb-0 fw-bold">{{ $visitorsMonthly ?? 0 }}</h2>
            </div>
        </div>
    </div>

</div>

<!-- Activity Log Table -->
<div class="row clearfix mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity Log</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="visitorsTable" class="table table-striped display nowrap" style="width:100%">
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
                                        @php
                                            $statusColor = match($log->status) {
                                                'Awaiting Host permission' => 'warning',
                                                'Checked In' => 'info',
                                                'Checked Out' => 'secondary',
                                                default => 'success',
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">
                                            {{ $log->status }}
                                        </span>
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

@section('page-scripts')
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(function () {
            $('#visitorsTable').DataTable({
                responsive: true,
                pageLength: 10,
                ordering: true
            });
        });
    </script>
@endsection