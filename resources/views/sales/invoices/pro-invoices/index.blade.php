@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function createInvoice(index) {
        document.getElementById('create-invoice-'+index).submit();
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4 ">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
                <form class="dashform row g-1" action="{{ url('f-pro-invoices') }}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-5">
                        <a  class="btn btn-success btn-sm" href="{{route('pro-invoices.create')}}"><i class="fa fa-edit"></i> New Profoma Invoice</a>
                    </div>
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="col-md-7">
                        <button type="button" class="btn btn-default pull-right" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row clearfix">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                    	<table id="del-multiple" class="table table-striped display nowrap" style="width: 100%;">
                      		<thead>
                       			<tr>
                       				<th>#</th>
                                    <th>User</th>
                         			<th>Customer</th>
                                    <th>PFI No.</th>
                                    <th>Invoice Date</th>
                         			<th>Due Date</th>
                         			<th>Last updated</th>
                          	      	<th>Status</th>
                         			<th>Actions</th>
                         		</tr>
                      		</thead>
                      		<tbody>
                      		    @foreach($invoices as $index => $invoice)
                                <tr>
                                    <td>{{$invoice->id}}</td>
                                    <td>{{$invoice->first_name}} {{$invoice->last_name}}</td>
                                    <td>{{$invoice->name}}</td>
                                    <td><a href="{{ route('pro-invoices.show', encrypt($invoice->id)) }}">{{ sprintf('%04d', $invoice->invoice_no)}}</a> </td>
                                    <td>{{$invoice->time_created}}</td>
    			                    <td>{{$invoice->due_date}}</td>
    			                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoice->updated_at)->diffForHumans() }}</td>
                                    <td>{{$invoice->status}}</td>
                                    <td>
                                        <a href="{{ route('pro-invoices.show', encrypt($invoice->id)) }}" title="View Invoice"><span class="fa fa-eye"></span></a>@if($invoice->status == 'Approved')|
                                        <a href="javascript:;" onclick="createInvoice('<?php echo $index; ?>')" style="color: green;" title="Create Tax Invoice"><i class="fa fa-file"></i></a>
                                        <form id="create-invoice-{{$index}}" action="{{ url('create-invoice') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$invoice->id}}">
                                        </form> @endif
                                        @if($invoice->status == 'Awaiting for Approval' || $invoice->status == 'Pending')
                                        | <a href="{{url('cancel-profoma/'.encrypt($invoice->id))}}" style="color: orange;" title="Cancel Proforma Invoice"> <i class="fa fa-times-circle"></i></a> |
                                        <a href="{{ route('pro-invoices.edit', encrypt($invoice->id)) }}" title="edit Profoma Invoice"><i class="fa fa-edit" style="color: blue;"></i></a> |
                                        <a href="{{ url('pro-invoices/destroy/'.encrypt($invoice->id))}}" onclick="return confirm('Are you sure you want to delete this record')"><i class="fa fa-trash" style="color: red;"></i></a>
                                        @elseif($invoice->status == 'Cancelled')
                                        <a href="{{url('resume-profoma/'.encrypt($invoice->id))}}"> Resume</a>
                                        @endif
    			                    </td>
                                </tr>
                                @endforeach
    	                    </tbody>
    	                </table>
                    </div>
                    <form id="frm-example" action="{{ url('delete-multiple-pro-invoices') }}" method="POST">
                        @csrf
                        <button id="submitButton" class="btn btn-danger btn-sm">{{ trans('navmenu.delete_selected') }}</button>
                    </form>
	            </div>
	        </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <!-- Datatables -->
    <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script>

    <script src="{{ asset('side/assets/vendor/sweetalert/sweetalert.min.js') }}"></script> <!-- SweetAlert Plugin Js --> 

    <script src="https://cdn.datatables.net/plug-ins/1.10.11/sorting/date-eu.js"></script>

    <script>
        $(function () {

            var userlang = "<?php echo app()->getLocale(); ?>";
            var languageUrl = "";
            if (userlang === 'en') {
                languageUrl = "{{ asset('side/assets/vendor/libs/English.json') }}";
            } else {
                languageUrl = "{{ asset('side/assets/vendor/libs/Swahili.json') }}";
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
        });
    </script>
@endsection