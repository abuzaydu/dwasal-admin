@extends('layouts.vml')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')

    {{-- Breadcrumb --}}
    <div class="block-header pt-4 py-lg-4 py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{ url('home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>

        
            <div class="col-md-6 col-sm-12 text-md-end d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                <form class="dashform report-form d-inline"
                    action="{{ url('visitors-dash') }}"
                    method="POST">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{ $start_date }}">
                    <input type="hidden" name="end_date"   id="end_input"   value="{{ $end_date }}">

                    <button type="button" class="btn btn-white btn-sm" id="reportrange">
                        <span><i class="fa fa-calendar"></i></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>


    {{-- Stats Cards --}}
    @php
        $period    = request('period', 'today');
        $startDate = request('start_date', $start_date);
        $endDate   = request('end_date',   $end_date);

        $exportParams = ['period' => $period];
        if (request('start_date') && request('end_date')) {
            $exportParams['start_date'] = request('start_date');
            $exportParams['end_date']   = request('end_date');
        }

        $cards = [
            [
                'type'  => 'total',
                'label' => 'Total Visitors',
                'icon'  => 'fa-users',
                'color' => 'text-primary',
                'count' => $totalVisitors ?? 0,
            ],
            [
                'type'  => 'pending',
                'label' => 'Pending Visitors',
                'icon'  => 'fa-clock-o',
                'color' => 'text-warning',
                'count' => $pendingVisitors ?? 0,
            ],
            [
                'type'  => 'checkedin',
                'label' => 'Checked-in Visitors',
                'icon'  => 'fa-check-circle',
                'color' => 'text-success',
                'count' => $checkedinVisitors ?? 0,
            ],
            [
                'type'  => 'checkedout',
                'label' => 'Checked-out Visitors',
                'icon'  => 'fa-sign-out',
                'color' => 'text-info',
                'count' => $checkedoutVisitors ?? 0,
            ],
        ];
    @endphp

    <div class="row clearfix">
        @foreach ($cards as $card)
            @php
                $listUrl   = route('visitors.list', array_merge($exportParams, ['type' => $card['type']]));
                $excelUrl  = route('visitors.export', array_merge($exportParams, ['type' => $card['type'], 'format' => 'xlsx']));
                $pdfUrl    = route('visitors.export', array_merge($exportParams, ['type' => $card['type'], 'format' => 'pdf']));
            @endphp

            <div class="col-lg-3 col-md-6 col-sm-12 d-flex align-items-stretch">
                <div class="card w-100 shadow-sm border-0 position-relative">

                    {{-- Export dropdown --}}
                    <div class="dropdown position-absolute top-0 end-0 m-2" style="z-index: 10;">
                        <button class="btn btn-sm btn-light border-0 shadow-none"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Export">
                            <i class="fa fa-download text-secondary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <a class="dropdown-item" href="{{ $excelUrl }}">
                                    <i class="fa fa-file-excel-o text-success me-2"></i> Export Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ $pdfUrl }}">
                                    <i class="fa fa-file-pdf-o text-danger me-2"></i> Export PDF
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Clickable card body --}}
                    <a href="{{ $listUrl }}" class="card-body text-center text-decoration-none text-reset stretched-link">
                        <div class="mb-2">
                            <i class="fa {{ $card['icon'] }} fa-2x {{ $card['color'] }}"></i>
                        </div>
                        <h6 class="text-muted mb-2">{{ $card['label'] }}</h6>
                        <h2 class="mb-0 fw-bold">{{ $card['count'] }}</h2>
                    </a>

                </div>
            </div>
        @endforeach
    </div>


    {{-- Activity Log Table --}}
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
                                        <td>{{ $log->email ?? '-' }}</td>
                                        <td>{{ optional($log->user)->first_name }} {{ optional($log->user)->last_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                                        <td>
                                            @php
                                                $statusColor = match($log->status) {
                                                    'Awaiting Host permission' => 'warning',
                                                    'Permission Granted'       => 'success',
                                                    'Checked In'               => 'info',
                                                    'Checked Out'              => 'secondary',
                                                    default                    => 'dark',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">{{ $log->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No visitor logs found for the selected period.
                                        </td>
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
                ordering  : true,
            });
        });
    </script>
@endsection