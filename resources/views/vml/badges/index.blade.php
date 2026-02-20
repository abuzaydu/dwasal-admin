@extends('layouts.vml')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')

    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ url('prod-dash') }}"><i class="fa fa-home"></i></a>
                    </li>
                    <li class="breadcrumb-item">Badges Management</li>
                    <li class="breadcrumb-item active">{{ $page }}</li>
                </ul>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-12 text-end">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#badgeModal">
                    <i class="fa fa-plus-square"></i> New Badge
                </button>
                
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <!--badges table-->
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-new2">
                        <li class="nav-item">
                            <a class="nav-link active show" data-bs-toggle="tab" href="#tab_0">Badges List</a>
                        </li>
                    </ul>

                    <div class="tab-content pt-2">
                        <div class="tab-pane fade show active" id="tab_0" role="tabpanel">
                            <div class="table-responsive" id="badge-list">
                                <table id="badges" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Badge No.</th>
                                            <th>Company</th>
                                            <th>Status</th>
                                            {{-- <th style="text-align: center;">Actions</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($badges as $key => $badge)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $badge->badge_number }}</td>
                                            <td>{{ $badge->company->name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $statusColor = match($badge->status) {
                                                        'available' => 'success',
                                                        'assigned'  => 'primary',
                                                        'lost'      => 'warning',
                                                        'damaged'   => 'danger',
                                                        default     => 'secondary',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">
                                                    {{ ucfirst($badge->status) }}
                                                </span>
                                            </td>
                                            {{-- <td style="text-align: center;">
                                               
                                                <form method="POST" action="{{ route('badges.destroy', encrypt($badge->id)) }}" id="delete-form-{{ $key }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDelete({{ $key }})">
                                                        <i class="fa fa-trash" style="color: red;"></i>
                                                    </a>
                                                </form> 
                                            </td> --}}
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!--end badges table-->

    <!-- Badge Generate Modal -->
    <div class="modal animated zoomIn" id="badgeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Badges</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form row g-3" method="POST" action="{{ route('badges.storeBulk') }}">
                        @csrf

                        {{-- Company --}}
                        <div class="col-md-6">
                            <label class="form-label">Company <span style="color: red; font-weight: bold;">*</span></label>
                            <select name="company_id" id="company_id" class="form-select form-select-sm mb-1" required>
                                <option value="">-- Select Company --</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Badge Prefix --}}
                        <div class="col-md-6">
                            <label class="form-label">Badge Prefix <small class="text-muted">(optional, default: B)</small></label>
                            <input type="text" name="badge_prefix" id="badge_prefix"
                                class="form-control form-control-sm mb-1"
                                value="{{ old('badge_prefix', 'B') }}"
                                maxlength="10"
                                placeholder="e.g. B, VIP, TEMP">
                            @error('badge_prefix')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Badge Count --}}
                        <div class="col-md-6">
                            <label class="form-label">Number of Badges to Generate <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="number" name="badge_count" id="badge_count"
                                class="form-control form-control-sm mb-1"
                                value="{{ old('badge_count', 1) }}"
                                min="1" max="100"
                                placeholder="Enter count"
                                required>
                            @error('badge_count')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Live Preview --}}
                        <div class="col-md-6 d-flex align-items-center">
                            <div id="preview" class="alert alert-info w-100 mb-1 py-2" style="display: none;">
                                <p class="mb-1 small">Badges to be generated: <strong id="preview-count"></strong></p>
                                <p class="mb-0 small">Example range: <strong id="preview-example"></strong></p>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm px-4 radius-30">
                                <i class="fa fa-cogs"></i> Generate Badges
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end badge modal -->

@endsection

@section('page-scripts')
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <script>
        $(function () {
            $('#badges').DataTable();
        });

        // Live preview for badge generation
        const countInput  = document.getElementById('badge_count');
        const prefixInput = document.getElementById('badge_prefix');
        const preview     = document.getElementById('preview');

        function updatePreview() {
            const count  = parseInt(countInput.value) || 0;
            const prefix = prefixInput.value || 'B';

            if (count > 0) {
                preview.style.display = 'block';
                document.getElementById('preview-count').textContent   = count;
                document.getElementById('preview-example').textContent =
                    `${prefix}0001 → ${prefix}${String(count).padStart(4, '0')}`;
            } else {
                preview.style.display = 'none';
            }
        }

        countInput.addEventListener('input', updatePreview);
        prefixInput.addEventListener('input', updatePreview);
    </script>
@endsection