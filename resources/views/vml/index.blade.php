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


{{-- Filter Tabs --}}
<div class="row mb-3">
    <div class="col-12 text-end">
        <div class="dropdown">
            <button class="btn btn-sm btn-primary dropdown-toggle" 
                    type="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                {{ ucfirst(request('period', 'today')) }}
            </button>

            <ul class="dropdown-menu">
                @foreach(['today' => 'Today', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly', 'total' => 'Total'] as $key => $label)
                    <li>
                        <a class="dropdown-item {{ request('period', 'today') === $key ? 'active' : '' }}"
                           href="{{ request()->fullUrlWithQuery(['period' => $key]) }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="row clearfix">

    {{-- Helper: export dropdown macro --}}
    @php
        $period = request('period', 'today');

        // type = the card/data type slug passed to export route
        $exportDropdown = fn(string $type) => '
            <div class="dropdown position-absolute top-0 end-0 m-2">
                <button class="btn btn-sm btn-light border-0 shadow-none"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        title="Export">
                    <i class="fa fa-download text-secondary"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <a class="dropdown-item" href="' . route('visitors.export', ['type' => $type, 'period' => $period, 'format' => 'xlsx']) . '">
                            <i class="fa fa-file-excel-o text-success me-2"></i> Export Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="' . route('visitors.export', ['type' => $type, 'period' => $period, 'format' => 'pdf']) . '">
                            <i class="fa fa-file-pdf-o text-danger me-2"></i> Export PDF
                        </a>
                    </li>
                </ul>
            </div>
        ';
    @endphp

    <!-- Total Visitors -->
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm border-0 position-relative">
            {!! $exportDropdown('total') !!}
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fa fa-users fa-2x text-primary"></i>
                </div>
                <h6 class="text-muted mb-2">
                    Total Visitors
                    <span class="badge bg-secondary">{{ ucfirst($period) }}</span>
                </h6>
                <h2 class="mb-0 fw-bold">{{ $totalVisitors ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <!-- Pending Visitors -->
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm border-0 position-relative">
            {!! $exportDropdown('pending') !!}
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fa fa-clock-o fa-2x text-warning"></i>
                </div>
                <h6 class="text-muted mb-2">Pending Visitors</h6>
                <h2 class="mb-0 fw-bold">{{ $pendingVisitors ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <!-- Checked-in Visitors -->
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm border-0 position-relative">
            {!! $exportDropdown('checkedin') !!}
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fa fa-check-circle fa-2x text-success"></i>
                </div>
                <h6 class="text-muted mb-2">Checked-in Visitors</h6>
                <h2 class="mb-0 fw-bold">{{ $checkedinVisitors ?? 0 }}</h2>
            </div>
        </div>
    </div>

        <!-- Checked-out Visitors -->
    <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
        <div class="card w-100 shadow-sm border-0 position-relative">
            {!! $exportDropdown('checkedout') !!}
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="fa fa-sign-out fa-2x text-info"></i>
                </div>
                <h6 class="text-muted mb-2">Checked-out Visitors</h6>
                <h2 class="mb-0 fw-bold">{{ $checkedoutVisitors ?? 0 }}</h2>
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