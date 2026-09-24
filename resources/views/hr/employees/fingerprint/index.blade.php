@extends('layouts.hr')

@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-8 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="{{ url('home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('employees') }}">Employees</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button"
                class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    <div class="card p-2">
        <div class="card-body p-0">
            <div class="d-lg-flex align-items-center mb-2">
                <div>
                    <h6 class="mb-0 text-uppercase">Fingerprint Enrolled Employees</h6><span
                        class="badge bg-primary ms-2">{{ $fingerprintCards->count() }} enrolled</span>
                </div>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="{{ route('employees.fingerprint.settings') }}" class="btn btn-primary">Settings</a>
                </div>
            </div>
            @if ($fingerprintCards->isEmpty())
                <div class="text-center py-5 text-muted"><i class="fa fa-fingerprint fa-3x mb-3"></i>
                    <p>No employees have fingerprints enrolled yet.</p>
                </div>
            @else
                <table id="fingerprint-employees" class="table table-striped table-bordered items"
                    style="width:100%;white-space:nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Emp ID</th>
                            <th>Photo</th>
                            <th>Full Name</th>
                            <th>Position</th>
                            <th>Finger</th>
                            <th>Algorithm</th>
                            <th>Enrolled</th>
                            <th>Last verified</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fingerprintCards as $key => $card)
                            @php($emp = $card->employee)<tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $emp->emp_id }}</td>
                                <td class="text-center">
                                    @if ($card->photo_url)
                                        <img src="{{ $card->photo_url }}" class="rounded-circle" width="30"
                                        height="30" alt="{{ $card->full_name }}">@else<i
                                            class="fa fa-user text-muted"></i>
                                    @endif
                                </td>
                                <td>{{ $card->full_name ?: '—' }}</td>
                                <td>{{ $emp->position_name ?? '—' }}</td>
                                <td>{{ $emp->fingerprint_finger ?? '—' }}</td>
                                <td>{{ $emp->fingerprint_algorithm_version ?? '—' }}</td>
                                <td>{{ $emp->fingerprint_registered_at?->format('d M Y H:i') ?? '—' }}</td>
                                <td>{{ $emp->fingerprint_last_verified_at?->format('d M Y H:i') ?? '—' }}</td>
                                <td><span
                                        class="badge {{ $emp->fingerprint_enabled ? 'bg-success' : 'bg-secondary' }}">{{ $emp->fingerprint_enabled ? 'Enabled' : 'Disabled' }}</span>
                                </td>
                                <td>
                                    <form method="POST"
                                        action="{{ route('employees.fingerprint.toggle', encrypt($emp->id)) }}"
                                        class="d-inline">@csrf @method('PATCH')<button
                                            class="btn btn-sm btn-outline-warning">{{ $emp->fingerprint_enabled ? 'Disable' : 'Enable' }}</button>
                                    </form>
                                    <form method="POST"
                                        action="{{ route('employees.fingerprint.destroy', encrypt($emp->id)) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Remove this fingerprint enrollment? The employee must re-enroll on the app.');">
                                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i
                                                class="fa fa-trash"></i> Remove</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
@section('page-scripts')
    <script>
        $(function() {
            $('#fingerprint-employees').DataTable({
                destroy: true,
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                    targets: [2, 10]
                }]
            });
        });
    </script>
@endsection
