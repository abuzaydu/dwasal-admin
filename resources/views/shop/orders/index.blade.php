@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
@endsection
<script type="text/javascript">
    function createorder(index) {
        document.getElementById('create-order-'+index).submit();
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="icon-home"></i></a></li>
                    <li class="breadcrumb-item">Ouotes & Orders</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-6 col-md-6 col-sm-12 text-right">
                <form class="dashform row mb-0" action="{{url('f-orders')}}" method="POST" id="dashform">
                    @csrf
                    <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                    <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                    <!-- Date and time range -->
                    <div class="col-sm-12">
                        <div class="input-group mb-0">
                            <button type="button" class="btn btn-default mb-0 pull-right" id="reportrange"><span><i class="fa fa-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                        </div>
                    </div>
                    <!-- /.form group -->
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
                                    <th>Date Time</th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                         			<th>Last updated</th>
                         			<th style="text-align: center;">Actions</th>
                         		</tr>
                      		</thead>
                      		<tbody>
                      		    @foreach($orders as $key => $order)
                                <tr>
                                    <td>{{$order->id}}</td>
                                    <td>{{ $order->created_at }}</td>
                                    <td>{{$order->uuid}}</td>
                                    <td><a href="{{ route('orders.show', encrypt($order->id)) }}">{{ $order->first_name }} {{ $order->last_name}}</a></td>
                                    <td>{{ number_format($order->total, 2, '.', ',') }}</td>
                                    <td>{{ $order->status }}</td>
    			                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->updated_at)->diffForHumans() }}</td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('orders.show', encrypt($order->id)) }}" title="View order"><span class="fa fa-eye"></span></a>@if($order->status == 'Approved')|
                                        <a href="javascript:;" onclick="createorder('<?php echo $index; ?>')" style="color: green;" title="Create Tax order"><i class="fa fa-file"></i></a>
                                        <form id="create-order-{{$index}}" action="{{ url('create-order') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$order->id}}">
                                        </form> @endif
                                        @if($order->status == 'Awaiting for Approval')
                                        | <a href="{{url('cancel-profoma/'.encrypt($order->id))}}" style="color: orange;" title="Cancel Proforma order"> <i class="fa fa-times-circle"></i></a> |
                                        <a href="{{ route('orders.edit', encrypt($order->id)) }}" title="edit Profoma order"><i class="fa fa-edit" style="color: blue;"></i></a> |
                                        <a href="{{ url('pro-orders/destroy/'.encrypt($order->id))}}" onclick="return confirm('Are you sure you want to delete this record')"><i class="fa fa-trash" style="color: red;"></i></a>
                                        @elseif($order->status == 'Cancelled')
                                        <a href="{{url('resume-profoma/'.encrypt($order->id))}}"> Resume</a>
                                        @endif
    			                    </td>
                                </tr>
                                @endforeach
    	                    </tbody>
    	                </table>
                    </div>
                    <form id="frm-example" action="{{ url('delete-multiple-pro-orders') }}" method="POST">
                        @csrf
                        <button id="submitButton" class="btn btn-danger btn-sm">Delete Selected</button>
                    </form>
	            </div>
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
        });
    </script>
@endsection