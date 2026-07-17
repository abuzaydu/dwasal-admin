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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card p-2">
                <div class="card-body p-0">
                    <div class="d-lg-flex align-items-center mb-1 gap-0">
                        <div class="psetting-relative">
                            <h6 class="mb-0 text-uppercase" id="list-title">Face ID — Enrolled Employees</h6>
                            <span class="badge bg-primary ms-2">{{ $faceCards->count() }} enrolled</span>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('employees.create') }}" class="btn btn-primary"><i
                                    class="fa fa-plus-square"></i> Add Employee</a>
                        </div>
                    </div>

                    <div id="item-list">
                        @if ($faceCards->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="fa fa-user-times fa-3x mb-3"></i>
                                <p>No employees have Face ID enrolled yet.</p>
                            </div>
                        @else
                            <table id="face-id-employees" class="table table-striped table-bordered items"
                                style="width:100%; font-size 14px; white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">Emp ID</th>
                                        <th style="text-align: center;">Photo</th>
                                        <th style="text-align: center;">Full Name</th>
                                        <th style="text-align: center;">Position</th>
                                        <th style="text-align: center;">Face Model</th>
                                        <th style="text-align: center;">Enrolled Date</th>
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($faceCards as $key => $card)
                                        @php $emp = $card->employee; @endphp
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $emp->emp_id }}</td>
                                            <td class="width45" style="text-align: center;">
                                                @if ($card->photo_url)
                                                    <img src="{{ $card->photo_url }}" class="rounded-circle width35"
                                                        height="30px" width="30px" alt="{{ $card->full_name }}">
                                                @else
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                                        style="width:30px; height:30px;">
                                                        <i class="fa fa-user text-muted small"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $card->full_name ?: '—' }}</td>
                                            <td>{{ $emp->position_name ?? '—' }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $emp->face_model_version ?? 'facenet' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($emp->face_registered_at)
                                                    {{ $emp->face_registered_at->format('d M Y H:i') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                <form method="POST"
                                                    action="{{ route('employees.face-id.destroy', encrypt($emp->id)) }}"
                                                    onsubmit="return confirm('Remove Face ID for this employee? They will need to re-enroll on the app.');"
                                                    style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fa fa-trash"></i> Remove
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        $(function() {
            $('#face-id-employees').DataTable({
                destroy: true,
                columnDefs: [{
                    orderable: false,
                    searchable: false,
                    targets: [-1]
                }]
            });
        });
    </script>
@endsection
