@extends('layouts.app')
@section('page-styles')
    <link href="{{ asset('side/assets/vendor/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/css/dataTables.checkboxes.css') }}" rel="stylesheet" />
    <link href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css" rel="stylesheet">
@endsection
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmCancel(id) {
        Swal.fire({
          title: "Are you sure you want to cancel this contract",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes, Cancel It",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href = "{{ url('cancel-contract') }}/"+id;
            Swal.fire(
              "cancelled",
              "cancelled",
              'success'
            )
          }
        })
    }
</script>

@section('content')
    <!--breadcrumb-->
    <div class="block-header pt-4">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('my-default-page') }}"><i class="fa fa-home"></i></a></li>                            
                    <li class="breadcrumb-item">Sales & Invoices</li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>            
            <div class="col-lg-8 col-md-8 col-sm-12 text-right">
                <form class="dashform row g-1" action="{{ url('f-contracts') }}" method="POST" id="stockform">
                    @csrf
                    <div class="col-md-5">
                        @if(Auth::user()->can('create-contract'))
                        <a  class="btn btn-success btn-sm" href="{{route('contracts.create')}}"><i class="fa fa-edit"></i> New contract</a>
                        @endif
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
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content py-0">
                        <div class="tab-pane fade show active" id="manage-returns" role="tabpanel">
                            <table id="example" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px; display: block; overflow-x: auto;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th>Contract ID</th>
                                        <th>TL Name</th>
                                        <th>Driver Name</th>
                                        <th>Driver's Mobile</th>
                                        <th>Plate No.</th>
                                        <th>Chasis No.</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Created by</th>
                                        <th>Created At</th>
                                        <th>Last updated</th>
                                        <th style="text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contracts as $index => $contract)
                                    <tr>
                                        <td style="text-align: center;"><a href="{{ route('contracts.show', encrypt($contract->id)) }}">{{ $contract->cuid }}</a></td>
                                        <td>{{$contract->tl_name}}</td>
                                        <td>{{$contract->name}}</td>
                                        <td>{{$contract->phone}}</td>
                                        <td>{{$contract->device_number}}</td>
                                        <td>{{$contract->device_name}}</td>
                                        <td>{{date('d/m/Y', strtotime($contract->start_date))}}</td>
                                        <td>{{date('d/m/Y', strtotime($contract->end_date))}}</td>
                                        <td>{{ $contract->status }}</td>
                                        <td>{{ $contract->first_name }}</td>
                                        <td>{{$contract->created_at}}</td>
                                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $contract->updated_at)->diffForHumans() }}</td>
                                        <td style="text-align: center;">
                                                @if(!$contract->is_deleted && Auth::user()->can('edit-contract'))
                                            <a href="{{ route('contracts.edit', encrypt($contract->id)) }}"><i class="fa fa-edit" style="color: blue;"></i></a> |@endif
                                            @if(!$contract->is_deleted && Auth::user()->can('cancel-contract'))
                                            <a href="#" onclick="confirmCancel('<?php echo encrypt($contract->id); ?>')"><i class="fa fa-close" style="color: orange;"></i></a> |@endif
                                            @if($contract->is_deleted && Auth::user()->can('delete-contract'))
                                            <form id="delete-form-{{$index}}" method="POST" action="{{ route('contracts.destroy', encrypt($contract->id))}}" style="display: inline;">
                                                @csrf
                                                @method("DELETE")
                                                <a href="#" onclick="confirmDelete('<?php echo $index; ?>')"><i class="fa fa-trash" style="color: red;"></i></a>
                                            </form>
                                            @endif
                                        </td>
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
@endsection
@section('page-scripts')
    <!-- Datatables -->
    <!-- <script src="{{ asset('side/assets/vendor/datatable/js/jquery.dataTables.min.js') }}"></script> -->
    <!-- <script src="{{ asset('side/assets/vendor/datatable/js/dataTables.bootstrap5.min.js') }}"></script> -->
    <!-- <script src="{{ asset('side/assets/vendor/datatables-select/js/dataTables.select.min.js') }}"></script> -->
    <!-- <script src="{{ asset('side/assets/vendor/jquery-datatables-checkboxes-1.2.12/js/dataTables.checkboxes.js') }}"></script> -->

    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.colVis.min.js"></script>
    <script>
        $(function () {
            new DataTable('#example', {
                "columnDefs": [
                    // Hide second, third and fourth columns
                    { "visible": false, "targets": [9, 10, 11] }
                ],
                layout: {
                    topStart: {
                        buttons: [
                            'colvis'
                        ]
                    }
                }
            });
        });
    </script>
@endsection
