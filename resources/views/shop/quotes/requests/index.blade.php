@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function createqrequest(index) {
        document.getElementById('create-qrequest-'+index).submit();
    }

    function confirmDeleteQrequest(id) {
        Swal.fire({
            title: "{{ trans('navmenu.are_you_sure_delete') }}",
            text: "{{ trans('navmenu.no_revert') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
            cancelButtonText: "{{ trans('navmenu.no') }}"
        }).then((result) => {
            if (result.value) {
                document.getElementById('delete-qrequest-form-' + id).submit();
                Swal.fire(
                    "{{ trans('navmenu.deleted') }}",
                    "{{ trans('navmenu.cancelled') }}",
                    'success'
                )
            }
        })
        return false;
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>                            
                    <li class="breadcrumb-item">Quotes</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <form class="dashform row mb-0" action="{{url('f-quote-requests')}}" method="POST" id="dashform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-sm-12">
                        <div class="input-group mb-0">
                            <button type="button" class="btn btn-default mb-0 pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5>{{$page}}</h5>
                    {{-- <button type="button" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#createQuoteModal">
                        <i class="fa fa-plus"></i> New Quote Request
                    </button> --}}
                    <a href="{{ route('quote-requests.create') }}" class="btn btn-primary btn-sm float-right">
                        <i class="fa fa-plus"></i> New Quote Request
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    	<table id="del-multiple" class="table table-striped display nowrap" style="width: 100%;">
                      		<thead>
                       			<tr>
                       				<th>#</th>
                         			<th>Customer</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                         			<th>Address</th>
                                    <th>Created At</th>
                         			<th>Last updated</th>
                          	      	<th>Status</th>
                         			<th>Actions</th>
                         		</tr>
                      		</thead>
                      		<tbody>
                      		    @foreach($quoterequests as $index => $qrequest)
                                <tr>
                                    <td>{{$qrequest->id}}</td>
                                    <td><a href="{{ route('quote-requests.show', encrypt($qrequest->id)) }}">{{$qrequest->name}}</a></td>
                                    <td>{{ $qrequest->email }}</a> </td>
                                    <td>{{ $qrequest->phone }}</td>
    			                    <td>{{ $qrequest->address }}</td>
                                    <td>{{ $qrequest->created_at }}</td>
    			                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $qrequest->updated_at)->diffForHumans() }}</td>
                                    <td>
                                        {{$qrequest->status}}
                                        @if($qrequest->is_quoted)
                                            <span class="badge bg-success ms-1" title="Quoted on {{ $qrequest->quoted_at }}">Quoted</span>
                                        @endif
                                    </td>                                   
                                    <td>
                                        <a href="{{ route('quote-requests.show', encrypt($qrequest->id)) }}" title="View qrequest"><span class="fa fa-eye"></span></a>
                                        @if($qrequest->status == 'SENT')      
                                            |
                                            <a href="javascript:;"
                                            title="Edit qrequest"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editQuoteModal"
                                            data-id="{{ $qrequest->id }}"
                                            data-encrypted_id="{{ encrypt($qrequest->id) }}"
                                            data-name="{{ $qrequest->name }}"
                                            data-email="{{ $qrequest->email }}"
                                            data-phone="{{ $qrequest->phone }}"
                                            data-address="{{ $qrequest->address }}"
                                            data-product="{{ $qrequest->product }}"
                                            data-message="{{ $qrequest->message }}"
                                            data-status="{{ $qrequest->status }}"
                                            class="edit-quote-btn">
                                                <i class="fa fa-pencil" style="color: #17a2b8;"></i>
                                            </a>
                                            |
                                        @endif
                                        <form id="delete-qrequest-form-{{ $qrequest->id }}" action="{{ route('quote-requests.destroy', encrypt($qrequest->id)) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:;" title="Delete qrequest" onclick="return confirmDeleteQrequest({{ $qrequest->id }})">
                                                <i class="fa fa-trash-o" style="color: red;"></i>
                                            </a>
                                        </form>
    			                    </td>
                                </tr>
                                @endforeach
    	                    </tbody>
    	                </table>
                    </div>
                    <form id="frm-example" action="{{ url('delete-multiple-pro-qrequests') }}" method="POST">
                        @csrf
                        <button id="submitButton" class="btn btn-danger btn-sm">{{ trans('navmenu.delete_selected') }}</button>
                    </form>
	            </div>
	        </div>
        </div>
    </div>

    <div class="modal fade" id="createQuoteModal" tabindex="-1" aria-labelledby="createQuoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('quote-requests') }}" method="POST">
                    @csrf
                    <div class="modal-header py-2">
                        <h6 class="modal-title" id="createQuoteModalLabel">New Quote Request</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-2">
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label for="c_name" class="form-label mb-1">Name</label>
                                <input type="text" name="name" id="c_name" class="form-control form-control-sm" value="{{ old('name') }}">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="c_email" class="form-label mb-1">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="c_email" class="form-control form-control-sm" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="c_phone" class="form-label mb-1">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="c_phone" class="form-control form-control-sm" value="{{ old('phone') }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="c_address" class="form-label mb-1">Address</label>
                                <input type="text" name="address" id="c_address" class="form-control form-control-sm" value="{{ old('address') }}">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="c_product" class="form-label mb-1">Product(s)</label>
                                <input type="text" name="product" id="c_product" class="form-control form-control-sm" value="{{ old('product') }}">
                            </div>
                            <div class="col-12 mb-1">
                                <label for="c_message" class="form-label mb-1">Message <span class="text-danger">*</span></label>
                                <textarea name="message" id="c_message" rows="2" class="form-control form-control-sm" required>{{ old('message') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editQuoteModal" tabindex="-1" aria-labelledby="editQuoteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editQuoteForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header py-2">
                        <h6 class="modal-title" id="editQuoteModalLabel">Edit Quote Request</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-2">
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label for="edit_name" class="form-label mb-1">Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="edit_email" class="form-label mb-1">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="edit_email" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="edit_phone" class="form-label mb-1">Mobile <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="edit_phone" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label for="edit_status" class="form-label mb-1">Status</label>
                                <select name="status" id="edit_status" class="form-select form-select-sm">
                                    <option value="SENT">SENT</option>
                                    <option value="Awaiting for Approval">Awaiting for Approval</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_address" class="form-label mb-1">Address</label>
                                <input type="text" name="address" id="edit_address" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 mb-2">
                                <label for="edit_product" class="form-label mb-1">Product(s)</label>
                                <input type="text" name="product" id="edit_product" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 mb-1">
                                <label for="edit_message" class="form-label mb-1">Message <span class="text-danger">*</span></label>
                                <textarea name="message" id="edit_message" rows="2" class="form-control form-control-sm" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 

    <script src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>

    <script>
        $(function () {

            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('assets/vendor/libs/Swahili.json') }}";
            }

            var deltable = $('#del-multiple').DataTable({
                "scrollX": true,
                language: {
                    url: languageUrl
                },
                'columnDefs': [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                }],
                'select': {
                    'style': 'multi'
                },
                // 'order': [[1, 'asc']]
            })

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false) : $('#submitButton').prop(
                    "disabled", true);
                counterChecked < 0 ? counterChecked = 0 : counterChecked;
                console.log(counterChecked);
            });

            $('#submitButton').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ trans('navmenu.are_you_sure_delete') }}",
                    text: "{{ trans('navmenu.no_revert') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ trans('navmenu.cancel_it') }}",
                    cancelButtonText: "{{ trans('navmenu.no') }}"
                }).then((result) => {
                    if (result.value) {
                        $('#frm-example').submit();
                        Swal.fire(
                            "{{ trans('navmenu.deleted') }}",
                            "{{ trans('navmenu.cancelled') }}",
                            'success'
                        )
                    }
                })
            });

              // Handle form submission event 
            $('#frm-example').on('submit', function(e) {
                var form = this;
                var rows_selected = deltable.column(0).checkboxes.selected();
                if (rows_selected.length > 0) {
                    // Iterate over all selected checkboxes
                    $.each(rows_selected, function(index, rowId) {
                        // Create a hidden element 
                        $(form).append(
                            $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'ids[]')
                            .val(rowId)
                        );
                    });
                }
            });

            // Populate Edit modal from clicked row's data attributes
            $(document).on('click', '.edit-quote-btn', function () {
                var btn = $(this);

                $('#edit_name').val(btn.data('name'));
                $('#edit_email').val(btn.data('email'));
                $('#edit_phone').val(btn.data('phone'));
                $('#edit_address').val(btn.data('address'));
                $('#edit_product').val(btn.data('product'));
                $('#edit_message').val(btn.data('message'));
                $('#edit_status').val(btn.data('status'));

                $('#editQuoteForm').attr('action', "{{ url('quote-requests') }}/" + btn.data('encrypted_id'));
            });

            @if ($errors->any())
                // Reopen the create modal automatically if validation failed on submit
                var createModal = new bootstrap.Modal(document.getElementById('createQuoteModal'));
                createModal.show();
            @endif
        });
    </script>
@endsection