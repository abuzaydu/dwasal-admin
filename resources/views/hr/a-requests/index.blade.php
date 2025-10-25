@extends('layouts.hr')
    <script type="text/javascript">
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure, You want to delete this record?',
                showDenyButton: true,
                confirmButtonText: 'Yes Delete',
                denyButtonText: `Don't Delete`,
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                    Swal.fire('Deleted!', '', 'success')
                } else if (result.isDenied) {
                    Swal.fire('Record not deleted', '', 'info')
                }
            })
        }

        function submitEditForm(key) {
            document.getElementById('edit-form-' + key).submit();
        }
    </script>
@section('content')
    <div class="block-header py-lg-4 py-3">
        <div class="row g-3">
            <div class="col-md-6 col-sm-12">
                <ul class="breadcrumb mb-0 pt-2">
                    <li class="breadcrumb-item"><a href="javascript:void(0);" class="btn btn-sm btn-link ps-0 btn-toggle-fullwidth"><i class="fa fa-arrow-left"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ url('home')}}"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active">{{$page}}</li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end">
                
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <form class="report-form row g-3" action="{{ url('f-requisitions') }}" method="POST">
                @csrf
                @csrf
                <div class="col-md-5">
                    <a href="{{ route('requisitions.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus-square"></i> New Requisition</a>
                </div>
                <div class="col-md-3"></div>
                <div class="col-md-4">
                    <input type="hidden" name="start_date" id="start_input" value="">
                    <input type="hidden" name="end_date" id="end_input" value="">
                    <!-- Date and time range -->
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="arequests" class="table card-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Request ID</th>
                                <th style="text-align: center;">Request Date</th>
                                <th style="text-align: center;">Requested By</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Created At</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($arequests as $key => $request)
                            <tr class="row-selectable">
                                <td style="text-align: center;"><a href="{{ route('requisitions.show', encrypt($request->id)) }}">{{ $request->request_id }}</a></td>
                                <td>{{ date('d-m-Y', strtotime($request->request_date))}}</td>
                                <td>{{ $request->name}}</td>
                                <td>{{ $request->status }}</td>
                                <td>{{ $request->created_at }}</td>
                                <td>
                                    <a href="{{ route('requisitions.show', encrypt($request->id)) }}" class="text-secondary" title="View"><i class="fa fa-file-text-o"></i></a>
                                    @if(Session::get('curr_role') == 'team leader' && $request->status == 'Payment Received') | 
                                    <form id="edit-form-{{$key}}" action="{{ url('reconcile')}}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{$request->id}}">
                                        <a href="javascript:;" onclick="submitEditForm('<?php echo $key; ?>')" class="text-success" title=""><i class="fa fa-refresh"></i> Reconcile</a>
                                    </form>
                                    @endif
                                    @if(Session::get('curr_role') == 'project manager' || Session::get('curr_role') == 'team leader')
                                    @if($request->status == 'Awaiting for Approval' || $request->status == 'Approval in Progress') | 
                                    <form id="edit-form-{{$key}}" action="{{ url('requisitions/edit')}}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="request_id" value="{{$request->id}}">
                                        <a href="javascript:;" onclick="submitEditForm('<?php echo $key; ?>')" class="text-primary" title="Edit"><i class="fa fa-pencil"></i></a> | 
                                    </form>
                                    <form id="delete-form-{{$key}}" method="POST" action="{{ route('requisitions.destroy', encrypt($request->id)) }}" style="display: inline;">
                                        @csrf
                                        @method("DELETE")
                                        <a href="javascript:;" onclick="confirmDelete('<?php echo $key; ?>')" class="text-danger" title="Delete"><i class="fa fa-trash"></i></a>
                                    </form>
                                    @endif
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
@endsection