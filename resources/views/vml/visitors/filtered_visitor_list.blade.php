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
                            <a href="{{ route('visitors.dashboard', request()->only('period', 'start_date', 'end_date')) }}"
                            class="btn btn-sm btn-link ps-0">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item"><a href="{{ url('home') }}"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('visitors.dashboard') }}">Visitor Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $typeLabel }}</li>
                    </ul>
                </div>

            
            </div>
        </div>

        {{-- Visitor List Table --}}
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            {{ $typeLabel }}
                            <span class="badge bg-secondary ms-2">{{ $visitors->count() }}</span>
                        </h5>
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
                                        <th>Purpose</th>
                                        <th>Visit Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($visitors as $visitor)
                                        @php
                                            $hostName = trim(
                                                optional($visitor->user)->first_name . ' ' .
                                                optional($visitor->user)->last_name
                                            ) ?: '—';

                                            $statusColor = match($visitor->status) {
                                                'Awaiting Host permission' => 'warning',
                                                'Permission Granted'       => 'success',
                                                'Checked In'               => 'info',
                                                'Checked Out'              => 'secondary',
                                                default                    => 'dark',
                                            };
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>{{ $visitor->name }}</td>
                                            <td>{{ $visitor->email ?? '—' }}</td>
                                            <td>{{ $hostName }}</td>
                                            <td>{{ $visitor->purpose ?? '—' }}</td>
                                            <td data-order="{{ $visitor->created_at->timestamp }}">
                                                {{ $visitor->created_at->format('d M Y, H:i') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusColor }}">
                                                    {{ $visitor->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                No visitors found for the selected filter.
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
                responsive : true,
                pageLength : 25,
                lengthMenu : [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
                ordering   : true,
                order      : [[5, 'desc']],
                columnDefs : [
                    { orderable: false, targets: [0] },
                ],
                language: {
                    search           : '',
                    searchPlaceholder: 'Search visitors…',
                    info             : 'Showing _START_ to _END_ of _TOTAL_ visitors',
                    infoEmpty        : 'No visitors found',
                    infoFiltered     : '(filtered from _MAX_ total)',
                    zeroRecords      : 'No matching visitors found',
                },
            });
        });
    </script>
@endsection