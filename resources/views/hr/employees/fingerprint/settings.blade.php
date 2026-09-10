@extends('layouts.hr')
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="{{ url('home') }}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employees.fingerprint.index') }}">Fingerprint</a></li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button"
                class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        <div class="col-xl-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        <div>
                            <h6 class="mb-0 text-uppercase">Fingerprint Settings</h6>
                        </div>
                    </div>

                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate method="POST"
                            action="{{ route('employees.fingerprint.settings.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="allow_fingerprint_enrollment" name="allow_fingerprint_enrollment" value="1"
                                        @checked($allowFingerprintEnrollment)>
                                    <label class="form-check-label" for="allow_fingerprint_enrollment">
                                        Enroll fingerprint (show in mobile side menu)
                                    </label>
                                </div>
                                <p class="text-muted small mt-2 mb-0">
                                    When this is off, the Enroll Fingerprint item is hidden in the app menu and new
                                    enrollments are blocked. Fingerprint attendance still works for already enrolled
                                    employees.
                                </p>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                <a href="{{ route('employees.fingerprint.index') }}"
                                    class="btn btn-warning px-4 radius-30">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
