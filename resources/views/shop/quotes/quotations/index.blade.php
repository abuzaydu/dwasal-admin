@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
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
                <form class="dashform row mb-0" action="{{ route('quotations.filter') }}" method="POST" id="dashform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
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
                    {{-- <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm float-right">
                        <i class="fa fa-plus"></i> New Quotation
                    </a> --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="del-multiple" class="table table-striped display nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Quote No.</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Proinvoice</th>
                                    <th>Created At</th>
                                    <th>Valid Until</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotations as $quotation)
                                <tr>
                                    <td>{{$quotation->id}}</td>
                                    <td>{{$quotation->quote_number}}</td>
                                    <td><a href="{{ route('quotations.show', encrypt($quotation->id)) }}">{{$quotation->customer_name ?? '-'}}</a></td>
                                    <td>{{$quotation->email}}</td>
                                    <td>{{ number_format($quotation->total, 2) }}</td>
                                    <td>
                                        <span class="badge 
                                            {{ $quotation->status == 'Accepted' ? 'bg-success' : ($quotation->status == 'Rejected' ? 'bg-danger' : ($quotation->status == 'Sent' ? 'bg-info' : ($quotation->status == 'Expired' ? 'bg-secondary' : 'bg-warning'))) }}">
                                            {{ $quotation->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $quotation->is_proinvoice_created ? 'bg-success' : 'bg-info' }}">
                                            {{ $quotation->is_proinvoice_created ? 'Proinvoice Created' : 'Not Yet Created' }}
                                        </span>
                                    </td>
                                    <td>{{$quotation->created_at}}</td>
                                    <td>{{$quotation->valid_until ?? '-'}}</td>
                                    <td>
                                        <a href="{{ route('quotations.show', encrypt($quotation->id)) }}" title="View"><i class="fa fa-eye"></i></a> 
                                        @if ($quotation->status === 'Draft')
                                            |
                                            <a href="{{ route('quotations.edit', encrypt($quotation->id)) }}" title="Edit"><i class="fa fa-edit" style="color: blue;"></i></a> |
                                            <form action="{{ route('quotations.destroy', encrypt($quotation->id)) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link p-0" title="Delete" style="border:none; background:none;">
                                                    <i class="fa fa-trash" style="color: red;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <form id="frm-example" action="{{ url('delete-multiple-quotations') }}" method="POST">
                        @csrf
                        <button id="submitButton" class="btn btn-danger btn-sm">{{ trans('navmenu.delete_selected') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script src="{{ asset('assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert/sweetalert.min.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>

    <script>
        $(function () {
            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = userlang === 'en'
                ? "{{ asset('assets/vendor/libs/English.json') }}"
                : "{{ asset('assets/vendor/libs/Swahili.json') }}";

            var deltable = $('#del-multiple').DataTable({
                "scrollX": true,
                language: { url: languageUrl },
                'columnDefs': [{ 'targets': 0, 'checkboxes': { 'selectRow': true } }],
                'select': { 'style': 'multi' },
            });

            var counterChecked = 0;
            $('#submitButton').prop("disabled", true);

            $('body').on('change', 'input[type="checkbox"]', function() {
                this.checked ? counterChecked++ : counterChecked--;
                counterChecked > 0 ? $('#submitButton').prop("disabled", false) : $('#submitButton').prop("disabled", true);
                counterChecked < 0 ? counterChecked = 0 : counterChecked;
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
                    }
                });
            });

            $('#frm-example').on('submit', function(e) {
                var form = this;
                var rows_selected = deltable.column(0).checkboxes.selected();
                if (rows_selected.length > 0) {
                    $.each(rows_selected, function(index, rowId) {
                        $(form).append($('<input>').attr('type', 'hidden').attr('name', 'ids[]').val(rowId));
                    });
                }
            });
        });
    </script>
@endsection