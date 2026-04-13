@extends('layouts.vms')

@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')

<!--breadcrumb-->
<div class="block-header pt-4">
    <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Vehicle Management</li>
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ul>
        </div>

        <div class="col-lg-7 col-md-4 col-sm-12 mt-2 mt-md-0">
            <div class="d-flex flex-wrap justify-content-start justify-content-md-end align-items-center gap-2">

                <form class="dashform" action="{{ url('f-vendors') }}" method="POST" id="stockform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <button type="button" class="btn btn-default btn-sm w-auto" id="reportrange" style="white-space: nowrap;">
                        <i class="fa fa-calendar"></i>
                        <span id="reportrange-label" class="mx-1"></span>
                        <i class="fa fa-caret-down"></i>
                    </button>
                </form>

                <button type="button"
                    class="btn btn-success btn-sm w-auto"
                    style="white-space: nowrap; font-size: 13px; padding: 4px 10px;"
                    data-bs-toggle="modal"
                    data-bs-target="#vendorModal">
                    <i class="fa fa-user-plus me-1"></i> New Vendor
                </button>

            </div>
        </div>
    </div>
</div>
<!--end breadcrumb-->

<div class="row">
    <div class="col-md-12 mx-auto">
        <div class="card radius-6">
            <div class="card-body table-responsive">
                <table id="example1" class="table table-striped table-bordered nowrap" style="width:100%; font-size:13px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendor Name</th>
                            <th>{{ trans('navmenu.contact_number') }}</th>
                            <th>{{ trans('navmenu.email_address') }}</th>
                            <th>{{ trans('navmenu.address') }}</th>
                            <th>{{ trans('navmenu.created_at') }}</th>
                            <th class="text-center">{{ trans('navmenu.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $i => $vendor)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <a href="{{ route('vendors.show', encrypt($vendor->id)) }}">
                                    {{ $vendor->vendor_name }}
                                </a>
                            </td>
                            <td>{{ $vendor->phone }}</td>
                            <td>{{ $vendor->email }}</td>
                            <td>{{ $vendor->address }}</td>
                            <td>{{ \Carbon\Carbon::parse($vendor->created_at)->format('d-m-Y') }}</td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="{{ route('vendors.edit', encrypt($vendor->id)) }}" class="text-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                                |
                                <form method="POST"
                                    action="{{ route('vendors.destroy', encrypt($vendor->id)) }}"
                                    id="delete-form-{{ $i }}"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <a href="javascript:;" onclick="return confirmDelete({{ $i }})" class="text-danger">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- New Vendor Modal --}}
<div class="modal fade" id="vendorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('vendors.store') }}">
                @csrf
                <input type="hidden" name="vendor_for" value="Parts">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" required
                                placeholder="Enter vendor name"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="phone"
                                placeholder="Enter vendor mobile number"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email"
                                placeholder="Enter vendor email address"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address"
                                placeholder="Enter vendor address"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number"
                                placeholder="Account Number"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Account Name</label>
                            <input type="text" name="account_name"
                                placeholder="Account Name"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Swift Code</label>
                            <input type="text" name="swift_code"
                                placeholder="Swift Code"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name"
                                placeholder="Bank Name"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="form-label">Branch Name</label>
                            <input type="text" name="branch_name"
                                placeholder="Branch Name"
                                class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page-scripts')
<script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

<script>
    $(function () {
        $('#example1').DataTable({
            scrollX: true,
            responsive: false
        });
    });

    function confirmDelete(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection