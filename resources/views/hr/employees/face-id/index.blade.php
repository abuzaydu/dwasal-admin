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
            <div class="col-md-4 text-md-end">
                <a href="{{ route('employees.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-plus"></i> Add Employee
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center mb-4 gap-2">
                <h6 class="mb-0 text-uppercase"><i class="fa fa-user-circle"></i> Face ID — Enrolled Employees</h6>
                <span class="badge bg-primary">{{ $faceCards->count() }} enrolled</span>
            </div>
            @if($faceCards->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="fa fa-user-times fa-3x mb-3"></i>
                    <p>No employees have Face ID enrolled yet.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($faceCards as $card)
                        @php $emp = $card->employee; @endphp
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card h-100 shadow-sm border">
                                <div class="card-body text-center p-3">
                                    <div class="face-id-avatar mx-auto mb-3 position-relative">
                                        @if($card->photo_url)
                                            <img src="{{ $card->photo_url }}" alt="{{ $card->full_name }}"
                                                 class="rounded-circle face-id-photo">
                                        @else
                                            <div class="face-id-silhouette rounded-circle d-flex align-items-center justify-content-center">
                                                <svg viewBox="0 0 120 140" width="90" height="105" aria-hidden="true">
                                                    <ellipse cx="60" cy="52" rx="38" ry="46" fill="#e8eef5"/>
                                                    <ellipse cx="60" cy="118" rx="48" ry="22" fill="#dce4ec"/>
                                                    <circle cx="42" cy="48" r="5" fill="#94a3b8"/>
                                                    <circle cx="78" cy="48" r="5" fill="#94a3b8"/>
                                                    <path d="M48 68 Q60 78 72 68" stroke="#94a3b8" stroke-width="2" fill="none"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="badge bg-success position-absolute bottom-0 end-0">Face ID</span>
                                    </div>

                                    <h6 class="mb-1">{{ $card->full_name ?: '—' }}</h6>
                                    <p class="text-muted small mb-1">ID: {{ $emp->emp_id }}</p>
                                    @if($emp->position_name)
                                        <p class="text-muted small mb-2">{{ $emp->position_name }}</p>
                                    @endif
                                    <p class="small mb-3">
                                        <span class="badge bg-light text-dark">
                                            {{ $emp->face_model_version ?? 'facenet' }}
                                        </span>
                                        @if($emp->face_registered_at)
                                            <br><span class="text-muted">Enrolled {{ $emp->face_registered_at->format('d M Y H:i') }}</span>
                                        @endif
                                    </p>

                                    <form method="POST"
                                          action="{{ route('employees.face-id.destroy', encrypt($emp->id)) }}"
                                          onsubmit="return confirm('Remove Face ID for this employee? They will need to re-enroll on the app.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            <i class="fa fa-trash"></i> Remove Face ID
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        .face-id-avatar { width: 110px; height: 110px; }
        .face-id-photo {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border: 3px solid #28a745;
        }
        .face-id-silhouette {
            width: 110px;
            height: 110px;
            background: #f4f6f9;
            border: 3px solid #28a745;
        }
    </style>
@endsection
